<?php

namespace App\Listeners;

use App\Jobs\RawgJobs;

class UpdateGamesRawgListener
{

    /**
     * Handle for order events.
     *
     * @param $event
     * @return bool
     */
    public function handle($event): bool
    {
        dispatch(new RawgJobs(self::getShortClassName($event), $event->data));
        return true;
    }

    /**
     * return short class name by class
     *
     * @param $class
     * @return string
     */
    public static function getShortClassName($class): string
    {
        if (!is_object($class)) {
            return '';
        }

        $path = explode('\\', get_class($class));

        $shortName = array_pop($path);

        return $shortName ?: '';
    }
}
