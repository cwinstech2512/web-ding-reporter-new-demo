<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportPTResource extends JsonResource
{
    public function toArray($request)
    {
        $this->report_pt->items = json_decode($this->report_pt->items);
        return parent::toArray($request);
    }
}