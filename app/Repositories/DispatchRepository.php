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
        return Dispatch::orderBy('created_at', 'desc')->where('status', 0)->get();
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
                    $this->saveFunPathByArray($reportData, $reporExplope[1]);
                }
            }
            $array['status'] = 1;
        }
        return true;
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

    public function saveFunPathByArray($array, $command)
    {
        $first = null;
        if(empty($command)) return;
        $command = substr(ucfirst($command), 0, 2);
        $model = app("App\Models\Report{$command}");
        
        if(isset($array['id']) && $array['id'] > 0) {
            if(!($first = $model::find($array['id']))) {
                $first = new $model;
            }
        } else {
            if(isset($array['dispatch_id']) && $array['dispatch_id'] > 0) {
                // 第一次建立派單後，產生得報告黨
                if(!($first = $model::where('dispatch_id', $array['dispatch_id'])->first())) {
                    $first = new $model;
                    $array['status'] = 2;
                }
            } else {
                $first = new $model;
            }
        }
        if(empty($first->items) || is_null($first->items)) {
            $array['items'] = $this->initItemReportData($array, $command);
        }
        
        $first->fill($array);
        return $first->save() && $this->createReportSerialNumber($first, $command);
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

    private function initItemReportData($object, $command)
    {
        if($command == "Ut") {
            if(!isset($object['items']) || empty($object['items'])) {
                return ('[{"thickness_result":"E1-C","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E1-T","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E1-B","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E1-R","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E1-L","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S1-C","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S1-T","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S1-B","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S1-R","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S1-L","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S2-C","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S2-T","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S2-B","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S2-R","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S2-L","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S3-C","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S3-T","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S3-B","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S3-R","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"S3-L","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E2-C","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E2-T","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E2-B","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E2-R","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null},{"thickness_result":"E2-L","design_thickness":null,"corrosion_margin":null,"minimum_thickness":null,"date1":null,"date2":null,"date3":null}]');
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