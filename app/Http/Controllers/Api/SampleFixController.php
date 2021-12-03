<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\SampleService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ResponseResource;
use File;
use Carbon\Carbon;

class SampleFixController extends Controller
{
    private $smpService = null;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SampleService $smpService)
    {
        $this->smpService = $smpService;
    }
    /**
     * 
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // return new ResponseResource($this->smpService->allLists());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // return new ResponseResource($this->smpService->update($request->all(), $id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $corperation, $command)
    {
        if(!$request->has('ex_file')) {
            return;
        }
        $file = $request->file('ex_file');
        $serial = Carbon::now()->format('ymdhms');
        $customFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . $serial . '.' . $file->getClientOriginalExtension();
        switch ($corperation . '.' . $command) {
            case 'chimei.pt':
                // dd(Storage::disk('public_sample')->put($corperation .'/'.$serial, $file));
                if(!$path = Storage::disk('public_sample')->put($corperation .'/'.$serial, $file)) {
                    return false;
                }
                // $path = $corperation .'/'.$customFileName;
                $this->smpService->syncSample($path, $corperation . '.' . $command);
                return new ResponseResource($this->smpService->getChimeiPT());
                break;
            case 'chimei.rt':
                // dd(Storage::disk('public_sample')->put($corperation .'/'.$serial, $file));
                if(!$path = Storage::disk('public_sample')->put($corperation .'/'.$serial, $file)) {
                    return false;
                }
                // $path = $corperation .'/'.$customFileName;
                $this->smpService->syncSample($path, $corperation . '.' . $command);
                return new ResponseResource($this->smpService->getChimeiRT());
                break;
            case 'chimei.mt':
                // dd(Storage::disk('public_sample')->put($corperation .'/'.$serial, $file));
                if(!$path = Storage::disk('public_sample')->put($corperation .'/'.$serial, $file)) {
                    return false;
                }
                // $path = $corperation .'/'.$customFileName;
                $this->smpService->syncSample($path, $corperation . '.' . $command);
                return new ResponseResource($this->smpService->getChimeiMT());
                break;
            case 'chimei.mt':
                // dd(Storage::disk('public_sample')->put($corperation .'/'.$serial, $file));
                if(!$path = Storage::disk('public_sample')->put($corperation .'/'.$serial, $file)) {
                    return false;
                }
                // $path = $corperation .'/'.$customFileName;
                $this->smpService->syncSample($path, $corperation . '.' . $command);
                return new ResponseResource($this->smpService->getChimeiMT());
                break;
            case 'chimei.it':
                // dd(Storage::disk('public_sample')->put($corperation .'/'.$serial, $file));
                if(!$path = Storage::disk('public_sample')->put($corperation .'/'.$serial, $file)) {
                    return false;
                }
                // $path = $corperation .'/'.$customFileName;
                $this->smpService->syncSample($path, $corperation . '.' . $command);
                return new ResponseResource($this->smpService->getChimeiIT());
                break;
            default:
                # code...
                break;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // return new ResponseResource($this->smpService->delete($id));
    }

    public function active(Request $request, $corperation, $command)
    {
        switch ($corperation . '.' . $command) {
            case 'chimei.pt':
                if($request->has('q')) {
                    if($request->input('q') == 'all') {
                        return new ResponseResource($this->smpService->logsChimeiPT());
                    }
                }
                return new ResponseResource($this->smpService->getChimeiPT());
                break;
            case 'chimei.rt':
                if($request->has('q')) {
                    if($request->input('q') == 'all') {
                        return new ResponseResource($this->smpService->logsChimeiRT());
                    }
                }
                return new ResponseResource($this->smpService->getChimeiRT());
                break;
            case 'chimei.mt':
                if($request->has('q')) {
                    if($request->input('q') == 'all') {
                        return new ResponseResource($this->smpService->logsChimeiMT());
                    }
                }
                return new ResponseResource($this->smpService->getChimeiMT());
                break;
            case 'chimei.ut':
                if($request->has('q')) {
                    if($request->input('q') == 'all') {
                        return new ResponseResource($this->smpService->logsChimeiUT());
                    }
                }
                return new ResponseResource($this->smpService->getChimeiUT());
                break;
            case 'chimei.it':
                if($request->has('q')) {
                    if($request->input('q') == 'all') {
                        return new ResponseResource($this->smpService->logsChimeiIT());
                    }
                }
                return new ResponseResource($this->smpService->getChimeiIT());
                break;
            default:
                # code...
                break;
        }
    }

    public function functionPathGroup(Request $request)
    {
        return new ResponseResource($this->smpService->groupFunctionPath());
    }
}
