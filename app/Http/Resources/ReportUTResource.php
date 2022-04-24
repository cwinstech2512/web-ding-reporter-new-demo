<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportUTResource extends JsonResource
{
    public function toArray($request)
    {
        if(isset($this->report_ut->items)) {
            $this->report_ut->items = json_decode($this->report_ut->items);
        }
        if(isset($this->report_ut->thickness_objs)) {
            $this->report_ut->thickness_objs = isset($this->report_ut->thickness_objs)? json_decode($this->report_ut->thickness_objs) : null;
        }
        if(isset($this->report_ut->surface_status)) {
            $this->report_ut->surface_status = isset($this->report_ut->surface_status)? json_decode($this->report_ut->surface_status) : null;
        }
        if(isset($this->report_ut->exam_time)) {
            $this->report_ut->exam_time = isset($this->report_ut->exam_time)? json_decode($this->report_ut->exam_time) : null;
        }
        if(isset($this->report_ut->testing_method)) {
            $this->report_ut->testing_method = isset($this->report_ut->testing_method)? json_decode($this->report_ut->testing_method) : null;
        }
        if(isset($this->report_ut->reference_block)) {
            $this->report_ut->reference_block = isset($this->report_ut->reference_block)? json_decode($this->report_ut->reference_block) : null;
        }
        if(isset($this->report_ut->calibration_block)) {
            $this->report_ut->calibration_block = isset($this->report_ut->calibration_block)? json_decode($this->report_ut->calibration_block) : null;
        }
        if(isset($this->report_ut->coupling)) {
            $this->report_ut->coupling = isset($this->report_ut->coupling)? json_decode($this->report_ut->coupling) : null;
        }
        return parent::toArray($request);
    }
}