<?php
namespace App\Repositories;

use App\Models\TpReportImhqst;

class TaipowerRepository
{
    protected $model;

    public function __construct(TpReportImhqst $model)
    {
        $this->model = $model;
    }

    public function list()
    {
        return $this->model::orderBy('created_at', 'desc')/*->where('status', 0)*/->get();
    }
}