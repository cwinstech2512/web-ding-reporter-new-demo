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
        
        return parent::toArray($request);
    }
}