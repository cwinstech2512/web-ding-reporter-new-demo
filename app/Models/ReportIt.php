<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserStampsTrait;

class ReportIt extends Model
{
    use UserStampsTrait;
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
    protected $fillable = ['dispatch_id', 'report_version', 'job_name', 'job_no', 'exam_date', 'exam_place', 'exam_type', 'material', 
    'pipeline_no', 'equipement', 'testing_method', 'thickness', 'voltage', 'obsert', 'type', 'items', 'thickness_objs',
    'customer', 'applicable_code', 'criteria', 'procedure_no', 'region', 'contract_no', 'exam_method',
    'updated_user',
    'created_user'];
}
