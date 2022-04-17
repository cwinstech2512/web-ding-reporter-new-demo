<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportVTResource extends JsonResource
{
    public function toArray($request)
    {
        if(isset($this->report_vt->items)) {
            $this->report_vt->items = json_decode($this->report_vt->items);
        }
        if(isset($this->report_vt->thickness_objs)) {
            $this->report_vt->thickness_objs = isset($this->report_vt->thickness_objs)? json_decode($this->report_vt->thickness_objs) : null;
        }
        if(isset($this->report_vt->surface_status)) {
            $this->report_vt->surface_status = isset($this->report_vt->surface_status)? json_decode($this->report_vt->surface_status) : null;
        }
        if(isset($this->report_vt->exam_time)) {
            $this->report_vt->exam_time = isset($this->report_vt->exam_time)? json_decode($this->report_vt->exam_time) : null;
        }
        if(isset($this->report_vt->testing_method)) {
            $this->report_vt->testing_method = isset($this->report_vt->testing_method)? json_decode($this->report_vt->testing_method) : null;
        }
        if(isset($this->report_vt->joint_type)) {
            $this->report_vt->joint_type = isset($this->report_vt->joint_type)? json_decode($this->report_vt->joint_type) : null;
        }
        
        return parent::toArray($request);
    }
}