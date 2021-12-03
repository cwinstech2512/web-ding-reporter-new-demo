<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    private $chatService = null;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }
    /**
     * 
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return new ResponseResource($this->chatService->listRoom($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        return new ResponseResource($this->chatService->firstRoom($request->all(), $id));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function chatHistory(Request $request, $id)
    {
        return new ResponseResource($this->chatService->chatHistoryRoom($request->all(), $id));
    }
}
