<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RTNessMaster;

class RTNessSlave extends Model
{
    protected $fillable = ['numness', 'thickness', 'rt_ness_master_id'];
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rt_ness_slave';

    public function master()
    {
        return $this->belongsTo(RTNessMaster::class, 'id');
    }
}