<?php
namespace App\Services;

use Carbon\Carbon;
use App\Repositories\RTNessRepository;
use App\Repositories\RTPRNessRepository;
use App\Repositories\DiaLengthRepository;
use Carbon\Exceptions\InvalidArgumentException;

class ImportService
{
    private $rtRepo = null;
    private $rtprRepo = null;
    private $diaRepo = null;

	public function __construct(RTNessRepository $rtRepo, DiaLengthRepository $diaRepo, RTPRNessRepository $rtprRepo)
	{
		$this->rtRepo = $rtRepo;
		$this->rtprRepo = $rtprRepo;
		$this->diaRepo = $diaRepo;
	}

    public function getRtNessDatas()
    {
        return $this->rtRepo->getMaster();
    }

    public function getRtNessData($id)
    {
        return $this->rtRepo->getSlave($id);
    }

    public function getRtPrNessDatas()
    {
        return $this->rtprRepo->getMaster();
    }

    public function getRtPrNessData($id)
    {
        return $this->rtprRepo->getSlave($id);
    }

    public function latelyRtPrNessData()
    {
        return $this->rtprRepo->lately();
    }

    public function latelyRtNessData()
    {
        return $this->rtRepo->lately();
    }

    public function getDiaLengthDatas()
    {
        return $this->diaRepo->getMaster();
    }

    public function getDiaLengthData($id)
    {
        return $this->diaRepo->getSlave($id);
    }

    public function searchDiaLength($array)
    {
        if(isset($array['params'])) {
            if(isset($array['params']['date']) && isset($array['params']['select_id'])) {
                try {
                    Carbon::createFromFormat('Y-m-d', $array['params']['date']);
                    return $this->diaRepo->searchDate($array['params']['date'], $array['params']['select_id']);
                } catch(InvalidArgumentException $e) {
                    return false;
                }
            } else {
                return $this->diaRepo->searchDate(null);
            }
        }
    }
}
