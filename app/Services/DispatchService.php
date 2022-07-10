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

    public function copyDispatch($array, $id = null)
    {
        $params = $array['params'];
        if(isset($params['copy_function']) && !empty($params['copy_function'])) {
            $reporExplope = explode('.', $params['copy_function']);
            $old = $this->dispstchRepo->fetchDispatchWithReport($id)->toArray();
            if(!empty($old['function_path'])) {
                $explode = explode('.', $old['function_path']);
                $tmpReport = $old['report_' . $explode[1]];
                $old['report_' . $explode[1]] = null;
                $tmpReport['items'] = null;
                $tmpReport['thickness_objs'] = null;
            }
            if(isset($tmpReport)) {
                // dd($old['report_'.$reporExplope[1]]);
                $old['report_'.$reporExplope[1]] = $tmpReport;
            }
            $old['function_path'] = $params['copy_function'];
            if($this->dispstchRepo->copyDispatchWithNewArray($old)) {
                $out['code'] = 1000;
                $out['message'] = "資料更新成功";
            } else {
                $out['code'] = 50002;
                $out['message'] = "資料更新錯誤";
            }
        } else {
            $out['code'] = 50001;
            $out['message'] = "資料更新錯誤";
        }
        return $out;
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

    public function verifyBackDispatch($function_path, $id = -1)
    {
        if($this->dispstchRepo->verifyBackFunPathDispatch($function_path, $id)) {
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
            if(isset($array['params']['thickness_objs']) && is_array($array['params']['thickness_objs'])) {
                $array['params']['thickness_objs'] = json_encode($array['params']['thickness_objs']);
            }
            // dd($array,$command, $corperation);
            if($this->dispstchRepo->saveFunPathByArray($array['params'], $command, $corperation)) {
                $tmp['params']['job_name'] = $array['params']['job_name'];
                $tmp['params']['exam_place'] = $array['params']['exam_place'];
                $tmp['params']['exam_date'] = $array['params']['exam_date'];
                $tmp['params']['original_no'] = $array['params']['original_no'];
                $tmp['params']['applicable_code'] = isset($array['params']['applicable_code'])? $array['params']['applicable_code'] : '';
                $tmp['params']['criteria'] = isset($array['params']['criteria'])? $array['params']['criteria'] : '';
                $tmp['params']['procedure_no'] = isset($array['params']['procedure_no'])? $array['params']['procedure_no'] : '';
                $tmp['params']['report_version'] = isset($array['params']['report_version'])? $array['params']['report_version'] : '';
                $tmp['params']['inspector'] = isset($array['params']['inspector'])? $array['params']['inspector'] : '';
                $tmp['params']['obsert'] = isset($array['params']['obsert'])? $array['params']['obsert'] : '';
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
            $array['id'] = $id;
            $array['status'] = 5;
        }
        if($this->dispstchRepo->saveDispatchByArray($array)) {
            $out['code'] = 1000;
            $out['message'] = "資料刪除成功";
        } else {
            $out['code'] = 50001;
            $out['message'] = "資料刪除錯誤";
        }
        return $out;
    }
}
