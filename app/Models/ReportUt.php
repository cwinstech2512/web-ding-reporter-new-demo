<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserStampsTrait;

class ReportUt extends Model
{
    use UserStampsTrait;
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
    protected $fillable = ['dispatch_id', 'report_version', 'items_head_dates', 'job_name', 'job_no', 'exam_date', 'exam_place', 'equlpment_no', 'ut_equipment', 'material', 
    'equipement', 'probe_size', 'surface_status', 'exam_time', 'couplant', 'coupling', 'Others', 'reference_block', 'reference_serial_no', 'calibration_block', 'testing_method', 'items', 'editItemHeader_date1', 'editItemHeader_date2', 'editItemHeader_date3', 'obsert',
    'customer', 'applicable_code', 'criteria', 'procedure_no', 'region', 'contract_no', 'exam_method',
    'brand_type_ser_no','cali_date','scanning_sensitivity','probe_no_ser_no','probe_size','incident_angle','probe_frequency','calibration_check_before','calibration_check_after','probe_nb_ab',
    'thickness_objs',
    'updated_user',
    'created_user'];
}
