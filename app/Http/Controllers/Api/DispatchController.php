<?php

namespace App\Http\Controllers\Api;

use App\Models\ReportRt;
use Illuminate\Http\Request;
use App\Services\SampleService;
use App\Services\DispatchService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReportITResource;
use App\Http\Resources\ReportMTResource;
use App\Http\Resources\ReportPTResource;
use App\Http\Resources\ReportRTResource;
use App\Http\Resources\ReportUTResource;
use App\Http\Resources\ResponseResource;

class DispatchController extends Controller
{
    private $dispatchService = null;
    private $smpService = null;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(DispatchService $dispatchService, SampleService $smpService)
    {
        $this->dispatchService = $dispatchService;
        $this->smpService = $smpService;
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
                return new ResponseResource($this->dispatchService->getListData()); 
            break;
        }
    }

    public function doubleVerify(Request $request, $corperation, $command, $id)
    {
        $function_path = $corperation . '.' . $command;
        return new ResponseResource($this->dispatchService->doubleVerifyDispatch($function_path, $id));
    }

    public function verifyBack(Request $request, $corperation, $command, $id)
    {
        $function_path = $corperation . '.' . $command;
        return new ResponseResource($this->dispatchService->verifyBackDispatch($function_path, $id));
    }

    public function verify(Request $request, $corperation, $command, $id)
    {
        $function_path = $corperation . '.' . $command;
        // $this->smpService->parseReportChimeiPT($function_path, $id);
        switch($function_path) {
            case 'chimei.pt':
                $this->smpService->parseReportChimeiPT($function_path, $id);
            break;
            case 'chimei.rt':
                $this->smpService->parseReportChimeiRT($function_path, $id);
            break;
            case 'chimei.mt':
                $this->smpService->parseReportChimeiMT($function_path, $id);
            break;
            case 'chimei.ut':
                $this->smpService->parseReportChimeiUT($function_path, $id);
            break;
            case 'chimei.it':
                $this->smpService->parseReportChimeiIT($function_path, $id);
            break;
            case 'fpg.rtoil':
                $this->smpService->parseReportFpgRT_Oil($function_path, $id);
            break;
            case 'fpg.rtolll':
                $this->smpService->parseReportFpgRT_Olll($function_path, $id);
            break;
            case 'fpg.rthree':
                $this->smpService->parseReportFpgRT_Three($function_path, $id);
            break;
        }
        return new ResponseResource($this->dispatchService->verifyDispatch($function_path, $id));
    }

    public function funPathDispatchExport(Request $request, $corperation, $command, $id)
    {
        $function_path = $corperation . '.' . $command;
        switch($function_path) {
            case 'chimei.pt':
                return new ResponseResource($this->smpService->parseReportChimeiPT($function_path, $id));
            break;
            case 'chimei.rt':
                return new ResponseResource($this->smpService->parseReportChimeiRT($function_path, $id));
            break;
            case 'chimei.mt':
                return new ReportMTResource($this->smpService->parseReportChimeiMT($function_path, $id));
            break;
            case 'chimei.ut':
                return new ReportUTResource($this->smpService->parseReportChimeiUT($function_path, $id));
            break;
            case 'chimei.it':
                return new ReportITResource($this->smpService->parseReportChimeiIT($function_path, $id));
            break;
            case 'fpg.rtoil':
                return new ReportRTResource($this->smpService->parseReportFpgRT_Oil($function_path, $id));
            break;
            case 'fpg.rtolll':
                return new ReportRTResource($this->smpService->parseReportFpgRT_Olll($function_path, $id));
            break;
            case 'fpg.rthree':
                return new ReportRTResource($this->smpService->parseReportFpgRT_Three($function_path, $id));
            break;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $type, $id)
    {
        switch($type) {
            case 'first':
                return new ResponseResource($this->dispatchService->getData($id)); 
            break;
        }
    }

    public function download(Request $request, $corperation, $command, $id) 
    {
        $function_path = $corperation . '.' . $command;
        return new ResponseResource($this->smpService->toDownloadReportFile($function_path, $id));
    }

    public function funPathDispatch(Request $request, $corperation, $command) 
    {
        $function_path = $corperation . '.' . $command;
        return new ResponseResource($this->dispatchService->funPathDispatch($function_path));
    }

    public function showFunPathDispatch(Request $request, $corperation, $command, $id) 
    {
        $function_path = $corperation . '.' . $command;
        switch($function_path) {
            case 'chimei.pt':
                return new ReportPTResource($this->dispatchService->funPathDispatch($function_path, $id));
            break;
            case 'chimei.rt':
                return new ReportRTResource($this->dispatchService->funPathDispatch($function_path, $id));
            break;
            case 'chimei.mt':
                return new ReportMTResource($this->dispatchService->funPathDispatch($function_path, $id));
            break;
            case 'chimei.ut':
                return new ReportUTResource($this->dispatchService->funPathDispatch($function_path, $id));
            break;
            case 'chimei.it':
                return new ReportITResource($this->dispatchService->funPathDispatch($function_path, $id));
            break;
            case 'fpg.rtoil':
                return new ReportRTResource($this->dispatchService->funPathDispatch($function_path, $id));
            break;
            case 'fpg.rtolll':
                return new ReportRTResource($this->dispatchService->funPathDispatch($function_path, $id));
            break;
            case 'fpg.rthree':
                return new ReportRTResource($this->dispatchService->funPathDispatch($function_path, $id));
            break;
        }
        
    }

    public function storeFunPathDispatch(Request $request, $corperation, $command, $id)
    {
        $function_path = $corperation . '.' . $command;
        return new ResponseResource($this->dispatchService->syncFunPath($request->all(), $id, $corperation, $command));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return new ResponseResource($this->dispatchService->syncDispatch($request->all()));
    }

    public function update(Request $request, $id) 
    {
        return new ResponseResource($this->dispatchService->syncDispatch($request->all()), $id);
    }

    public function delete(Request $request, $id) 
    {
        return new ResponseResource($this->dispatchService->deleteDispatch($id));
    }
}
