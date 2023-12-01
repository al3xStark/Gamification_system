<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\EventUser;
use Exception;

class EventJobs extends Jobs
{

    /**
     * @var string
     */
    public $queue = 'events';

    /**
     * @var EventUser
     */
    private EventUser $eventUser;

    /**
     * @var string
     */
    private string $eventType;

    /**
     * Create a new job instance.
     *
     * @param string $eventType
     * @param EventUser $eventUser
     */
    public function __construct(string $eventType, EventUser $eventUser)
    {
        $this->eventType = $eventType;
        $this->eventUser = $eventUser;
    }

    /**
     * Execute the job.
     *
     * @return bool
     * @throws Exception
     */
    public function handle(): bool
    {
        $eventUser = $this->eventUser->refresh();
        $achievment = $eventUser->event->achievments()->where([
            'event_fields_hash' => $eventUser->fields_hash,
        ])->first();

        if (
            $eventUser
            && $achievment
            && empty($achievment->users()->where(['user_id' => $eventUser->user_id])->first())
            && $eventUser->count >= $achievment->count
        ) {
            $achievment->users()->sync($eventUser->user_id, false);
        }

        return true;
    }
}
