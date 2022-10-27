<?php
namespace App\Services;

use Arr;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\ParseReport\ParseFpg;
use Illuminate\Support\Facades\Storage;
use App\Repositories\DispatchRepository;
use App\Repositories\{SampleLogRepository};
use App\Services\ParseReport\ParseNormal;
use App\Services\ParseReport\ParseSdm;

class SampleService
{
	private $spRepo = null;
    private $dispstchRepo = null;
    
	public function __construct(SampleLogRepository $spRepo, DispatchRepository $dispstchRepo) 
	{
		$this->spRepo = $spRepo;
        $this->dispstchRepo = $dispstchRepo;
	}

	public function getChimeiPT()
	{
		return $this->spRepo->lately('chimei.pt');
	}
    public function getChimeiRT()
	{
		return $this->spRepo->lately('chimei.rt');
	}
    public function getChimeiMT()
	{
		return $this->spRepo->lately('chimei.mt');
	}
    public function getChimeiUT()
	{
		return $this->spRepo->lately('chimei.ut');
	}
    public function getChimeiIT()
	{
		return $this->spRepo->lately('chimei.it');
	}
    public function getDispatch()
	{
		return $this->spRepo->lately('dispatch.20220701');
	}

    public function logsChimeiPT()
	{
		return $this->spRepo->logsAll('chimei.pt');
	}
    public function logsChimeiRT()
	{
		return $this->spRepo->logsAll('chimei.rt');
	}
    public function logsChimeiMT()
	{
		return $this->spRepo->logsAll('chimei.mt');
	}
    public function logsChimeiUT()
	{
		return $this->spRepo->logsAll('chimei.ut');
	}
    public function logsChimeiIT()
	{
		return $this->spRepo->logsAll('chimei.it');
	}
    public function logsNormalPT()
	{
		return $this->spRepo->logsAll('normal.pt');
	}

	public function syncSample($path, $funName, $id = null)
	{
		$out = [];
        $array = [];
        // if(!is_null($id)) {
        //     $array['id'] = $id;
        // }
        if(!empty($path) && !empty($funName)) {
            // $fileName = basename($path);
            $fileName = pathinfo($path, PATHINFO_FILENAME);
            $array = [
                'file_name' =>$fileName,
                'function_path' =>$funName,
                'full_path' =>$path,
            ];
            // dd($array);
            if($this->spRepo->saveByArray($array)) {
                // $this->convertWordToPDF(config('filesystems.disks.public_sample.realation_path') . '/' . $path);
                $out['code'] = 1000;
                $out['message'] = "資料更新成功";
            } else {
                $out['code'] = 50001;
                $out['message'] = "資料更新錯誤";
            }
        } else {
            $out['code'] = 50002;
            $out['message'] = "資料更新錯誤";
        }
        
        return $out;
	}

    public function groupFunctionPath()
    {
        return $this->spRepo->groupFunctionPath();
    }

    public function parseDispatch($function_path, $id)
    {
        $out = [];
        $sample = $this->getDispatch();
        $reporter = $this->dispstchRepo->funPathDispatch(null, $id);
        
        $realPath = $sample->full_path;
        $serial = Carbon::now()->format('Ymd');
        $copyPath = 'dispath/'.$serial.'/' . $reporter->serial_no . '.docx';
        if (Storage::disk('export')->put($copyPath, Storage::disk('public_sample')->get($realPath))) {
            // dd(Storage::disk('public_sample'));
            $dirname = dirname($realPath);
            $samplePath = config('filesystems.disks.public_sample.root'). '/' .$dirname . '/' . basename($realPath);
            // $samplePath = public_path('uploads/sample/'.$dirname . '/' . basename($realPath));
            // dd($samplePath);
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($samplePath);
            $templateProcessor->setValue('Customer', $reporter->customer);
            $templateProcessor->setValue('serial_no', $reporter->serial_no);
            $templateProcessor->setValue('delegator', $reporter->delegator);
            $templateProcessor->setValue('concat_phone', $reporter->concat_phone);
            $templateProcessor->setValue('concat_name', $reporter->concat_name);
            $templateProcessor->setValue('exam_date', $reporter->exam_date);
            $templateProcessor->setValue('exam_place', $reporter->exam_place);
            $templateProcessor->setValue('material', $reporter->material);
            $templateProcessor->setValue('thickness', $reporter->thickness);
            $templateProcessor->setValue('leng', $reporter->leng);
            $templateProcessor->setValue('editor', $reporter->editor);
            $templateProcessor->setValue('inspector', $reporter->inspector);
            $templateProcessor->setValue('transport', $reporter->transport);
            
            $templateProcessor->saveAs(Storage::disk('export')->path($copyPath));
            $array['id'] = $id;
            $array['export_final_path'] = $copyPath;
            if($this->dispstchRepo->saveDispatchByArray($array)) {
                $out['code'] = 1000;
                $out['data'] = $array;
                $out['message'] = "資料更新成功";
                Log::info(json_encode($array));
            } else {
                $out['code'] = 50001;
                $out['message'] = "資料更新錯誤";
            }
        }
        return $out;
    }

    public function parseReportFpgRT_Oil($function_path, $id)
    {
        $fpg = new ParseFpg($this->spRepo, $this->dispstchRepo);
        return $fpg->parseReportFpgRT_Oil($function_path, $id);
    }

    public function parseReportFpgRT_Olll($function_path, $id)
    {
        $fpg = new ParseFpg($this->spRepo, $this->dispstchRepo);
        return $fpg->parseReportFpgRT_Olll($function_path, $id);
    }

