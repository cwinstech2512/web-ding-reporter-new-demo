<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportRt extends Model
{
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
    protected $fillable = ['dispatch_id', 'exam_date', 'exam_place', 'exam_type', 'material', 'radiographic_tech',
    'thicknes', 'layers', 'nominal_size', 'film_type', 'sn', 'identify', 'film_brand', 'film_size', 'focal_spot', 'file_qty', 'sod', 'geometric_unsharpness', 
    'exposure_time', 'iqi_location_mark', 'iqi_no', 'interpret','energy_strength' , 'film_processing_temp', 'film_processing_min', 'obsert', 'items'];
}
