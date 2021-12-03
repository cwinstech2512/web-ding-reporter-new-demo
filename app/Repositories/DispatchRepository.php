<?php
namespace App\Repositories;

use App\Models\Dispatch;
use App\Models\ReportPt;

class DispatchRepository
{
	protected $model;

    public function __construct(Dispatch $model)
    {
        $this->model = $model;
    }

    public function dataListByModel()
    {
        return Dispatch::orderBy('created_at', 'desc')->get();
    }

    public function dataById($id)
    {
        return Dispatch::find($id);
    }

    public function saveDispatchByArray($array)
    {
        $first = null;
        if(isset($array['id'])) {
            if($first = Dispatch::find($array['id'])) {
                if(!$first) $first = new Dispatch;
            }
        } else {
            $first = new Dispatch;
        }
        $first->fill($array);
        return $first->save();
    }

    public function doubleVerifyFunPathDispatch($function_path, $id = -1)
    {
        if(!empty($function_path) && $id == -1) {
        } else if(!empty($function_path) && $id > 0) {
            $tmp = $this->model::where('function_path', $function_path)->where('id', $id)->first();
            $tmp->status = 4;
            return $tmp->save();
        }
    }

    public function funPathDispatch($function_path, $id = -1)
    {
        if(!empty($function_path) && $id == -1) {
            return $this->model::where('function_path', $function_path)->get();
        } else if(!empty($function_path) && $id > 0) {
            $reporExplope = explode('.', $function_path);
            if(sizeof($reporExplope)==2) {
                // dd($this->model::where('function_path', $function_path)->where('id', $id)->with(
                //     [
                //         'report_'.$reporExplope[1] => function($query) {
                    
                //         }
                //     ]
                // )->first());
                return $this->model::where('function_path', $function_path)->where('id', $id)->with(
                    [
                        'report_'.$reporExplope[1] => function($query) {
                    
                        }
                    ]
                )->first();
            }
            return null;
        }
    }

    public function verifyFunPathDispatch($function_path, $id = -1)
    {
        if(!empty($function_path) && $id == -1) {
        } else if(!empty($function_path) && $id > 0) {
            $tmp = $this->model::where('function_path', $function_path)->where('id', $id)->first();
            $tmp->status = 3;
            return $tmp->save();
        }
    }

    public function saveFunPathByArray($array, $command)
    {
        $first = null;
        if(empty($command)) return;
        $command = substr(ucfirst($command), 0, 2);
        $model = app("App\Models\Report{$command}");
        
        if(isset($array['id'])) {
            if(!($first = $model::find($array['id']))) {
                $first = new $model;
            }
        } else {
            $first = new $model;
        }
        $first->fill($array);
        return $first->save();
    }
}