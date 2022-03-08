<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Services\TransportationService;
use Illuminate\Http\Request;

class TransportationController extends Controller
{
    private $transportationService = null;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TransportationService $transportationService)
    {
        $this->transportationService = $transportationService;
    }
    /**
     * 
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return new ResponseResource($this->transportationService->getDatas());
    }

}
