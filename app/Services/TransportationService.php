<?php
namespace App\Services;

use Carbon\Carbon;
use App\Repositories\{TransportationRespository};

use Arr;
use Illuminate\Support\Facades\Log;

class TransportationService
{
	private $transRepo = null;

	public function __construct(TransportationRespository $disRepo) 
	{
		$this->transRepo = $disRepo;
	}

    public function getDatas()
    {
        return $this->transRepo->getDatas();
    }
}