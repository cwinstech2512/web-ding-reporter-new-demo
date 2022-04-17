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
        if(isset($this->report_mt->surface_status)) {
            $this->report_mt->surface_status = isset($this->report_mt->surface_status)? json_decode($this->report_mt->surface_status) : null;
        }
        if(isset($this->report_mt->exam_time)) {
            $this->report_mt->exam_time = isset($this->report_mt->exam_time)? json_decode($this->report_mt->exam_time) : null;
        }
        if(isset($this->report_mt->type_of_particle_olor)) {
            $this->report_mt->type_of_particle_olor = isset($this->report_mt->type_of_particle_olor)? json_decode($this->report_mt->type_of_particle_olor) : null;
        }
        if(isset($this->report_mt->exam_method)) {
            $this->report_mt->exam_method = isset($this->report_mt->exam_method)? json_decode($this->report_mt->exam_method) : null;
        }
        if(isset($this->report_mt->magnetic_equipment)) {
            $this->report_mt->magnetic_equipment = isset($this->report_mt->magnetic_equipment)? 
                (!is_null(json_decode($this->report_mt->magnetic_equipment))? json_decode($this->report_mt->magnetic_equipment) : $this->report_mt->magnetic_equipment) : 
                null;
        }
        if(isset($this->report_mt->magnetizing_current)) {
            $this->report_mt->magnetizing_current = isset($this->report_mt->magnetizing_current)? json_decode($this->report_mt->magnetizing_current) : null;
        }
        if(isset($this->report_mt->particles_manufacturer)) {
            $this->report_mt->particles_manufacturer = isset($this->report_mt->particles_manufacturer)? json_decode($this->report_mt->particles_manufacturer) : null;
        }
        if(isset($this->report_mt->applying_method)) {
            $this->report_mt->applying_method = isset($this->report_mt->applying_method)? json_decode($this->report_mt->applying_method) : null;
        }
        if(isset($this->report_mt->magnetizing_methon)) {
            $this->report_mt->magnetizing_methon = isset($this->report_mt->magnetizing_methon)? json_decode($this->report_mt->magnetizing_methon) : null;
        }
        if(isset($this->report_mt->joint_type)) {
            $this->report_mt->joint_type = isset($this->report_mt->joint_type)? json_decode($this->report_mt->joint_type) : null;
        }
        return parent::toArray($request);
    }
}