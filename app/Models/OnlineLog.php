<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineLog extends Model
{
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'onlinelog';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['online_time', 'type', 'name', 'userid'];
}
