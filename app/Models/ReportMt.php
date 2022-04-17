<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserStampsTrait;

class ReportMt extends Model
{
    use UserStampsTrait;
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_mt';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['dispatch_id', 'report_version', 'job_name', 'job_no', 'exam_date', 'exam_place', 'equlpment_no', 'material', 
    'magnetic_equipment', 'black_light_meter', 'surface_status', 'exam_time', 'type_of_particle_olor', 'applying_method', 'magnetizing_methon', 'obsert', 'items',
    'customer', 'applicable_code', 'criteria', 'procedure_no', 'region', 'contract_no', 'exam_method',
    'white_light_meter', 'surface_temperature', 'light_intensity', 'lifting_test', 'magnetic_particles', 'magnetic_field_indicator',
    'thickness_objs', 'yoke_probe_space', 'yoke_calibration_date', 'particles_manufacturer', 'magnetizing_current','background_paint','joint_type',
    'updated_user',
    'created_user'];
}
