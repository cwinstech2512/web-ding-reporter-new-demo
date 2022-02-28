<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserStampsTrait;

class ReportPt extends Model
{
    use UserStampsTrait;
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_pt';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['dispatch_id', 'job_name', 'job_no', 'exam_date', 'exam_place', 'exam_type', 'material', 
    'thicknes', 'nominal_size', 'surface_status', 'exam_time', 'light_intensity', 'white_ligth_meter', 'surface_temp', 'obsert', 'items',
    'thickness_objs',
    'updated_user',
    'created_user'];
}
