<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Import\TaipowerDailyImport;
use App\Services\TaipowerService;
use App\Services\SampleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaipowerDailyController extends Controller
{
    private $taiService = null;
    private $sampleService = null;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TaipowerService $taiService, SampleService $sampleService)
    {
        $this->taiService = $taiService;
        $this->sampleService = $sampleService;
    }
    /**
     * 
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $type)
    {
        switch($type) {
            case 'list':
                return new ResponseResource($this->taiService->list()); 
            case 'daily_list':
                return new ResponseResource($this->taiService->list()); 
            break;
        }
    }

    public function importDaily(Request $request)
    {
        $validated = $request->validate([
            'report_date' => 'required|date', // 例: 验证是否为日期格式
            'jkFile' => 'required',
            'tpFile' => 'required', 
        ]);
        ini_set('memory_limit', '512M');  // 或更高，根据需要调整
        $arrayData = [
            'report_date' => $validated['report_date'],
            'report_type' => ''
        ];
        $jkFile = null;
        $tpFile = null;
        if($request->has('jkFile')) {
            $jkFile = $request->file('jkFile');
        }
        if($request->has('tpFile')) {
            $tpFile = $request->file('tpFile');
        }
        $file_name = Carbon::now()->format('ymdhms');
        $arrayData['report_type'] = 'JK';
        $import_jk = new TaipowerDailyImport($file_name, $arrayData, $jkFile);

        Log::debug(memory_get_usage() / 1024 / 1024);
        $sheet = $import_jk->import($jkFile);
        $result_JK_Channel = $import_jk->getChannelSums();

        unset($sheet);
        unset($import_jk);
       
        gc_enable(); // 确保垃圾收集器开启
        gc_collect_cycles(); // 强制运行垃圾收集器

        $arrayData['report_type'] = 'TP';
        $import_tp = new TaipowerDailyImport($file_name, $arrayData, $tpFile);
        $sheet = $import_tp->import($tpFile);
        Log::debug(memory_get_usage() / 1024 / 1024);
        $result_TP_Channel = $import_tp->getChannelSums();
        $result_Report_Date = $import_tp->getrReportDate();
        unset($sheet);
        unset($import_tp);
        // dd($result_TP_Channel, $result_JK_Channel, $result_Report_Date);
        gc_enable(); // 确保垃圾收集器开启
        gc_collect_cycles(); // 强制运行垃圾收集器
        $this->taiService->rendomXlxs($result_TP_Channel, $result_JK_Channel, $result_Report_Date);

        $out['code'] = 1000;
        $out['message'] = "Success";
        $out['data'] = '';

        return new ResponseResource($out);
    }
}
