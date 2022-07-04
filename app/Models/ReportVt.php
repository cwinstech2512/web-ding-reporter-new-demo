<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserStampsTrait;

class ReportVt extends Model
{
    use UserStampsTrait;
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_vt';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['dispatch_id', 'report_version', 'items_head_dates', 'job_name', 'job_no', 'exam_date', 'exam_place', 'equlpment_no', 'material', 
    'lumin_meter', 'exam_time', 'surface_status', 'weld_gage', 'Others', 'light_intensity', 'joint_type', 'testing_method',
    'customer', 'applicable_code', 'criteria', 'procedure_no', 'procedure_version', 'region', 'contract_no', 'exam_method',
    'thickness_objs', 'items',
    'updated_user',
    'created_user'];
}
