<?php
namespace App\Services\ParseReport;

use Illuminate\Support\Facades\Log;
use App\Repositories\{SampleLogRepository};
use App\Repositories\DispatchRepository;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ParseNormal
{
    private $spRepo = null;
    private $dispstchRepo = null;

    public function __construct(SampleLogRepository $spRepo, DispatchRepository $dispstchRepo) 
	{
		$this->spRepo = $spRepo;
        $this->dispstchRepo = $dispstchRepo;
	}

    public function getNormalPT($version)
	{
		return $this->spRepo->lately('normal.pt.' . $version);
	}

    public function parseReportNormalMT($function_path, $id)
    {
    }
    public function parseReportNormalUT($function_path, $id)
    {
    }
    public function parseReportNormalRT($function_path, $id)
    {
    }
    public function parseReportNormalIT($function_path, $id)
    {
    }

    public function parseReportNormalPT($function_path, $id)
    {
        $out = [];
        $reporter = $this->dispstchRepo->funPathDispatch($function_path, $id);
        
        if(!isset($reporter->report_pt)) {
            return false;
        }
        
        $sample = $this->getNormalPT($reporter->report_pt->report_version);
        
        $realPath = $sample->full_path;
       
        $serial = Carbon::now()->format('Ymd');
        // $copyPath = 'normal/'.$serial.'/pt/XID-PT-' . $serial .'-' . str_pad($id, 5, "0", STR_PAD_LEFT) . '.docx';
        $copyPath = 'normal/'.$serial.'/pt/XID-PT-' . $reporter->report_pt->original_no . '.docx';
        if (Storage::disk('export')->put($copyPath, Storage::disk('public_sample')->get($realPath))) {
            // dd(Storage::disk('public_sample'));
            $dirname = dirname($realPath);
            $samplePath = config('filesystems.disks.public_sample.root'). '/' .$dirname . '/' . basename($realPath);
            // $samplePath = public_path('uploads/sample/'.$dirname . '/' . basename($realPath));
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($samplePath);
            $templateProcessor->setValue('Report No', $reporter->report_pt->original_no);
            $templateProcessor->setValue('Customer', $reporter->customer);
            $templateProcessor->setValue('Job Name', $reporter->job_name);
            $templateProcessor->setValue('Exam Date', $reporter->exam_date);
            $templateProcessor->setValue('Exam. Place', $reporter->exam_place);
            if(isset($reporter->report_pt)) {
                $templateProcessor->setValue('Material', $reporter->report_pt->material);
                $templateProcessor->setValue('Thicknes', $reporter->report_pt->thicknes);
                $templateProcessor->setValue('Light Intensity', $reporter->report_pt->light_intensity);
                $templateProcessor->setValue('Surface Temp', $reporter->report_pt->surface_temp);
                $items = json_decode($reporter->report_pt->items);
                $thickness_objs = json_decode($reporter->report_pt->thickness_objs? $reporter->report_pt->thickness_objs : '');
                $templateProcessor->cloneBlock('block_detail', sizeof($thickness_objs), true, true);
                
                $hash = 1;
                foreach($items as $objs) {
                    $values = [];
                    foreach($objs as $obj) {
                        $itit['Object No#'.$hash] = $obj->object_no;
                        $itit['Weld Length#'.$hash] = $obj->weld_length;
                        $itit['Defect Location#'.$hash] = $obj->defect_location;
                        $itit['Type of Defect#'.$hash] = $obj->type_of_defect;
                        $itit['Defect Length#'.$hash] = $obj->defect_length;
                        $itit['Result#'.$hash] = $obj->result == 0? '合格' : '不合格';
                        $itit['Remark#'.$hash] = $obj->remark;
                        array_push($values, $itit);
                    }
                    $templateProcessor->cloneRowAndSetValues('Object No#'.$hash, $values);
                    $hash++;
                }
            }

            $templateProcessor->saveAs(Storage::disk('export')->path($copyPath));
            $array['id'] = $id;
            $array['export_final_path'] = $copyPath;
            if($this->dispstchRepo->saveDispatchByArray($array)) {
                $out['code'] = 1000;
                $out['message'] = "資料更新成功";
                Log::info(json_encode($array));
            } else {
                $out['code'] = 50001;
                $out['message'] = "資料更新錯誤";
            }
        }
    }
}