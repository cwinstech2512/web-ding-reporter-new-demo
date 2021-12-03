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
        
        return parent::toArray($request);
    }
}