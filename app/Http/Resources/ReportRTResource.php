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
                if(isset($this->report_rtolll->items)) {
                    $this->report_rtolll->items = isset($this->report_rtolll->items)? json_decode($this->report_rtolll->items) : null;
                }
                unset($this->report_rtoil);
                unset($this->report_rt);
                unset($this->report_rthree);
            } else if($this->function_path == 'chimei.rt') {
                if(isset($this->report_rt->items)) {
                    $this->report_rt->items = isset($this->report_rt->items)? json_decode($this->report_rt->items) : null;
                }
                if(isset($this->report_rt->thickness_objs)) {
                    $this->report_rt->thickness_objs = isset($this->report_rt->thickness_objs)? json_decode($this->report_rt->thickness_objs) : null;
                }
                unset($this->report_rtoil);
                unset($this->report_rtolll);
                unset($this->report_rthree);
            } else if($this->function_path == 'fpg.rtoil') {
                if(isset($this->report_rtoil->items)) {
                    $this->report_rtoil->items = isset($this->report_rtoil->items)? json_decode($this->report_rtoil->items) : null;
                }
                unset($this->report_rtolll);
                unset($this->report_rt);
                unset($this->report_rthree);
            } else if($this->function_path == 'fpg.rthree') {
                if(isset($this->report_rthree->items)) {
                    $this->report_rthree->items = isset($this->report_rthree->items)? json_decode($this->report_rthree->items) : null;
                }
                unset($this->report_rtolll);
                unset($this->report_rtoil);
                unset($this->report_rt);
            }
        }
        return parent::toArray($request);
    }
}