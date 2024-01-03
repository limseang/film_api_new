<?php

namespace App\Jobs;

use App\Models\Film;
use App\Models\Type;
use App\Models\UserLogin;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log as log;
use Exception;

class CommingSoonFilm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{
        $film = Film::where('type', 10)->get();
        $today = date('d/m/Y');
        foreach ($film as $item) {
            var_dump($item->release_date, $today);
          if($item->release_date == $today){
              $item->type = 9;
              $item->created_at = now();
              $item->save();
              $user = UserLogin::all();
              foreach ($user as $items){
                  $data = [
                      'token' => $items->fcm_token,
                      'title' => $film->title,
                      'body' => 'Now Showing',
                      'data' => [
                          'id' => $item->id,
                          'type' => '2',
                      ]
                  ];
                  PushNotificationService::pushNotification($data);
              }
          }

        }
        }catch (Exception $e){
            log::error($e->getMessage());
        }

    }

}
