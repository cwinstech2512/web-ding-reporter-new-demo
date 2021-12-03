<?php

namespace App\Imports;

use App\Models\RTNessSlave;
use Maatwebsite\Excel\Concerns\ToModel;

class RTnessImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        return new RTNessSlave([
           'thinkness'     => $row['厚度'],
           'numberness'    => $row['係數'],
        ]);
    }
}