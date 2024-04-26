<?php
namespace App\Services;

use Carbon\Carbon;
use App\Repositories\TaipowerRepository;
use App\Repositories\BotChatlogRepository;
use Arr;

class TaipowerService
{
    private $taipowerRepo = null;

	public function __construct(TaipowerRepository $taipowerRepo) 
	{
		$this->taipowerRepo = $taipowerRepo;
	}

    public function list()
    {
        return $this->taipowerRepo->list();
    }
}