<?php
namespace App\Services;

use Carbon\Carbon;
use App\Repositories\{StoredProcedureRepository};
use Arr;

class AnalyticsService
{
	private $spRepo = null;
	public function __construct(StoredProcedureRepository $spRepo) 
	{
		$this->spRepo = $spRepo;
	}

	public function dashboard()
	{
		$out = [];
		Arr::set($out, 'sc.count', sizeof($this->spRepo->dashboardSC()));
		Arr::set($out, 'bc.count', sizeof($this->spRepo->dashboardBC()));
		Arr::set($out, 'sc.point', ($this->spRepo->dashboardSCPoint()));

		return $out;
	}
}