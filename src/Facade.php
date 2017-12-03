<?php

namespace Backtheweb\ReCaptcha;


class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'ReCaptcha';
    }
}