<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Peminjaman;
use Midtrans\Config;
use Midtrans\Transaction;
use Exception;

class SyncPaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'payment:sync {--order-id= : Specific order ID to sync}';

    /**
     * The console command description.
     */
    protected $description = 'Sync payment status with Midtrans for pending payments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Set Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        $this->info('🔄 Starting payment status synchronization...');

        $query = Peminjaman::where('payment_status', 'pending')
            ->whereNotNull('midtrans_order_id');

        // If specific order ID is provided
        if ($orderId = $this->option('order-id')) {
            $query->where('midtrans_order_id', $orderId);
        }

        $peminjamans = $query->get();

        if ($peminjamans->isEmpty()) {
            $this->info('✅ No pending payments found to sync.');
            return;
        }

        $this->info("Found {$peminjamans->count()} pending payment(s) to check.");

        $updated = 0;
        $failed = 0;

        foreach ($peminjamans as $peminjaman) {
            $this->line("Checking Order ID: {$peminjaman->midtrans_order_id}");
            
            try {
                // Check status from Midtrans
                $status = Transaction::status($peminjaman->midtrans_order_id);
                
                $transactionStatus = $status->transaction_status ?? 'unknown';
                $this->line("  Midtrans Status: {$transactionStatus}");
                
                // Update status based on Midtrans response
                if (in_array($transactionStatus, ['capture', 'settlement'])) {
                    $peminjaman->update([
                        'payment_status' => 'paid',
                        'paid_at' => now(),
                        'status' => 'disetujui', // Auto approve when paid
                        'midtrans_response' => json_encode($status)
                    ]);
                    $this->info("  ✅ Updated to PAID and APPROVED");
                    $updated++;
                    
                } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                    $peminjaman->update([
                        'payment_status' => 'failed',
                        'midtrans_response' => json_encode($status)
                    ]);
                    $this->warn("  ❌ Updated to FAILED");
                    $updated++;
                    
                } else {
                    $this->line("  ⏳ Remains PENDING");
                }
                
            } catch (Exception $e) {
                $this->error("  ❌ Error: " . $e->getMessage());
                $failed++;
            }
        }

        $this->info("\n📊 Summary:");
        $this->info("   Updated: {$updated}");
        $this->info("   Failed: {$failed}");
        $this->info("   Total processed: " . ($updated + $failed));
        
        if ($updated > 0) {
            $this->info("✅ Payment status synchronization completed successfully!");
        }
    }
}
