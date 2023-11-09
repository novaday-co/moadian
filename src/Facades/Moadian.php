<?php

namespace Novaday\Moadian\Facades;

use Illuminate\Support\Facades\Facade;

class Moadian extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Novaday\Moadian\Moadian';
    }
}