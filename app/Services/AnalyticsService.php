<?php
namespace App\Services;

use Carbon\Carbon;
use App\Repositories\{DispatchRepository};

use Arr;

class AnalyticsService
{
	private $disRepo = null;
	public function __construct(DispatchRepository $disRepo) 
	{
		$this->disRepo = $disRepo;
	}

	public function dashboard()
	{
		$out = [];
		$status = [
			[
				'actionIcon'=> 'mdi-clock-outline',
				'actionText'=> 'Get More Space...',
				'color'=> '#FD9A13',
				'icon'=> 'mdi-sofa-single',
				'title'=> '已派遣',
				'value'=> '0',
				'status'=> 0,
				'updated_date' => Carbon::now()->format('Y-m-d h:m:s')
			],
			[
				'actionIcon'=> 'mdi-clock-outline',
				'actionText'=> 'Get More Space...',
				'color'=> 'blue darken-1',
				'icon'=> 'mdi-file-word-outline',
				'title'=> '報告製作中',
				'value'=> '0',
				'status'=> 2,
				'updated_date' => Carbon::now()->format('Y-m-d h:m:s')
			],
			[
				'actionIcon'=> 'mdi-clock-outline',
				'actionText'=> 'Get More Space...',
				'color'=> 'red darken-1',
				'icon'=> 'mdi-briefcase-search-outline',
				'title'=> '審核中',
				'value'=> '0',
				'status'=> 3,
				'updated_date' => Carbon::now()->format('Y-m-d h:m:s')
			],
			[
				'actionIcon'=> 'mdi-clock-outline',
				'actionText'=> 'Get More Space...',
				'color'=> 'green darken-1',
				'icon'=> 'mdi-checkbox-marked-circle-outline',
				'title'=> '通過審核',
				'value'=> '0',
				'status'=> 4,
				'updated_date' => Carbon::now()->format('Y-m-d h:m:s')
			]
		];

		foreach($status as $key => $item) {
			$out[$key] = $item;
			$out[$key]['value'] = $this->disRepo->countStatus($item['status']);
		}

		return $out;
	}
	public function dispatch()
	{
		$out = [];
		Arr::set(
			$out, 
			'month', 
			(
				$this->disRepo->funPathPoint
				(
					'month',
					Carbon::now()->subMonths(11)->firstOfMonth()->format('Y-m-d'),
					Carbon::now()->endOfMonth()->format('Y-m-d')
				)
			)
		);
		Arr::set(
			$out, 
			'dately', 
			(
				$this->disRepo->funPathPoint
				(
					'dately',
					Carbon::now()->subDays(7)->format('Y-m-d'),
					Carbon::now()->format('Y-m-d')
				)
			)
		);
		return $out;
	}
}