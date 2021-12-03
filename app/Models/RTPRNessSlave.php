<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RTPRNessMaster;

class RTPRNessSlave extends Model
{
    protected $fillable = ['numness', 'thickness', 'rtpr_ness_master_id'];
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rtpr_ness_slave';

    public function master()
    {
        return $this->belongsTo(RTPRNessMaster::class, 'id');
    }
}