    public function parseReportFpgRT_Three($function_path, $id)
    {
        $fpg = new ParseFpg($this->spRepo, $this->dispstchRepo);
        return $fpg->parseReportFpgRT_Three($function_path, $id);
    }

    public function parseReportNormalPT($function_path, $id)
    {
        $fpg = new ParseNormal($this->spRepo, $this->dispstchRepo);
        return $fpg->parseReportNormalPT($function_path, $id);
    }
    public function parseReportNormalIT($function_path, $id)
    {
        $fpg = new ParseNormal($this->spRepo, $this->dispstchRepo);
        return $fpg->parseReportNormalIT($function_path, $id);
    }
    public function parseReportNormalUT($function_path, $id)
    {
        $fpg = new ParseNormal($this->spRepo, $this->dispstchRepo);
        return $fpg->parseReportNormalUT($function_path, $id);
    }
    public function parseReportNormalRT($function_path, $id)
    {
        $fpg = new ParseNormal($this->spRepo, $this->dispstchRepo);
        return $fpg->parseReportNormalRT($function_path, $id);
    }
    public function parseReportNormalMT($function_path, $id)
    {
        $fpg = new ParseNormal($this->spRepo, $this->dispstchRepo);
        return $fpg->parseReportNormalMT($function_path, $id);
    }

    public function parseReportSdmMT($function_path, $id)
    {
        $fpg = new ParseSdm($this->spRepo, $this->dispstchRepo);
        return $fpg->parseReportSdmMT($function_path, $id);
    }

    public function parseReportSdmVT($function_path, $id)
    {
        $fpg = new ParseSdm($this->spRepo, $this->dispstchRepo);
        return $fpg->parseReportSdmVT($function_path, $id);
    }

    public function parseReportSdmUT($function_path, $id)
    {
        $fpg = new ParseSdm($this->spRepo, $this->dispstchRepo);
        return $fpg->parseReportSdmUT($function_path, $id);
    }

