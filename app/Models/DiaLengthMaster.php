<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\DiaLengthSlave;
use Illuminate\Database\Eloquent\Model;

class DiaLengthMaster extends Model
{
    protected $fillable = ['file_name', 'identify', 'description', 'length', 'diagonal', 'diameter', 'status'];
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dia_length_master';

    public function slave()
    {
        return $this->hasMany(DiaLengthSlave::class, 'dia_length_master_id');
    }

    public function getCreatedAtAttribute($date)
    {
        return (new Carbon($date))->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtAttribute($date)
    {
        return (new Carbon($date))->format('Y-m-d H:i:s');
    }
}