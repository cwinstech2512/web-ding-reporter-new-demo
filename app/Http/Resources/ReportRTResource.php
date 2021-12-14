<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportRTResource extends JsonResource
{
    public function toArray($request)
    {
        // dd($this->report_rtolll);
        if(isset($this->function_path)) {
            if($this->function_path == 'fpg.rtolll') {
                $this->report_rtolll->items = isset($this->report_rtolll->items)? json_decode($this->report_rtolll->items) : null;
                unset($this->report_rtoil);
                unset($this->report_rt);
                unset($this->report_rthree);
            } else if($this->function_path == 'chimei.rt') {
                $this->report_rt->items = isset($this->report_rt->items)? json_decode($this->report_rt->items) : null;
                unset($this->report_rtoil);
                unset($this->report_rtolll);
                unset($this->report_rthree);
            } else if($this->function_path == 'fpg.rtoil') {
                $this->report_rtoil->items = isset($this->report_rtoil->items)? json_decode($this->report_rtoil->items) : null;
                unset($this->report_rtolll);
                unset($this->report_rt);
                unset($this->report_rthree);
            } else if($this->function_path == 'fpg.rthree') {
                $this->report_rthree->items = isset($this->report_rthree->items)? json_decode($this->report_rthree->items) : null;
                unset($this->report_rtolll);
                unset($this->report_rtoil);
                unset($this->report_rt);
            }
        }
        return parent::toArray($request);
    }
}