<?php
namespace App\Services;

use Carbon\Carbon;
use App\Repositories\ChatlogRepository;
use App\Repositories\BotChatlogRepository;
use Arr;

class ChatService
{
	private $chatRepo = null;
	private $botChatRepo = null;
	public function __construct(ChatlogRepository $chatRepo, BotChatlogRepository $botChatRepo) 
	{
		$this->chatRepo = $chatRepo;
		$this->botChatRepo = $botChatRepo;
	}

	public function listRoom($array) 
	{
		if(isset($array['params']['dates'])) {
			return $this->chatRepo->listRoomGroupBy($array['params']['dates']);
		}
		return $this->chatRepo->listRoomGroupBy();
	}
	public function firstRoom($array, $roomid)
	{
		if(isset($array['roomid']) || isset($roomid)) {
			$out = [];
			$resultUserToken= $this->chatRepo->getUserTokenRoomBy($roomid);
			// dd($resultUserToken);
			if($resultUserToken && isset($resultUserToken->to_userid)) {
				$resultBotChat= $this->botChatRepo->botChatLogToken($resultUserToken->to_userid);
				// dd($resultBotChat);
				foreach ($resultBotChat as $key => $value) {
					$tmpObject = clone $resultUserToken;
					// user_item U=guest M=agent
					if($value->user_item == 'U') {
						$tmpObject->to_userid = 'ehs-bot-cms';
						$tmpObject->to_clientid = 'ehs-bot-cms';
						$tmpObject->to_name = $value->user_id;
						$tmpObject->userid = $resultUserToken->to_userid;
						$tmpObject->clientid = $resultUserToken->to_clientid;
						$tmpObject->name = $resultUserToken->to_name;
					}
					$tmpObject->content = $value->content;
					$tmpObject->chat_time = $value->chat_time;
					$tmpObject->updated_at = $value->chat_time;
					$tmpObject->created_at = $value->chat_time;

					array_push($out, $tmpObject);
				}
				$out = array_merge($out, $this->chatRepo->firstRoomBy($roomid)->toArray());
			}
			return $out;
		} else {
			return [];
		}
	}

	public function chatHistoryRoom($array, $roomid) 
	{
		if(isset($array['roomid']) || isset($roomid)) {
			$out = [];
			$resultUserToken= $this->chatRepo->getUserTokenRoomBy($roomid);
			// dd($resultUserToken);
			if($resultUserToken && isset($resultUserToken->to_userid)) {
				$resultBotChat= $this->botChatRepo->botChatHistoryToken($resultUserToken->to_userid);
				// dd($resultBotChat);
				foreach ($resultBotChat as $key => $value) {
					$tmpObject = clone $resultUserToken;
					// user_item U=guest M=agent
					if($value->user_item == 'U') {
						$tmpObject->to_userid = 'ehs-bot-cms';
						$tmpObject->to_clientid = 'ehs-bot-cms';
						$tmpObject->to_name = $value->user_id;
						$tmpObject->userid = $resultUserToken->to_userid;
						$tmpObject->clientid = $resultUserToken->to_clientid;
						$tmpObject->name = $resultUserToken->to_name;
					}
					$tmpObject->content = $value->content;
					$tmpObject->chat_time = $value->chat_time;
					$tmpObject->updated_at = $value->chat_time;
					$tmpObject->created_at = $value->chat_time;

					array_push($out, $tmpObject);
				}
				$out = array_merge($out, $this->chatRepo->chatHistoryRoomBy($roomid)->toArray());
			}
			return $out;
		} else {
			return [];
		}
	}

	public function storeChatLog($array) 
	{
		$response = array();
        if(isset($array['user_token']) && isset($array['data'])) {
            $userToken = $array['user_token'];
            $data = $array['data'];

            if(empty($data['to_name']) && empty($data['to_userid']) && !empty($data['to_clientid'])) {
            	$toUser = $this->chatRepo->fetchByToClientId($data['to_clientid']);
            	if($toUser) {
            		$data['to_name'] = $toUser->to_name;
            		$data['to_userid'] = $toUser->to_userid;
            	}
            }
            if($repo = $this->chatRepo->storeByUserToken($data)) {
            	$response['cdoe'] = 200;
            	$response['data'] = $repo;
            } else {
            	$response['cdoe'] = 520;
            	$response['message'] = "SQL Insert Fail";
            }
        } else {
            $response['cdoe'] = 520;
            $response['message'] = 'user_token mission';
        }
		return $response;
	}

	public function listChatLog($array) 
	{
		if(isset($array['user_token'])) {
			return $this->chatRepo->fetAllByUserToken($array['user_token']);
		} else {
			$response['cdoe'] = 520;
            $response['message'] = 'user_token mission';
		}
		return $response;
	}
}