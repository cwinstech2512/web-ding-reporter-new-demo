<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleLog extends Model
{
    protected $fillable = ['full_path', 'file_name', 'function_path', 'updated_user', 'created_user'];
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sample_log';

    public function getFullPathAttribute()
    {
        return config('filesystems.disks.public_sample.realation_path') . '/' . $this->attributes['full_path'];
    }
}