<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportPTResource extends JsonResource
{
    public function toArray($request)
    {
        if(isset($this->report_pt->items)) {
            $this->report_pt->items = json_decode($this->report_pt->items);
        }
        if(isset($this->report_pt->thickness_objs)) {
            $this->report_pt->thickness_objs = isset($this->report_pt->thickness_objs)? json_decode($this->report_pt->thickness_objs) : null;
        }
        if(isset($this->report_pt->exam_time)) {
            $this->report_pt->exam_time = isset($this->report_pt->exam_time)? json_decode($this->report_pt->exam_time) : null;
        }
        if(isset($this->report_pt->exam_method)) {
            $this->report_pt->exam_method = isset($this->report_pt->exam_method)? json_decode($this->report_pt->exam_method) : null;
        }
        if(isset($this->report_pt->post_clean)) {
            $this->report_pt->post_clean = isset($this->report_pt->post_clean)? json_decode($this->report_pt->post_clean) : null;
        }
        if(isset($this->report_pt->penetrant_application)) {
            $this->report_pt->penetrant_application = isset($this->report_pt->penetrant_application)? json_decode($this->report_pt->penetrant_application) : null;
        }
        if(isset($this->report_pt->removal)) {
            $this->report_pt->removal = isset($this->report_pt->removal)? json_decode($this->report_pt->removal) : null;
        }
        if(isset($this->report_pt->on_site)) {
            $this->report_pt->on_site = isset($this->report_pt->on_site)? json_decode($this->report_pt->on_site) : null;
        }
        
        return parent::toArray($request);
    }
}