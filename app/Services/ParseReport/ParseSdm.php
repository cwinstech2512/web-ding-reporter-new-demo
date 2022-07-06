<?php
namespace App\Services\ParseReport;

use Illuminate\Support\Facades\Log;
use App\Repositories\{SampleLogRepository};
use App\Repositories\DispatchRepository;
use Illuminate\Support\Facades\Storage;
use App\Services\ExportConfig\ExcelConfig;
use Carbon\Carbon;
use File;

class ParseSdm
{
    private $spRepo = null;
    private $dispstchRepo = null;

    public function __construct(SampleLogRepository $spRepo, DispatchRepository $dispstchRepo)
	{
		$this->spRepo = $spRepo;
        $this->dispstchRepo = $dispstchRepo;
	}

    public function getSdmMT($version = '')
	{
		return $this->spRepo->lately('sdm.mt.' . $version);
	}
    public function getSdmVT($version = '')
	{
		return $this->spRepo->lately('sdm.vt.' . $version);
	}
    public function getSdmUT($version = '')
	{
		return $this->spRepo->lately('sdm.ut.' . $version);
	}

    public function parseReportSdmVT($function_path, $id)
    {
        $out = [];
        $reporter = $this->dispstchRepo->funPathDispatch($function_path, $id);
        
        if(!isset($reporter->report_vt)) {
            return false;
        }
        $reportVersion = empty($reporter->report_version)? $reporter->report_version : 'WanChi';
        $sample = $this->getSdmVT('2022.' . $reportVersion);

        $realPath = $sample->full_path;
        $serial = Carbon::now()->format('Ymd');
        $copyPath = 'sdm/'.$serial.'/vt/' . $reporter->report_vt->original_no . '_' . Carbon::now()->timestamp . '.xlsx';
        if(is_null($reporter)){
            $out['code'] = 50001;
            $out['message'] = "資料更新錯誤";
            return $out;
        }
        if (Storage::disk('export')->put($copyPath, Storage::disk('public_sample')->get($realPath))) {
            // dd($reporter);
            $dirname = dirname($realPath);
            $samplePath = config('filesystems.disks.public_sample.root'). '/' .$dirname . '/' . basename($realPath);
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($samplePath);
            // dd($spreadsheet->getSheet(1));
            $spreadsheet_0 = $spreadsheet->getSheet(0);
            // 工程名稱/案號:		
            $spreadsheet_0->setCellValue('D7', $reporter->job_name . ' / ' . $reporter->job_no);
            // 檢測日期
            $spreadsheet_0->setCellValue('D9', $reporter->exam_date);
            // 檢測日期
            $spreadsheet_0->setCellValue('N1', '報告編號:' . $reporter->report_vt->original_no);
            // 工件名稱/編號
            $spreadsheet_0->setCellValue('B6', $reporter->equlpment_no);
            // 版次
            $spreadsheet_0->setCellValue('I11', $reporter->procedure_version);
            // $spreadsheet_0->getStyle('A')->applyFromArray(ExcelConfig::sdmSheetStyle());
             /* report_vt */
            if(isset($reporter->report_vt)) {
                $tmpReport = $reporter->report_vt;
                //檢測時機:	 Inspection Time
                $listExamTime= ['銲接後 After Welded(>48hrs)','軋延後 After Rolling','鍛造後 After Forging','其他 Other'
                ,'銲接後 After Welded(< 48hrs)','熱處理前 Before H.T','熱處理後 After H.T','維修檢測 In-service'];
                $listRowExamTime = ['D', 'H', 'K', 'N'];
                $r_exam_time = explode(',', $tmpReport->exam_time);
                if(is_array($r_exam_time)) {
                    for($i = 0; $i < sizeof($listExamTime); $i++) {
                        if(in_array($i, $r_exam_time)) {
                            if($i < 4)
                            $spreadsheet_0->setCellValue($listRowExamTime[$i] . '15', '▓' . $listExamTime[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowExamTime[$i%4] . '16', '▓' . $listExamTime[$i]);
                        } else {
                            if($i < 4)
                            $spreadsheet_0->setCellValue($listRowExamTime[$i] . '15', '□' . $listExamTime[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowExamTime[$i%4] . '16', '□' . $listExamTime[$i]);
                        }
                    }
                }

                //表面狀況:	 Surface Condition
                $listSurfaceStatus= ['銲接面 As Welded','軋延面 As Rolled','鍛造面 As Forged'
                ,'加工面 As Machining','研磨面 As Grinded','其他 Other'];
                $listRowSurfaceStatus = ['D', 'G', 'J'];
                $r_surface_status = explode(',', $tmpReport->surface_status);
                if(is_array($r_surface_status)) {
                    for($i = 0; $i < sizeof($listSurfaceStatus); $i++) {
                        if(in_array($i, $r_surface_status)) {
                            if($i < sizeof($listRowSurfaceStatus))
                            $spreadsheet_0->setCellValue($listRowSurfaceStatus[$i] . '17', '▓' . $listSurfaceStatus[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowSurfaceStatus[$i%sizeof($listRowSurfaceStatus)] . '18', '▓' . $listSurfaceStatus[$i]);
                        } else {
                            if($i < sizeof($listRowSurfaceStatus))
                            $spreadsheet_0->setCellValue($listRowSurfaceStatus[$i] . '17', '□' . $listSurfaceStatus[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowSurfaceStatus[$i%sizeof($listRowSurfaceStatus)] . '18', '□' . $listSurfaceStatus[$i]);
                        }
                    }
                }

                //照 度 計		    lumin_meter
                $r_lumin_meter = $tmpReport->lumin_meter != 'N/A'? explode('/', $tmpReport->lumin_meter) : '';
                $listRowLuminMeter = ['D', 'G', 'J'];
                if(is_array($r_lumin_meter)) {
                    for($i = 0; $i < sizeof($r_lumin_meter); $i++) {
                        $spreadsheet_0->setCellValue($listRowLuminMeter[$i] . '21', $r_lumin_meter[$i]);
                    }
                } else {
                    $spreadsheet_0->setCellValue($listRowLuminMeter[0] . '21', $tmpReport->lumin_meter);
                }

                //銲道規		    weld_gage
                $r_weld_gage = $tmpReport->weld_gage != 'N/A'? explode('/', $tmpReport->weld_gage) : '';
                $listRowWeldGage = ['D', 'G', 'J'];
                if(is_array($r_weld_gage)) {
                    for($i = 0; $i < sizeof($r_weld_gage); $i++) {
                        $spreadsheet_0->setCellValue($listRowWeldGage[$i] . '23', $r_weld_gage[$i]);
                    }
                } else {
                    $spreadsheet_0->setCellValue($listRowWeldGage[0] . '23', $tmpReport->weld_gage);
                }

                //其它		    Others
                $r_Others = $tmpReport->Others != 'N/A'? explode('/', $tmpReport->Others) : '';
                $listRowOthers = ['D', 'G', 'J'];
                if(is_array($r_Others)) {
                    for($i = 0; $i < sizeof($r_Others); $i++) {
                        $spreadsheet_0->setCellValue($listRowOthers[$i] . '25', $r_Others[$i]);
                    }
                } else {
                    $spreadsheet_0->setCellValue($listRowOthers[0] . '25', $tmpReport->Others);
                }
                // 檢測區照度:	
                $spreadsheet_0->setCellValue('O19', $tmpReport->light_intensity);

                //檢測方式:
                $listTestingMethod= ['直接目視法 Direct VT','間接目視法 Remote VT'
                ,'透視法 Translucent VT','其他 Other'];
                $listRowTestingMethod = ['M', 'P'];
                $r_testing_method = explode(',', $tmpReport->testing_method);
                if(is_array($r_testing_method)) {
                    for($i = 0; $i < sizeof($listTestingMethod); $i++) {
                        if(in_array($i, $r_testing_method)) {
                            if($i < sizeof($listRowTestingMethod))
                            $spreadsheet_0->setCellValue($listRowTestingMethod[$i] . '22', '▓' . $listTestingMethod[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowTestingMethod[$i%sizeof($listRowTestingMethod)] . '23', '▓' . $listTestingMethod[$i]);
                        } else {
                            if($i < sizeof($listRowTestingMethod))
                            $spreadsheet_0->setCellValue($listRowTestingMethod[$i] . '22', '□' . $listTestingMethod[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowTestingMethod[$i%sizeof($listRowTestingMethod)] . '23', '□' . $listTestingMethod[$i]);
                        }
                    }
                }
                //接頭形式	 joint_type
                $listJointType= ['X','V',
                'K', 'TYK joint(NODE)',
                'Other'];
                $listRowJointType = ['K', 'L', 'M', 'N', 'P'];
                $r_joint_type = explode(',', $tmpReport->joint_type);
                if(is_array($r_joint_type)) {
                    for($i = 0; $i < sizeof($listRowJointType); $i++) {
                        if(in_array($i, $r_joint_type)) {
                            $spreadsheet_0->setCellValue($listRowJointType[$i] . '27', '▓' . $listJointType[$i]);
                        } else {
                            $spreadsheet_0->setCellValue($listRowJointType[$i] . '27', '□' . $listJointType[$i]);
                        }
                    }
                }

                $items = json_decode($tmpReport->items);
                // $sheet1CellNames = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U'];
                if(is_array($items)) {
                    $i = 0; $index = 32;
                    $runOnce = true;
                    foreach($items as $objs) {
                        for($i = 0; $i < sizeof($objs)+1; $i++) {
                            // 流水號	
                            $serial_no = (string)isset($objs[$i]->serial_no)? $objs[$i]->serial_no : ($runOnce? '以下空白' : '');
                            $spreadsheet_0->setCellValue('A' . ($index+$i), $serial_no);
                            // 銲道編號		
                            $spreadsheet_0->setCellValue('C' . ($index+$i), (string)isset($objs[$i]->weld_no)? $objs[$i]->weld_no : '');
                            // 材質/厚度			
                            $spreadsheet_0->setCellValue('E' . ($index+$i), (string)isset($objs[$i]->material_thickness)? $objs[$i]->material_thickness : '');
                            // 圖號				
                            $spreadsheet_0->setCellValue('G' . ($index+$i), (string)isset($objs[$i]->drawing_no)? $objs[$i]->drawing_no : '');
                            // "檢驗長度"				
                            $spreadsheet_0->setCellValue('I' . ($index+$i), (string)isset($objs[$i]->inspection_length)? $objs[$i]->inspection_length : '');
                            // // "缺陷編號"				
                            // $spreadsheet_0->setCellValue('J' . ($index+$i), (string)isset($objs[$i]->defect_no)? $objs[$i]->defect_no : '');
                            // // 缺陷尺寸				
                            // $spreadsheet_0->setCellValue('K' . ($index+$i), (string)isset($objs[$i]->exam_length)? $objs[$i]->exam_length : '');
                            // // 缺陷位置				
                            // $spreadsheet_0->setCellValue('L' . ($index+$i), (string)isset($objs[$i]->defect_location[0])? 'V' : '');
                            // // 缺陷位置				
                            // $spreadsheet_0->setCellValue('M' . ($index+$i), (string)isset($objs[$i]->defect_location[1])? 'V' : '');
                            // 缺陷類型				
                            $spreadsheet_0->setCellValue('M' . ($index+$i), (string)isset($objs[$i]->type_of_defect)? $objs[$i]->type_of_defect : '');
                            // 檢驗日期時間				
                            $spreadsheet_0->setCellValue('K' . ($index+$i), (string)isset($objs[$i]->exam_datetime)? $objs[$i]->exam_datetime : '');
                            // 結果				
                            $spreadsheet_0->setCellValue('O' . ($index+$i), (string)isset($objs[$i]->result)? ($objs[$i]->result == 0? 'Acc' : 'Reg') : '');
                            // 備註				
                            $spreadsheet_0->setCellValue('Q' . ($index+$i), (string)isset($objs[$i]->remark)? $objs[$i]->remark : '');
                            
                            if($runOnce && $serial_no == '以下空白') {
                                $runOnce = false;
                            }
                        }
                    }
                }
            }
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(Storage::disk('export')->path($copyPath));

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

    public function parseReportSdmUT($function_path, $id)
    {
        $out = [];
        $reporter = $this->dispstchRepo->funPathDispatch($function_path, $id);
        
        if(!isset($reporter->report_ut)) {
            return false;
        }
        $reportVersion = empty($reporter->report_version)? $reporter->report_version : 'WanChi';
        $sample = $this->getSdmUT('2022.'.$reportVersion);

        $realPath = $sample->full_path;
        $serial = Carbon::now()->format('Ymd');
        $copyPath = 'sdm/'.$serial.'/ut/' . $reporter->report_ut->original_no . '_' . Carbon::now()->timestamp . '.xlsx';
        if(is_null($reporter)){
            $out['code'] = 50001;
            $out['message'] = "資料更新錯誤";
            return $out;
        }
        if (Storage::disk('export')->put($copyPath, Storage::disk('public_sample')->get($realPath))) {
            // dd($reporter);
            $dirname = dirname($realPath);
            $samplePath = config('filesystems.disks.public_sample.root'). '/' .$dirname . '/' . basename($realPath);
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($samplePath);
            // dd($spreadsheet->getSheet(1));
            $spreadsheet_0 = $spreadsheet->getSheet(0);
            // 工程名稱/案號:		
            $spreadsheet_0->setCellValue('D7', $reporter->job_name . ' / ' . $reporter->job_no);
            // 檢測日期
            $spreadsheet_0->setCellValue('D9', $reporter->exam_date);
            // 檢測日期
            $spreadsheet_0->setCellValue('N1', '報告編號:' . $reporter->report_ut->original_no);
            // 工件名稱/編號
            $spreadsheet_0->setCellValue('B6', $reporter->equlpment_no);
            // 版次
            $spreadsheet_0->setCellValue('I11', $reporter->procedure_version);
            // $spreadsheet_0->getStyle('A')->applyFromArray(ExcelConfig::sdmSheetStyle());
             /* report_ut */
            if(isset($reporter->report_ut)) {
                $tmpReport = $reporter->report_ut;
                //檢測時機:	 Inspection Time
                $listExamTime= ['銲接後 After Welded(>48hrs)','軋延後 After Rolling','鍛造後 After Forging','其他 Other'
                ,'銲接後 After Welded(< 48hrs)','熱處理前 Before H.T','熱處理後 After H.T','維修檢測 In-service'];
                $listRowExamTime = ['D', 'H', 'K', 'N'];
                $r_exam_time = explode(',', $tmpReport->exam_time);
                if(is_array($r_exam_time)) {
                    for($i = 0; $i < sizeof($listExamTime); $i++) {
                        if(in_array($i, $r_exam_time)) {
                            if($i < 4)
                            $spreadsheet_0->setCellValue($listRowExamTime[$i] . '15', '▓' . $listExamTime[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowExamTime[$i%4] . '16', '▓' . $listExamTime[$i]);
                        } else {
                            if($i < 4)
                            $spreadsheet_0->setCellValue($listRowExamTime[$i] . '15', '□' . $listExamTime[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowExamTime[$i%4] . '16', '□' . $listExamTime[$i]);
                        }
                    }
                }

                //表面狀況:	 Surface Condition
                $listSurfaceStatus= ['銲接面 As Welded','軋延面 As Rolled','鍛造面 As Forged'
                ,'加工面 As Machining','研磨面 As Grinded','其他 Other'];
                $listRowSurfaceStatus = ['D', 'G', 'J'];
                $r_surface_status = explode(',', $tmpReport->surface_status);
                if(is_array($r_surface_status)) {
                    for($i = 0; $i < sizeof($listSurfaceStatus); $i++) {
                        if(in_array($i, $r_surface_status)) {
                            if($i < sizeof($listRowSurfaceStatus))
                            $spreadsheet_0->setCellValue($listRowSurfaceStatus[$i] . '17', '▓' . $listSurfaceStatus[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowSurfaceStatus[$i%sizeof($listRowSurfaceStatus)] . '18', '▓' . $listSurfaceStatus[$i]);
                        } else {
                            if($i < sizeof($listRowSurfaceStatus))
                            $spreadsheet_0->setCellValue($listRowSurfaceStatus[$i] . '17', '□' . $listSurfaceStatus[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowSurfaceStatus[$i%sizeof($listRowSurfaceStatus)] . '18', '□' . $listSurfaceStatus[$i]);
                        }
                    }
                }

                //探傷儀		    UT Equipment
                $r_ut_equipment = $tmpReport->ut_equipment != 'N/A'? explode('/', $tmpReport->ut_equipment) : '';
                $listRowUtEquipment = ['D', 'F', 'H'];
                if(is_array($r_ut_equipment)) {
                    for($i = 0; $i < sizeof($r_ut_equipment); $i++) {
                        $spreadsheet_0->setCellValue($listRowUtEquipment[$i] . '21', $r_ut_equipment[$i]);
                    }
                } else {
                    $spreadsheet_0->setCellValue($listRowUtEquipment[0] . '21', $tmpReport->ut_equipment);
                }

                //其它		    Others
                $r_Others = $tmpReport->Others != 'N/A'? explode('/', $tmpReport->Others) : '';
                $listRowOthers = ['D', 'F', 'H'];
                if(is_array($r_Others)) {
                    for($i = 0; $i < sizeof($r_Others); $i++) {
                        $spreadsheet_0->setCellValue($listRowOthers[$i] . '23', $r_Others[$i]);
                    }
                } else {
                    $spreadsheet_0->setCellValue($listRowOthers[0] . '23', $tmpReport->Others);
                }

                //底片大小 Film Size
                $coupling = "";
                $listCoupling = ['CMC','Water','Machine Oil'];
                $r_coupling = explode(',', $tmpReport->coupling);
                if(is_array($r_coupling)) {
                    for($i = 0; $i < sizeof($listCoupling); $i++) {
                        if(in_array($i, $r_coupling)) {
                            // if($i == 0)
                            $coupling .= ('▓' . $listCoupling[$i] . " ");
                        } else {
                            // if($i == 0)
                            $coupling .= ('□' . $listCoupling[$i] . " ");
                        }
                    }
                }
                $spreadsheet_0->setCellValue('D25', $coupling);

                // 檢測區照度:	
                $spreadsheet_0->setCellValue('O19', $tmpReport->light_intensity);
                // 參考規塊No:	
                $spreadsheet_0->setCellValue('P26', $tmpReport->reference_serial_no);

                //參考規塊	 reference_block
                $listReferenceBlock= ['T40-L','T75-L',
                'T40-T', 'T75-T'];
                $listRowReferenceBlock = ['L', 'N'];
                $r_reference_block = explode(',', $tmpReport->reference_block);
                if(is_array($r_reference_block)) {
                    for($i = 0; $i < sizeof($listRowReferenceBlock); $i++) {
                        if(in_array($i, $r_reference_block)) {
                            if($i < sizeof($listRowReferenceBlock))
                            $spreadsheet_0->setCellValue($listRowReferenceBlock[$i] . '25', '▓' . $listReferenceBlock[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowReferenceBlock[$i] . '26', '▓' . $listReferenceBlock[$i]);
                        } else {
                            if($i < sizeof($listRowReferenceBlock))
                            $spreadsheet_0->setCellValue($listRowReferenceBlock[$i] . '25', '□' . $listReferenceBlock[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowReferenceBlock[$i] . '26', '□' . $listReferenceBlock[$i]);
                        }
                    }
                }

                //檢測方式:
                $listTestingMethod= ['直接接觸法  Direct Contact','局部水浸法 Water Box'];
                $listRowTestingMethod = ['L'];
                $r_testing_method = explode(',', $tmpReport->testing_method);
                if(is_array($r_testing_method)) {
                    for($i = 0; $i < sizeof($listTestingMethod); $i++) {
                        if(in_array($i, $r_testing_method)) {
                            if($i < sizeof($listRowTestingMethod))
                            $spreadsheet_0->setCellValue($listRowTestingMethod[$i] . '21', '▓' . $listTestingMethod[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowTestingMethod[$i%sizeof($listRowTestingMethod)] . '22', '▓' . $listTestingMethod[$i]);
                        } else {
                            if($i < sizeof($listRowTestingMethod))
                            $spreadsheet_0->setCellValue($listRowTestingMethod[$i] . '21', '□' . $listTestingMethod[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowTestingMethod[$i%sizeof($listRowTestingMethod)] . '22', '□' . $listTestingMethod[$i]);
                        }
                    }
                }

                //接頭形式	 joint_type
                $listJointType= ['X','V',
                'K', 'TYK joint(NODE)',
                'Other'];
                $listRowJointType = ['M', 'N', 'O', 'P', 'R'];
                $r_joint_type = explode(',', $tmpReport->joint_type);
                if(is_array($r_joint_type)) {
                    for($i = 0; $i < sizeof($listRowJointType); $i++) {
                        if(in_array($i, $r_joint_type)) {
                            $spreadsheet_0->setCellValue($listRowJointType[$i] . '39', '▓' . $listJointType[$i]);
                        } else {
                            $spreadsheet_0->setCellValue($listRowJointType[$i] . '39', '□' . $listJointType[$i]);
                        }
                    }
                }

                $items = json_decode($tmpReport->items);
                $sheet1CellNames = ['E', 'G', 'I', 'K', 'M', 'O', 'Q'];
                if(is_array($items)) {
                    $i = 0; $index = 32;
                    foreach($items as $objs) {
                        for($i = 0; $i < sizeof($objs); $i++) {
                            $spreadsheet_0->setCellValue($sheet1CellNames[$i] . '28', (string)isset($objs[$i]->prob_serial_no)? $objs[$i]->prob_serial_no : '');
                            $spreadsheet_0->setCellValue($sheet1CellNames[$i] . '29', (string)isset($objs[$i]->probe_angle)? $objs[$i]->probe_angle : '');
                            $spreadsheet_0->setCellValue($sheet1CellNames[$i] . '30', (string)isset($objs[$i]->probe_freq)? $objs[$i]->probe_freq : '');
                            $spreadsheet_0->setCellValue($sheet1CellNames[$i] . '31', (string)isset($objs[$i]->probe_size)? $objs[$i]->probe_size : '');
                            $spreadsheet_0->setCellValue($sheet1CellNames[$i] . '32', (string)isset($objs[$i]->correction_angle)? $objs[$i]->correction_angle : '');
                            $spreadsheet_0->setCellValue($sheet1CellNames[$i] . '33', (string)isset($objs[$i]->sensibility)? $objs[$i]->sensibility : '');
                            $spreadsheet_0->setCellValue($sheet1CellNames[$i] . '34', (string)isset($objs[$i]->transfer_correction)? $objs[$i]->transfer_correction : '');
                            $spreadsheet_0->setCellValue($sheet1CellNames[$i] . '35', (string)isset($objs[$i]->time_base_range)? $objs[$i]->time_base_range : '');
                            $spreadsheet_0->setCellValue($sheet1CellNames[$i] . '36', (string)isset($objs[$i]->probe_position)? $objs[$i]->probe_position : '');
                        }
                    }
                }
            }
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(Storage::disk('export')->path($copyPath));

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

    public function parseReportSdmMT($function_path, $id)
    {
        $out = [];
        $reporter = $this->dispstchRepo->funPathDispatch($function_path, $id);
        
        if(!isset($reporter->report_mt)) {
            return false;
        }
        $reportVersion = empty($reporter->report_version)? $reporter->report_version : 'WanChi';
        $sample = $this->getSdmMT('2022.' . $reportVersion);

        $realPath = $sample->full_path;
        $serial = Carbon::now()->format('Ymd');
        $copyPath = 'sdm/'.$serial.'/mt/' . $reporter->report_mt->original_no . '_' . Carbon::now()->timestamp . '.xlsx';
        if(is_null($reporter)){
            $out['code'] = 50001;
            $out['message'] = "資料更新錯誤";
            return $out;
        }
        if (Storage::disk('export')->put($copyPath, Storage::disk('public_sample')->get($realPath))) {
            // dd($reporter);
            $dirname = dirname($realPath);
            $samplePath = config('filesystems.disks.public_sample.root'). '/' .$dirname . '/' . basename($realPath);
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($samplePath);
            // dd($spreadsheet->getSheet(1));
            $spreadsheet_0 = $spreadsheet->getSheet(0);
            // 工程名稱/案號:		
            $spreadsheet_0->setCellValue('D7', $reporter->job_name . ' / ' . $reporter->job_no);
            // 檢測日期
            $spreadsheet_0->setCellValue('D9', $reporter->exam_date);
            // 檢測日期
            $spreadsheet_0->setCellValue('N1', '報告編號:' . $reporter->report_mt->original_no);
            // 工件名稱/編號
            $spreadsheet_0->setCellValue('B6', $reporter->equlpment_no);
            // 版次
            $spreadsheet_0->setCellValue('I11', $reporter->procedure_version);
            // $spreadsheet_0->getStyle('A')->applyFromArray(ExcelConfig::sdmSheetStyle());
             /* report_rt */
            if(isset($reporter->report_mt)) {
                $tmpReport = $reporter->report_mt;

                //檢測時機:	 Inspection Time
                $listExamTime= ['銲接後 After Welded(>48hrs)','軋延後 After Rolling','鍛造後 After Forging','其他 Other'
                ,'銲接後 After Welded(< 48hrs)','熱處理前 Before H.T','熱處理後 After H.T','維修檢測 In-service'];
                $listRowExamTime = ['D', 'H', 'K', 'N'];
                $r_exam_time = explode(',', $tmpReport->exam_time);
                if(is_array($r_exam_time)) {
                    for($i = 0; $i < sizeof($listExamTime); $i++) {
                        if(in_array($i, $r_exam_time)) {
                            if($i < 4)
                            $spreadsheet_0->setCellValue($listRowExamTime[$i] . '15', '▓' . $listExamTime[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowExamTime[$i%4] . '16', '▓' . $listExamTime[$i]);
                        } else {
                            if($i < 4)
                            $spreadsheet_0->setCellValue($listRowExamTime[$i] . '15', '□' . $listExamTime[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowExamTime[$i%4] . '16', '□' . $listExamTime[$i]);
                        }
                    }
                }

                //表面狀況:	 Surface Condition
                $listSurfaceStatus= ['銲接面 As Welded','軋延面 As Rolled','鍛造面 As Forged'
                ,'加工面 As Machining','研磨面 As Grinded','其他 Other'];
                $listRowSurfaceStatus = ['D', 'G', 'J'];
                $r_surface_status = explode(',', $tmpReport->surface_status);
                if(is_array($r_surface_status)) {
                    for($i = 0; $i < sizeof($listSurfaceStatus); $i++) {
                        if(in_array($i, $r_surface_status)) {
                            if($i < sizeof($listRowSurfaceStatus))
                            $spreadsheet_0->setCellValue($listRowSurfaceStatus[$i] . '17', '▓' . $listSurfaceStatus[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowSurfaceStatus[$i%sizeof($listRowSurfaceStatus)] . '18', '▓' . $listSurfaceStatus[$i]);
                        } else {
                            if($i < sizeof($listRowSurfaceStatus))
                            $spreadsheet_0->setCellValue($listRowSurfaceStatus[$i] . '17', '□' . $listSurfaceStatus[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowSurfaceStatus[$i%sizeof($listRowSurfaceStatus)] . '18', '□' . $listSurfaceStatus[$i]);
                        }
                    }
                }
                // 磁 化 設 備		 magnetic_equipment
                $r_magnetic_equipment = $tmpReport->magnetic_equipment != 'N/A'? explode('/', $tmpReport->magnetic_equipment) : '';
                $listRowMagneticEquipment = ['D', 'F', 'H'];
                if(is_array($r_magnetic_equipment)) {
                    for($i = 0; $i < sizeof($r_magnetic_equipment); $i++) {
                        $spreadsheet_0->setCellValue($listRowMagneticEquipment[$i] . '21', $r_magnetic_equipment[$i]);
                    }
                } else {
                    $spreadsheet_0->setCellValue($listRowMagneticEquipment[0] . '21', $tmpReport->magnetic_equipment);
                }
                //對 比 漆		background_paint
                $r_background_paint = $tmpReport->background_paint != 'N/A'? explode('/', $tmpReport->background_paint) : '';
                $listRowBackgroundPaint = ['D', 'F', 'H'];
                if(is_array($r_background_paint)) {
                    for($i = 0; $i < sizeof($r_background_paint); $i++) {
                        $spreadsheet_0->setCellValue($listRowBackgroundPaint[$i] . '23', $r_background_paint[$i]);
                    }
                } else {
                    $spreadsheet_0->setCellValue($listRowBackgroundPaint[0] . '23', $tmpReport->background_paint);
                }
                //磁 液 (粒)    magnetic_particles
                $r_magnetic_particles = $tmpReport->magnetic_particles != 'N/A'? explode('/', $tmpReport->magnetic_particles) : '';
                $listRowMagneticParticles = ['D', 'F', 'H'];
                if(is_array($r_magnetic_particles)) {
                    for($i = 0; $i < sizeof($r_magnetic_particles); $i++) {
                        $spreadsheet_0->setCellValue($listRowMagneticParticles[$i] . '25', $r_magnetic_particles[$i]);
                    }
                } else {
                    $spreadsheet_0->setCellValue($listRowMagneticParticles[0] . '25', $tmpReport->magnetic_particles);
                }
                //照 度 計		    white_light_meter
                $r_white_light_meter = $tmpReport->white_light_meter != 'N/A'? explode('/', $tmpReport->white_light_meter) : '';
                $listRowWhiteLightMeter = ['D', 'F', 'H'];
                if(is_array($r_white_light_meter)) {
                    for($i = 0; $i < sizeof($r_white_light_meter); $i++) {
                        $spreadsheet_0->setCellValue($listRowWhiteLightMeter[$i] . '27', $r_white_light_meter[$i]);
                    }
                } else {
                    $spreadsheet_0->setCellValue($listRowWhiteLightMeter[0] . '27', $tmpReport->white_light_meter);
                }
                //磁場指示塊		 magnetic_field_indicator
                $r_magnetic_field_indicator = $tmpReport->magnetic_field_indicator != 'N/A'? explode('/', $tmpReport->magnetic_field_indicator) : '';
                $listRowMagneticFieldIndicator = ['D', 'F', 'H'];
                if(is_array($r_magnetic_field_indicator)) {
                    for($i = 0; $i < sizeof($r_magnetic_field_indicator); $i++) {
                        $spreadsheet_0->setCellValue($listRowMagneticFieldIndicator[$i] . '29', $r_magnetic_field_indicator[$i]);
                    }
                } else {
                    $spreadsheet_0->setCellValue($listRowMagneticFieldIndicator[0] . '29', $tmpReport->magnetic_field_indicator);
                }
                //黑光照度計		black_light_meter
                $r_black_light_meter = $tmpReport->black_light_meter != 'N/A'? explode('/', $tmpReport->black_light_meter) : '';
                $listRowBlackLightMeter = ['D', 'F', 'H'];
                if(is_array($r_black_light_meter)) {
                    for($i = 0; $i < sizeof($r_black_light_meter); $i++) {
                        $spreadsheet_0->setCellValue($listRowBlackLightMeter[$i] . '31', $r_black_light_meter[$i]);
                    }
                } else {
                    $spreadsheet_0->setCellValue($listRowBlackLightMeter[0] . '31', $tmpReport->black_light_meter);
                }
                //磁化方法	 magnetizing_methon
                $listMagnetizingMethon= ['AC 磁軛','其他 Others	',
                '連續法', '剩磁法',
                '縱向磁化','周向磁化'];
                $listRowMagnetizingMethon = ['L', 'N'];
                $r_magnetizing_methon = explode(',', $tmpReport->magnetizing_methon);
                if(is_array($r_magnetizing_methon)) {
                    for($i = 0; $i < sizeof($listRowMagnetizingMethon); $i++) {
                        if(in_array($i, $r_magnetizing_methon)) {
                            if($i < sizeof($listRowMagnetizingMethon))
                            $spreadsheet_0->setCellValue($listRowMagnetizingMethon[$i] . '19', '▓' . $listMagnetizingMethon[$i]);
                            else if($i < sizeof($listRowMagnetizingMethon) * 2)
                            $spreadsheet_0->setCellValue($listRowMagnetizingMethon[$i%sizeof($listRowMagnetizingMethon)] . '21', '▓' . $listMagnetizingMethon[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowMagnetizingMethon[$i%sizeof($listRowMagnetizingMethon)] . '23', '▓' . $listMagnetizingMethon[$i]);
                        } else {
                            if($i < sizeof($listRowMagnetizingMethon))
                            $spreadsheet_0->setCellValue($listRowMagnetizingMethon[$i] . '19', '□' . $listMagnetizingMethon[$i]);
                            else if($i < sizeof($listRowMagnetizingMethon) * 2)
                            $spreadsheet_0->setCellValue($listRowMagnetizingMethon[$i%sizeof($listRowMagnetizingMethon)] . '21', '□' . $listMagnetizingMethon[$i]);
                            else
                            $spreadsheet_0->setCellValue($listRowMagnetizingMethon[$i%sizeof($listRowMagnetizingMethon)] . '23', '□' . $listMagnetizingMethon[$i]);
                        }
                    }
                }
                // 磁化電流
                $spreadsheet_0->setCellValue('L25', $reporter->magnetizing_current);

                // 檢測區照度
                $spreadsheet_0->setCellValue('L27', $reporter->light_intensity);

                // 構件溫度:	
                $spreadsheet_0->setCellValue('L29', $reporter->surface_temperature);

                // 吸舉力	:	
                $spreadsheet_0->setCellValue('L29', $reporter->lifting_test);

                // 黑光強度		:	
                $spreadsheet_0->setCellValue('L29', $reporter->black_light_meter);

                // 磁化時間		:	
                $spreadsheet_0->setCellValue('R21', $reporter->yoke_calibration_date);

                // 磁軛間距		:	
                $spreadsheet_0->setCellValue('R23', $reporter->yoke_probe_space);

                //接頭形式	 joint_type
                $listJointType= ['X','V',
                'K', 'TYK joint(NODE)',
                'Other'];
                $listRowJointType = ['L', 'M', 'N', 'O', 'Q'];
                $r_joint_type = explode(',', $tmpReport->joint_type);
                if(is_array($r_joint_type)) {
                    for($i = 0; $i < sizeof($listRowJointType); $i++) {
                        if(in_array($i, $r_joint_type)) {
                            $spreadsheet_0->setCellValue($listRowJointType[$i] . '31', '▓' . $listJointType[$i]);
                        } else {
                            $spreadsheet_0->setCellValue($listRowJointType[$i] . '31', '□' . $listJointType[$i]);
                        }
                    }
                }

                $items = json_decode($tmpReport->items);
                // $sheet1CellNames = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U'];
                if(is_array($items)) {
                    $i = 0; $index = 38;
                    $runOnce = true;
                    foreach($items as $objs) {
                        for($i = 0; $i < 26; $i++) {
                            // 流水號	
                            $serial_no = (string)isset($objs[$i]->serial_no)? $objs[$i]->serial_no : ($runOnce? '以下空白' : '');
                            $spreadsheet_0->setCellValue('A' . ($index+$i), $serial_no);
                            // 銲道編號		
                            $spreadsheet_0->setCellValue('C' . ($index+$i), (string)isset($objs[$i]->weld_no)? $objs[$i]->weld_no : '');
                            // 材質/厚度			
                            $spreadsheet_0->setCellValue('E' . ($index+$i), (string)isset($objs[$i]->material_thickness)? $objs[$i]->material_thickness : '');
                            // 圖號				
                            $spreadsheet_0->setCellValue('G' . ($index+$i), (string)isset($objs[$i]->drawing_no)? $objs[$i]->drawing_no : '');
                            // "檢驗長度"				
                            $spreadsheet_0->setCellValue('I' . ($index+$i), (string)isset($objs[$i]->inspection_length)? $objs[$i]->inspection_length : '');
                            // "缺陷編號"				
                            $spreadsheet_0->setCellValue('J' . ($index+$i), (string)isset($objs[$i]->defect_no)? $objs[$i]->defect_no : '');
                            // 缺陷尺寸				
                            $spreadsheet_0->setCellValue('K' . ($index+$i), (string)isset($objs[$i]->exam_length)? $objs[$i]->exam_length : '');
                            // 缺陷位置				
                            $spreadsheet_0->setCellValue('L' . ($index+$i), (string)isset($objs[$i]->defect_location[0])? 'V' : '');
                            // 缺陷位置				
                            $spreadsheet_0->setCellValue('M' . ($index+$i), (string)isset($objs[$i]->defect_location[1])? 'V' : '');
                            // 缺陷類型				
                            $spreadsheet_0->setCellValue('N' . ($index+$i), (string)isset($objs[$i]->type_of_defect)? $objs[$i]->type_of_defect : '');
                            // 檢驗日期時間				
                            $spreadsheet_0->setCellValue('O' . ($index+$i), (string)isset($objs[$i]->exam_datetime)? $objs[$i]->exam_datetime : '');
                            // 結果				
                            $spreadsheet_0->setCellValue('Q' . ($index+$i), (string)isset($objs[$i]->result)? ($objs[$i]->result == 0? 'Acc' : 'Reg') : '');
                            // 備註				
                            $spreadsheet_0->setCellValue('R' . ($index+$i), (string)isset($objs[$i]->remark)? $objs[$i]->remark : '');
                            
                            if($runOnce && $serial_no == '以下空白') {
                                $runOnce = false;
                            }
                        }
                    }
                }
            }
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(Storage::disk('export')->path($copyPath));

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