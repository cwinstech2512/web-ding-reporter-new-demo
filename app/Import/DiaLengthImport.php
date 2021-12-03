<?php
namespace App\Import;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\Importable;

class DiaLengthImport implements ToArray
{
    use Importable;

    public function array(array $array)
    {
        return $array;
    }
}