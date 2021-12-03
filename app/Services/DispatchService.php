<?php
namespace App\Services;

use App\Repositories\DispatchRepository;
use Carbon\Carbon;

class DispatchService
{
    private $dispstchRepo = null;

	public function __construct(DispatchRepository $dispstchRepo)
	{
		$this->dispstchRepo = $dispstchRepo;
	}

    public function getListData()
    {
        return $this->dispstchRepo->dataListByModel();
    }

    public function getData($id)
    {
        return $this->dispstchRepo->dataById($id);
    }

    public function funPathDispatch($function_path, $id = -1)
    {
        return $this->dispstchRepo->funPathDispatch($function_path, $id);
    }

    public function verifyDispatch($function_path, $id = -1)
    {
        if($this->dispstchRepo->verifyFunPathDispatch($function_path, $id)) {
            $out['code'] = 1000;
            $out['message'] = "資料更新成功";
        } else {
            $out['code'] = 50001;
            $out['message'] = "資料更新錯誤";
        }
        return $out;
    }

    public function doubleVerifyDispatch($function_path, $id = -1)
    {
        if($this->dispstchRepo->doubleVerifyFunPathDispatch($function_path, $id)) {
            $out['code'] = 1000;
            $out['message'] = "資料更新成功";
        } else {
            $out['code'] = 50001;
            $out['message'] = "資料更新錯誤";
        }
        return $out;
    }

    public function syncFunPath($array, $id = null, $corperation = '', $command = '')
    {
        if(!is_null($id) && $id > 0) {
            $array['id'] = $id;
        } else {
            unset($array['id']);
        }
        if(isset($array['params'])) {
            if(isset($array['params']['items']) && is_array($array['params']['items'])) {
                $array['params']['items'] = json_encode($array['params']['items']);
            }
            if($this->dispstchRepo->saveFunPathByArray($array['params'], $command)) {
                $tmp['params']['job_name'] = $array['params']['job_name'];
                $tmp['params']['exam_place'] = $array['params']['exam_place'];
                $tmp['params']['exam_date'] = $array['params']['exam_date'];
                // $tmp = array_where($array['params'], function($key, $value)
                // {
                //     return $key == 'job_name' || $key == 'exam_place' || $key == 'exam_date';
                // });
                // $tmp1['params'] = $tmp;
                $tmp['params']['status'] = 2;
                ($this->syncDispatch($tmp, $array['params']['dispatch_id']));
                $out['code'] = 1000;
                $out['message'] = "資料更新成功";
            } else {
                $out['code'] = 50001;
                $out['message'] = "資料更新錯誤";
            }
        } else {
            $out['code'] = 50002;
            $out['message'] = "資料更新錯誤";
        }
        return $out;
    }

    public function syncDispatch($array, $id = null)
    {   
        $out = [];
        if(!is_null($id)) {
            $array['params']['id'] = $id;
        }
        if(isset($array['params'])) {
            if($this->dispstchRepo->saveDispatchByArray($array['params'])) {
                $out['code'] = 1000;
                $out['message'] = "資料更新成功";
            } else {
                $out['code'] = 50001;
                $out['message'] = "資料更新錯誤";
            }
        } else {
            $out['code'] = 50002;
            $out['message'] = "資料更新錯誤";
        }
        
        return $out;
    }

    public function deleteDispatch($id)
    {
        $out = [];
        if(!is_null($id)) {
            $array['params']['id'] = $id;
            $array['params']['status'] = -1;
        }
        if($this->dispstchRepo->saveDispatchByArray($array)) {
            $out['code'] = 1000;
            $out['message'] = "資料刪除成功";
        } else {
            $out['code'] = 50001;
            $out['message'] = "資料刪除錯誤";
        }
    }
}
