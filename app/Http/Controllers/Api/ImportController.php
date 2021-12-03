<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\RTNessSlave;
use App\Models\RTNessMaster;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use App\Imports\RTnessImport;
use App\Models\DiaLengthSlave;
use App\Models\DiaLengthMaster;
use App\Services\ImportService;
use App\Import\DiaLengthImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Concerns\ToArray;
use App\Http\Resources\ResponseResource;
use Maatwebsite\Excel\Concerns\Importable;

class ImportController extends Controller
{
    private $importService = null;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }
    /**
     * 
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $type)
    {
        switch($type) {
            case 'rt_ness':
                return new ResponseResource($this->importService->getRtNessDatas()); 
            break;
            case 'rtpr_ness':
                return new ResponseResource($this->importService->getRtPrNessDatas()); 
            break;
            case 'dialength':
                return new ResponseResource($this->importService->getDiaLengthDatas()); 
            break;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $type, $id)
    {
        // return new ResponseResource($this->chatService->firstRoom($request->all(), $id));
        switch($type) {
            case 'rt_ness':
                return new ResponseResource($this->importService->getRtNessData($id)); 
            break;
            case 'rtpr_ness':
                return new ResponseResource($this->importService->getRtPrNessData($id)); 
            break;
            case 'dialength':
                return new ResponseResource($this->importService->getDiaLengthData($id)); 
            break;
        }
    }

    public function searchDiaLength(Request $request) 
    {
        return new ResponseResource($this->importService->searchDiaLength($request->all())); 
    }

    /**
     * Import RT Thinkness.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importRTNess(Request $request)
    {
        $description = '';
        $file_name = Carbon::now()->format('ymdhms');
        if($request->has('description')) {
            $description = $request->input('description');
        }
        $import = new class($file_name, $description) implements ToArray
        {
            use Importable;

            public $called = false;
            private $file_name = 'default';
            private $description = '';

            public function __construct($file_name, $description)
            {
                $this->file_name = $file_name;
                $this->description = $description;
            }
            /**
             * @param  array  $array
             */
            public function array(array $array)
            {
                if(!RTNessMaster::where('file_name', $this->file_name)->first()) {
                    if(is_array($array) && sizeof($array) > 0) {
                        $aIndex = array_search('厚度', $array[0]);
                        $bIndex = array_search('係數', $array[0]);
    
                        // $slaveArray = [];
                        for($i = 1; $i < sizeof($array); $i++) {
                            // $array[$i] = array_filter($array[$i], function( $k ) use ($aIndex, $bIndex){ return $k == $aIndex || $k == $bIndex; }, ARRAY_FILTER_USE_KEY);
                            $array[$i] = $this->replaceArrayKey($array[$i], $aIndex, 'thickness');
                            $array[$i] = $this->replaceArrayKey($array[$i], $bIndex, 'numness');
    
                            // array_push($slaveArray, new RTNessSlave($array[$i]));
                        }
                        
                        RTNessMaster::create([
                            'file_name'     => $this->file_name,
                            'description'   => $this->description,
                        ])->slave()->createMany(array_slice($array, 1));
                        // dd(array_slice($array, 1));
                    }
                }
            }

            private function replaceArrayKey($array, $oldKey, $newKey){
                //If the old key doesn't exist, we can't replace it...
                if(!isset($array[$oldKey])){
                    return $array;
                }
                //Get a list of all keys in the array.
                $arrayKeys = array_keys($array);
                //Replace the key in our $arrayKeys array.
                $oldKeyIndex = array_search($oldKey, $arrayKeys);
                $arrayKeys[$oldKeyIndex] = $newKey;
                //Combine them back into one array.
                $newArray =  array_combine($arrayKeys, $array);
                return $newArray;
            } 
        };

        

        $import->import($request->file('ex_file'));
        // return new ResponseResource($this->chatService->chatHistoryRoom($request->all()));
    }

    /**
     * Import RTPR Thinkness.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importRTPRNess(Request $request)
    {
        $description = '';
        $file_name = Carbon::now()->format('ymdhms');
        if($request->has('description')) {
            $description = $request->input('description');
        }
        $import = new class($file_name, $description) implements ToArray
        {
            use Importable;

            public $called = false;
            private $file_name = 'default';
            private $description = '';

            public function __construct($file_name, $description)
            {
                $this->file_name = $file_name;
                $this->description = $description;
            }
            /**
             * @param  array  $array
             */
            public function array(array $array)
            {
                if(!RTNessMaster::where('file_name', $this->file_name)->first()) {
                    if(is_array($array) && sizeof($array) > 0) {
                        $aIndex = array_search('厚度', $array[0]);
                        $bIndex = array_search('係數', $array[0]);
    
                        // $slaveArray = [];
                        for($i = 1; $i < sizeof($array); $i++) {
                            // $array[$i] = array_filter($array[$i], function( $k ) use ($aIndex, $bIndex){ return $k == $aIndex || $k == $bIndex; }, ARRAY_FILTER_USE_KEY);
                            $array[$i] = $this->replaceArrayKey($array[$i], $aIndex, 'thickness');
                            $array[$i] = $this->replaceArrayKey($array[$i], $bIndex, 'numness');
    
                            // array_push($slaveArray, new RTNessSlave($array[$i]));
                        }
                        
                        RTNessMaster::create([
                            'file_name'     => $this->file_name,
                            'description'   => $this->description,
                        ])->slave()->createMany(array_slice($array, 1));
                        // dd(array_slice($array, 1));
                    }
                }
            }

            private function replaceArrayKey($array, $oldKey, $newKey){
                //If the old key doesn't exist, we can't replace it...
                if(!isset($array[$oldKey])){
                    return $array;
                }
                //Get a list of all keys in the array.
                $arrayKeys = array_keys($array);
                //Replace the key in our $arrayKeys array.
                $oldKeyIndex = array_search($oldKey, $arrayKeys);
                $arrayKeys[$oldKeyIndex] = $newKey;
                //Combine them back into one array.
                $newArray =  array_combine($arrayKeys, $array);
                return $newArray;
            } 
        };

        

        $import->import($request->file('ex_file'));
        // return new ResponseResource($this->chatService->chatHistoryRoom($request->all()));
    }

    /**
     * Import 射源表.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importDiaLength(Request $request)
    {
        $description = '';
        $identify = '';
        $file = null;
        // $file_name = Carbon::now()->format('ymdhms');
        if($request->has('ex_file')) {
            $file = $request->file('ex_file');
        }
        if($request->has('identify')) {
            $identify = $request->input('identify');
        } else {
            return;
        }
        if($request->has('description')) {
            $description = $request->input('description');
        }
        if($request->has('length')) {
            $length = $request->input('length');
        }
        if($request->has('diameter')) {
            $diameter = $request->input('diameter');
        }
        if($request->has('diagonal')) {
            $diagonal = $request->input('diagonal');
        }
        $pass['identify'] = $identify;
        $pass['description'] = $description;
        $pass['length'] = $length;
        $pass['diameter'] = $diameter;
        $pass['diagonal'] = $diagonal;

        $array = ((new DiaLengthImport)->toArray($file));
        $out = [];
        if(is_array($array)) {
            $file_name = $file->getClientOriginalName();
            if(!DiaLengthMaster::where('file_name', $file_name)->first()) {
                $array = $array[0];
                if(is_array($array) && sizeof($array) > 0) {
                    $head = explode(':', $array[0][0]);
                    $startDate = '';
                    if(sizeof($head) > 0 && $head[1] == $pass['identify']) {
                        $dateIndex = array_search(strtolower('Date'), array_map('strtolower', $array[1]));
                        $ciIndex = array_search(strtolower('Ci'), array_map('strtolower', $array[1]));

                        $startDate = $array[2][$dateIndex];
                    } else {
                        $out['code'] = 5003;
                        $out['message'] = "請確認許可證字號是否相同";
                        return new ResponseResource($out); 
                    }
                    if(is_numeric($startDate)) {
                        $startDate = Carbon::parse('1899-12-30')->addDays($startDate);
                    } else {
                        $startDate = Carbon::parse($startDate);
                    }
                    $startDate->subDays(1);
                    // dd($startDate);
                    $slaveArray = [];
                    for($i = 2; $i < sizeof($array); $i++) {
                        for ($j=0; $j < $dateIndex; $j++) { 
                            $tmp['use_date'] = $startDate->addDays(1)->format('Y-m-d');
                            $tmp['data_value'] = $array[$i][$dateIndex-1-$j];
                            $slaveArray[] = $tmp;
                        }
                    }
                    DiaLengthMaster::create([
                        'file_name'     => $file_name,
                        'identify'     => $pass['identify'],
                        'description'   => $pass['description'],
                        'length'   => isset($pass['length'])? $pass['length'] : '',
                        'diameter'   => isset($pass['diameter'])? $pass['diameter'] : '',
                        'diagonal'   => isset($pass['diagonal'])? $pass['diagonal'] : '',
                        'status'   => '1',
                    ])->slave()->createMany($slaveArray);

                    $out['code'] = 1000;
                    $out['message'] = '';
                }
            } else {
                $out['code'] = 5003;
                $out['message'] = "檔名不得重複";
                return new ResponseResource($out); 
            }
        }
        return new ResponseResource($out);
    }

    public function dately(Request $request, $type, $command)
    {
        switch($type) {
            case 'rt_ness':
                return new ResponseResource($this->importService->latelyRtNessData()); 
            break;
            case 'rtpr_ness':
                return new ResponseResource($this->importService->latelyRtPrNessData()); 
            break;
            case 'dialength':
                // return new ResponseResource($this->importService->getDiaLengthData($id)); 
            break;
        }
    }
}
