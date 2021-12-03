<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RTNessSlave;

class RTNessMaster extends Model
{
    protected $fillable = ['file_name', 'description'];
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rt_ness_master';

    public function slave()
    {
        return $this->hasMany(RTNessSlave::class, 'rt_ness_master_id');
    }
}