<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\EventPartnerUser;
use Exception;
use Illuminate\Support\Facades\Http;

class EventJobs extends Jobs
{

    /**
     * @var string
     */
    public $queue = 'events';

    /**
     * @var EventPartnerUser
     */
    private EventPartnerUser $eventPartnerUser;

    /**
     * @var string
     */
    private string $eventType;

    /**
     * Create a new job instance.
     *
     * @param string $eventType
     * @param EventPartnerUser $eventPartnerUser
     */
    public function __construct(string $eventType, EventPartnerUser $eventPartnerUser)
    {
        $this->eventType = $eventType;
        $this->eventPartnerUser = $eventPartnerUser;
    }

    /**
     * Execute the job.
     *
     * @return bool
     * @throws Exception
     */
    public function handle(): bool
    {
        $eventPartnerUser = $this->eventPartnerUser->refresh();
        $achievements = $eventPartnerUser->event->achievements()->where([
            'active' => true,
            'event_fields_hash' => $eventPartnerUser->fields_hash,
        ])->get();

        if (!$eventPartnerUser){
            return false;
        }

        foreach ($achievements as $achievement) {
            if (
                empty($achievement->partnerUsers()->where(['partner_users.id' => $eventPartnerUser->user->id])->first())
                && $eventPartnerUser->count >= $achievement->count
            ) {
                $achievement->partnerUsers()->sync($eventPartnerUser->user->id, false);

                if ($achievement->project->callback_url) {
                    $data = [
                        "type" => "newUserAchievement",
                        "project_id" => $achievement->project->id,
                        "user_id" => $eventPartnerUser->user->user_id,
                        "achievement" => $achievement->name,
                    ];

                    ksort($data);
                    $sign = hash('sha256', urldecode(http_build_query($data)) . $achievement->project->api_key);

                    Http::withHeaders([
                        'signature' => $sign
                    ])->post($achievement->project->callback_url, $data);
                }
            }
        }

        return true;
    }
}
