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
        $data = $this->taipowerRepo->list();

        return $data->map(function ($item) {
            if (is_string($item->export_paths) && $paths = json_decode($item->export_paths, true)) {
                $item->export_paths = json_encode(array_map(function ($path) {
                    return config('app.url') . '/storage/file/export/' . $path;
                }, $paths));
            }
            return $item;
        });
    }
}