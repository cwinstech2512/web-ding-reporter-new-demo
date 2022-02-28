<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UserStampsTrait;

class ReportRt extends Model
{
    use UserStampsTrait;
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_rt';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['dispatch_id', 'exam_date', 'exam_place', 'job_no', 'job_name', 'exam_type', 'material', 'radiographic_tech',
    'thicknes', 'layers', 'nominal_size', 'equlpment_no', 'film_type', 'sn', 'identify', 'film_brand', 'sin_dou', 'film_size', 'focal_spot', 'file_qty', 'sod', 'geometric_unsharpness', 
    'exposure_time', 'iqi_location_mark', 'radiation_energy', 'screen_thick', 'iqi_no', 'interpret','energy_strength' , 'film_processing_temp', 'film_processing_min', 'obsert', 'approved', 'items',
    'thickness_objs',
    'updated_user',
    'created_user'];
}
