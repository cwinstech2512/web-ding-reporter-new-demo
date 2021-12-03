<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportRTResource extends JsonResource
{
    public function toArray($request)
    {
        if(isset($this->report_rt->items)) {
            $this->report_rt->items = json_decode($this->report_rt->items);
            unset($this->report_rtoil);
        }

        if(isset($this->report_rtoil->items)) {
            $this->report_rtoil->items = json_decode($this->report_rtoil->items);
            unset($this->report_rt);
        }
        
        return parent::toArray($request);
    }
}