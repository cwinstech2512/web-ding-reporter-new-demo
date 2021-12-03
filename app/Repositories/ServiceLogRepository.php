<?php
namespace App\Repositories;

use App\Models\ServiceLog;

class ServiceLogRepository
{
	protected $model;

    public function __construct(ServiceLog $model)
    {
        $this->model = $model;
    }

    public function save($array)
    {
        if(isset($array['roomid'])) {
            if($exist = $this->model::where('roomid', $array['roomid'])->first()) {
                $exist->fill($array);
                $exist->save();
            } else {
                $this->model = new ServiceLog;
                $this->model->fill($array);
                return $this->model->save();
            }
        }
    }
}
