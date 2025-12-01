<?php

namespace App\Services;

use App\Models\Peminjaman;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Illuminate\Support\Facades\Log;
use Exception;

class MidtransService
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createSnapToken(Peminjaman $peminjaman): array
    {
        try {
            Log::info('Creating snap token for peminjaman', [
                'peminjaman_id' => $peminjaman->id,
                'total_biaya_sewa' => $peminjaman->total_biaya_sewa,
                'total_deposit' => $peminjaman->total_deposit,
                'total_pembayaran' => $peminjaman->total_pembayaran,
                'denda_keterlambatan' => $peminjaman->denda_keterlambatan
            ]);

            // Pastikan peminjaman memiliki relasi barang dan user
            if (!$peminjaman->barang) {
                $peminjaman->load('barang');
            }
            if (!$peminjaman->user) {
                $peminjaman->load('user');
            }

            // Hitung biaya jika belum ada
            if (!$peminjaman->total_pembayaran || $peminjaman->total_pembayaran <= 0) {
                Log::warning('Total pembayaran kosong, menghitung ulang');
                $peminjaman->updateBiaya();
                $peminjaman->refresh();
            }

            // Validasi total pembayaran
            if (!$peminjaman->total_pembayaran || $peminjaman->total_pembayaran <= 0) {
                throw new Exception('Total pembayaran tidak valid: ' . $peminjaman->total_pembayaran);
            }

            // Validasi minimal amount (Midtrans minimal 1 rupiah)
            $grossAmount = max(1, (int) round($peminjaman->total_pembayaran));

            $orderId = $peminjaman->kode_peminjaman . '-' . time();
            
            // Build item details dengan validasi
            $itemDetails = $this->buildItemDetails($peminjaman);
            
            if (empty($itemDetails)) {
                throw new Exception('Item details kosong');
            }

            // Determine base URL berdasarkan environment
            $baseUrl = $this->getBaseUrl();
            
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'customer_details' => [
                    'first_name' => $peminjaman->user->name ?? 'Customer',
                    'email' => $peminjaman->user->email ?? 'customer@example.com',
                ],
                'item_details' => $itemDetails,
                'callbacks' => [
                    'finish' => $baseUrl . '/frontend/peminjaman/payment/finish',
                    'unfinish' => $baseUrl . '/frontend/peminjaman/payment/unfinish',
                    'error' => $baseUrl . '/frontend/peminjaman/payment/error'
                ]
            ];

            Log::info('Midtrans params', $params);

            $snapToken = Snap::getSnapToken($params);

            // Update peminjaman dengan order ID
            $peminjaman->update([
                'midtrans_order_id' => $orderId
            ]);

            Log::info('Snap token created successfully', [
                'order_id' => $orderId,
                'snap_token' => $snapToken
            ]);

            return [
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId
            ];

        } catch (Exception $e) {
            Log::error('Error creating snap token', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'peminjaman_id' => $peminjaman->id ?? null
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function buildItemDetails(Peminjaman $peminjaman): array
    {
        $itemDetails = [];

        // Tambahkan biaya sewa jika ada
        if ($peminjaman->total_biaya_sewa && $peminjaman->total_biaya_sewa > 0) {
            $itemDetails[] = [
                'id' => 'sewa-' . $peminjaman->barang->id,
                'price' => max(1, (int) round($peminjaman->total_biaya_sewa)),
                'quantity' => 1,
                'name' => 'Sewa ' . $peminjaman->barang->nama . ' (' . $peminjaman->jumlah_hari . ' hari)',
            ];
        }

        // Tambahkan deposit jika ada
        if ($peminjaman->total_deposit && $peminjaman->total_deposit > 0) {
            $itemDetails[] = [
                'id' => 'deposit-' . $peminjaman->barang->id,
                'price' => max(1, (int) round($peminjaman->total_deposit)),
                'quantity' => 1,
                'name' => 'Deposit ' . $peminjaman->barang->nama,
            ];
        }

        // Tambahkan denda jika ada
        if ($peminjaman->denda_keterlambatan && $peminjaman->denda_keterlambatan > 0) {
            $itemDetails[] = [
                'id' => 'denda-' . $peminjaman->id,
                'price' => max(1, (int) round($peminjaman->denda_keterlambatan)),
                'quantity' => 1,
                'name' => 'Denda Keterlambatan',
            ];
        }

        // Jika masih kosong, buat item default
        if (empty($itemDetails)) {
            $itemDetails[] = [
                'id' => 'peminjaman-' . $peminjaman->id,
                'price' => max(1, (int) round($peminjaman->total_pembayaran)),
                'quantity' => 1,
                'name' => 'Peminjaman ' . $peminjaman->barang->nama,
            ];
        }

        return $itemDetails;
    }

    private function getBaseUrl(): string
    {
        $baseUrl = config('app.url');
        
        // Handle localhost dengan port
        if (str_contains($baseUrl, 'localhost')) {
            return $baseUrl;
        }
        
        return $baseUrl;
    }

    public function handleCallback(array $data): array
    {
        try {
            $orderId = $data['order_id'] ?? null;
            $transactionStatus = $data['transaction_status'] ?? null;
            $statusCode = $data['status_code'] ?? null;

            if (!$orderId) {
                throw new Exception('Order ID tidak ditemukan');
            }

            // Verifikasi signature jika ada
            if (isset($data['signature_key'])) {
                $signatureKey = hash('sha512', $orderId . $statusCode . $data['gross_amount'] . config('midtrans.server_key'));
                if ($data['signature_key'] !== $signatureKey) {
                    throw new Exception('Invalid signature key');
                }
            }

            // Cari peminjaman berdasarkan order ID
            $peminjaman = Peminjaman::where('midtrans_order_id', $orderId)->first();
            
            if (!$peminjaman) {
                throw new Exception('Peminjaman tidak ditemukan untuk order ID: ' . $orderId);
            }

            // Update status berdasarkan response Midtrans
            switch ($transactionStatus) {
                case 'capture':
                case 'settlement':
                    $peminjaman->update([
                        'payment_status' => Peminjaman::PAYMENT_PAID,
                        'paid_at' => now(),
                        'midtrans_response' => $data
                    ]);
                    Log::info('Payment successful', ['order_id' => $orderId]);
                    break;
                    
                case 'pending':
                    $peminjaman->update([
                        'payment_status' => Peminjaman::PAYMENT_PENDING,
                        'midtrans_response' => $data
                    ]);
                    Log::info('Payment pending', ['order_id' => $orderId]);
                    break;
                    
                case 'deny':
                case 'expire':
                case 'cancel':
                    $peminjaman->update([
                        'payment_status' => Peminjaman::PAYMENT_FAILED,
                        'midtrans_response' => $data
                    ]);
                    Log::info('Payment failed/cancelled', ['order_id' => $orderId, 'status' => $transactionStatus]);
                    break;
                    
                default:
                    Log::warning('Unknown transaction status', ['order_id' => $orderId, 'status' => $transactionStatus]);
            }

            return [
                'success' => true,
                'message' => 'Callback processed successfully'
            ];

        } catch (Exception $e) {
            Log::error('Error handling callback', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
