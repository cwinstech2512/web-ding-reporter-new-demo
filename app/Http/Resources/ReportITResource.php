<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportITResource extends JsonResource
{
    public function toArray($request)
    {
        if(isset($this->report_it->items)) {
            $this->report_it->items = json_decode($this->report_it->items);
        }
        if(isset($this->report_it->thickness_objs)) {
            $this->report_it->thickness_objs = isset($this->report_it->thickness_objs)? json_decode($this->report_it->thickness_objs) : null;
        }
        
        return parent::toArray($request);
    }
}