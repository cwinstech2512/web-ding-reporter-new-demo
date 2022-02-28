<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportMTResource extends JsonResource
{
    public function toArray($request)
    {
        if(isset($this->report_mt->items)) {
            $this->report_mt->items = json_decode($this->report_mt->items);
        }
        if(isset($this->report_mt->thickness_objs)) {
            $this->report_mt->thickness_objs = isset($this->report_mt->thickness_objs)? json_decode($this->report_mt->thickness_objs) : null;
        }
        
        return parent::toArray($request);
    }
}