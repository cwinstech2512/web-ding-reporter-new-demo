<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportRTResource extends JsonResource
{
    public function toArray($request)
    {
        // dd($this->report_rtolll);
        $this->function_path = explode(",",$this->function_path);
        if(isset($this->function_path)) {
            if(in_array('fpg.rtolll', $this->function_path)) {
                if(isset($this->report_rtolll->items)) {
                    $this->report_rtolll->items = isset($this->report_rtolll->items)? json_decode($this->report_rtolll->items) : null;
                }
                unset($this->report_rtoil);
                unset($this->report_rt);
                unset($this->report_rthree);
            } else if(in_array('chimei.rt', $this->function_path) || in_array('normal.rt', $this->function_path)) {
                
                if(isset($this->report_rt->items)) {
                    $this->report_rt->items = isset($this->report_rt->items)? json_decode($this->report_rt->items) : null;
                }
                if(isset($this->report_rt->thickness_objs)) {
                    $this->report_rt->thickness_objs = isset($this->report_rt->thickness_objs)? json_decode($this->report_rt->thickness_objs) : null;
                }
                if(isset($this->report_rt->iqi_location_mark)) {
                    $this->report_rt->iqi_location_mark = isset($this->report_rt->iqi_location_mark)? json_decode($this->report_rt->iqi_location_mark) : null;
                }
                if(isset($this->report_rt->exam_time)) {
                    $this->report_rt->exam_time = isset($this->report_rt->exam_time)? json_decode($this->report_rt->exam_time) : null;
                }
                if(isset($this->report_rt->iqi_no)) {
                    $this->report_rt->iqi_no = isset($this->report_rt->iqi_no)? json_decode($this->report_rt->iqi_no) : null;
                }
                if(isset($this->report_rt->film_size)) {
                    $this->report_rt->film_size = isset($this->report_rt->film_size)? json_decode($this->report_rt->film_size) : null;
                }
                if(isset($this->report_rt->film_brand) && !in_array('normal.rt', $this->function_path)) {
                    $this->report_rt->film_brand = isset($this->report_rt->film_brand)? json_decode($this->report_rt->film_brand) : null;
                }
                unset($this->report_rtoil);
                unset($this->report_rtolll);
                unset($this->report_rthree);
            } else if(in_array('fpg.rtoil', $this->function_path)) {
                if(isset($this->report_rtoil->items)) {
                    $this->report_rtoil->items = isset($this->report_rtoil->items)? json_decode($this->report_rtoil->items) : null;
                }
                unset($this->report_rtolll);
                unset($this->report_rt);
                unset($this->report_rthree);
            } else if(in_array('fpg.rthree', $this->function_path)) {
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