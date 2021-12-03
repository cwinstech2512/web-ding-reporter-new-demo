<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DiaLengthMaster;

class DiaLengthSlave extends Model
{
    protected $fillable = ['data_value', 'use_date', 'dia_length_master_id'];
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dia_length_slave';

    public function master()
    {
        return $this->belongsTo(DiaLengthMaster::class, 'id');
    }
}