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
    protected $fillable = ['dispatch_id', 'report_version', 'job_name', 'job_no', 'exam_date', 'exam_place', 'exam_type', 'material', 
    'thicknes', 'nominal_size', 'surface_status', 'exam_time', 'light_intensity', 'white_ligth_meter', 'surface_temp', 'obsert', 'items',
    'customer', 'applicable_code', 'criteria', 'procedure_no', 'region', 'contract_no', 'exam_method', 'procedure_version', 'equlpment_no',
    'penetrant_application', //施加滲透劑
      'dwell_time', //滲透時間
      'surface_temperature', //檢測時表面溫度
      'removal', //滲透劑去除
      'drying_time', //乾燥時間
      'developing_time', //顯像時間
      'post_clean', //後清理
      'developing', //施加顯像劑
      'cleaner', //清洗劑
      'penetrant', //滲透劑
      'developer', //顯像劑dd
      'on_site',
    'thickness_objs',
    'updated_user',
    'created_user'];

    protected $attributes = [
        'material' => '',
        'thicknes' => '',
    ];
}
