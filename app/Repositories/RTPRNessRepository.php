<?php
namespace App\Repositories;

use App\Models\RTPRNessMaster;
use App\Models\RTPRNessSlave;

class RTPRNessRepository
{
	protected $slave;
	protected $master;

    public function __construct(RTPRNessSlave $slave, RTPRNessMaster $master)
    {
        $this->master = $master;
        $this->slave = $slave;
    }

    //取得 Master
    public function getMaster() 
    {
        return $this->master->orderBy('created_at', 'desc')->get();
    }

    //取得 Slave
    public function getSlave($id)  
    {
        return $this->slave->where('rtpr_ness_master_id', $id)->get();
    }

    public function lately()
    {
        return $this->master::with([
            'slave' => function($query) {

            }
        ])->latest()->first();
    }
}