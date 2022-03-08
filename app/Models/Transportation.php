<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transportation extends Model
{
    protected $fillable = ['id', 'num_no', 'name', 'sort'];
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transportation';
}