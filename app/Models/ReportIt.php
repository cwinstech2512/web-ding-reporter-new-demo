<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportIt extends Model
{
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_it';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['dispatch_id', 'job_name', 'job_no', 'exam_date', 'exam_place', 'exam_type', 'material', 
    'pipeline_no', 'equipement', 'testing_method', 'thickness', 'voltage', 'obsert', 'type', 'items'];
}
