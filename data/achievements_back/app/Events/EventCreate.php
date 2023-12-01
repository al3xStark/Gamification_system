<?php

namespace App\Events;

use App\Models\EventPartnerUser;

class EventCreate
{
    public EventPartnerUser $eventPartnerUser;

    /**
     * @param EventPartnerUser $eventPartnerUser
     */
    public function __construct(EventPartnerUser $eventPartnerUser)
    {
        $this->eventPartnerUser = $eventPartnerUser;
    }
}
