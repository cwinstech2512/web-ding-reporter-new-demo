<?php
namespace App\Services\ParseReport;

use Illuminate\Support\Facades\Log;
use App\Repositories\{SampleLogRepository};
use App\Repositories\DispatchRepository;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use File;

class ParseNormal
{
    private $spRepo = null;
    private $dispstchRepo = null;
    private $previewNotUse = array("&");

    public function __construct(SampleLogRepository $spRepo, DispatchRepository $dispstchRepo)
	{
		$this->spRepo = $spRepo;
        $this->dispstchRepo = $dispstchRepo;
	}

    public function getNormalPT($version)
	{
		return $this->spRepo->lately('normal.pt.' . $version);
	}

    public function getNormalRT($version)
	{
		return $this->spRepo->lately('normal.rt.' . $version);
	}

    public function getNormalMT($version)
	{
		return $this->spRepo->lately('normal.mt.' . $version);
	}

    public function getNormalUT($version)
	{
		return $this->spRepo->lately('normal.ut.' . $version);
	}

    public function parseReportNormalMT($function_path, $id)
    {
        $out = [];
        $reporter = $this->dispstchRepo->funPathDispatch($function_path, $id);
        
        if(!isset($reporter->report_mt)) {
            return false;
        }
        
        $sample = $this->getNormalMT($reporter->report_mt->report_version);
        
        $realPath = $sample->full_path;
       
        $serial = Carbon::now()->format('Ymd');
        // $copyPath = 'normal/'.$serial.'/pt/XID-PT-' . $serial .'-' . str_pad($id, 5, "0", STR_PAD_LEFT) . '.docx';
        $copyPath = 'normal/'.$serial.'/mt/' . $reporter->report_mt->original_no . '_' . Carbon::now()->timestamp . '.docx';
        
        if (Storage::disk('export')->put($copyPath, Storage::disk('public_sample')->get($realPath))) {
            // dd(Storage::disk('public_sample'));
            $dirname = dirname($realPath);
            $samplePath = config('filesystems.disks.public_sample.root'). '/' .$dirname . '/' . basename($realPath);
            // $samplePath = public_path('uploads/sample/'.$dirname . '/' . basename($realPath));
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($samplePath);
            $templateProcessor->setValue('Report No', $reporter->report_mt->original_no);
            $templateProcessor->setValue('Customer', $reporter->customer);
            $templateProcessor->setValue('Job Name', $reporter->job_name);
            $templateProcessor->setValue('Exam Date', $reporter->exam_date);
            $templateProcessor->setValue('Exam. Place', $reporter->exam_place);
            if(isset($reporter->report_mt)) {
                $tmpReport = $reporter->report_mt;
                $templateProcessor->setValue('Material', $tmpReport->material);
                $templateProcessor->setValue('Thicknes', $tmpReport->thicknes);
                $templateProcessor->setValue('Region', $tmpReport->region);
                $templateProcessor->setValue('Applicable Code', str_replace($this->previewNotUse, " ", $tmpReport->applicable_code));
                $templateProcessor->setValue('Contract No', $tmpReport->contract_no);
                $templateProcessor->setValue('Criteria', $tmpReport->criteria);
                $templateProcessor->setValue('Procedure No', $tmpReport->procedure_no);

                //檢測時機 exam_time
                $exam_time = "";
                $r_exam_time = explode(',', $tmpReport->exam_time);
                if(is_array($r_exam_time)) {
                    $listExamTime = [
                        '銲接後After Welded',
                        '熱處理前Before HT.',
                        '熱處理後After HT.',
                        '試壓後After Hydro. Test',
                        '其它Other'
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
                $templateProcessor->setValue('Exam. Time', $exam_time);

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
                //白光燈 magnetic_equipment
                $templateProcessor->setValue('White Light Meter', $tmpReport->white_light_meter);
                //試件表面溫度
                $templateProcessor->setValue('Surface Temperature', $tmpReport->surface_temperature);
                //試件表面亮度
                $templateProcessor->setValue('Light Intensity', $tmpReport->light_intensity);
                //舉力測試
                $templateProcessor->setValue('Lifting Test', $tmpReport->lifting_test);
                //磁軛間距
                $templateProcessor->setValue('Yoke Probe Space', $tmpReport->yoke_probe_space);
                //磁場指示計
                $templateProcessor->setValue('Magnetic Field Indicator', $tmpReport->magnetic_field_indicator);
                //磁軛校正日期
                $templateProcessor->setValue('Yoke Calibration Date', $tmpReport->yoke_calibration_date);
                //磁粒
                $templateProcessor->setValue('Magnetic Particles', $tmpReport->magnetic_particles);

                //施加方式 magnetic_equipment
                $applying_method = '';
                if($tmpReport->applying_method == 0) {
                    $applying_method = "■撒 Dusting or Blowing □噴 Spraying";
                } else if($tmpReport->applying_method == 1) {
                    $applying_method = "□撒 Dusting or Blowing ■噴 Spraying";
                }
                $templateProcessor->setValue('Applying Method', $applying_method);

                //磁粒型式
                $particles_manufacturer = "";
                $r_particles_manufacturer = explode(',', $tmpReport->particles_manufacturer);
                if(is_array($r_particles_manufacturer)) {
                    $listTypeOfParticle = [
                        '色比Visible',
                        '螢光Fluorescent',
                        '乾式Dry Type',
                        '濕式Wet Type',
                    ];
                    foreach($listTypeOfParticle as $key => $elem) {
                        if(in_array($key, $r_particles_manufacturer)) {
                            $particles_manufacturer .= "■";
                        } else {
                            $particles_manufacturer .= "□";
                        }
                        $particles_manufacturer .= $elem; 
                    }
                }
                $templateProcessor->setValue('Type of Particle', $particles_manufacturer);
                //磁粒顏色
                $particles_manufacturer = "";
                $r_particles_manufacturer = explode(',', $tmpReport->particles_manufacturer);
                if(is_array($r_particles_manufacturer)) {
                    $listTypeOfColor = [
                        '紅Red',
                        '淺灰Gray',
                        '黃綠Yelloe Green',
                    ];
                    foreach($listTypeOfColor as $key => $elem) {
                        if(in_array($key, $r_particles_manufacturer)) {
                            $particles_manufacturer .= "■";
                        } else {
                            $particles_manufacturer .= "□";
                        }
                        $particles_manufacturer .= $elem; 
                    }
                }
                $templateProcessor->setValue('Type of Color', $particles_manufacturer);

                //磁粒製造廠商
                $particles_manufacturer = "";
                $r_particles_manufacturer = explode(',', $tmpReport->particles_manufacturer);
                if(is_array($r_particles_manufacturer)) {
                    $listParticlesManufacturer = [
                        'Eishin',
                        'Magnaflux',
                    ];
                    foreach($listParticlesManufacturer as $key => $elem) {
                        if(in_array($key, $r_particles_manufacturer)) {
                            $particles_manufacturer .= "■";
                        } else {
                            $particles_manufacturer .= "□";
                        }
                        $particles_manufacturer .= $elem; 
                    }
                }
                $templateProcessor->setValue('Particles Manufacturer', $particles_manufacturer);

                //磁化電流
                $magnetizing_current = "";
                $r_magnetizing_current = explode(',', $tmpReport->magnetizing_current);
                if(is_array($r_magnetizing_current)) {
                    $listMagnetizingCurrent = [
                        '交流 AC 110V',
                        '直流 DC',
                    ];
                    foreach($listMagnetizingCurrent as $key => $elem) {
                        if(in_array($key, $r_magnetizing_current)) {
                            $magnetizing_current .= "■";
                        } else {
                            $magnetizing_current .= "□";
                        }
                        $magnetizing_current .= $elem; 
                    }
                }
                $templateProcessor->setValue('Magnetizing Current', $magnetizing_current);

                //磁化方法
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
                        $magnetizing_methon .= ($elem . " "); 
                    }
                }
                $templateProcessor->setValue('Magnetizing Methon', $magnetizing_methon);

                $examinationResults = true;
                $items = !is_null(json_decode($tmpReport->items))? json_decode($tmpReport->items) : [];
                // dd($tmpReport);
                $tmpThicknessObj = json_decode($tmpReport->thickness_objs? $tmpReport->thickness_objs : '');
                $thickness_objs = !is_null($tmpThicknessObj)? $tmpThicknessObj  : [];
                
                $templateProcessor->cloneBlock('block_detail', sizeof($thickness_objs), true, true);

                $hash = 1;
                $runOnce = true;
                foreach($items as $objs) {
                    $values = [];
                    for($j = 0; $j < 23; $j++) {
                        $itit['No#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->object_no : ($runOnce? '以下空白' : '');
                        $itit['Welder No#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->weld_no : '';
                        $itit['Exam. Length#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->exam_length : '';
                        // $itit['Welder No#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->object_no : '';
                        $itit['Type of Defect#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->type_of_defect: '';
                        $itit['Defect Location#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->defect_location : '';
                        $itit['Defect Length#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->defect_length : '';
                        $itit['Result#'.$hash] = isset($objs[$j]->object_no)? ($objs[$j]->result == 0? '合格' : '不合格') : '';
                        $itit['Duplicate examines#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->duplicate_examines : '';
                        $itit['Remark#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->remark : '';
                        array_push($values, $itit);
                        if($examinationResults && isset($objs[$j])) {
                            $examinationResults = $objs[$j]->result == 0;
                        }
                        if($itit['No#'.$hash] == '以下空白') {
                            $runOnce = false;
                        }
                    }
                    $templateProcessor->cloneRowAndSetValues('No#'.$hash, $values);
                    $hash++;
                }
                // 檢  測  結  果 Examination results
                $examination_results = '';
                if($examinationResults) {
                    $examination_results = "■ 合格         □ 不合格";
                } else {
                    $examination_results = "□ 合格         ■ 不合格";
                }
                $templateProcessor->setValue('Examine Result', $examination_results);
            }

            $templateProcessor->saveAs(Storage::disk('export')->path($copyPath));
            // File::copy($templateProcessor->save(), Storage::disk('export')->path($copyPath));
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
    public function parseReportNormalUT($function_path, $id)
    {
        $out = [];
        $reporter = $this->dispstchRepo->funPathDispatch($function_path, $id);
        
        if(!isset($reporter->report_ut)) {
            return false;
        }
        
        $sample = $this->getNormalUT($reporter->report_ut->report_version);
        
        $report_version_footer = [
            'ASME-SA578' => 'ASME-SA578-02',
            'CNS14136' => 'CNS14136',
            'ASME-SA435' => 'ASME-SA435-02',
            'CNS14135' => 'CNS14135-02',
        ];
        
        $realPath = $sample->full_path;
       
        $serial = Carbon::now()->format('Ymd');
        // $copyPath = 'normal/'.$serial.'/pt/XID-PT-' . $serial .'-' . str_pad($id, 5, "0", STR_PAD_LEFT) . '.docx';
        $copyPath = 'normal/'.$serial.'/ut/' . $reporter->report_ut->original_no . '_' . Carbon::now()->timestamp . '.docx';
        
        if (Storage::disk('export')->put($copyPath, Storage::disk('public_sample')->get($realPath))) {
            // dd(Storage::disk('public_sample'));
            $dirname = dirname($realPath);
            $samplePath = config('filesystems.disks.public_sample.root'). '/' .$dirname . '/' . basename($realPath);
            // $samplePath = public_path('uploads/sample/'.$dirname . '/' . basename($realPath));
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($samplePath);
            $templateProcessor->setValue('Report No', $reporter->report_ut->original_no);
            $templateProcessor->setValue('Customer', $reporter->customer);
            $templateProcessor->setValue('Job Name', $reporter->job_name);
            $templateProcessor->setValue('Exam Date', $reporter->exam_date);
            $templateProcessor->setValue('Exam. Place', $reporter->exam_place);
            $templateProcessor->setValue('Pro Ver. Footer', $report_version_footer[$reporter->report_ut->report_version]);
            if(isset($reporter->report_ut)) {
                $tmpReport = $reporter->report_ut;
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
        return $out;
    }
    public function parseReportNormalRT($function_path, $id)
    {
        $out = [];
        $reporter = $this->dispstchRepo->funPathDispatch($function_path, $id);
        
        if(!isset($reporter->report_rt)) {
            return false;
        }
        
        $sample = $this->getNormalRT($reporter->report_rt->report_version);

        $report_version_footer = [
            'api650' => 'API 650-02',
            'asme02' => 'ASME-02',
            'awsd1' => 'AWS D1.1│D1.1M-02',
            'cns11226' => 'CNS11226-02',
            'cns12619' => 'CNS12619-02',
            'cns13020' => 'CNS13020-02',
            'jis3104' => 'JIS3104-02',
        ];
        
        $realPath = $sample->full_path;
       
        $serial = Carbon::now()->format('Ymd');
        // $copyPath = 'normal/'.$serial.'/pt/XID-PT-' . $serial .'-' . str_pad($id, 5, "0", STR_PAD_LEFT) . '.docx';
        $copyPath = 'normal/'.$serial.'/rt/' . $reporter->report_rt->original_no . '_' . Carbon::now()->timestamp . '.docx';
        
        if (Storage::disk('export')->put($copyPath, Storage::disk('public_sample')->get($realPath))) {
            // dd(Storage::disk('public_sample'));
            $dirname = dirname($realPath);
            $samplePath = config('filesystems.disks.public_sample.root'). '/' .$dirname . '/' . basename($realPath);
            // $samplePath = public_path('uploads/sample/'.$dirname . '/' . basename($realPath));
            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($samplePath);
            $templateProcessor->setValue('Report No', $reporter->report_rt->original_no);
            $templateProcessor->setValue('Customer', $reporter->customer);
            $templateProcessor->setValue('Job Name', $reporter->job_name);
            $templateProcessor->setValue('Exam Date', $reporter->exam_date);
            $templateProcessor->setValue('Exam. Place', $reporter->exam_place);
            $templateProcessor->setValue('Pro Ver. Footer', $report_version_footer[$reporter->report_rt->report_version]);
            if(isset($reporter->report_rt)) {
                $tmpReport = $reporter->report_rt;
                $templateProcessor->setValue('Material', $tmpReport->material);
                $templateProcessor->setValue('Thicknes', $tmpReport->thicknes);
                $templateProcessor->setValue('Region', $tmpReport->region);
                $templateProcessor->setValue('Applicable Code', str_replace($this->previewNotUse, " ", $tmpReport->applicable_code));
                $templateProcessor->setValue('Contract No', $tmpReport->contract_no);
                $templateProcessor->setValue('Criteria', $tmpReport->criteria);
                $templateProcessor->setValue('Procedure No', $tmpReport->procedure_no);

                //檢測時機 exam_time
                $exam_time = "";
                $r_exam_time = explode(',', $tmpReport->exam_time);
                if(is_array($r_exam_time)) {
                    $listExamTime = [
                        '銲接後After Welded',
                        '熱處理前Before HT.',
                        '熱處理後After HT.',
                        '試壓後After Hydro. Test',
                        '其它Other'
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
                $templateProcessor->setValue('Exam. Time', $exam_time);

                $filmBrand = ''; $filmType = '';
                if($tmpReport->film_brand == '1') {
                    $filmBrand = '■D4    □D7';
                    $filmType = '■Class I   □Class II';
                } else if($tmpReport->film_brand == '1.5') {
                    $filmBrand = '■D5    □D7';
                    $filmType = '■Class I   □Class II';
                } else if($tmpReport->film_brand == '3') {
                    $filmBrand = '□D5    ■D7';
                    $filmType = '□Class II   ■Class II';
                }
                $templateProcessor->setValue('Film Brand', $filmBrand);
                $templateProcessor->setValue('Film Type', $filmType);
                $templateProcessor->setValue('Focal Spot', $tmpReport->focal_spot . ' mm');
                $templateProcessor->setValue('Energy/Strength', '■' . $tmpReport->energy_strength);

                //底片大小 Film Size
                $film_size = "";
                $listFilmSize = ['4 1/2 in.×8 1/2 in.','3 1/3 in.× 6 in.'];
                $r_report_size = explode(',', $tmpReport->film_size);
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
                if($tmpReport->film_size == 0) {
                    $film_size = "■ 單 Single         □ 雙 Double";
                } else if($tmpReport->film_size == 1) {
                    $film_size = "□ 單 Single         ■ 雙 Double";
                }
                $templateProcessor->setValue('Film Size', $film_size);

                //照射方法
                $radiographicTech = "";
                if($tmpReport->radiographic_tech == 0) {
                    $radiographicTech = "■ 單層壁 Single Wall Exposure □ 雙層壁 Double Wall Exposure";
                } else if($tmpReport->radiographic_tech == 1) {
                    $radiographicTech = "□ 單層壁 Single Wall Exposure ■ 雙層壁 Double Wall Exposure";
                }
                $templateProcessor->setValue('Radiographic Tech', $radiographicTech);

                $templateProcessor->setValue('sod', $tmpReport->sod);
                $templateProcessor->setValue('G_U', $tmpReport->geometric_unsharpness);

                //底片判讀方式 Interpret
                $interpret = "";
                if($tmpReport->interpret == 0) {
                    $interpret = "■單壁判讀Single View □雙壁判讀Double View";
                    
                } else if($tmpReport->interpret == 1) {
                    $interpret = "□單壁判讀Single View ■雙壁判讀Double View";
                }
                $templateProcessor->setValue('Interpret', $interpret);

                 //像質計位置 iqi_location_mark
                 $iqi_location_mark = "";
                 if($tmpReport->iqi_location_mark == 0) {
                     $iqi_location_mark = "■射源側 Source Side □底片側 Film Side";
                 } else if($tmpReport->iqi_location_mark == 1) {
                     $iqi_location_mark = "□射源側 Source Side ■底片側 Film Side";
                 }
                 $templateProcessor->setValue('IQI/Location Mark', $iqi_location_mark);

                  //像質計編號 IQI  No.
                $iqi_no = "";
                $listIQINo = ['ASTM', '1A', '1B', 'CNS', 'F02', 'F04'];
                $r_iqi_no = explode(',', $tmpReport->iqi_no);
                if(is_array($r_iqi_no)) {
                    for($i = 0; $i < sizeof($listIQINo); $i++) {
                        if(in_array($i, $r_iqi_no)) {
                            $iqi_no .= ('■' . $listIQINo[$i] . " ");
                        } else {
                            $iqi_no .= ('□' . $listIQINo[$i] . " ");
                        }
                        if($listIQINo[$i] == '1B' && ($i + 1) < sizeof($listIQINo)) $iqi_no .= " ";
                    }
                }
                $templateProcessor->setValue('IQI No', $iqi_no);

                $templateProcessor->setValue('Exposure Time', $tmpReport->exposure_time);
                $templateProcessor->setValue('FP_Tmp', '  ' . $tmpReport->film_processing_temp . '  ');
                $templateProcessor->setValue('FP_Time', '  ' . $tmpReport->film_processing_min . '  ');

                $examinationResults = true;
                $items = !is_null(json_decode($tmpReport->items))? json_decode($tmpReport->items) : [];
                // dd($tmpReport);
                $tmpThicknessObj = json_decode($tmpReport->thickness_objs? $tmpReport->thickness_objs : '');
                $thickness_objs = !is_null($tmpThicknessObj)? $tmpThicknessObj  : [];
                
                $templateProcessor->cloneBlock('block_detail', sizeof($thickness_objs), true, true);
                $interpret0 = false;
                $interpret1 = false;
                $interpret2 = false;
                foreach($thickness_objs as $thickness_obj) {
                    $radiographic_tech = $thickness_obj->radiographic_tech;
                    if($radiographic_tech == 1 && $tmpReport->interpret == 1) {
                        $interpret0 = true;
                    } 
                    if($radiographic_tech == 1 && $tmpReport->interpret == 0) {
                        $interpret1 = true;
                    } 
                    if($radiographic_tech == 0 && $tmpReport->interpret == 0) {
                        $interpret2 = true;
                    }
                }
                $templateProcessor->setValue('Ip0', ($interpret0? '■' : '□'));
                $templateProcessor->setValue('Ip1', ($interpret1? '■' : '□'));
                $templateProcessor->setValue('Ip2', ($interpret2? '■' : '□'));

                $hash = 1;
                $runOnce = true;
                foreach($items as $objs) {
                    $values = [];
                    for($j = 0; $j < 26; $j++) {
                        $itit['No#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->object_no : ($runOnce? '以下空白' : '');
                        $itit['FoWNo#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->film_or_weld_no : '';
                        $itit['Thickness#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->thickness : '';
                        $itit['Welder No#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->welder_no : '';
                        $itit['Sensitivity#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->sensitivity: '';
                        $itit['Defect Code#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->defect_code : '';
                        $itit['Acceptable#'.$hash] = isset($objs[$j]->object_no)? ($objs[$j]->result == 0? 'V': '') : '';
                        $itit['Rejected#'.$hash] = isset($objs[$j]->object_no)? ($objs[$j]->result == 1? 'V': '') : '';
                        $itit['Remark#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->remark : '';
                        array_push($values, $itit);
                        if($examinationResults && isset($objs[$j])) {
                            $examinationResults = $objs[$j]->result == 0;
                        }
                        if($itit['No#'.$hash] == '以下空白') {
                            $runOnce = false;
                        }
                    }
                    $templateProcessor->cloneRowAndSetValues('No#'.$hash, $values);
                    $hash++;
                }
                // 檢  測  結  果 Examination results
                $examination_results = '';
                if($examinationResults) {
                    $examination_results = "■ 合格         □ 不合格";
                } else {
                    $examination_results = "□ 合格         ■ 不合格";
                }
                $templateProcessor->setValue('Examine Result', $examination_results);
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
        return $out;
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
        
        $report_version_footer = [
            'asme02' => 'ASME-02',
            'awsd1' => 'AWS D1.1│D1.1M-02',
            'cns11398' => 'CNS11398-02',
            'cns13464' => 'CNS13464-02',
        ];

        $realPath = $sample->full_path;
       
        $serial = Carbon::now()->format('Ymd');
        // $copyPath = 'normal/'.$serial.'/pt/XID-PT-' . $serial .'-' . str_pad($id, 5, "0", STR_PAD_LEFT) . '.docx';
        $copyPath = 'normal/'.$serial.'/pt/' . $reporter->report_pt->original_no . '_' . Carbon::now()->timestamp . '.docx';
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
            $templateProcessor->setValue('Pro Ver. Footer', $report_version_footer[$reporter->report_pt->report_version]);
            if(isset($reporter->report_pt)) {
                $tmpReport = $reporter->report_pt;
                $templateProcessor->setValue('Material', $tmpReport->material);
                $templateProcessor->setValue('Thicknes', $tmpReport->thicknes);
                $templateProcessor->setValue('Region', $tmpReport->region);
                $templateProcessor->setValue('Applicable Code', str_replace($this->previewNotUse, " ", $tmpReport->applicable_code));
                $templateProcessor->setValue('Contract No', $tmpReport->contract_no);
                $templateProcessor->setValue('Criteria', $tmpReport->criteria);
                $templateProcessor->setValue('Procedure No', $tmpReport->procedure_no);
                $templateProcessor->setValue('Light Intensity', $tmpReport->light_intensity);
                // $templateProcessor->setValue('Surface Temp', $tmpReport->surface_temp);
                
                //滲透時間
                $templateProcessor->setValue('Dwell Time', $tmpReport->dwell_time);
                //檢測時表面溫度 Surface Temperature
                $templateProcessor->setValue('Surface Temp', $tmpReport->surface_temperature);
                //施加顯像劑 Developing
                $templateProcessor->setValue('Developing', $tmpReport->developing);
                //顯像時間 Developing Time
                $templateProcessor->setValue('D Ti', $tmpReport->developing_time);
                //乾燥時間 Drying Time
                $templateProcessor->setValue('Drying Time', $tmpReport->developing_time);
                //清洗劑 Cleaner
                $templateProcessor->setValue('Cleaner', $tmpReport->cleaner);
                //滲透劑 Penetrant
                $templateProcessor->setValue('Penetrant', $tmpReport->penetrant);
                //顯像劑 Developer
                $templateProcessor->setValue('Developer', $tmpReport->developer);

                //檢測時機 exam_time
                $exam_time = "";
                $r_exam_time = explode(',', $tmpReport->exam_time);
                if(is_array($r_exam_time)) {
                    $listExamTime = [
                        '銲接後After Welded',
                        '熱處理前Before HT.',
                        '熱處理後After HT.',
                        '試壓後After Hydro. Test',
                        '其它Other'
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
                $templateProcessor->setValue('Exam. Time', $exam_time);
                
                //施加滲透劑 Penetrant Application
                $penetrant_application = '';
                if($tmpReport->penetrant_application == 0) {
                    $penetrant_application = "■噴霧Spraying □刷塗Brushing";
                } else if($tmpReport->penetrant_application == 1) {
                    $penetrant_application = "□噴霧Spraying ■刷塗Brushing";
                }
                $templateProcessor->setValue('Penetrant Application', $penetrant_application);

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

                //檢測方法
                $examination_method = "";
                $r_examination_method = explode(',', $tmpReport->examination_method);
                if(is_array($r_examination_method)) {
                    $listTypeOfParticle = [
                        '色比Visible',
                        '螢光Fluorescent',
                        // '乾式Dry Type',
                        // '濕式Wet Type',
                    ];
                    foreach($listTypeOfParticle as $key => $elem) {
                        if(in_array($key, $r_examination_method)) {
                            $examination_method .= "■";
                        } else {
                            $examination_method .= "□";
                        }
                        $examination_method .= $elem; 
                    }
                }
                $templateProcessor->setValue('Examination Method1', $examination_method);
                
                //檢測方法2
                $examination_method = "";
                $r_examination_method = explode(',', $tmpReport->examination_method);
                if(is_array($r_examination_method)) {
                    $listTypeOfParticle = [
                        '溶劑去除Solvent Removable',
                        '水	洗    Water Washable',
                    ];
                    foreach($listTypeOfParticle as $key => $elem) {
                        if(in_array($key, $r_examination_method)) {
                            $examination_method .= "■";
                        } else {
                            $examination_method .= "□";
                        }
                        $examination_method .= $elem; 
                    }
                }
                $templateProcessor->setValue('Examination Method2', $examination_method);
                
                //滲透劑去除 Removal
                $removal = "";
                $r_removal = explode(',', $tmpReport->removal);
                if(is_array($r_removal)) {
                    $listTypeOfParticle = [
                        '清潔布Dry Cloths',
                        '擦拭紙Absorbent Paper',
                        // '乾式Dry Type',
                        // '濕式Wet Type',
                    ];
                    foreach($listTypeOfParticle as $key => $elem) {
                        if(in_array($key, $r_removal)) {
                            $removal .= "■";
                        } else {
                            $removal .= "□";
                        }
                        $removal .= $elem; 
                    }
                }
                $templateProcessor->setValue('Removal', $removal);
                
                //後清理
                $post_clean = "";
                $r_post_clean = explode(',', $tmpReport->post_clean);
                if(is_array($r_post_clean)) {
                    $listTypeOfParticle = [
                        '溶劑Solvent',
                        '水洗Water',
                        '其他Other',
                    ];
                    foreach($listTypeOfParticle as $key => $elem) {
                        if(in_array($key, $r_post_clean)) {
                            $post_clean .= "■";
                        } else {
                            $post_clean .= "□";
                        }
                        $post_clean .= $elem; 
                    }
                }
                $templateProcessor->setValue('Post Clean', $post_clean);
                
                $items = !is_null(json_decode($tmpReport->items))? json_decode($tmpReport->items) : [];
                // dd($tmpReport);
                $tmpThicknessObj = json_decode($tmpReport->thickness_objs? $tmpReport->thickness_objs : '');
                $thickness_objs = !is_null($tmpThicknessObj)? $tmpThicknessObj  : [];
                
                $templateProcessor->cloneBlock('block_detail', sizeof($thickness_objs), true, true);
                
                $hash = 1;
                $runOnce = true;
                $examinationResults = true;
                foreach($items as $objs) {
                    $values = [];
                    for($j = 0; $j < 26; $j++) {
                        $itit['No#'.$hash] = ($j + 1) . ' ';
                        $itit['Object No#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->object_no : ($runOnce? '以下空白' : '');
                        $itit['Weld Length#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->weld_length : '';
                        $itit['Defect Location#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->defect_location : '';
                        $itit['Type of Defect#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->type_of_defect : '';
                        $itit['Defect Length#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->defect_length : '';
                        $itit['Result#'.$hash] = isset($objs[$j]->object_no)? ($objs[$j]->result == 0? '合格' : '不合格') : '';
                        $itit['Remark#'.$hash] = isset($objs[$j]->object_no)? $objs[$j]->remark : '';

                        if($itit['No#'.$hash] == '以下空白') {
                            $runOnce = false;
                        }

                        array_push($values, $itit);
                        if($examinationResults && isset($objs[$j])) {
                            $examinationResults = $objs[$j]->result == 0;
                        }
                    }
                    $templateProcessor->cloneRowAndSetValues('No#'.$hash, $values);
                    $hash++;
                }
                // 檢  測  結  果 Examination results
                $examination_results = '';
                if($examinationResults) {
                    $examination_results = "■ 合格         □ 不合格";
                } else {
                    $examination_results = "□ 合格         ■ 不合格";
                }
                $templateProcessor->setValue('Examine Result', $examination_results);
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
        return $out;
    }
}