    public function parseReportChimeiPT($function_path, $id)
    {
        $out = [];
        $sample = $this->getChimeiPT();
        $reporter = $this->dispstchRepo->funPathDispatch($function_path, $id);
        // dd($reporter);
        $realPath = $sample->full_path;
        $serial = Carbon::now()->format('Ymd');
        $copyPath = 'chimei/'.$serial.'/pt/' . $reporter->report_pt->original_no . '.docx';
        if (Storage::disk('export')->put($copyPath, Storage::disk('public_sample')->get($realPath))) {
            // dd(Storage::disk('public_sample'));
            $dirname = dirname($realPath);
            $samplePath = config('filesystems.disks.public_sample.root'). '/' .$dirname . '/' . basename($realPath);
            // $samplePath = public_path('uploads/sample/'.$dirname . '/' . basename($realPath));
            // dd($samplePath);
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($samplePath);
            $templateProcessor->setValue('Report No', $reporter->report_pt->original_no);
            $templateProcessor->setValue('Customer', $reporter->customer);
            $templateProcessor->setValue('Job Name', $reporter->delegator . "/" . $reporter->exam_place . "/" . $reporter->job_name);
            $templateProcessor->setValue('Exam Date', $reporter->exam_date);
            $templateProcessor->setValue('Exam. Place', $reporter->exam_place);
            if(isset($reporter->report_pt)) {
                $templateProcessor->setValue('Obsert', $reporter->report_pt->obsert);
                $templateProcessor->setValue('Job No', $reporter->report_pt->job_no);
                $templateProcessor->setValue('eType2', $reporter->report_pt->on_site == 1? '■' : '□');
                $templateProcessor->setValue('eType1', $reporter->report_pt->on_site == 0? '■' : '□');
                $templateProcessor->setValue('Material', $reporter->report_pt->material);
                $templateProcessor->setValue('Light Intensity', $reporter->report_pt->light_intensity);
                $items = json_decode($reporter->report_pt->items);
                $thickness_objs = json_decode($reporter->report_pt->thickness_objs? $reporter->report_pt->thickness_objs : '');
                $templateProcessor->cloneBlock('block_detail', sizeof($thickness_objs), true, true);
                
                $hash = 1;
                $runOnce = true;
                $thicknesStr = '';
                foreach($items as $key => $objs) {
                    $values = [];
                    // foreach($objs as $obj) {
                    $thickOrQu = $thickness_objs[$key]->thickOrQu;
                    $thickOrQuUnit = "";
                    switch($thickOrQu) {
                        case '0':
                            $thickOrQuUnit = '"';
                            if(isset($thickness_objs[$key]->mThincknessNomalSize)) {
                                $thicknesStr .= ($thickness_objs[$key]->mThincknessNomalSize . $thickOrQuUnit . ';');
                            }
                            break;
                        case '1':
                            $thickOrQuUnit = 'm2';
                            if(isset($thickness_objs[$key]->mAreaSize)) {
                                $thicknesStr .= ($thickness_objs[$key]->mAreaSize . $thickOrQuUnit . ';');
                            }
                            break;
                        case '2':
                            $thickOrQuUnit = 'm';
                            if(isset($thickness_objs[$key]->mAreaSize)) {
                                $thicknesStr .= ($thickness_objs[$key]->mAreaSize . $thickOrQuUnit . ';');
                            }
                            break;
                    }
                    for($j = 0; $j < 23; $j++) {
                        $itit['No#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->object_no : '';
                        $itit['Object No#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->object_no : ($runOnce? '以下空白' : '');
                        $itit['Weld Length#'.$hash] = isset($objs[$j]->weld_length)? $objs[$j]->weld_length . $thickOrQuUnit:'';
                        $itit['Welder No#'.$hash] = isset($objs[$j]->welder_no)? $objs[$j]->welder_no:'';
                        $itit['Defect Location#'.$hash] = isset($objs[$j]->defect_location)?$objs[$j]->defect_location:'';
                        $itit['Type of Defect#'.$hash] = isset($objs[$j]->type_of_defect)?$objs[$j]->type_of_defect:'';
                        $itit['Defect Length#'.$hash] = isset($objs[$j]->defect_length)?$objs[$j]->defect_length:'';
                        $itit['Result#'.$hash] = isset($objs[$j]->result)?  ($objs[$j]->result== 0? '合格' : '不合格') : '';
                        $itit['Remark#'.$hash] = isset($objs[$j]->remark)? $objs[$j]->remark : '';
                        array_push($values, $itit);

                        if($itit['Object No#'.$hash] == '以下空白') {
                            $runOnce = false;
                        }
                    }
                    $templateProcessor->cloneRowAndSetValues('No#'.$hash, $values);
                    $hash++;
                }

                $templateProcessor->setValue('Thicknes', $thicknesStr);
                // for($i = 1; $i < 21; $i++) {
                //     if(isset($items[$i-1])) {
                //         $templateProcessor->setValue('i'.$i.'1', isset($items[$i-1]->object_no)? $i : '');
                //         $templateProcessor->setValue('i'.$i.'2', isset($items[$i-1]->object_no)? $items[$i-1]->object_no : '');
                //         $templateProcessor->setValue('i'.$i.'3', isset($items[$i-1]->weld_length)? $items[$i-1]->weld_length . '”' : '');
                //         $templateProcessor->setValue('i'.$i.'4', isset($items[$i-1]->welder_no)? $items[$i-1]->welder_no : '');
                //         $templateProcessor->setValue('i'.$i.'5', isset($items[$i-1]->defect_location)? $items[$i-1]->defect_location : '');
                //         $templateProcessor->setValue('i'.$i.'6', isset($items[$i-1]->type_of_defect)? $items[$i-1]->type_of_defect : '');
                //         $templateProcessor->setValue('i'.$i.'7', isset($items[$i-1]->defect_length)? $items[$i-1]->defect_length : '');
                //         $templateProcessor->setValue('i'.$i.'8', isset($items[$i-1]->result)? $items[$i-1]->result : '');
                //         $templateProcessor->setValue('i'.$i.'9', isset($items[$i-1]->remark)? $items[$i-1]->remark : '');
                //     } else {
                //         for($j = 1; $j < 10; $j++) {
                //             $templateProcessor->setValue('i'.$i.$j, '');
                //         }
                //     }
                // }
            }
            // dd(Storage::disk('export')->path($copyPath));
            // $templateProcessor->saveAs(Storage::disk('export')->path($copyPath));
            $templateProcessor->saveAs(Storage::disk('export')->path($copyPath));
            $array['id'] = $id;
            $array['export_final_path'] = $copyPath;
            if($this->dispstchRepo->saveDispatchByArray($array)) {
                $out['code'] = 1000;
                $out['data'] = $array;
                $out['message'] = "資料更新成功";
                Log::info(json_encode($array));
            } else {
                $out['code'] = 50001;
                $out['message'] = "資料更新錯誤";
            }
        }
        return $out;
    }

    public function parseReportChimeiRT($function_path, $id)
    {
        $out = [];
        $sample = $this->getChimeiRT();
        $reporter = $this->dispstchRepo->funPathDispatch($function_path, $id);
        // dd($reporter);
        $realPath = $sample->full_path;
        $serial = Carbon::now()->format('Ymd');
        $copyPath = 'chimei/'.$serial.'/rt/XID-RT-' . $serial .'-' . str_pad($id, 5, "0", STR_PAD_LEFT) . '.docx';
        if (Storage::disk('export')->put($copyPath, Storage::disk('public_sample')->get($realPath))) {
            // dd(Storage::disk('public_sample'));
            $dirname = dirname($realPath);
            $samplePath = config('filesystems.disks.public_sample.root'). '/' .$dirname . '/' . basename($realPath);
            // $samplePath = public_path('uploads/sample/'.$dirname . '/' . basename($realPath));
            // dd($samplePath);
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($samplePath);
            $templateProcessor->setValue('Report No', 'XID-RT-' . $serial .'-' . str_pad($id, 5, "0", STR_PAD_LEFT));
            $templateProcessor->setValue('Customer', $reporter->customer);
            $templateProcessor->setValue('Delegator', $reporter->delegator);
            $templateProcessor->setValue('Exam Date', $reporter->exam_date);
            $templateProcessor->setValue('Examination place', $reporter->exam_place);
            if(isset($reporter->report_rt)) {
                $tmpReport = $reporter->report_rt;

                $templateProcessor->setValue('Job No', $tmpReport->job_no);
                $templateProcessor->setValue('Obsert', $tmpReport->obsert);
                $templateProcessor->setValue('Job Name', $tmpReport->job_name);

                $thickness_objs = json_decode($tmpReport->thickness_objs? $tmpReport->thickness_objs : '', true);
                $filmBrand = ''; $filmType = '';
                //底片廠牌 Film Brand
                $listFilmBrand = ['1' => 'D4', '1.5' => 'D5', '3' => 'D7'];
                // $r_report_size = explode(',', $tmpReport->film_brand);
                if(is_array($thickness_objs)) {
                    $film_brands = array_column($thickness_objs, 'film_brand');
                    foreach($listFilmBrand as $key => $value) {
                        if(in_array($key, $film_brands)) {
                            $filmBrand .= ('■' . $value . " ");
                        } else {
                            $filmBrand .= ('□' . $value . " ");
                        }
                    }
                }
                $templateProcessor->setValue('Film Brand', $filmBrand);
                $templateProcessor->setValue('Film Type', $filmType);

                $templateProcessor->setValue('Material', $reporter->report_rt->material);
                $templateProcessor->setValue('Thicknes', $reporter->report_rt->thicknes);
                $templateProcessor->setValue('Focal Spot', $reporter->report_rt->focal_spot);
                $templateProcessor->setValue('Energy/Strength', $reporter->report_rt->energy_strength);

                //照射方法
                $radiographicTech = "";
                if($reporter->report_rt->radiographic_tech == 0) {
                    $radiographicTech = "■ 單層壁 Single Wall Exposure     □ 雙層壁 Double Wall Exposure";
                } else if($reporter->report_rt->radiographic_tech == 1) {
                    $radiographicTech = "□ 單層壁 Single Wall Exposure     ■ 雙層壁 Double Wall Exposure";
                }
                $templateProcessor->setValue('Radiographic Tech', $radiographicTech);

                $templateProcessor->setValue('sod', $reporter->report_rt->sod);
                $templateProcessor->setValue('Geometric Unsharpness', $reporter->report_rt->geometric_unsharpness);

                //像質計位置 iqi_location_mark
                $iqi_location_mark = "";
                if($reporter->report_rt->iqi_location_mark == 0) {
                    $iqi_location_mark = "■射源側 Source Side     □底片側 Film Side";
                } else if($reporter->report_rt->iqi_location_mark == 1) {
                    $iqi_location_mark = "□射源側 Source Side     ■底片側 Film Side";
                }
                $templateProcessor->setValue('IQI/Location Mark', $iqi_location_mark);

                //像質計編號 IQI  No.
                $iqi_no = "";
                if($reporter->report_rt->iqi_no == 0) {
                    $iqi_no = "■1A□1B";
                } else if($reporter->report_rt->iqi_no == 1) {
                    $iqi_no = "□1A■1B";
                }
                $templateProcessor->setValue('IQI No', $iqi_no);

                //底片判讀方式 Interpret
                $interpret = "";
                if($reporter->report_rt->interpret == 0) {
                    $interpret = "■單壁判讀Single View     □雙壁判讀Double View";
                    
                } else if($reporter->report_rt->interpret == 1) {
                    $interpret = "□單壁判讀Single View     ■雙壁判讀Double View";
                }
                $templateProcessor->setValue('Interpret', $interpret);

                if($reporter->report_rt->radiographic_tech == 1 && $reporter->report_rt->interpret == 0) {
                    $templateProcessor->setValue('Interpret0', 'V');
                    $templateProcessor->setValue('Interpret1', '');
                } else if($reporter->report_rt->radiographic_tech == 1 && $reporter->report_rt->interpret == 1) {
                    $templateProcessor->setValue('Interpret0', '');
                    $templateProcessor->setValue('Interpret1', 'V');
                } else {
                    $templateProcessor->setValue('Interpret0', '');
                    $templateProcessor->setValue('Interpret1', '');
                }

                //底片大小 Film Size
                $film_size = "";
                $listFilmSize = ['4 1/2 in.×8 1/2 in.','3 1/3 in.× 6 in.'];
                $r_report_size = explode(',', $reporter->report_rt->film_size);
                if(is_array($r_report_size)) {
                    for($i = 0; $i < sizeof($listFilmSize); $i++) {
                        if(in_array($i, $r_report_size)) {
                            $film_size .= ('■' . $listFilmSize[$i] . " ");
                        } else {
                            $film_size .= ('□' . $listFilmSize[$i] . " ");
                        }
                    }
                }
                $templateProcessor->setValue('Film Q’ty', $film_size);
                
                $film_size = '';
                if($reporter->report_rt->film_size == 0) {
                    $film_size = "■ 單 Single         □ 雙 Double";
                } else if($reporter->report_rt->film_size == 1) {
                    $film_size = "□ 單 Single         ■ 雙 Double";
                }
                $templateProcessor->setValue('Film Size', $film_size);
                
                // if($reporter->report_rt->exposure_time >= 1) {
                // $min = intval($reporter->report_rt->exposure_time);
                // $sec = intval(($reporter->report_rt->exposure_time - $min) * 60);
                // }
                // $templateProcessor->setValue('Exposure Time', Carbon::create($reporter->report_rt->exposure_time)->format("i 分 s 秒"));
                // $templateProcessor->setValue('Exposure Time', $min . " 分 " . $sec  . " 秒");
                $templateProcessor->setValue('Exposure Time', $reporter->report_rt->exposure_time);
                
                $examinationResults = true;
                $items = json_decode($reporter->report_rt->items);
                // dd($reporter->report_pt);
                $thickness_objs = json_decode($reporter->report_rt->thickness_objs? $reporter->report_rt->thickness_objs : '');
                
                $templateProcessor->cloneBlock('block_detail', sizeof($thickness_objs), true, true);
                
                $hash = 1;
                $runOnce = true;
                foreach($items as $objs) {
                    $values = [];
                    // foreach($objs as $obj) {
                    for($j = 0; $j < 23; $j++) {
                        $itit['No#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->object_no : '';
                        $itit['FoWNo#'.$hash] = isset($objs[$j]->film_or_weld_no)? $objs[$j]->film_or_weld_no : ($runOnce? '以下空白' : '');
                        $itit['Thickness#'.$hash] = isset($objs[$j]->thickness)? $objs[$j]->thickness . 'mm' : '';
                        $itit['Welder No#'.$hash] = isset($objs[$j]->welder_no)? $objs[$j]->welder_no : '';
                        $itit['Sensitivity#'.$hash] = isset($objs[$j]->sensitivity)? $objs[$j]->sensitivity : '';
                        $itit['Defect Code#'.$hash] = isset($objs[$j]->defect_code)?$objs[$j]->defect_code:'';
                        $itit['Acceptable#'.$hash] = isset($objs[$j]->result)? ($objs[$j]->result== 0? 'V': '') :"";
                        $itit['Rejected#'.$hash] = isset($objs[$j]->result)? ($objs[$j]->result== 1? 'V': '') :"";
                        $itit['Remark#'.$hash] = isset($objs[$j]->remark)? $objs[$j]->remark : '';
                        array_push($values, $itit);
                        if($examinationResults) {
                            $examinationResults = isset($objs[$j]->result)? ($objs[$j]->result== 0) : false;
                        }

                        if($itit['FoWNo#'.$hash] == '以下空白') {
                            $runOnce = false;
                        }
                    }
                    $templateProcessor->cloneRowAndSetValues('No#'.$hash, $values);
                    $hash++;
                }
                // for($i = 1; $i < 21; $i++) {
                //     if(isset($items[$i-1])) {
                //         $templateProcessor->setValue('i'.$i.'1', $i);
                //         // $templateProcessor->setValue('i'.$i.'2', isset($items[$i-1]->object_no)? $items[$i-1]->object_no : '');
                //         $templateProcessor->setValue('i'.$i.'2', isset($items[$i-1]->film_or_weld_no)? $items[$i-1]->film_or_weld_no . '”' : '');
                //         $templateProcessor->setValue('i'.$i.'3', isset($items[$i-1]->thickness)? $items[$i-1]->thickness : '');
                //         $templateProcessor->setValue('i'.$i.'4', isset($items[$i-1]->welder_no)? $items[$i-1]->welder_no : '');
                //         $templateProcessor->setValue('i'.$i.'5', isset($items[$i-1]->sensitivity)? $items[$i-1]->sensitivity : '');
                //         $templateProcessor->setValue('i'.$i.'6', isset($items[$i-1]->defect_code)? $items[$i-1]->defect_code : '');
                //         $templateProcessor->setValue('i'.$i.'7', isset($items[$i-1]->result)? ($items[$i-1]->result == 0? 'V': ''): ''); //合格
                //         $templateProcessor->setValue('i'.$i.'8', isset($items[$i-1]->result)? ($items[$i-1]->result == 1? 'V': '') : '');//不合格
                //         $templateProcessor->setValue('i'.$i.'9', isset($items[$i-1]->remark)? $items[$i-1]->remark : '');

                //         if($examinationResults) {
                //             $examinationResults = $items[$i-1]->result == 0;
                //         }
                //     } else {
                //         for($j = 1; $j < 10; $j++) {
                //             $templateProcessor->setValue('i'.$i.$j, '');
                //         }
                //     }
                // }
                // 檢  測  結  果 Examination results
                $templateProcessor->setValue('Examination results', $examinationResults? "合格" : "不合格");
            }
            // dd(Storage::disk('export')->path($copyPath));
            $templateProcessor->saveAs(Storage::disk('export')->path($copyPath));
            $array['id'] = $id;
            $array['export_final_path'] = $copyPath;
            if($this->dispstchRepo->saveDispatchByArray($array)) {
                $out['code'] = 1000;
                $out['data'] = $array;
                $out['message'] = "資料更新成功";
                Log::info(json_encode($array));
            } else {
                $out['code'] = 50001;
                $out['message'] = "資料更新錯誤";
            }
        }
        return $out;
    }

    public function parseReportChimeiMT($function_path, $id)
    {
        $out = [];
        $sample = $this->getChimeiMT();
        $reporter = $this->dispstchRepo->funPathDispatch($function_path, $id);
        // dd($reporter);
        $realPath = $sample->full_path;
        $serial = Carbon::now()->format('Ymd');
        $copyPath = 'chimei/'.$serial.'/mt/XID-MT-' . $serial .'-' . str_pad($id, 5, "0", STR_PAD_LEFT) . '.docx';
        if (Storage::disk('export')->put($copyPath, Storage::disk('public_sample')->get($realPath))) {
            $dirname = dirname($realPath);
            $samplePath = config('filesystems.disks.public_sample.root'). '/' .$dirname . '/' . basename($realPath);
            // $samplePath = public_path('uploads/sample/'.$dirname . '/' . basename($realPath));
            // dd($samplePath);
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($samplePath);
            $templateProcessor->setValue('Report No', 'XID-MT-' . $serial .'-' . str_pad($id, 5, "0", STR_PAD_LEFT));
            $templateProcessor->setValue('Customer', $reporter->customer);
            $templateProcessor->setValue('Job Name', $reporter->job_name);
            $templateProcessor->setValue('Exam Date', $reporter->exam_date);
            $templateProcessor->setValue('Obsert', $reporter->customer);
            $templateProcessor->setValue('Examination place', $reporter->exam_place);

            if(isset($reporter->report_mt)) {
                $tmpReport = $reporter->report_mt;

                //設備編號 equlpment_no
                $templateProcessor->setValue('Equlpment No', $tmpReport->equlpment_no);

                //表面狀況 surface_status
                $surface_status = "";
                $r_surface_status = explode(',', $tmpReport->surface_status);
                if(is_array($r_surface_status)) {
                    $listSurfaceStatus = [
                        '銲接表面As-Welded',
                        '輥軋表面As-Rolled',
                        '研磨表面As-ground',
                        '機械加工As-machined',
                        '其它Other'
                    ];
                    foreach($listSurfaceStatus as $key => $elem) {
                        if(in_array($key, $r_surface_status)) {
                            $surface_status .= "■";
                        } else {
                            $surface_status .= "□";
                        }
                        $surface_status .= $elem; 
                    }
                }
                $templateProcessor->setValue('Surface Preparation', $surface_status);

                //檢測時機 exam_time
                $exam_time = "";
                $r_exam_time = explode(',', $tmpReport->exam_time);
                if(is_array($r_exam_time)) {
                    $listExamTime = [
                        '銲接後After Welded',
                        '銲前表面準備Prepared Surface',
                        '熱處理後 After HT',
                        '試壓後After Hydro. Test',
                        '其它Other機械加工As-machined'
                    ];
                    foreach($listExamTime as $key => $elem) {
                        if(in_array($key, $r_exam_time)) {
                            $exam_time .= "■";
                        } else {
                            $exam_time .= "□";
                        }
                        $exam_time .= $elem; 
                    }
                }
                $templateProcessor->setValue('Exam Time', $exam_time);
                //磁粒型式/顏色 type_of_particle_olor
                $type_of_particle_olor = "";
                $r_type_of_particle_olor = explode(',', $tmpReport->type_of_particle_olor);
                if(is_array($r_type_of_particle_olor)) {
                    $listTypeOfParticleOlor = [
                        '色比Visible',
                        '螢光Fluorescent',
                        '黑色Black',
                        '乾式Dry Type',
                        '濕式Wet Type'
                    ];
                    foreach($listTypeOfParticleOlor as $key => $elem) {
                        if(in_array($key, $r_type_of_particle_olor)) {
                            $type_of_particle_olor .= "■";
                        } else {
                            $type_of_particle_olor .= "□";
                        }
                        $type_of_particle_olor .= $elem; 
                    }
                }
                $templateProcessor->setValue('Type of Particle/ Color', $type_of_particle_olor);

                //磁化設備 magnetic_equipment
                $templateProcessor->setValue('Magnetic Equipment', $tmpReport->magnetic_equipment);
                //黑光燈 magnetic_equipment
                $templateProcessor->setValue('Black Light Meter', $tmpReport->black_light_meter);
                //施加方式 magnetic_equipment
                $applying_method = '';
                if($tmpReport->applying_method == 0) {
                    $applying_method = "■撒 Dusting or Blowing     □噴 Spraying";
                } else if($tmpReport->applying_method == 1) {
                    $applying_method = "□撒 Dusting or Blowing     ■噴 Spraying";
                }
                $templateProcessor->setValue('Applying Method', $applying_method);

                //磁化方法 magnetizing_methon
                $magnetizing_methon = "";
                $r_magnetizing_methon = explode(',', $tmpReport->magnetizing_methon);
                if(is_array($r_magnetizing_methon)) {
                    $listMagnetizingMethon = [
                        '連續法Continuous',
                        '退磁Demagnetization',
                        '不退磁Non-Demagnetization',
                    ];
                    foreach($listMagnetizingMethon as $key => $elem) {
                        if(in_array($key, $r_magnetizing_methon)) {
                            $magnetizing_methon .= "■";
                        } else {
                            $magnetizing_methon .= "□";
                        }
                        $magnetizing_methon .= ($elem . '     '); 
                    }
                }
                $templateProcessor->setValue('Magnetizing Methon', $magnetizing_methon);
                $examinationResults = true;
                $items = json_decode($reporter->report_mt->items);
                for($i = 1; $i < 20; $i++) {
                    if(isset($items[$i-1])) {
                        // $templateProcessor->setValue('i'.$i.'1', $i);
                        $templateProcessor->setValue('i'.$i.'1', isset($items[$i-1]->object_no)? $items[$i-1]->object_no : '');
                        $templateProcessor->setValue('i'.$i.'2', isset($items[$i-1]->exam_length)? $items[$i-1]->exam_length . '”' : '');
                        $templateProcessor->setValue('i'.$i.'3', isset($items[$i-1]->defect_location)? $items[$i-1]->defect_location : '');
                        $templateProcessor->setValue('i'.$i.'4', isset($items[$i-1]->type_of_defect)? $items[$i-1]->type_of_defect : '');
                        $templateProcessor->setValue('i'.$i.'5', isset($items[$i-1]->defect_length)? $items[$i-1]->defect_length : '');
                        $templateProcessor->setValue('i'.$i.'6', isset($items[$i-1]->result)? ($items[$i-1]->result == 0? '合格': '不合格'): ''); //合格
                        $templateProcessor->setValue('i'.$i.'7', isset($items[$i-1]->remark)? $items[$i-1]->remark : '');

                        if($examinationResults) {
                            $examinationResults = $items[$i-1]->result == 0;
                        }
                        // $templateProcessor->setValue('i'.$i.'8', isset($items[$i-1]->result)? ($items[$i-1]->result == 1? 'V': '') : '');//不合格
                        // $templateProcessor->setValue('i'.$i.'9', isset($items[$i-1]->remark)? $items[$i-1]->remark : '');
                    } else {
                        for($j = 1; $j < 10; $j++) {
                            $templateProcessor->setValue('i'.$i.$j, '');
                        }
                    }
                }
                // 檢  測  結  果 Examination results
                $templateProcessor->setValue('Examination results', $examinationResults? "合格" : "不合格");
            }
        }
        $templateProcessor->saveAs(Storage::disk('export')->path($copyPath));
        $array['id'] = $id;
        $array['export_final_path'] = $copyPath;
        if($this->dispstchRepo->saveDispatchByArray($array)) {
            $out['code'] = 1000;
            $out['data'] = $array;
            $out['message'] = "資料更新成功";
            Log::info(json_encode($array));
        } else {
            $out['code'] = 50001;
            $out['message'] = "資料更新錯誤";
        }
    }

    public function parseReportChimeiUT($function_path, $id)
    {
        $out = [];
        $sample = $this->getChimeiUT();
        $reporter = $this->dispstchRepo->funPathDispatch($function_path, $id);
        // dd($reporter);
        $realPath = $sample->full_path;
        $serial = Carbon::now()->format('Ymd');
        $copyPath = 'chimei/'.$serial.'/ut/XID-UT-' . $serial .'-' . str_pad($id, 5, "0", STR_PAD_LEFT) . '.docx';
        if (Storage::disk('export')->put($copyPath, Storage::disk('public_sample')->get($realPath))) {
            $dirname = dirname($realPath);
            $samplePath = config('filesystems.disks.public_sample.root'). '/' .$dirname . '/' . basename($realPath);
            // $samplePath = public_path('uploads/sample/'.$dirname . '/' . basename($realPath));
            // dd($samplePath);
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($samplePath);
            $templateProcessor->setValue('Report No', 'XID-UT-' . $serial .'-' . str_pad($id, 5, "0", STR_PAD_LEFT));
            $templateProcessor->setValue('Customer', $reporter->customer);
            $templateProcessor->setValue('Equlpment No', $reporter->equlpment_no);
            $templateProcessor->setValue('Job Name', $reporter->job_name);
            $templateProcessor->setValue('Exam Date', $reporter->exam_date);
            $templateProcessor->setValue('Obsert', $reporter->customer);
            $templateProcessor->setValue('Examination place', $reporter->exam_place);

            if(isset($reporter->report_ut)) {
                $tmpReport = $reporter->report_ut;
                $templateProcessor->setValue('Equipement', $tmpReport->equipement);

                if(!empty($tmpReport->editItemHeader_date1)) {
                    $templateProcessor->setValue('editItemHeader_date1', Carbon::parse($tmpReport->editItemHeader_date1)->format('Y年m月d日'));
                } else {
                    $templateProcessor->setValue('editItemHeader_date1', '年  月  日');
                }

                if(!empty($tmpReport->editItemHeader_date2)) {
                    $templateProcessor->setValue('editItemHeader_date2', Carbon::parse($tmpReport->editItemHeader_date2)->format('Y年m月d日'));
                } else {
                    $templateProcessor->setValue('editItemHeader_date2', '年  月  日');
                }

                if(!empty($tmpReport->editItemHeader_date3)) {
                    $templateProcessor->setValue('editItemHeader_date3', Carbon::parse($tmpReport->editItemHeader_date3)->format('Y年m月d日'));
                } else {
                    $templateProcessor->setValue('editItemHeader_date3', '年  月  日');
                }
                $items = json_decode($tmpReport->items);
                for($i = 1; $i < 26; $i++) {
                    if(isset($items[$i-1])) {
                        // $templateProcessor->setValue('i'.$i.'1', $i);
                        $templateProcessor->setValue('i'.$i.'1', isset($items[$i-1]->design_thickness)? $items[$i-1]->design_thickness : '');
                        $templateProcessor->setValue('i'.$i.'2', isset($items[$i-1]->corrosion_margin)? $items[$i-1]->corrosion_margin . '”' : '');
                        $templateProcessor->setValue('i'.$i.'3', isset($items[$i-1]->minimum_thickness)? $items[$i-1]->minimum_thickness : '');
                        $templateProcessor->setValue('i'.$i.'4', isset($items[$i-1]->date1)? $items[$i-1]->date1 : '');
                        $templateProcessor->setValue('i'.$i.'5', isset($items[$i-1]->date2)? $items[$i-1]->date2 : '');
                        // $templateProcessor->setValue('i'.$i.'6', isset($items[$i-1]->result)? ($items[$i-1]->result == 0? '合格': '不合格'): ''); //合格
                        $templateProcessor->setValue('i'.$i.'6', isset($items[$i-1]->date3)? $items[$i-1]->date3 : '');
                        // $templateProcessor->setValue('i'.$i.'8', isset($items[$i-1]->result)? ($items[$i-1]->result == 1? 'V': '') : '');//不合格
                        // $templateProcessor->setValue('i'.$i.'9', isset($items[$i-1]->remark)? $items[$i-1]->remark : '');
                    } else {
                        for($j = 1; $j < 10; $j++) {
                            $templateProcessor->setValue('i'.$i.$j, '');
                        }
                    }
                }
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

    public function parseReportChimeiIT($function_path, $id)
    {
        $out = [];
        $sample = $this->getChimeiIT();
        $reporter = $this->dispstchRepo->funPathDispatch($function_path, $id);
        // dd($reporter);
        $realPath = $sample->full_path;
        $serial = Carbon::now()->format('Ymd');
        $copyPath = 'chimei/'.$serial.'/it/XID-IT-' . $serial .'-' . str_pad($id, 5, "0", STR_PAD_LEFT) . '.docx';
        if (Storage::disk('export')->put($copyPath, Storage::disk('public_sample')->get($realPath))) {
            $dirname = dirname($realPath);
            $samplePath = config('filesystems.disks.public_sample.root'). '/' .$dirname . '/' . basename($realPath);
            // $samplePath = public_path('uploads/sample/'.$dirname . '/' . basename($realPath));
            // dd($samplePath);
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($samplePath);
            $templateProcessor->setValue('Report No', 'XID-IT-' . $serial .'-' . str_pad($id, 5, "0", STR_PAD_LEFT));
            $templateProcessor->setValue('Customer', $reporter->customer);
            $templateProcessor->setValue('Job No', $reporter->job_no);
            $templateProcessor->setValue('Job Name', $reporter->job_name);
            $templateProcessor->setValue('Exam Date', $reporter->exam_date);
            $templateProcessor->setValue('Obsert', $reporter->obsert);
            $templateProcessor->setValue('Examination place', $reporter->exam_place);

            if(isset($reporter->report_it)) {
                $tmpReport = $reporter->report_it;

                $templateProcessor->setValue('Equipement', $tmpReport->equipement);
                // 材    質 Material
                $templateProcessor->setValue('Material', $tmpReport->material);
                // 檢 測 方 式 Testing Method
                $templateProcessor->setValue('Testing Method', $tmpReport->material);
                // 包 覆 厚 度 Thickness
                $templateProcessor->setValue('Thickness', $tmpReport->material);
                // 檢 測 電 壓 Voltage
                $templateProcessor->setValue('Voltage', $tmpReport->material);
                // 電 擊 形 式 Type
                $templateProcessor->setValue('Type', $tmpReport->material);

                $examinationResults = true;
                $items = json_decode($reporter->report_it->items);
                for($i = 1; $i < 20; $i++) {
                    if(isset($items[$i-1])) {
                        // $templateProcessor->setValue('i'.$i.'1', $i);
                        // $templateProcessor->setValue('i'.$i.'1', isset($items[$i-1]->exam_length)? $items[$i-1]->exam_length : '');
                        $templateProcessor->setValue('i'.$i.'1', isset($items[$i-1]->exam_length)? $items[$i-1]->exam_length . '”' : '');
                        $templateProcessor->setValue('i'.$i.'2', isset($items[$i-1]->defect_location)? $items[$i-1]->defect_location : '');
                        $templateProcessor->setValue('i'.$i.'3', isset($items[$i-1]->type_of_defect)? $items[$i-1]->type_of_defect : '');
                        $templateProcessor->setValue('i'.$i.'4', isset($items[$i-1]->defect_length)? $items[$i-1]->defect_length : '');
                        $templateProcessor->setValue('i'.$i.'5', isset($items[$i-1]->result)? ($items[$i-1]->result == 0? '合格': '不合格'): ''); //合格
                        $templateProcessor->setValue('i'.$i.'6', isset($items[$i-1]->remark)? $items[$i-1]->remark : '');
                        if($examinationResults) {
                            $examinationResults = $items[$i-1]->result == 0;
                        }
                        // $templateProcessor->setValue('i'.$i.'8', isset($items[$i-1]->result)? ($items[$i-1]->result == 1? 'V': '') : '');//不合格
                        // $templateProcessor->setValue('i'.$i.'9', isset($items[$i-1]->remark)? $items[$i-1]->remark : '');
                    } else {
                        for($j = 1; $j < 10; $j++) {
                            $templateProcessor->setValue('i'.$i.$j, '');
                        }
                    }
                }
                // 檢  測  結  果 Examination results
                $templateProcessor->setValue('Examination results', $examinationResults? "合格" : "不合格");
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
	
    public function convertWordToPDF($path)
    {
        $pathNameWithoutExt = pathinfo($path, PATHINFO_FILENAME);
        $dirname = dirname($path);
            /* Set the PDF Engine Renderer Path */
        $domPdfPath = base_path('vendor/dompdf/dompdf');
        
        \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');
        // dd($dirname . basename($path));
        //Load word file
        $Content = \PhpOffice\PhpWord\IOFactory::load(public_path($dirname . '/' . basename($path)), 'MsDoc'); 
        dd($Content);
        //Save it into PDF
        $PDFWriter = \PhpOffice\PhpWord\IOFactory::createWriter($Content,'PDF');
        $PDFWriter->save(public_path($dirname . $pathNameWithoutExt . '.pdf')); 
        echo 'File has been successfully converted';
    }

    public function toDownloadReportFile($function_path, $id)
    {
        $out = [];
        $reporter = $this->dispstchRepo->funPathDispatch($function_path, $id);
        if($reporter && $reporter->export_final_path) {
            if($reporter->status == 4) {
                // dd($file_path);
                $out['code'] = 1000;
                $out['message'] = "Success";
                $out['data'] = config('app.url').'/storage/file/export/' . $reporter->export_final_path;
            } else {
                $out['code'] = 5004;
                $out['message'] = "尚未通過審核";
            }
        } else {
            $out['code'] = 5004;
            $out['message'] = "尚未通過審核";
            Log::info(json_encode($out));
        }
        return $out;
    }
}