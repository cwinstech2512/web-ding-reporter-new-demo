<?php
namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait UserStampsTrait
{
    public static function boot()
    {
        parent::boot();
        if(Auth::user()) {
            // first we tell the model what to do on a creating event
            static::creating( function($modelName) {
                $createdByColumnName = 'created_user';
                $modelName->$createdByColumnName = Auth::user()->name;
            });

            // // then we tell the model what to do on an updating event
            static::updating( function($modelName) {
                $updatedByColumnName = 'updated_user';
                $modelName->$updatedByColumnName = Auth::user()->name;
            });
        }
    }
}