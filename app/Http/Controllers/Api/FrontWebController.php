<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Services\ChatService;
use App\Services\JoinRoomService;
use App\Models\BotChatLog;
use App\Models\BotChatLogHis;
use Illuminate\Http\Request;

class FrontWebController extends Controller
{
    private $chatService = null;
    private $joinRoomService = null;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ChatService $chatService, JoinRoomService $joinRoomService)
    {
        $this->chatService = $chatService;
        $this->joinRoomService = $joinRoomService;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function vaild(Request $request)
    {
        if($request->has('user_token')) {
            $userToken = $request->input('user_token');
            $chatLog = BotChatLog::where('user_token', $userToken)->first();
            $chatLogHis = BotChatLogHis::where('user_token', $userToken)->first();

            if($chatLog || $chatLogHis) return true;
        }
        return false;
    }

    public function storeChatLog(Request $request)
    {
        return new ResponseResource($this->chatService->storeChatLog($request->all()));
    }

    public function listChatLog(Request $request)
    {
        return new ResponseResource($this->chatService->lilstChatLog($request->all()));
    }

    public function vaildAdminToken(Request $request)
    {
        if($request->has('token')) {
            return \JWTAuth::parseToken()->check();
        }

        return false;
    }

    public function logJoinRoom(Request $request, $opt)
    {
        if($request->has('token') && \JWTAuth::parseToken()->check()) {
            $user = \JWTAuth::parseToken()->toUser();
        }
        if($opt == 'service' && isset($user)) {
            return new ResponseResource($this->joinRoomService->serviceJoin($request->all()));
        } else {
            return new ResponseResource($this->joinRoomService->guestJoin($request->all()));
        }
    }

    private function errorVaildAdmin()
    {

    }
}
