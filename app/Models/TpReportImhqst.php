<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TpReportImhqst extends Model
{
    protected $fillable = ['id', 'name', 'export_paths', 'upload_path'];
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tp_report_imhqst';
}