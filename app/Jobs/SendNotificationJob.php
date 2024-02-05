<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Models\UserLogin;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $episode;
    public function __construct($subject, $message)
    {
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $subject = $this->subject;

            $user = UserLogin::all();
            $fcmToken = [];

            foreach ($user as $item) {
                $fcmToken[] = $item->fcm_token;
            }
//
            PushNotificationService::pushMultipleNotification([
                'token' => $fcmToken,
                'title' => $subject['title'],
                'body' => 'New Episode has been post',
                'data' => [
                    'id' => '1',
                    'type' => '2',
                ]]);
        }catch (Exception $e){
            Log::error($e->getMessage());
        }
    }
}
