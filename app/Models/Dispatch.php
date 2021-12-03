<?php

namespace App\Models;

use App\Models\ReportPt;
use App\Models\ReportMt;
use App\Traits\DateTimeFormatTrait;
use Illuminate\Database\Eloquent\Model;


class Dispatch extends Model
{
    use DateTimeFormatTrait;
    
    protected $fillable = ['customer', 'exam_date', 'job_name', 'exam_place', 'status', 'function_path', 'export_final_path'];
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dispatch';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function report_pt()
    {
        return $this->hasOne(ReportPt::class, 'dispatch_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function report_rt()
    {
        return $this->hasOne(ReportRt::class, 'dispatch_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function report_mt()
    {
        return $this->hasOne(ReportMt::class, 'dispatch_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function report_ut()
    {
        return $this->hasOne(ReportUt::class, 'dispatch_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function report_it()
    {
        return $this->hasOne(ReportIt::class, 'dispatch_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function report_rtoil()
    {
        return $this->hasOne(ReportRt::class, 'dispatch_id');
    }
}