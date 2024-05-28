<?php
namespace App\Services;

use Carbon\Carbon;
use App\Repositories\TaipowerRepository;
use Illuminate\Support\Facades\Storage;
use App\Models\SampleLog;
use App\Models\TpReportImhqst;

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

    public function rendomXlxs($result_TP, $result_JK, $date, $group_name = '') 
    {
        $out = [];
        $sample = SampleLog::where('function_path', 'taipower.daily'.'.20240501')->latest()->first();;
        $realPath = $sample->full_path;
        $serial = Carbon::now()->format('Ymd');
        $copyPath = 'taipower/'.$serial.'/'."{$serial}-監工日報" . $date->format('md') . '.xlsx';
        if (Storage::disk('export')->put($copyPath, Storage::disk('public_sample')->get($realPath))) {
            $dirname = dirname($realPath);
            $samplePath = config('filesystems.disks.public_sample.root'). '/' .$dirname . '/' . basename($realPath);
            // $samplePath = public_path('uploads/sample/'.$dirname . '/' . basename($realPath));
            // dd($samplePath);
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($samplePath);
            $spreadsheet_0 = $spreadsheet->getSheetByName('離岸風電');

            $spreadsheet_0->setCellValue('E11', number_format($result_TP[16] + $result_JK[16], 3)); // 1.目視檢測(內外雙側一次計價)(公尺) ------  VT(JK+TP)
            $spreadsheet_0->setCellValue('E12', number_format($result_TP[17] + $result_JK[17], 3)); // 2.磁粒(液滲)檢測(內外雙側一次計價)(公尺)  ------  MT(JK+TP)
            $spreadsheet_0->setCellValue('E13', '');
            $spreadsheet_0->setCellValue('E14', '');
            $spreadsheet_0->setCellValue('E15', number_format($result_TP[18], 3));                   // 5.超音波檢測（連階段及其相連接構件TP)。(公尺) ---------  UT(TP)
            $spreadsheet_0->setCellValue('E16', number_format($result_JK[18], 3));                    // 6.超音波檢測（TKY管件及附屬構件，以開槽支管周長轉換成公尺）(公尺) ------  UT(JK)

            // 星期的中文映射
            $dayOfWeekMap = [
                1 => '一', 2 => '二', 3 => '三', 4 => '四',
                5 => '五', 6 => '六', 7 => '日'
            ];

            // 格式化日期
            $formattedDate = sprintf(
                "日    期：%s年%s月%s日 星期%s",
                $date->year,  // 年
                $date->month, // 月
                $date->day,   // 日
                $dayOfWeekMap[$date->dayOfWeekIso] // 星期，使用 Carbon 的 dayOfWeekIso
            );

            $spreadsheet_0->setCellValue('O5', $formattedDate);

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(Storage::disk('export')->path($copyPath));

            $model = new TpReportImhqst;
            $model->name = "{$serial}-監工日報" . $date->format('md');
            $model->upload_path = "";
            $model->report_type = "";
            $model->report_date = $date->format('Ymd');
            $model->export_paths = json_encode([$copyPath]);
            $model->ststus = 1;
            $model->save();
        }
        return $copyPath;
    }
}