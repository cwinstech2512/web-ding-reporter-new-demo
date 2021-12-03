<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RTPRNessSlave;

class RTPRNessMaster extends Model
{
    protected $fillable = ['file_name', 'description'];
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rtpr_ness_master';

    public function slave()
    {
        return $this->hasMany(RTPRNessSlave::class, 'rtpr_ness_master_id');
    }
}