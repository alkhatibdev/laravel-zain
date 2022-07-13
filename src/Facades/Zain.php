<?php

namespace AlkhatibDev\LaravelZain\Facades;

use Illuminate\Support\Facades\Facade;

class Zain extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Zain';
    }

}
