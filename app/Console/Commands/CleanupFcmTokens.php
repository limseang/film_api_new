<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserLogin;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Support\Facades\Log;

class CleanupFcmTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fcm:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up invalid FCM tokens from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting FCM token cleanup...');
        
        try {
            // Get all FCM tokens
            $userLogins = UserLogin::whereNotNull('fcm_token')->get();
            $this->info('Found ' . $userLogins->count() . ' FCM tokens to check');
            
            if ($userLogins->isEmpty()) {
                $this->warn('No FCM tokens found in the database');
                return 0;
            }
            
            // Initialize Firebase Messaging
            $credentialsPath = base_path('firebase_credentials.json');
            
            if (!file_exists($credentialsPath)) {
                $this->error('Firebase credentials file not found at: ' . $credentialsPath);
                return 1;
            }
            
            $firebase = (new Factory)->withServiceAccount($credentialsPath);
            $messaging = $firebase->createMessaging();
            
            // Check if Firebase connection is working
            try {
                // Try to send a test message to a non-existent token to verify Firebase connection
                $testToken = 'test_token_for_validation_only';
                $messaging->validateRegistrationTokens([$testToken]);
                $this->info('Firebase connection is working');
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'invalid_grant') !== false) {
                    $this->error('Firebase credentials have expired. Please refresh your credentials file.');
                    $this->info('You can generate a new private key from your Firebase project settings.');
                    return 1;
                }
                
                $this->info('Firebase connection validated');
            }
            
            // Process tokens in batches
            $tokenCount = $userLogins->count();
            $validCount = 0;
            $invalidCount = 0;
            $processedCount = 0;
            
            $this->output->progressStart($tokenCount);
            
            foreach ($userLogins->chunk(100) as $chunk) {
                $tokens = $chunk->pluck('fcm_token')->toArray();
                
                try {
                    $result = $messaging->validateRegistrationTokens($tokens);
                    
                    $validTokens = $result['valid'] ?? [];
                    $invalidTokens = $result['invalid'] ?? [];
                    $unknownTokens = $result['unknown'] ?? [];
                    
                    $validCount += count($validTokens);
                    $invalidCount += count($invalidTokens) + count($unknownTokens);
                    
                    // Remove invalid tokens from database
                    if (!empty($invalidTokens) || !empty($unknownTokens)) {
                        $tokensToRemove = array_merge($invalidTokens, $unknownTokens);
                        UserLogin::whereIn('fcm_token', $tokensToRemove)
                            ->update(['fcm_token' => null]);
                    }
                } catch (\Exception $e) {
                    $this->error('Error validating tokens: ' . $e->getMessage());
                    Log::error('FCM token validation error: ' . $e->getMessage());
                }
                
                $processedCount += $chunk->count();
                $this->output->progressAdvance($chunk->count());
                
                // Add a small delay to prevent rate limiting
                usleep(500000); // 0.5 seconds
            }
            
            $this->output->progressFinish();
            
            $this->info('FCM token cleanup completed:');
            $this->info("- Total tokens processed: $processedCount");
            $this->info("- Valid tokens: $validCount");
            $this->info("- Invalid tokens removed: $invalidCount");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error cleaning up FCM tokens: ' . $e->getMessage());
            Log::error('FCM token cleanup error: ' . $e->getMessage());
            return 1;
        }
    }
}