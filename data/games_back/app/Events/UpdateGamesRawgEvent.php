<?php

namespace App\Events;

class UpdateGamesRawgEvent
{
    public mixed $data;

    /**
     * @param mixed $data
     */
    public function __construct(mixed $data)
    {
        $this->data = $data;
    }
}
