<?php

namespace App\Util;
use DateTime;
use DateTimeInterface;

class Date
{
    public function get($datetime = 'now', $timezone = null): DateTimeInterface
    {
        return new DateTime($datetime, $timezone);
    }
}