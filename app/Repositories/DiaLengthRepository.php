<?php
namespace App\Repositories;

use App\Models\DiaLengthMaster;
use App\Models\DiaLengthSlave;

class DiaLengthRepository
{
	protected $slave;
	protected $master;

    public function __construct(DiaLengthSlave $slave, DiaLengthMaster $master)
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
        return $this->slave->where('dia_length_master_id', $id)->get();
    }

    public function searchDate($date, $select_id = -1)
    {
        if(is_null($date)) {
            return $this->master::where('status', 1)->orderBy('created_at', 'desc')->get();
        } else {
            return $this->master::with([
                'slave' => function($query) use ($date, $select_id) {
                    $query->where('use_date', $date);
                    $query->where('dia_length_master_id', $select_id);
                }
            ])->where('id', $select_id)->first();
            // return $this->slave->where('use_date', $date)->where('dia_length_master_id', $select_id)->first();
        }
    }
}