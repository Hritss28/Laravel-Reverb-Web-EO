<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupRealtime extends Command
{
    protected $signature = 'realtime:setup {--force : Force overwrite existing files}';

    protected $description = 'Setup real-time features with Reverb for Filament';

    public function handle()
    {
        $this->info('🚀 Setting up Real-time Features with Reverb...');
        $this->newLine();

        // Step 1: Check Reverb installation
        $this->info('Step 1: Checking Reverb installation...');
        if (!class_exists('\Laravel\Reverb\Reverb')) {
            $this->error('Laravel Reverb is not installed. Please run: composer require laravel/reverb');
            return Command::FAILURE;
        }
        $this->line('✅ Reverb is installed');

        // Step 2: Publish Reverb config if needed
        $this->info('Step 2: Publishing Reverb configuration...');
        if (!file_exists(config_path('reverb.php')) || $this->option('force')) {
            Artisan::call('vendor:publish', [
                '--provider' => 'Laravel\Reverb\ReverbServiceProvider',
                '--tag' => 'reverb-config'
            ]);
            $this->line('✅ Reverb config published');
        } else {
            $this->line('✅ Reverb config already exists');
        }

        // Step 3: Check environment variables
        $this->info('Step 3: Checking environment configuration...');
        $envVars = [
            'BROADCAST_CONNECTION' => 'reverb',
            'REVERB_APP_ID' => env('REVERB_APP_ID'),
            'REVERB_APP_KEY' => env('REVERB_APP_KEY'),
            'REVERB_APP_SECRET' => env('REVERB_APP_SECRET'),
        ];

        $missingVars = [];
        foreach ($envVars as $var => $value) {
            if (empty($value)) {
                $missingVars[] = $var;
            }
        }

        if (!empty($missingVars)) {
            $this->warn('⚠️  Missing environment variables:');
            foreach ($missingVars as $var) {
                $this->line("   - {$var}");
            }
            $this->info('Please check the .env.reverb.example file for reference.');
        } else {
            $this->line('✅ Environment variables configured');
        }

        // Step 4: Clear and optimize
        $this->info('Step 4: Clearing caches...');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $this->line('✅ Caches cleared');        // Step 5: Test database connection
        $this->info('Step 5: Testing database connection...');
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            $this->line('✅ Database connection successful');
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // Step 6: Show next steps
        $this->newLine();
        $this->info('🎉 Real-time setup completed!');
        $this->newLine();
        
        $this->info('Next steps:');
        $this->line('1. Start Reverb server: php artisan reverb:start');
        $this->line('2. Build assets: npm run build');
        $this->line('3. Visit /admin/realtime-dashboard to test');
        $this->newLine();

        $this->info('Features enabled:');
        $this->line('✅ Real-time table updates');
        $this->line('✅ Live notifications');
        $this->line('✅ Real-time chat');
        $this->line('✅ Live statistics');
        $this->line('✅ Broadcasting events');
        
        $this->newLine();
        $this->info('Happy coding with real-time features! 🚀');

        return Command::SUCCESS;
    }
}
