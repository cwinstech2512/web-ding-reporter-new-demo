<?php
namespace App\Repositories;

use App\Models\Dispatch;
use App\Models\ReportPt;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DispatchRepository
{
	protected $model;

    public function __construct(Dispatch $model)
    {
        $this->model = $model;
    }

    public function dataListByModel()
    {
        return Dispatch::orderBy('created_at', 'desc')/*->where('status', 0)*/->get();
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
                if(!$first) {
                    $first = new Dispatch;
                    $array['status'] = 1;
                }
            } else {
                $first = new Dispatch;
                $array['status'] = 1;
            }
        } else {
            $first = new Dispatch;
            $array['status'] = 1;
        }
        $first->fill($array);
        $first->save();
        $dispatch_id = $first->id;
        if(isset($array['function_path']) && !empty($array['function_path'])) {
            $function_path = $array['function_path'];
            $exFun = explode(',', $function_path);
            $reportData = $array;
            foreach($exFun as $ex) {
                $reporExplope = explode('.', $ex);
                if(sizeof($reporExplope)==2) {
                    $reportData['dispatch_id'] = $dispatch_id;
                    unset($reportData['id']);
                    // 建立報告
                    $this->saveFunPathByArray($reportData, $reporExplope[1], $reporExplope[0]);
                }
            }
            $array['status'] = 1;
        }
        
        return $this->createDispatchSerialNumber($first);
    }

    public function doubleVerifyFunPathDispatch($function_path, $id = -1)
    {
        if(!empty($function_path) && $id == -1) {
        } else if(!empty($function_path) && $id > 0) {
            $tmp = $this->model::where('function_path', 'like', "%".$function_path."%")->where('id', $id)->first();
            $tmp->status = 4;
            return $tmp->save();
        }
    }

    public function funPathDispatch($function_path, $id = -1)
    {
        if(!empty($function_path) && $id == -1) {
            $reporExplope = explode('.', $function_path);
            return $this->model::where('function_path', 'like', "%".$function_path."%")->where('status', '<', 5)->with(
                [
                    'report_'.$reporExplope[1] => function($query) {
                        
                    }
                ]
            )->orderBy('created_at', 'desc')->get();
        } else if(!empty($function_path) && $id > 0) {
            $reporExplope = explode('.', $function_path);
            if(sizeof($reporExplope)==2) {
                return $this->model::where('function_path', 'like', "%".$function_path."%")->where('id', $id)->with(
                    [
                        'report_'.$reporExplope[1] => function($query) {
                            
                        }
                    ]
                )->first();
            }
            return null;
        }
    }

    public function copyDispatchWithNewArray($from = null, $to = null)
    {
        if(is_null($from) || !is_array($from)) {
            return null;
        }
        if(is_null($to)) {
            $to = new Dispatch;
        }
        unset($from['export_final_path']);
        $from['status'] = 2;
        $to->fill($from);
        $to->save();
        $dispatch_id = $to->id;
        if(isset($to->function_path) && !empty($to->function_path)) {
            $function_path = $to->function_path;
            $exFun = explode(',', $function_path);
            
            foreach($exFun as $ex) {
                $reporExplope = explode('.', $ex);
                if(sizeof($reporExplope)==2) {
                    $reportData = $from['report_'.$reporExplope[1]];
                    $reportData['dispatch_id'] = $dispatch_id;
                    unset($reportData['id']);
                    // 建立報告
                    $this->saveFunPathByArray($reportData, $reporExplope[1], $reporExplope[0]);
                }
            }
            $array['status'] = 1;
        }

        return $this->createDispatchSerialNumber($to);;
    }

    public function fetchDispatchWithReport($id)
    {
        $fromFirst = $this->dataById($id);
        if(!$fromFirst) {
            return null;
        }
        if(empty($fromFirst->function_path)) {
            return null;
        }
        $reporExplope = explode('.', $fromFirst->function_path);
        return $this->model::where('id', $id)->with(
            [
                'report_'.$reporExplope[1] => function($query) {
                    
                }
            ]
        )->first();
    }

    public function verifyFunPathDispatch($function_path, $id = -1)
    {
        if(!empty($function_path) && $id == -1) {
        } else if(!empty($function_path) && $id > 0) {
            $tmp = $this->model::where('function_path', 'like', "%".$function_path."%")->where('id', $id)->first();
            $tmp->status = 3;
            return $tmp->save();
        }
    }

    public function verifyBackFunPathDispatch($function_path, $id = -1)
    {
        if(!empty($function_path) && $id == -1) {
        } else if(!empty($function_path) && $id > 0) {
            $tmp = $this->model::where('function_path', 'like', "%".$function_path."%")->where('id', $id)->first();
            $tmp->status = 2;
            return $tmp->save();
        }
    }

    public function saveFunPathByArray($array, $command, $corperation = null)
    {
        $first = null;
        if(empty($command)) return;
        $command = substr(ucfirst($command), 0, 2);
        $model = app("App\Models\Report{$command}");

        // 有帶入報告id，報告已存在，報告介面更新
        if(isset($array['id']) && $array['id'] > 0) {
            if(!($first = $model::find($array['id']))) {
                $first = new $model;
            }
        } else {
            // 派單以建立
            if(isset($array['dispatch_id']) && $array['dispatch_id'] > 0) {
                // 第一次建立派單後，產生得報告黨
                if(!($first = $model::where('dispatch_id', $array['dispatch_id'])->first())) {
                    // 產生空白報告黨
                    $first = new $model;
                    // 預設為編輯報告狀態
                    $array['status'] = 2;
                }
            } else {
                // 不應該有
                $first = new $model;
                return;
            }
        }
        if(empty($first->items) || is_null($first->items)) {
            $array['items'] = $this->initItemReportData($array, $command, $corperation);
        }
        
        $first->fill(array_filter( $array, function( $v ) { return !is_null( $v ); } ));
        return $first->save() && $this->createReportSerialNumber($first, $command);
    }

    private function createDispatchSerialNumber($model)
    {
        if(empty($model->serial_no)) {
            if(!empty($model->id)) {
                $serialNo = Carbon::now()->format('Ymd') . '-' . str_pad($model::whereDate('created_at', Carbon::now()->format('Y-m-d'))->count()+1, 4, "0", STR_PAD_LEFT);
                $model->serial_no = $serialNo;
                return $model->save();
            }
        }
        return true;
    }

    private function createReportSerialNumber($model, $command)
    {
        if(empty($model->original_no)) {
            $modelD = Dispatch::find($model->dispatch_id);
            if($modelD && !is_null($modelD->e_exam_date)) {
                $originNo = ('XID-' . strtoupper($command) .'-'. Carbon::parse($modelD->e_exam_date)->format('Ymd') . '-' . str_pad($modelD::where('e_exam_date', $modelD->e_exam_date)->count(), 4, "0", STR_PAD_LEFT));
                $model->original_no = $originNo;
                return $model->save();
            }
        }
        return true;
    }

    private function initItemReportData($object, $command, $corperation)
    {
        if($command == "Ut" && $corperation == 'sdm') {
            if(!isset($object['items']) || empty($object['items'])) {
                // return ('[{"thickness_result":"E1-C","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E1-T","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E1-B","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E1-R","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E1-L","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S1-C","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S1-T","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S1-B","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S1-R","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S1-L","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S2-C","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S2-T","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S2-B","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S2-R","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S2-L","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S3-C","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S3-T","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S3-B","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S3-R","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S3-L","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E2-C","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E2-T","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E2-B","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E2-R","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E2-L","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null}]');
                return ('[[{"serial_no":null,"prob_serial_no":"S-14237","probe_angle":"70\u02da","probe_freq":"2MHz","probe_size":"14*14","correction_angle":"68.3","sensibility":"36.8dB","transfer_correction":"+7dB","time_base_range":"1.5","probe_position":"2,3,11,12","object_no":1},{"serial_no":null,"prob_serial_no":"S-14234","probe_angle":"60\u02da","probe_freq":"2MHz","probe_size":"14*14","correction_angle":"63","sensibility":"37.5dB","transfer_correction":"+6dB","time_base_range":"1.5","probe_position":"2,3,11,12","object_no":2},{"serial_no":null,"prob_serial_no":"S-14232","probe_angle":"45\u02da","probe_freq":"2MHz","probe_size":"14*14","correction_angle":"47","sensibility":"37.2dB","transfer_correction":"+6dB","time_base_range":"1.5","probe_position":"2,3,11,12","object_no":3},{"serial_no":null,"prob_serial_no":"S-14192","probe_angle":"0\u02da","probe_freq":"2MHz","probe_size":"20","correction_angle":"0","sensibility":"49dB","transfer_correction":"+0dB","time_base_range":"2ndBW","probe_position":"1","object_no":4}]]');
            }
        }

        return isset($object['items'])? $object['items'] : ('');
    }

    public function countStatus(int $status)
    {
        return $this->model::where('status', $status)->count();
    }

    public function countFunPath()
    {
        return DB::table('dispatch')
                ->where('status', '>', 0)
                ->whereNotNull('function_path')
                ->select('function_path', DB::raw('count(*) as total'))
                ->groupBy('function_path')
                ->get();
    }

    public function funPathPoint($type, $start=null, $end=null) 
    {
        if($type == 'dately') {
            return DB::table('dispatch')
                ->where('status', '>', 0)
                ->whereNotNull('function_path')
                ->whereBetween('created_at', [$start, $end])
                ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('DAY(created_at) as day'), DB::raw('count(id) as count'))
                ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'), DB::raw('DAY(created_at)'))
                ->get();
        } else if($type == 'month') {
            return DB::table('dispatch')
                ->where('status', '>', 0)
                ->whereNotNull('function_path')
                ->whereBetween('created_at', [$start, $end])
                ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('count(id) as count'))
                ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
                ->get();
        }
        return [];
    	// return DB::select("SELECT COUNT(id) AS count, DATE(created_at) AS created_at FROM dispatch WHERE function_path LIKE 'chimei%' GROUP BY MONTH(created_at)");
    }
}