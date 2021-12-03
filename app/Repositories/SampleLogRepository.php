<?php
namespace App\Repositories;

use App\Models\SampleLog;

class SampleLogRepository
{
	protected $model;

    public function __construct(SampleLog $model)
    {
        $this->model = $model;
    }

    public function lately($fun_path)
    {
        if(!empty($fun_path)) {
            return $this->model::where('function_path', $fun_path)->latest()->first();
        }
        return null;
    }

    public function logsAll($fun_path, $limit=5) 
    {
        if(!empty($fun_path)) {
            return $this->model::where('function_path', $fun_path)->latest()->limit($limit)->get();
        }
        return [];
    }

    public function groupFunctionPath() 
    {
        return $this->model::groupBy('function_path')->get();
    }

    public function saveByArray($array) 
    {
        $first = null;
        if(isset($array['id'])) {
            if($first = SampleLog::find($array['id'])) {
                if(!$first) $first = new SampleLog;
            }
        } else {
            $first = new SampleLog;
        }
        $first->fill($array);
        return $first->save();
    }
}
