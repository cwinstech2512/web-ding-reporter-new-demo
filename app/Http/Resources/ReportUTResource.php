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
        
        return parent::toArray($request);
    }
}