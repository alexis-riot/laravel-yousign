<?php

namespace AlexisRiot\Yousign\Facades;

use Illuminate\Support\Facades\Facade;

class Yousign extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'yousign';
    }
}
