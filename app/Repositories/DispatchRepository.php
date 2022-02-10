<?php
namespace App\Repositories;

use App\Models\Dispatch;
use App\Models\ReportPt;
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
                if(!$first) {
                    $first = new Dispatch;
                } else {
                    if(isset($array['function_path']) && !empty($array['function_path'])) {
                        $function_path = $array['function_path'];
                        $reporExplope = explode('.', $function_path);
                        if(sizeof($reporExplope)==2) {
                            $array['dispatch_id'] = $array['id'];
                            unset($array['id']);
                            $this->saveFunPathByArray($array, $reporExplope[1]);
                        }
                    }
                }
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

    public function verifyBackFunPathDispatch($function_path, $id = -1)
    {
        if(!empty($function_path) && $id == -1) {
        } else if(!empty($function_path) && $id > 0) {
            $tmp = $this->model::where('function_path', $function_path)->where('id', $id)->first();
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
                if(!($first = $model::where('dispatch_id', $array['dispatch_id'])->first())) {
                    $first = new $model;
                }
            } else {
                $first = new $model;
            }
        }
        if(empty($first->items) || is_null($first->items)) {
            $array['items'] = $this->initItemReportData($array, $command);
        }
        
        $first->fill($array);
        return $first->save();
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