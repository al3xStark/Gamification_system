<?php

namespace App\Events;

class UpdateGamesSteamEvent
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
