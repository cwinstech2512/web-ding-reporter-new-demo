<?php
namespace App\Repositories;

use App\Models\Transportation;

class TransportationRespository
{
    protected $model;

    public function __construct(Transportation $model)
    {
        $this->model = $model;
    }

    //取得 Master
    public function getDatas() 
    {
        return $this->model->orderBy('sort', 'asc')->orderBy('created_at', 'desc')->get();
    }
}