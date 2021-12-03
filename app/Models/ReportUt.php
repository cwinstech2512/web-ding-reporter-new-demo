<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportUt extends Model
{
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_ut';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['dispatch_id', 'items_head_dates', 'job_name', 'exam_date', 'exam_place', 'equlpment_no', 'material', 
    'equipement', 'probe_size', 'surface_status', 'exam_time', 'couplant', 'items', 'editItemHeader_date1', 'editItemHeader_date2', 'editItemHeader_date3', 'obsert'];
}
