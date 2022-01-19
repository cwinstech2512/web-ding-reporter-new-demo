<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    private $analyticsService = null;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }
    /**
     * 
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return new ResponseResource($this->analyticsService->dashboard());
    }

    /**
     * 
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function gDispatch(Request $request)
    {
        return new ResponseResource($this->analyticsService->dispatch());
    }
}
