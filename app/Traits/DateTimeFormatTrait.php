<?php
namespace App\Traits;

use Carbon\Carbon;

trait DateTimeFormatTrait
{
    public function getCreatedAtAttribute($date)
    {
        return (new Carbon($date))->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($date)
    {
        return (new Carbon($date))->format('Y-m-d H:i:s');
    }
}