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
        
        return parent::toArray($request);
    }
}