<?php

namespace Wirgen\HermitageClient;

use Illuminate\Support\Facades\Facade;

class HermitageClientFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return HermitageClient::class;
    }
}
