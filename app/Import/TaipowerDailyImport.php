<?php
namespace App\Import;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\SampleLog;
use App\Models\TpReportImhqst;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TaipowerDailyImport implements ToCollection, WithMultipleSheets
{
    use Importable;

    public $called = false;
    public $file = false;
    private $file_name = 'default';
    private $arrayData = '';
    private $reportDate = null;
    private $reportArea = null;
    private $totalSums = null;
    private $C_group = [
        16, // VT
        17, // MT
        18  // UT
    ];
    private $L_group = [
        16, // VT
        17, // MT
        18  // UT
    ];
    private $TKY_group = [
        16, // VT
        17, // MT
        18  // UT
    ];

    public function __construct($file_name, $arrayData, $file)
    {
        $this->file_name = $file_name;
        $this->arrayData = $arrayData;
        $this->reportDate = Carbon::parse($arrayData['report_date']);
        $this->reportArea = $arrayData['report_area'];
        $this->file = $file;
    }

    public function sheets(): array
    {
        // 只处理第一个工作表
        return [
            0 => $this  // `0` 是第一个工作表的索引
        ];
    }
    public function getChannelSums() 
    {
        return $this->totalSums;
    }
    public function getrReportDate() 
    {
        return $this->reportDate;
    }
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        // 从 Collection 中排除标题行，假设第一行是标题
        $data = $collection->slice(2);

        $report_type = !empty($this->arrayData['report_type']) ? $this->arrayData['report_type'] : "";
        $result_TKY = null;
        $result_C = null;
        $result_L = null;
        $export_path = [];
        $totalSums = ['16' => 0, '17' => 0, '18' => 0];  // 初始化总和数组
        switch($report_type)
        {
            case 'TP':
                $result_C = $this->filterGroup('C', $data);
                $result_L = $this->filterGroup('L', $data);
                $export_path = array_merge($result_C['export_path'], $result_L['export_path']);
                foreach (['16', '17', '18'] as $key) {
                    $totalSums[$key] += $result_C['totalSumByGroup'][$key] + $result_L['totalSumByGroup'][$key];
                }
            break;
            case 'JK':
                $result_C = $this->filterGroup('C', $data);
                $result_L = $this->filterGroup('L', $data);
                $result_TKY = $this->filterGroup('TKY', $data);
                $export_path = array_merge($result_C['export_path'], $result_L['export_path'], $result_TKY['export_path']);
                // 累加C、L和TKY组的总和
                foreach (['16', '17', '18'] as $key) {
                    $totalSums[$key] += $result_C['totalSumByGroup'][$key] + $result_L['totalSumByGroup'][$key] + $result_TKY['totalSumByGroup'][$key];
                }
            break;
            default:
        }
        $this->totalSums = $totalSums;

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
        unset($this->file, $export_path, $upload_path);
    }

    private function filterGroup($group_name, $data)
    {
        $arr_gp_key = $group_name == 'C'? $this->C_group : $this->L_group;
        $totalSumByGroup = []; // 新增一个数组来存储每个组别的总和
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

            // 初始化组别总和
            $totalSumByGroup[$gp_key] = 0;

            $isFirstGroup = true; // 用於標記是否是第一個分組
            $result = []; // 用於存儲最終結果
            // 进行分组后的数据处理
            $formattedData = $groupedData->map(function ($items, $key) use (&$isFirstGroup){
                if(!isset($isArrFirstGroup[$key])) {
                    $isArrFirstGroup[$key] = true;
                }
                // 進一步根據 $firstItem[10] 進行分組
                $subGroupedData = $items->groupBy(function ($item) {
                    return $item[10];
                });
                // 針對每一個 $firstItem[10] 分組進行格式化
                $release = $subGroupedData->map(function ($subItems, $subKey) use (&$isArrFirstGroup, $key) {
                    $firstItem = $subItems->first();
                    $baseInfo = "{$subKey}#{$firstItem[3]}";
                    if ($isArrFirstGroup[$key]) {
                        $baseInfo = "{$key}-{$baseInfo}";
                        $isArrFirstGroup[$key] = false;
                    }
                    $weldNumbers = $subItems->pluck(5)->implode('、');
                    $componentType = $firstItem[4];

                    // 返回格式化字符串
                    return "{$baseInfo}-{$weldNumbers}({$componentType})";
                });
                
                return $release->implode("<w:br/>");
                // dd($release);
                // return $subGroupedData->map(function ($subItems, $subKey) use ($key) {
                //     $firstItem = $subItems->first();
                //     $baseInfo = "{$key}-{$subKey}#{$firstItem[3]}";
                //     $weldNumbers = $subItems->pluck(5)->implode('、');
                //     $componentType = $firstItem[4];

                //     // 返回格式化字符串
                //     return "{$baseInfo}-{$weldNumbers}({$componentType})";
                // })->implode("<w:br/>");
                // $firstItem = $items->first();
                // $baseInfo = "{$key}-{$firstItem[10]}#{$firstItem[3]}";
                // $weldNumbers = $items->pluck(5)->implode('、');
                // $componentType = $firstItem[4];
                // // $work_date = Carbon::create(1899, 12, 30)->addDays($firstItem[0]);
                // // 返回格式化字符串，這將會在 map 的結果集合中為每一個分組建立一個條目
                // return "{$baseInfo}-{$weldNumbers}({$componentType})";
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
            $totalSumByGroup[$gp_key] += number_format($totalSum, 4);
            $result['work_date'] = $work_date;
            $export_path[] = $this->rendomWord($output1, $output2, $work_date, $group_name, $gp_key);

            // 释放不再需要的大型数据集
            unset($filteredData, $groupedData, $formattedData, $output1, $output2);
        }
        $result['export_path'] = $export_path;
        $result['totalSumByGroup'] = $totalSumByGroup;
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
            $templateProcessor->setValue('AR', $this->reportArea);
            $templateProcessor->setValue('C', $report_type == 'TP'? '5' : '6'); // TP 項目5，JK 項目6

            $templateProcessor->saveAs(Storage::disk('export')->path($copyPath));
            $templateProcessor = null;
        }
        return $copyPath;
    }
}