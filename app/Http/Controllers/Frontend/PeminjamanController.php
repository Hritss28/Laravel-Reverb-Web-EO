<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Barang;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }
    public function index()
    {
        $peminjamans = Peminjaman::with(['barang', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('frontend.peminjaman.index', compact('peminjamans'));
    }

    public function create(Request $request)
    {
        try {
            // Ambil data barang yang tersedia untuk dipilih
            $barangs = Barang::with('kategori')->tersedia()->get();
            
            // Cek apakah parameter barang_id ada
            $barang = null;
            if (request()->has('barang_id')) {
                $barang = Barang::find(request('barang_id'));
            }
            
            return view('frontend.peminjaman.create', compact('barangs', 'barang'));
        } catch (\Exception $e) {
            return view('frontend.peminjaman.create', [
                'barangs' => [],
                'barang' => null 
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'barang_id' => 'required|exists:barangs,id',
                'jumlah' => 'required|integer|min:1',
                'tanggal_pinjam' => 'required|date|after_or_equal:today',
                'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
                'keperluan' => 'nullable|string|max:500'
            ]);

            // Validasi stok tersedia
            $barang = Barang::find($validated['barang_id']);
            if ($validated['jumlah'] > $barang->stok_tersedia) {
                return back()->withErrors([
                    'jumlah' => 'Jumlah yang diminta melebihi stok tersedia (' . $barang->stok_tersedia . ' unit)'
                ])->withInput();
            }

            DB::beginTransaction();

            // Buat peminjaman baru
            $peminjaman = new Peminjaman();
            $peminjaman->user_id = Auth::id();
            $peminjaman->barang_id = $validated['barang_id'];
            $peminjaman->jumlah = $validated['jumlah'];
            $peminjaman->tanggal_pinjam = $validated['tanggal_pinjam'];
            $peminjaman->tanggal_kembali_rencana = $validated['tanggal_kembali_rencana'];
            $peminjaman->keperluan = $validated['keperluan'] ?? null;
            $peminjaman->status = 'pending';
            $peminjaman->payment_status = 'pending';
            $peminjaman->save();

            // Update biaya setelah save
            $peminjaman->updateBiaya();
            
            Log::info('Peminjaman created', [
                'peminjaman_id' => $peminjaman->id,
                'user_id' => $peminjaman->user_id,
                'barang_id' => $peminjaman->barang_id,
                'total_pembayaran' => $peminjaman->total_pembayaran
            ]);

            DB::commit();

            return redirect()->route('frontend.peminjaman.payment', $peminjaman->id)
                ->with('success', 'Peminjaman berhasil diajukan. Silakan lakukan pembayaran.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation errors to show them properly
            throw $e;
        } catch (\Exception $e) {
            DB::rollback();
            
            // Log the actual error for debugging
            Log::error('Error creating peminjaman', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            
            // Show user-friendly message
            return back()->withErrors([
                'error' => 'Terjadi kesalahan saat mengajukan peminjaman. Silakan coba lagi atau hubungi admin jika masalah berlanjut.'
            ])->withInput();
        }
    }

    public function payment($id)
    {
        try {
            $peminjaman = Peminjaman::with(['barang', 'user'])->findOrFail($id);
            
            // Pastikan hanya pemilik yang bisa akses
            if ($peminjaman->user_id !== Auth::id()) {
                abort(403);
            }

            // Debug log
            Log::info('Payment page data', [
                'peminjaman_id' => $peminjaman->id,
                'total_biaya_sewa' => $peminjaman->total_biaya_sewa,
                'total_deposit' => $peminjaman->total_deposit,
                'total_pembayaran' => $peminjaman->total_pembayaran,
                'formatted_total_pembayaran' => $peminjaman->formatted_total_pembayaran,
                'payment_status' => $peminjaman->payment_status
            ]);

            // Jika sudah dibayar, redirect ke detail
            if ($peminjaman->payment_status === 'paid') {
                return redirect()->route('frontend.peminjaman.show', $peminjaman->id)
                    ->with('info', 'Peminjaman ini sudah dibayar.');
            }

            // Pastikan ada biaya yang valid - Force update jika perlu
            if (!$peminjaman->total_pembayaran || $peminjaman->total_pembayaran <= 0) {
                Log::warning('Total pembayaran kosong, menghitung ulang', [
                    'peminjaman_id' => $peminjaman->id
                ]);
                
                $peminjaman->updateBiaya();
                $peminjaman->refresh();
                
                if (!$peminjaman->total_pembayaran || $peminjaman->total_pembayaran <= 0) {
                    return back()->withErrors([
                        'error' => 'Total pembayaran tidak valid. Silakan hubungi admin.'
                    ]);
                }
            }

            // Generate Midtrans snap token
            $midtransResponse = $this->midtransService->createSnapToken($peminjaman);

            if (!$midtransResponse['success']) {
                Log::error('Failed to create snap token', [
                    'peminjaman_id' => $peminjaman->id,
                    'error' => $midtransResponse['message']
                ]);
                
                return back()->withErrors([
                    'error' => 'Gagal membuat token pembayaran: ' . $midtransResponse['message']
                ]);
            }

            return view('frontend.peminjaman.payment', [
                'peminjaman' => $peminjaman,
                'snapToken' => $midtransResponse['snap_token'],
                'clientKey' => config('midtrans.client_key')
            ]);

        } catch (\Exception $e) {
            Log::error('Error in payment method', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'peminjaman_id' => $id
            ]);
            
            return redirect()->route('frontend.peminjaman.index')
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $peminjaman = Peminjaman::with(['barang', 'user'])->findOrFail($id);
        
        // Pastikan hanya pemilik yang bisa akses
        if ($peminjaman->user_id !== Auth::id()) {
            abort(403);
        }

        return view('frontend.peminjaman.show', compact('peminjaman'));
    }

    // API untuk mendapatkan detail barang dan hitung harga
    public function getBarangDetails(Request $request)
    {
        $barangId = $request->barang_id;
        $jumlah = $request->jumlah ?? 1;
        $tanggalPinjam = $request->tanggal_pinjam;
        $tanggalKembali = $request->tanggal_kembali;

        if (!$barangId || !$tanggalPinjam || !$tanggalKembali) {
            return response()->json(['error' => 'Data tidak lengkap'], 400);
        }

        $barang = Barang::find($barangId);
        if (!$barang) {
            return response()->json(['error' => 'Barang tidak ditemukan'], 404);
        }

        // Hitung jumlah hari
        $startDate = Carbon::parse($tanggalPinjam);
        $endDate = Carbon::parse($tanggalKembali);
        $jumlahHari = $startDate->diffInDays($endDate) + 1;

        // Hitung biaya
        $biayaSewa = $jumlahHari * $barang->harga_sewa_per_hari * $jumlah;
        $biayaDeposit = $barang->biaya_deposit * $jumlah;
        $totalBiaya = $biayaSewa + $biayaDeposit;

        return response()->json([
            'barang' => [
                'nama' => $barang->nama,
                'harga_sewa_per_hari' => $barang->harga_sewa_per_hari,
                'biaya_deposit' => $barang->biaya_deposit,
                'stok_tersedia' => $barang->stok_tersedia,
                'formatted_harga_sewa' => $barang->formatted_harga_sewa,
                'formatted_deposit' => $barang->formatted_deposit
            ],
            'perhitungan' => [
                'jumlah_hari' => $jumlahHari,
                'jumlah_barang' => $jumlah,
                'biaya_sewa' => $biayaSewa,
                'biaya_deposit' => $biayaDeposit,
                'total_biaya' => $totalBiaya,
                'formatted_biaya_sewa' => 'Rp ' . number_format($biayaSewa, 0, ',', '.'),
                'formatted_biaya_deposit' => 'Rp ' . number_format($biayaDeposit, 0, ',', '.'),
                'formatted_total' => 'Rp ' . number_format($totalBiaya, 0, ',', '.')
            ]
        ]);
    }

    // Callback handlers untuk Midtrans
    public function paymentFinish(Request $request)
    {
        $orderId = $request->order_id;
        $statusCode = $request->status_code;
        $transactionStatus = $request->transaction_status;

        Log::info('Payment finish callback', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'transaction_status' => $transactionStatus,
            'request_data' => $request->all()
        ]);

        $peminjaman = Peminjaman::where('midtrans_order_id', $orderId)->first();

        if ($peminjaman) {
            // Jika status dari Midtrans menunjukkan pembayaran berhasil
            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                $peminjaman->update([
                    'payment_status' => Peminjaman::PAYMENT_PAID,
                    'paid_at' => now(),
                    'midtrans_response' => $request->all()
                ]);
                
                Log::info('Payment status updated to paid', [
                    'peminjaman_id' => $peminjaman->id,
                    'order_id' => $orderId
                ]);
                
                return redirect()->route('frontend.peminjaman.show', $peminjaman->id)
                    ->with('success', 'Pembayaran berhasil diproses!');
            } else {
                // Jika masih pending atau status lainnya
                Log::info('Payment not yet completed', [
                    'peminjaman_id' => $peminjaman->id,
                    'order_id' => $orderId,
                    'status' => $transactionStatus
                ]);
                
                return redirect()->route('frontend.peminjaman.show', $peminjaman->id)
                    ->with('info', 'Status pembayaran: ' . ucfirst($transactionStatus));
            }
        }

        Log::error('Peminjaman not found for order_id', ['order_id' => $orderId]);
        
        return redirect()->route('frontend.peminjaman.index')
            ->with('error', 'Terjadi kesalahan dalam pemrosesan pembayaran.');
    }

    public function paymentUnfinish(Request $request)
    {
        return redirect()->route('frontend.peminjaman.index')
            ->with('warning', 'Pembayaran belum diselesaikan.');
    }

    public function paymentError(Request $request)
    {
        return redirect()->route('frontend.peminjaman.index')
            ->with('error', 'Terjadi kesalahan dalam pembayaran.');
    }

    // Webhook untuk callback Midtrans
    public function paymentCallback(Request $request)
    {
        // Log semua request untuk debugging
        Log::info('Midtrans callback received', [
            'all_data' => $request->all(),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'ip' => $request->ip()
        ]);

        $result = $this->midtransService->handleCallback($request->all());
        
        if ($result['success']) {
            Log::info('Callback processed successfully', $result);
            return response()->json(['status' => 'success']);
        }
        
        Log::error('Callback processing failed', $result);
        return response()->json(['status' => 'error', 'message' => $result['message']], 400);
    }
}