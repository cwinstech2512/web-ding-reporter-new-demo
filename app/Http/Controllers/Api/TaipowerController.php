<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Services\TaipowerService;
use App\Services\SampleService;
use Carbon\Carbon;
use App\Models\SampleLog;
use App\Models\TpReportImhqst;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TaipowerController extends Controller
{
    private $taiService = null;
    private $sampleService = null;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TaipowerService $taiService, SampleService $sampleService)
    {
        $this->taiService = $taiService;
        $this->sampleService = $sampleService;
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
            case 'list':
                return new ResponseResource($this->taiService->list()); 
            break;
        }
    }

    public function importIMHQST(Request $request)
    {
        $validated = $request->validate([
            'report_date' => 'required|date', // 例: 验证是否为日期格式
            'report_type' => 'required'
        ]);
        $reportDate = $validated['report_date'];
        $reportType = $validated['report_type'];

        $arrayData = [
            'report_date' => $reportDate,
            'report_type' => $reportType
        ];
        $file = null;
        // $file_name = Carbon::now()->format('ymdhms');
        if($request->has('ex_file')) {
            $file = $request->file('ex_file');
        }
        $description = '';
        $file_name = Carbon::now()->format('ymdhms');
        if($request->has('description')) {
            $description = $request->input('description');
        }

        $import = new class($file_name, $arrayData, $file) implements ToCollection, WithMultipleSheets
        {
            use Importable;

            public $called = false;
            public $file = false;
            private $file_name = 'default';
            private $arrayData = '';
            private $reportDate = null;
            private $C_group = [
                16, 17, 18
            ];
            private $L_group = [
                16, 17, 18
            ];

            public function __construct($file_name, $arrayData, $file)
            {
                $this->file_name = $file_name;
                $this->arrayData = $arrayData;
                $this->reportDate = Carbon::parse($arrayData['report_date']);
                $this->file = $file;
            }

            public function sheets(): array
            {
                // 只处理第一个工作表
                return [
                    0 => $this  // `0` 是第一个工作表的索引
                ];
            }

            /**
             * @param Collection $collection
             */
            public function collection(Collection $collection)
            {
                // 从 Collection 中排除标题行，假设第一行是标题
                $data = $collection->slice(2);
                $result_C = $this->filterGroup('C', $data);
                $result_L = $this->filterGroup('L', $data);
                $export_path = array_merge($result_C['export_path'], $result_L['export_path']);
                if(!empty($export_path)) {

                    // 確保 file 屬性存在並且是檔案的實例
                    $upload_path = '';
                    if ($this->file && $this->file instanceof UploadedFile) {
                        $upload_path = Storage::disk('public_sample')->putFileAs(
                            'import_data',
                            $this->file,
                            $this->file->getClientOriginalName()
                        );
                    }
                    $model = new TpReportImhqst;
                    $model->name = '檢測或人時數量統計表-' . ($this->reportDate ? $this->reportDate->format('md') : Carbon::now()->format('md'));
                    $model->upload_path = $upload_path;
                    $model->report_type = !empty($this->arrayData['report_type']) ? $this->arrayData['report_type'] : "";
                    $model->report_date = $this->reportDate;
                    $model->export_paths = json_encode($export_path);
                    $model->ststus = 1;
                    $model->save();
                }
            }

            private function filterGroup($group_name, $data)
            {
                $arr_gp_key = $group_name == 'C'? $this->C_group : $this->L_group;

                $export_path = [];
                $result = [
                    'work_date' => null,
                    'export_path' => '',
                ];
                foreach($arr_gp_key as $gp_key) {
                    $data = $data->where(6, $group_name);
                    $filteredData = $data->filter(function ($item) use ($group_name, $gp_key) {
                        // 这里的条件可以根据需要调整，比如检查第 17 个元素是否为空或为null
                        return $item[6] == $group_name && !empty($item[$gp_key]) && !is_null($item[$gp_key]);
                    });
                    // 按 B 列的值进行分组，假设 B 列在集合中的索引为 1
                    $groupedData = $filteredData->groupBy(1);
    
                    $work_date = $this->reportDate;
    
                    // 进行分组后的数据处理
                    $formattedData = $groupedData->map(function ($items, $key) use (&$work_date){
                        $firstItem = $items->first();
                        $baseInfo = "{$key}-{$firstItem[10]}#{$firstItem[3]}";
                        $weldNumbers = $items->pluck(5)->implode('、');
                        $componentType = $firstItem[4];
                        // $work_date = Carbon::create(1899, 12, 30)->addDays($firstItem[0]);
                        // 返回格式化字符串，這將會在 map 的結果集合中為每一個分組建立一個條目
                        return "{$baseInfo}-{$weldNumbers}({$componentType})";
                    });
                
                    // 將所有的分組格式化的字符串合併，使用 PHP_EOL 作為每一個分組的分隔符號來創建新行
                    $output1 = $formattedData->implode("<w:br/>");
                
                    // 按 B 列的值进行分组，假设 B 列在集合中的索引为 1
                    $groupedData = $data->groupBy(10);
                    // 初始化合計值
                    $totalSum = 0;
                    // 进行分组后的数据处理
                    $formattedData = $groupedData->map(function ($items, $key) use (&$totalSum, $gp_key){
                        // 首先计算每个值的出现次数
                        $valueCounts = $items->pluck($gp_key)->map(function ($value) {
                            return (string)$value;  // 将每个值转换为字符串
                        })->filter(function ($value) {
                            return !is_null($value) && $value !== '';  // 过滤掉 null 和空字符串
                        })->countBy();
                        if($valueCounts->count() == 0) {
                            return null;
                        }
                        // 輸出每個構件的計算結果
                        $results = $valueCounts->map(function ($count_item, $count_key) use (&$totalSum){
                            if (is_numeric($count_key) && !empty($count_key)) {
                                $calcValue = $count_key * $count_item; // 计算乘法结果
                                $totalSum += $calcValue; // 累加到总和中
                                // 返回格式化的字符串
                                return "{$count_key}*{$count_item}=" . number_format($calcValue, 4);
                            }
                            return null;
                        });
                        
                        // 拼接最終的輸出字符串
                        return "{$key}". "<w:br/>" . $results->implode("<w:br/>");
                    })->filter(function ($value) {
                        return !is_null($value) && $value !== '';  // 过滤掉 null 和空字符串
                    });
                    $output2 = $formattedData->implode('<w:br/>') . '<w:br/>' ."合计" . number_format($totalSum, 4);
                    $result['work_date'] = $work_date;
                    $export_path[] = $this->rendomWord($output1, $output2, $work_date, $group_name, $gp_key);
                }
                $result['export_path'] = $export_path;
                return $result;
            }

            private function rendomWord($line1, $line2, $date, $group_name, $gp_key) 
            {
                $out = [];
                $trans_formate = [
                    16 => 'VT',
                    17 => 'MT',
                    18 => 'UT',
                ];
                $sample = SampleLog::where('function_path', 'taipower.'.strtolower($trans_formate[$gp_key]).'.l.20240501')->latest()->first();;
                $realPath = $sample->full_path;
                $serial = Carbon::now()->format('Ymd');
                $report_type = !empty($this->arrayData['report_type']) ? $this->arrayData['report_type'] : "";
                $copyPath = 'taipower/'.$serial.'/'."{$report_type}-". $trans_formate[$gp_key] .'('.$group_name.'線)檢測或人時數量統計表-' . $date->format('md') . '.docx';
                if (Storage::disk('export')->put($copyPath, Storage::disk('public_sample')->get($realPath))) {
                    $dirname = dirname($realPath);
                    $samplePath = config('filesystems.disks.public_sample.root'). '/' .$dirname . '/' . basename($realPath);
                    // $samplePath = public_path('uploads/sample/'.$dirname . '/' . basename($realPath));
                    // dd($samplePath);
                    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($samplePath);

                    $templateProcessor->setValue('Line1', (string)$line1);
                    $templateProcessor->setValue('Line2', $line2);
                    $templateProcessor->setValue('rt', $report_type);
                    $templateProcessor->setValue('Line3', $date->format('Y年m月d日'));
                    $templateProcessor->setValue('M', $date->format('m'));
                    $templateProcessor->setValue('G', $group_name);

                    $templateProcessor->saveAs(Storage::disk('export')->path($copyPath));
                }
                return $copyPath;
            }
        };
        $import->import($file);

        $out['code'] = 1000;
        $out['message'] = "Success";
        $out['data'] = '';

        return new ResponseResource($out);
    }
}
