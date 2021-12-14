<?php
namespace App\Services\ParseReport;

use App\Services\ExportConfig\ExcelConfig;
use App\Repositories\{SampleLogRepository};
use App\Repositories\DispatchRepository;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ParseFpg
{
    private $spRepo = null;
    private $dispstchRepo = null;

    public function __construct(SampleLogRepository $spRepo, DispatchRepository $dispstchRepo) 
	{
		$this->spRepo = $spRepo;
        $this->dispstchRepo = $dispstchRepo;
	}

    public function getFpgRT_Oil()
	{
		return $this->spRepo->lately('fpg.rtoil');
	}
    public function getFpgRT_Olll()
	{
		return $this->spRepo->lately('fpg.rtolll');
	}
    public function getFpgRT_Three()
	{
		return $this->spRepo->lately('fpg.rthree');
	}
    public function parseReportFpgRT_Three($function_path, $id)
    {
        $out = [];
        $sample = $this->getFpgRT_Three();
        $reporter = $this->dispstchRepo->funPathDispatch($function_path, $id);
        
        $realPath = $sample->full_path;
        $serial = Carbon::now()->format('Ymd');
        $copyPath = 'fpg/'.$serial.'/rt/XID-RT-' . $serial .'-' . str_pad($id, 5, "0", STR_PAD_LEFT) . '.xlsx';
        if(is_null($reporter)){
            $out['code'] = 50001;
            $out['message'] = "資料更新錯誤";
            return $out;
        }
        if (Storage::disk('export')->put($copyPath, Storage::disk('public_sample')->get($realPath))) {
            // dd($reporter);
            $dirname = dirname($realPath);
            $samplePath = config('filesystems.disks.public_sample.root'). '' .$dirname . '/' . basename($realPath);
            // dd($samplePath);
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($samplePath);
            // dd($spreadsheet->getSheetByName('報告封面'));
            $spreadsheet_0 = $spreadsheet->getSheetByName('報告封面');
            /* dispatch */
            // 客戶
            $spreadsheet_0->setCellValue('B4', $reporter->customer);
            // 檢測日期
            // dd($reporter->exam_date);
            $spreadsheet_0->setCellValue('F4', $reporter->exam_date);
            // 工程名稱
            $spreadsheet_0->setCellValue('B6', $reporter->job_name);

            /* report_rtolll */
            if(isset($reporter->report_rthree)) {
                $tmpReport = $reporter->report_rthree;
                // dd($tmpReport);
                // 工程編號
                $spreadsheet_0->setCellValue('B8', $tmpReport->job_no);
                // 迴路編號  
                $spreadsheet_0->setCellValue('B10', $tmpReport->equlpment_no);
                // 檢測地點
                $spreadsheet_0->setCellValue('F6', $tmpReport->exam_place);
                // 材質及厚度
                // $spreadsheet_0->setCellValue('F8', $tmpReport->thicknes);
                // 檢測規範
                // $spreadsheet_0->setCellValue('F10', $tmpReport->customer);
                //  底片型號 
                $spreadsheet_0->setCellValue('F13', $tmpReport->film_brand);
                //  檢測儀器廠牌 
                $spreadsheet_0->setCellValue('B13', $tmpReport->sn);
                //  射源強度 
                $spreadsheet_0->setCellValue('F15', $tmpReport->energy_strength . ' Ci');
                //  射源類別 
                $radiationEnergy = '';
                if($tmpReport->radiation_energy == 0) {
                    $radiationEnergy = '▓Ir-192      □CO-60                     □X-Ray       □se-75';
                } else if($tmpReport->radiation_energy == 1) {
                    $radiationEnergy = '□Ir-192      ▓CO-60                     □X-Ray       □se-75';
                } else if($tmpReport->radiation_energy == 2) {
                    $radiationEnergy = '□Ir-192      □CO-60                     ▓X-Ray       □se-75';
                } else if($tmpReport->radiation_energy == 3) {
                    $radiationEnergy = '□Ir-192      □CO-60                     □X-Ray       ▓se-75';
                }
                $spreadsheet_0->setCellValue('B15', $radiationEnergy);

                //底片大小 Film Size
                $film_size1 = "";
                $film_size2 = "";
                $listFilmSize = ['10inX12in','8inX10in'];
                $r_report_size = explode(',', $tmpReport->film_size);
                if(is_array($r_report_size)) {
                    for($i = 0; $i < sizeof($listFilmSize); $i++) {
                        if(in_array($i, $r_report_size)) {
                            if($i == 0)
                            $film_size1 .= ('▓' . $listFilmSize[$i] . " ");
                            else if($i == 1)
                            $film_size2 .= ('▓' . $listFilmSize[$i] . " ");
                        } else {
                            if($i == 0)
                            $film_size1 .= ('□' . $listFilmSize[$i] . " ");
                            else if($i == 1)
                            $film_size2 .= ('□' . $listFilmSize[$i] . " ");
                        }
                    }
                }
                $spreadsheet_0->setCellValue('F17', $film_size1);
                $spreadsheet_0->setCellValue('F18', $film_size2);
                // 焦點尺寸 FOCUS SIZE
                $spreadsheet_0->setCellValue('B17', $tmpReport->focal_spot . 'mm');

                //底片黑度範圍 sin_dou
                $sin_dou1 = "";
                $sin_dou2 = "";
                $listSinDou= ['單片SINGLE1.8-4.0','雙片DOUBLE'];
                $r_sin_dou = explode(',', $tmpReport->sin_dou);
                if(is_array($r_sin_dou)) {
                    for($i = 0; $i < sizeof($listSinDou); $i++) {
                        if(in_array($i, $r_sin_dou)) {
                            if($i == 0)
                            $sin_dou1 .= ('▓' . $listSinDou[$i] . " ");
                            else if($i == 1)
                            $sin_dou2 .= ('▓' . $listSinDou[$i] . " ");
                        } else {
                            if($i == 0)
                            $sin_dou1 .= ('□' . $listSinDou[$i] . " ");
                            else if($i == 1)
                            $sin_dou2 .= ('□' . $listSinDou[$i] . " ");
                        }
                    }
                }
                $spreadsheet_0->setCellValue('B19', $sin_dou1);
                $spreadsheet_0->setCellValue('B20', $sin_dou2);

                //增感屏厚度 screen_thick
                $screen_thick1 = "";
                $screen_thick2 = "";
                $listScreenThick= ['前FRONT 鉛Pb-0.005','後BACK  鉛Pb-0.010'];
                $r_screen_thick = explode(',', $tmpReport->screen_thick);
                if(is_array($r_screen_thick)) {
                    for($i = 0; $i < sizeof($listScreenThick); $i++) {
                        if(in_array($i, $r_screen_thick)) {
                            if($i == 0)
                            $screen_thick1 .= ('▓' . $listScreenThick[$i] . " ");
                            else if($i == 1)
                            $screen_thick2 .= ('▓' . $listScreenThick[$i] . " ");
                        } else {
                            if($i == 0)
                            $screen_thick1 .= ('□' . $listScreenThick[$i] . " ");
                            else if($i == 1)
                            $screen_thick2 .= ('□' . $listScreenThick[$i] . " ");
                        }
                    }
                }
                $spreadsheet_0->setCellValue('B21', $screen_thick1);
                $spreadsheet_0->setCellValue('B22', $screen_thick2);

                //判片方式 interpret
                $interpret1 = "";
                $interpret2 = "";
                $listInterpret= ['單壁判讀SINGLE WALL','雙壁判讀DOUBLE WALL'];
                $r_interpret = explode(',', $tmpReport->interpret);
                if(is_array($r_interpret)) {
                    for($i = 0; $i < sizeof($listInterpret); $i++) {
                        if(in_array($i, $r_interpret)) {
                            if($i == 0)
                            $interpret1 .= ('▓' . $listInterpret[$i] . " ");
                            else if($i == 1)
                            $interpret2 .= ('▓' . $listInterpret[$i] . " ");
                        } else {
                            if($i == 0)
                            $interpret1 .= ('□' . $listInterpret[$i] . " ");
                            else if($i == 1)
                            $interpret2 .= ('□' . $listInterpret[$i] . " ");
                        }
                    }
                }
                $spreadsheet_0->setCellValue('F21', $interpret2);
                $spreadsheet_0->setCellValue('F22', $interpret1);

                //檢測人員  obsert
                $spreadsheet_0->setCellValue('B37', $tmpReport->obsert);

                //審查  Approved By
                $spreadsheet_0->setCellValue('F37', $tmpReport->approved);

                // $spreadsheet_1 = $spreadsheet->getSheet(1);
                $spreadsheet_1 = $spreadsheet->getSheetByName('彙整表');
                // $spreadsheet_2 = $spreadsheet->getSheet(2);
                $spreadsheet_2 = $spreadsheet->getSheetByName('檢測報告');

                // $spreadsheet_2->setCellValue('A2', '工程名稱：'. $tmpReport->job_name);
                // $spreadsheet_2->setCellValue('A3', '工程編號：'. $tmpReport->job_no);
                $spreadsheet_1->setCellValue('N4', $tmpReport->job_name);
                $spreadsheet_1->setCellValue('C4', $tmpReport->job_no);
                
                $items = json_decode($tmpReport->items);
                $sheet1CellNames = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U'];
                if(is_array($items)) {
                    $i = 0; $index = 9; $index2 = 4; $index2_M = 22;
                    foreach($items as $item) {
                        // 項次
                        $spreadsheet_1->setCellValue('A' . ($index+$i), (string)($i + 1));
                        // 管線編號
                        $spreadsheet_1->setCellValue('B' . ($index+$i), $item->pipeline_no);
                        // 底片編號
                        $spreadsheet_1->setCellValue('C' . ($index+$i), $item->film_no);
                        // 管徑
                        $spreadsheet_1->setCellValue('D' . ($index+$i), $item->pipeline_size);
                        // 檢測位置簡述
                        // $_m = str_starts_with($item->film_no, 'D')? '滯留區' : '';
                        // $_m = strlen($_m) > 0? $_m : (str_starts_with($item->film_no, 'L')? '水平長管' : '');
                        // $_m = strlen($_m) > 0? $_m : (str_starts_with($item->film_no, 'E')? '彎頭' : '');
                        // $_m = strlen($_m) > 0? $_m : (str_starts_with($item->film_no, 'T')? '三通' : '');
                        // $_m = strlen($_m) > 0? $_m : (str_starts_with($item->film_no, 'R')? '漸縮管' : '');
                        // $_m = strlen($_m) > 0? $_m : (str_starts_with($item->film_no, 'IP')? '穿透保溫' : '');
                        // $_m = strlen($_m) > 0? $_m : (str_starts_with($item->film_no, 'IV')? '垂直管' : '');
                        // $_m = strlen($_m) > 0? $_m : (str_starts_with($item->film_no, 'IE')? '保溫端點' : '');
                        // $_m = strlen($_m) > 0? $_m : (str_starts_with($item->film_no, 'IL')? '水平低點' : '');
                        
                        $spreadsheet_1->setCellValue('E' . ($index+$i), $item->brief_descript);
                        // 底片尺寸
                        $spreadsheet_1->setCellValue('F' . ($index+$i), $item->film_size);
                        // 部件材質
                        $spreadsheet_1->setCellValue('G' . ($index+$i), $item->material);
                        // 公稱厚度
                        $spreadsheet_1->setCellValue('H' . ($index+$i), $item->n_thickness);
                        // 剩餘厚度
                        $spreadsheet_1->setCellValue('I' . ($index+$i), $item->le_thickness);
                        // 減薄率
                        $a = (($item->n_thickness - $item->le_thickness) / $item->n_thickness) * 100;
                        $a = $a < 0? '0' : $a;
                        $spreadsheet_1->setCellValue('J' . ($index+$i), number_format($a, 2) . '%');
                        // 腐蝕內
                        $_r = $item->corrosion;
                        $spreadsheet_1->setCellValue('K' . ($index+$i), in_array(0, $_r)? '✓':'');
                        // 腐蝕外
                        $spreadsheet_1->setCellValue('L' . ($index+$i), in_array(1, $_r)? '✓':'');
                        // 腐蝕CUI
                        $spreadsheet_1->setCellValue('M' . ($index+$i), in_array(2, $_r)? '✓':'');
                        // 缺陷描述
                        $spreadsheet_1->setCellValue('N' . ($index+$i), $item->defect_descript);
                        // 結構最小厚度(mm)
                        $_o = $item->film_size < 0.5? '1' :'';
                        $_o = strlen($_o) > 0? $_o : ($item->film_size < 3? '1.8' : '');
                        $_o = strlen($_o) > 0? $_o : ($item->film_size < 4? '2' : '');
                        $_o = strlen($_o) > 0? $_o : ($item->film_size < 6? '2.3' : '');
                        $_o = strlen($_o) > 0? $_o : ($item->film_size < 20? '2.8' : '');
                        $_o = strlen($_o) > 0? $_o : ($item->film_size >= 20? '3.1' : '');
                        $spreadsheet_1->setCellValue('O' . ($index+$i), $_o);
                        // 備註
                        $spreadsheet_1->setCellValue('P' . ($index+$i), $item->remark);
                        // 安裝日期
                        $spreadsheet_1->setCellValue('Q' . ($index+$i), $item->exce_date);
                        // 檢查日
                        $spreadsheet_1->setCellValue('R' . ($index+$i), $reporter->exam_date);
                        // 使用年限
                        $_s = Carbon::parse($item->exce_date)->floatDiffInYears($reporter->exam_date);
                        $spreadsheet_1->setCellValue('S' . ($index+$i), number_format($_s, 1));
                        // 腐蝕率
                        $_t = $a == 0? 0 : (($item->n_thickness - $item->le_thickness) / $_s);
                        $spreadsheet_1->setCellValue('T' . ($index+$i), number_format($_t, 3));
                        // 剩餘壽命
                        $_u = $a == 0? '>20' : (($item->le_thickness < $_o)? '0' : (($item->le_thickness - $_o) / $_t < 20? ($item->le_thickness - $_o) / $_t : '>20'));
                        $spreadsheet_1->setCellValue('U' . ($index+$i), is_numeric($_u)? number_format($_u, 2) : $_u);

                        // 匡線繪製
                        foreach($sheet1CellNames as $nameValue) {
                            $spreadsheet_1->getStyle($nameValue . ($index+$i))->getBorders()->getTop()->setBorderStyle('thin');
                            $spreadsheet_1->getStyle($nameValue . ($index+$i))->getBorders()->getLeft()->setBorderStyle('thin');
                            $spreadsheet_1->getStyle($nameValue . ($index+$i))->getBorders()->getRight()->setBorderStyle('thin');
                            $spreadsheet_1->getStyle($nameValue . ($index+$i))->getBorders()->getBottom()->setBorderStyle('thin');
                            $spreadsheet_1->getStyle($nameValue . ($index+$i))->applyFromArray(ExcelConfig::fpgOilSheet1ListStyle());
                        }

                        $spreadsheet_2->getStyle((($index2+0)+($i*$index2_M)))->applyFromArray(ExcelConfig::fpgOilSheet2Line4Style());
                        $spreadsheet_2->getRowDimension((($index2+0)+($i*$index2_M)))->setRowHeight(29, 'pt'); //高度
                        $spreadsheet_2->getStyle((($index2+1)+($i*$index2_M)))->applyFromArray(ExcelConfig::fpgOilSheet2Line5Style());
                        $spreadsheet_2->getRowDimension((($index2+1)+($i*$index2_M)))->setRowHeight(29, 'pt'); //高度
                        $spreadsheet_2->getStyle((($index2+20)+($i*$index2_M)))->applyFromArray(ExcelConfig::fpgOlllSheet2Line24Style());
                        $spreadsheet_2->getRowDimension((($index2+20)+($i*$index2_M)))->setRowHeight(29, 'pt'); //高度
                        $spreadsheet_2->getStyle((($index2+21)+($i*$index2_M)))->applyFromArray(ExcelConfig::fpgOilSheet2Line25Style());
                        $spreadsheet_2->getRowDimension((($index2+21)+($i*$index2_M)))->setRowHeight(29, 'pt'); //高度

                        $spreadsheet_2->mergeCells('A' . (($index2+0)+($i*$index2_M)) . ':B' . (($index2+0)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('A' . (($index2+0)+($i*$index2_M)), '序號：' . (string)($i + 1));
                        // $spreadsheet_2->getStyle('A' . (($index2+0)+($i*$index2_M)))->applyFromArray(ExcelConfig::fpgOilSheet2Line24Style());
                        $spreadsheet_2->getStyle('A' . (($index2+0)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+0)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+0)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+0)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->setCellValue('C' . (($index2+0)+($i*$index2_M)), '迴路編號：');
                        $spreadsheet_2->setCellValue('E' . (($index2+0)+($i*$index2_M)), $tmpReport->equlpment_no);
                        $spreadsheet_2->mergeCells('C' . (($index2+0)+($i*$index2_M)) . ':D' . (($index2+0)+($i*$index2_M)));
                        $spreadsheet_2->mergeCells('E' . (($index2+0)+($i*$index2_M)) . ':L' . (($index2+0)+($i*$index2_M)));
                        // $spreadsheet_2->getStyle('B' . (($index2+0)+($i*$index2_M)) . ':G' . (($index2+0)+($i*$index2_M)))->getAlignment()->setHorizontal('right');
                        $spreadsheet_2->getStyle('C' . (($index2+0)+($i*$index2_M)) . ':D' . (($index2+0)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+0)+($i*$index2_M)) . ':D' . (($index2+0)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+0)+($i*$index2_M)) . ':D' . (($index2+0)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+0)+($i*$index2_M)) . ':D' . (($index2+0)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('E' . (($index2+0)+($i*$index2_M)) . ':L' . (($index2+0)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('E' . (($index2+0)+($i*$index2_M)) . ':L' . (($index2+0)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('E' . (($index2+0)+($i*$index2_M)) . ':L' . (($index2+0)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('E' . (($index2+0)+($i*$index2_M)) . ':L' . (($index2+0)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        $spreadsheet_2->mergeCells('M' . (($index2+0)+($i*$index2_M)) . ':O' . (($index2+0)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('M' . (($index2+0)+($i*$index2_M)), '設備/管線編號：');
                        $spreadsheet_2->mergeCells('P' . (($index2+0)+($i*$index2_M)) . ':S' . (($index2+0)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('P' . (($index2+0)+($i*$index2_M)), $item->pipeline_no);
                        // $spreadsheet_2->getStyle('H' . (($index2+0)+($i*$index2_M)))->getAlignment()->setHorizontal('left');
                        $spreadsheet_2->getStyle('M' . (($index2+0)+($i*$index2_M)) . ':O' . (($index2+0)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('M' . (($index2+0)+($i*$index2_M)) . ':O' . (($index2+0)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('M' . (($index2+0)+($i*$index2_M)) . ':O' . (($index2+0)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('M' . (($index2+0)+($i*$index2_M)) . ':O' . (($index2+0)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('P' . (($index2+0)+($i*$index2_M)) . ':S' . (($index2+0)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('P' . (($index2+0)+($i*$index2_M)) . ':S' . (($index2+0)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('P' . (($index2+0)+($i*$index2_M)) . ':S' . (($index2+0)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('P' . (($index2+0)+($i*$index2_M)) . ':S' . (($index2+0)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        $spreadsheet_2->mergeCells('T' . (($index2+0)+($i*$index2_M)) . ':U' . (($index2+0)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('T' . (($index2+0)+($i*$index2_M)), 'ISO DRG');
                        $spreadsheet_2->setCellValue('W' . (($index2+0)+($i*$index2_M)), 'Of');
                        
                        // 圖片標題
                        $spreadsheet_2->mergeCells('A' . (($index2+1)+($i*$index2_M)) . ':L' . (($index2+1)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('A' . (($index2+1)+($i*$index2_M)), 'A. 施照位置外觀照片');
                        $spreadsheet_2->getStyle('A' . (($index2+1)+($i*$index2_M)) . ':L' . (($index2+1)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+1)+($i*$index2_M)) . ':L' . (($index2+1)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+1)+($i*$index2_M)) . ':L' . (($index2+1)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+1)+($i*$index2_M)) . ':L' . (($index2+1)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        

                        $spreadsheet_2->mergeCells('M' . (($index2+1)+($i*$index2_M)) . ':X' . (($index2+1)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('G' . (($index2+1)+($i*$index2_M)), 'B. RT檢測片');
                        $spreadsheet_2->getStyle('M' . (($index2+1)+($i*$index2_M)) . ':X' . (($index2+1)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('M' . (($index2+1)+($i*$index2_M)) . ':X' . (($index2+1)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('M' . (($index2+1)+($i*$index2_M)) . ':X' . (($index2+1)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('M' . (($index2+1)+($i*$index2_M)) . ':X' . (($index2+1)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        // 圖片欄位
                        $spreadsheet_2->mergeCells('A' . (($index2+1)+($i*$index2_M)) . ':L' . (($index2+1)+($i*$index2_M)));
                        $spreadsheet_2->getStyle('A' . (($index2+1)+($i*$index2_M)) . ':L' . (($index2+1)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+1)+($i*$index2_M)) . ':L' . (($index2+1)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+1)+($i*$index2_M)) . ':L' . (($index2+1)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+1)+($i*$index2_M)) . ':L' . (($index2+1)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        $spreadsheet_2->mergeCells('M' . (($index2+1)+($i*$index2_M)) . ':X' . (($index2+1)+($i*$index2_M)));
                        $spreadsheet_2->getStyle('M' . (($index2+1)+($i*$index2_M)) . ':X' . (($index2+1)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('M' . (($index2+1)+($i*$index2_M)) . ':X' . (($index2+1)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('M' . (($index2+1)+($i*$index2_M)) . ':X' . (($index2+1)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('M' . (($index2+1)+($i*$index2_M)) . ':X' . (($index2+1)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        //表格標題
                        $spreadsheet_2->mergeCells('A' . (($index2+20)+($i*$index2_M)) . ':B' . (($index2+20)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('A' . (($index2+20)+($i*$index2_M)), '底片編號');
                        $spreadsheet_2->getStyle('A' . (($index2+20)+($i*$index2_M)) . ':B' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+20)+($i*$index2_M)) . ':B' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+20)+($i*$index2_M)) . ':B' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+20)+($i*$index2_M)) . ':B' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        $spreadsheet_2->setCellValue('C' . (($index2+20)+($i*$index2_M)), "管線\n尺寸");
                        $spreadsheet_2->getStyle('C' . (($index2+20)+($i*$index2_M)))->getAlignment()->setWrapText(true);
                        $spreadsheet_2->getStyle('C' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        $spreadsheet_2->mergeCells('D' . (($index2+20)+($i*$index2_M)) . ':E' . (($index2+20)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('D' . (($index2+20)+($i*$index2_M)), '公稱厚度');
                        $spreadsheet_2->getStyle('D' . (($index2+20)+($i*$index2_M)) . ':E' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+20)+($i*$index2_M)) . ':E' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+20)+($i*$index2_M)) . ':E' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+20)+($i*$index2_M)) . ':E' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        $spreadsheet_2->mergeCells('F' . (($index2+20)+($i*$index2_M)) . ':H' . (($index2+20)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('F' . (($index2+20)+($i*$index2_M)), '部件材質');
                        $spreadsheet_2->getStyle('F' . (($index2+20)+($i*$index2_M)) . ':H' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+20)+($i*$index2_M)) . ':H' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+20)+($i*$index2_M)) . ':H' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+20)+($i*$index2_M)) . ':H' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        $spreadsheet_2->mergeCells('I' . (($index2+20)+($i*$index2_M)) . ':J' . (($index2+20)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('I' . (($index2+20)+($i*$index2_M)), '剩餘厚度');
                        $spreadsheet_2->getStyle('I' . (($index2+20)+($i*$index2_M)) . ':J' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+20)+($i*$index2_M)) . ':J' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+20)+($i*$index2_M)) . ':J' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+20)+($i*$index2_M)) . ':J' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->mergeCells('K' . (($index2+20)+($i*$index2_M)) . ':L' . (($index2+20)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('K' . (($index2+20)+($i*$index2_M)), '減薄率');
                        $spreadsheet_2->getStyle('K' . (($index2+20)+($i*$index2_M)) . ':L' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('K' . (($index2+20)+($i*$index2_M)) . ':L' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('K' . (($index2+20)+($i*$index2_M)) . ':L' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('K' . (($index2+20)+($i*$index2_M)) . ':L' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->setCellValue('M' . (($index2+20)+($i*$index2_M)), '內腐蝕');
                        $spreadsheet_2->getStyle('M' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('M' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('M' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('M' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        // $spreadsheet_2->mergeCells('F' . (($index2+20)+($i*$index2_M)) . ':G' . (($index2+20)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('N' . (($index2+20)+($i*$index2_M)), '外腐蝕');
                        $spreadsheet_2->getStyle('N' . (($index2+20)+($i*$index2_M)) . ':F' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('N' . (($index2+20)+($i*$index2_M)) . ':F' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('N' . (($index2+20)+($i*$index2_M)) . ':F' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('N' . (($index2+20)+($i*$index2_M)) . ':F' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->setCellValue('O' . (($index2+20)+($i*$index2_M)), 'CUI');
                        $spreadsheet_2->getStyle('O' . (($index2+20)+($i*$index2_M)) . ':G' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('O' . (($index2+20)+($i*$index2_M)) . ':G' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('O' . (($index2+20)+($i*$index2_M)) . ':G' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('O' . (($index2+20)+($i*$index2_M)) . ':G' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->mergeCells('P' . (($index2+20)+($i*$index2_M)) . ':S' . (($index2+20)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('P' . (($index2+20)+($i*$index2_M)), '缺陷描述');
                        $spreadsheet_2->getStyle('P' . (($index2+20)+($i*$index2_M)) . ':S' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('P' . (($index2+20)+($i*$index2_M)) . ':S' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('P' . (($index2+20)+($i*$index2_M)) . ':S' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('P' . (($index2+20)+($i*$index2_M)) . ':S' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->mergeCells('T' . (($index2+20)+($i*$index2_M)) . ':V' . (($index2+20)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('T' . (($index2+20)+($i*$index2_M)), '建議處理方式');
                        $spreadsheet_2->getStyle('T' . (($index2+20)+($i*$index2_M)) . ':V' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('T' . (($index2+20)+($i*$index2_M)) . ':V' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('T' . (($index2+20)+($i*$index2_M)) . ':V' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('T' . (($index2+20)+($i*$index2_M)) . ':V' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->mergeCells('W' . (($index2+20)+($i*$index2_M)) . ':X' . (($index2+20)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('W' . (($index2+20)+($i*$index2_M)), '施照日期');
                        $spreadsheet_2->getStyle('W' . (($index2+20)+($i*$index2_M)) . ':X' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('W' . (($index2+20)+($i*$index2_M)) . ':X' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('W' . (($index2+20)+($i*$index2_M)) . ':X' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('W' . (($index2+20)+($i*$index2_M)) . ':X' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        // 表格內文
                        // 底片編號
                        $spreadsheet_2->mergeCells('A' . (($index2+21)+($i*$index2_M)) . ':B' . (($index2+21)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('A' . (($index2+21)+($i*$index2_M)), $item->film_no);
                        $spreadsheet_2->getStyle('A' . (($index2+21)+($i*$index2_M)) . ':B' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+21)+($i*$index2_M)) . ':B' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+21)+($i*$index2_M)) . ':B' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+21)+($i*$index2_M)) . ':B' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 管線尺寸
                        // $spreadsheet_2->mergeCells('' . (($index2+21)+($i*$index2_M)) . ':E' . (($index2+21)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('C' . (($index2+21)+($i*$index2_M)), $item->pipeline_size);
                        $spreadsheet_2->getStyle('C' . (($index2+21)+($i*$index2_M)) . ':C' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+21)+($i*$index2_M)) . ':C' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+21)+($i*$index2_M)) . ':C' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+21)+($i*$index2_M)) . ':C' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        // 公稱厚度
                        $spreadsheet_2->mergeCells('D' . (($index2+21)+($i*$index2_M)) . ':E' . (($index2+21)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('D' . (($index2+21)+($i*$index2_M)), $item->n_thickness);
                        $spreadsheet_2->getStyle('D' . (($index2+21)+($i*$index2_M)) . ':E' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+21)+($i*$index2_M)) . ':E' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+21)+($i*$index2_M)) . ':E' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+21)+($i*$index2_M)) . ':E' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 部件材質		
                        $spreadsheet_2->mergeCells('F' . (($index2+21)+($i*$index2_M)) . ':G' . (($index2+21)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('F' . (($index2+21)+($i*$index2_M)), $item->material);
                        $spreadsheet_2->getStyle('F' . (($index2+21)+($i*$index2_M)) . ':G' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+21)+($i*$index2_M)) . ':G' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+21)+($i*$index2_M)) . ':G' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+21)+($i*$index2_M)) . ':G' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        // 剩餘厚度
                        $spreadsheet_2->mergeCells('I' . (($index2+21)+($i*$index2_M)) . ':J' . (($index2+21)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('I' . (($index2+21)+($i*$index2_M)), $item->le_thickness);
                        $spreadsheet_2->getStyle('I' . (($index2+21)+($i*$index2_M)) . ':J' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+21)+($i*$index2_M)) . ':J' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+21)+($i*$index2_M)) . ':J' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+21)+($i*$index2_M)) . ':J' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 減薄率
                        $a = (($item->n_thickness - $item->le_thickness) / $item->n_thickness) * 100;
                        $spreadsheet_2->mergeCells('K' . (($index2+21)+($i*$index2_M)) . ':L' . (($index2+21)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('K' . (($index2+21)+($i*$index2_M)), number_format($a, 2) . '%'); 
                        $spreadsheet_2->getStyle('K' . (($index2+21)+($i*$index2_M)) . ':L' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('K' . (($index2+21)+($i*$index2_M)) . ':L' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('K' . (($index2+21)+($i*$index2_M)) . ':L' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('K' . (($index2+21)+($i*$index2_M)) . ':L' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 腐蝕內
                        $_r = $item->corrosion;
                        $spreadsheet_2->setCellValue('M' . (($index2+21)+($i*$index2_M)), in_array(0, $_r)? "P":'');
                        $spreadsheet_2->getStyle('M' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('M' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('M' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('M' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 腐蝕外
                        $spreadsheet_2->setCellValue('N' . (($index2+21)+($i*$index2_M)), in_array(1, $_r)? "P":'');
                        $spreadsheet_2->getStyle('N' . (($index2+21)+($i*$index2_M)) . ':N' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('N' . (($index2+21)+($i*$index2_M)) . ':N' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('N' . (($index2+21)+($i*$index2_M)) . ':N' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('N' . (($index2+21)+($i*$index2_M)) . ':N' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // CUI
                        $spreadsheet_2->setCellValue('O' . (($index2+21)+($i*$index2_M)), in_array(1, $_r)? "P":'');
                        $spreadsheet_2->getStyle('O' . (($index2+21)+($i*$index2_M)) . ':O' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('O' . (($index2+21)+($i*$index2_M)) . ':O' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('O' . (($index2+21)+($i*$index2_M)) . ':O' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('O' . (($index2+21)+($i*$index2_M)) . ':O' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        // 缺陷描述
                        $spreadsheet_2->mergeCells('P' . (($index2+21)+($i*$index2_M)) . ':S' . (($index2+21)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('P' . (($index2+21)+($i*$index2_M)), $item->defect_descript);
                        $spreadsheet_2->getStyle('P' . (($index2+21)+($i*$index2_M)) . ':S' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('P' . (($index2+21)+($i*$index2_M)) . ':S' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('P' . (($index2+21)+($i*$index2_M)) . ':S' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('P' . (($index2+21)+($i*$index2_M)) . ':S' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 建議處理方式
                        $spreadsheet_2->mergeCells('T' . (($index2+21)+($i*$index2_M)) . ':V' . (($index2+21)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('T' . (($index2+21)+($i*$index2_M)), $item->suggested_handling);
                        $spreadsheet_2->getStyle('T' . (($index2+21)+($i*$index2_M)) . ':V' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('T' . (($index2+21)+($i*$index2_M)) . ':V' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('T' . (($index2+21)+($i*$index2_M)) . ':V' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('T' . (($index2+21)+($i*$index2_M)) . ':V' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 施照日期
                        $spreadsheet_2->mergeCells('W' . (($index2+21)+($i*$index2_M)) . ':X' . (($index2+21)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('W' . (($index2+21)+($i*$index2_M)), $item->exce_date);
                        $spreadsheet_2->getStyle('W' . (($index2+21)+($i*$index2_M)) . ':X' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('W' . (($index2+21)+($i*$index2_M)) . ':X' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('W' . (($index2+21)+($i*$index2_M)) . ':X' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('W' . (($index2+21)+($i*$index2_M)) . ':X' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        $i++;
                    }
                }
                $spreadsheet_1->getStyle("A7:U8")->applyFromArray(ExcelConfig::fpgThreeSheet1ListStyle());
            }
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(Storage::disk('export')->path($copyPath));
        }
    }
    public function parseReportFpgRT_Olll($function_path, $id)
    {
        $out = [];
        $sample = $this->getFpgRT_Olll();
        $reporter = $this->dispstchRepo->funPathDispatch($function_path, $id);
        
        $realPath = $sample->full_path;
        $serial = Carbon::now()->format('Ymd');
        $copyPath = 'fpg/'.$serial.'/rt/XID-RT-' . $serial .'-' . str_pad($id, 5, "0", STR_PAD_LEFT) . '.xlsx';
        if(is_null($reporter)){
            $out['code'] = 50001;
            $out['message'] = "資料更新錯誤";
            return $out;
        }
        if (Storage::disk('export')->put($copyPath, Storage::disk('public_sample')->get($realPath))) {
            // dd($reporter);
            $dirname = dirname($realPath);
            $samplePath = config('filesystems.disks.public_sample.root'). '' .$dirname . '/' . basename($realPath);
            // dd($samplePath);
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($samplePath);
            // dd($spreadsheet->getSheetCount());
            $spreadsheet_0 = $spreadsheet->getSheet(0);
            /* dispatch */
            // 客戶
            $spreadsheet_0->setCellValue('B4', $reporter->customer);
            // 檢測日期
            // dd($reporter->exam_date);
            $spreadsheet_0->setCellValue('F4', $reporter->exam_date);
            // 工程名稱
            $spreadsheet_0->setCellValue('B6', $reporter->job_name);

            /* report_rtolll */
            if(isset($reporter->report_rtolll)) {
                $tmpReport = $reporter->report_rtolll;
                // dd($tmpReport);
                // E9 =IF(COUNTIF(C9,"D*"),"滯留區",IF(COUNTIF(C9,"L*"),"水平長管",IF(COUNTIF(C9,"E*"),"彎頭",IF(COUNTIF(C9,"T*"),"三通",IF(COUNTIF(C9,"R*"),"漸縮管",IF(COUNTIF(C9,"IP*"),"穿透保溫",IF(COUNTIF(C9,"IV*"),"垂直管",IF(COUNTIF(C9,"IE*"),"保溫端點",IF(COUNTIF(C9,"IL*"),"水平低點",)))))))))
                // 工程編號
                $spreadsheet_0->setCellValue('B8', $tmpReport->job_no);
                // 迴路編號  
                $spreadsheet_0->setCellValue('B10', $tmpReport->equlpment_no);
                // 檢測地點
                $spreadsheet_0->setCellValue('F6', $tmpReport->exam_place);
                // 材質及厚度
                // $spreadsheet_0->setCellValue('F8', $tmpReport->thicknes);
                // 檢測規範
                // $spreadsheet_0->setCellValue('F10', $tmpReport->customer);
                //  底片型號 
                $spreadsheet_0->setCellValue('F13', $tmpReport->film_brand);
                //  檢測儀器廠牌 
                $spreadsheet_0->setCellValue('B13', $tmpReport->sn);
                //  射源強度 
                $spreadsheet_0->setCellValue('F15', $tmpReport->energy_strength . ' Ci');
                //  射源類別 
                $radiationEnergy = '';
                if($tmpReport->radiation_energy == 0) {
                    $radiationEnergy = '▓Ir-192      □CO-60                     □X-Ray       □se-75';
                } else if($tmpReport->radiation_energy == 1) {
                    $radiationEnergy = '□Ir-192      ▓CO-60                     □X-Ray       □se-75';
                } else if($tmpReport->radiation_energy == 2) {
                    $radiationEnergy = '□Ir-192      □CO-60                     ▓X-Ray       □se-75';
                } else if($tmpReport->radiation_energy == 3) {
                    $radiationEnergy = '□Ir-192      □CO-60                     □X-Ray       ▓se-75';
                }
                $spreadsheet_0->setCellValue('B15', $radiationEnergy);

                //底片大小 Film Size
                $film_size1 = "";
                $film_size2 = "";
                $listFilmSize = ['10inX12in','8inX10in'];
                $r_report_size = explode(',', $tmpReport->film_size);
                if(is_array($r_report_size)) {
                    for($i = 0; $i < sizeof($listFilmSize); $i++) {
                        if(in_array($i, $r_report_size)) {
                            if($i == 0)
                            $film_size1 .= ('▓' . $listFilmSize[$i] . " ");
                            else if($i == 1)
                            $film_size2 .= ('▓' . $listFilmSize[$i] . " ");
                        } else {
                            if($i == 0)
                            $film_size1 .= ('□' . $listFilmSize[$i] . " ");
                            else if($i == 1)
                            $film_size2 .= ('□' . $listFilmSize[$i] . " ");
                        }
                    }
                }
                $spreadsheet_0->setCellValue('F17', $film_size1);
                $spreadsheet_0->setCellValue('F18', $film_size2);
                // 焦點尺寸 FOCUS SIZE
                $spreadsheet_0->setCellValue('B17', $tmpReport->focal_spot . 'mm');

                //底片黑度範圍 sin_dou
                $sin_dou1 = "";
                $sin_dou2 = "";
                $listSinDou= ['單片SINGLE1.8-4.0','雙片DOUBLE'];
                $r_sin_dou = explode(',', $tmpReport->sin_dou);
                if(is_array($r_sin_dou)) {
                    for($i = 0; $i < sizeof($listSinDou); $i++) {
                        if(in_array($i, $r_sin_dou)) {
                            if($i == 0)
                            $sin_dou1 .= ('▓' . $listSinDou[$i] . " ");
                            else if($i == 1)
                            $sin_dou2 .= ('▓' . $listSinDou[$i] . " ");
                        } else {
                            if($i == 0)
                            $sin_dou1 .= ('□' . $listSinDou[$i] . " ");
                            else if($i == 1)
                            $sin_dou2 .= ('□' . $listSinDou[$i] . " ");
                        }
                    }
                }
                $spreadsheet_0->setCellValue('B19', $sin_dou1);
                $spreadsheet_0->setCellValue('B20', $sin_dou2);

                //增感屏厚度 screen_thick
                $screen_thick1 = "";
                $screen_thick2 = "";
                $listScreenThick= ['前FRONT 鉛Pb-0.005','後BACK  鉛Pb-0.010'];
                $r_screen_thick = explode(',', $tmpReport->screen_thick);
                if(is_array($r_screen_thick)) {
                    for($i = 0; $i < sizeof($listScreenThick); $i++) {
                        if(in_array($i, $r_screen_thick)) {
                            if($i == 0)
                            $screen_thick1 .= ('▓' . $listScreenThick[$i] . " ");
                            else if($i == 1)
                            $screen_thick2 .= ('▓' . $listScreenThick[$i] . " ");
                        } else {
                            if($i == 0)
                            $screen_thick1 .= ('□' . $listScreenThick[$i] . " ");
                            else if($i == 1)
                            $screen_thick2 .= ('□' . $listScreenThick[$i] . " ");
                        }
                    }
                }
                $spreadsheet_0->setCellValue('B21', $screen_thick1);
                $spreadsheet_0->setCellValue('B22', $screen_thick2);

                //判片方式 interpret
                $interpret1 = "";
                $interpret2 = "";
                $listInterpret= ['單壁判讀SINGLE WALL','雙壁判讀DOUBLE WALL'];
                $r_interpret = explode(',', $tmpReport->interpret);
                if(is_array($r_interpret)) {
                    for($i = 0; $i < sizeof($listInterpret); $i++) {
                        if(in_array($i, $r_interpret)) {
                            if($i == 0)
                            $interpret1 .= ('▓' . $listInterpret[$i] . " ");
                            else if($i == 1)
                            $interpret2 .= ('▓' . $listInterpret[$i] . " ");
                        } else {
                            if($i == 0)
                            $interpret1 .= ('□' . $listInterpret[$i] . " ");
                            else if($i == 1)
                            $interpret2 .= ('□' . $listInterpret[$i] . " ");
                        }
                    }
                }
                $spreadsheet_0->setCellValue('F21', $interpret2);
                $spreadsheet_0->setCellValue('F22', $interpret1);

                //檢測人員  obsert
                $spreadsheet_0->setCellValue('B36', $tmpReport->obsert);

                //審查  Approved By
                $spreadsheet_0->setCellValue('F36', "");

                $spreadsheet_1 = $spreadsheet->getSheet(1);
                $spreadsheet_2 = $spreadsheet->getSheet(2);

                $spreadsheet_2->setCellValue('A2', '工程名稱：'. $tmpReport->job_name);
                $spreadsheet_2->setCellValue('A3', '工程編號：'. $tmpReport->job_no);

                $items = json_decode($tmpReport->items);
                $sheet1CellNames = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'];
                if(is_array($items)) {
                    $i = 0; $index = 9; $index2 = 4; $index2_M = 22;
                    foreach($items as $item) {
                        // 項次
                        $spreadsheet_1->setCellValue('A' . ($index+$i), (string)($i + 1));
                        // 管線編號
                        $spreadsheet_1->setCellValue('B' . ($index+$i), $item->pipeline_no);
                        // 底片編號
                        $spreadsheet_1->setCellValue('C' . ($index+$i), $item->film_no);
                        // 管徑
                        $spreadsheet_1->setCellValue('D' . ($index+$i), $item->pipeline_size);
                        // 檢測位置簡述
                        $_m = str_starts_with($item->film_no, 'D')? '滯留區' : '';
                        $_m = strlen($_m) > 0? $_m : (str_starts_with($item->film_no, 'L')? '水平長管' : '');
                        $_m = strlen($_m) > 0? $_m : (str_starts_with($item->film_no, 'E')? '彎頭' : '');
                        $_m = strlen($_m) > 0? $_m : (str_starts_with($item->film_no, 'T')? '三通' : '');
                        $_m = strlen($_m) > 0? $_m : (str_starts_with($item->film_no, 'R')? '漸縮管' : '');
                        $_m = strlen($_m) > 0? $_m : (str_starts_with($item->film_no, 'IP')? '穿透保溫' : '');
                        $_m = strlen($_m) > 0? $_m : (str_starts_with($item->film_no, 'IV')? '垂直管' : '');
                        $_m = strlen($_m) > 0? $_m : (str_starts_with($item->film_no, 'IE')? '保溫端點' : '');
                        $_m = strlen($_m) > 0? $_m : (str_starts_with($item->film_no, 'IL')? '水平低點' : '');
                        
                        $spreadsheet_1->setCellValue('E' . ($index+$i), $_m);
                        // 底片尺寸
                        $spreadsheet_1->setCellValue('F' . ($index+$i), $item->film_size);
                        // 部件材質
                        $spreadsheet_1->setCellValue('G' . ($index+$i), $item->material);
                        // 公稱厚度
                        $spreadsheet_1->setCellValue('H' . ($index+$i), $item->n_thickness);
                        // 剩餘厚度
                        $spreadsheet_1->setCellValue('I' . ($index+$i), $item->le_thickness);
                        // 減薄率
                        $a = (($item->n_thickness - $item->le_thickness) / $item->n_thickness) * 100;
                        $a = $a < 0? '0' : $a;
                        $spreadsheet_1->setCellValue('J' . ($index+$i), number_format($a, 2) . '%');
                        // 腐蝕內
                        $_r = $tmpReport->corrosion;
                        $spreadsheet_1->setCellValue('K' . ($index+$i), in_array(0, $_r)? '✓':'');
                        // 腐蝕外
                        $spreadsheet_1->setCellValue('L' . ($index+$i), in_array(1, $_r)? '✓':'');
                        // 腐蝕CUI
                        $spreadsheet_1->setCellValue('M' . ($index+$i), in_array(2, $_r)? '✓':'');
                        // 缺陷描述
                        $spreadsheet_1->setCellValue('N' . ($index+$i), $item->defect_descript);
                        // 結構最小厚度(mm)
                        $_o = $item->film_size < 0.5? '1' :'';
                        $_o = strlen($_o) > 0? $_o : ($item->film_size < 3? '1.8' : '');
                        $_o = strlen($_o) > 0? $_o : ($item->film_size < 4? '2' : '');
                        $_o = strlen($_o) > 0? $_o : ($item->film_size < 6? '2.3' : '');
                        $_o = strlen($_o) > 0? $_o : ($item->film_size < 20? '2.8' : '');
                        $_o = strlen($_o) > 0? $_o : ($item->film_size >= 20? '3.1' : '');
                        $spreadsheet_1->setCellValue('O' . ($index+$i), $_o);
                        // 備註
                        $spreadsheet_1->setCellValue('P' . ($index+$i), $item->remark);
                        // 安裝日期
                        $spreadsheet_1->setCellValue('Q' . ($index+$i), $item->exce_date);
                        // 檢查日
                        $spreadsheet_1->setCellValue('R' . ($index+$i), $reporter->exam_date);
                        // 使用年限
                        $_s = Carbon::parse($item->exce_date)->floatDiffInYears($reporter->exam_date);
                        $spreadsheet_1->setCellValue('S' . ($index+$i), number_format($_s, 1));
                        // 腐蝕率
                        $_t = $a == 0? 0 : (($item->n_thickness - $item->le_thickness) / $_s);
                        $spreadsheet_1->setCellValue('T' . ($index+$i), number_format($_t, 3));
                        // 剩餘壽命
                        $_u = $a == 0? '>20' : (($item->le_thickness < $_o)? '0' : (($item->le_thickness - $_o) / $_t < 20? ($item->le_thickness - $_o) / $_t : '>20'));
                        $spreadsheet_1->setCellValue('U' . ($index+$i), number_format($_u, 2));

                        // 匡線繪製
                        foreach($sheet1CellNames as $nameValue) {
                            $spreadsheet_1->getStyle($nameValue . ($index+$i))->getBorders()->getTop()->setBorderStyle('thin');
                            $spreadsheet_1->getStyle($nameValue . ($index+$i))->getBorders()->getLeft()->setBorderStyle('thin');
                            $spreadsheet_1->getStyle($nameValue . ($index+$i))->getBorders()->getRight()->setBorderStyle('thin');
                            $spreadsheet_1->getStyle($nameValue . ($index+$i))->getBorders()->getBottom()->setBorderStyle('thin');
                            $spreadsheet_1->getStyle($nameValue . ($index+$i))->applyFromArray(ExcelConfig::fpgOilSheet1ListStyle());
                        }

                        $spreadsheet_2->getStyle((($index2+0)+($i*$index2_M)))->applyFromArray(ExcelConfig::fpgOilSheet2Line4Style());
                        $spreadsheet_2->getRowDimension((($index2+0)+($i*$index2_M)))->setRowHeight(29, 'pt'); //高度
                        $spreadsheet_2->getStyle((($index2+1)+($i*$index2_M)))->applyFromArray(ExcelConfig::fpgOilSheet2Line5Style());
                        $spreadsheet_2->getRowDimension((($index2+1)+($i*$index2_M)))->setRowHeight(29, 'pt'); //高度
                        $spreadsheet_2->getStyle((($index2+20)+($i*$index2_M)))->applyFromArray(ExcelConfig::fpgOlllSheet2Line24Style());
                        $spreadsheet_2->getRowDimension((($index2+20)+($i*$index2_M)))->setRowHeight(29, 'pt'); //高度
                        $spreadsheet_2->getStyle((($index2+21)+($i*$index2_M)))->applyFromArray(ExcelConfig::fpgOilSheet2Line25Style());
                        $spreadsheet_2->getRowDimension((($index2+21)+($i*$index2_M)))->setRowHeight(29, 'pt'); //高度

                        $spreadsheet_2->setCellValue('A' . (($index2+0)+($i*$index2_M)), '序號：' . (string)($i + 1));
                        // $spreadsheet_2->getStyle('A' . (($index2+0)+($i*$index2_M)))->applyFromArray(ExcelConfig::fpgOilSheet2Line24Style());
                        $spreadsheet_2->getStyle('A' . (($index2+0)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+0)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+0)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+0)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->setCellValue('B' . (($index2+0)+($i*$index2_M)), '迴路編號：');
                        $spreadsheet_2->setCellValue('C' . (($index2+0)+($i*$index2_M)), $tmpReport->equlpment_no);
                        $spreadsheet_2->mergeCells('C' . (($index2+0)+($i*$index2_M)) . ':E' . (($index2+0)+($i*$index2_M)));
                        // $spreadsheet_2->getStyle('B' . (($index2+0)+($i*$index2_M)) . ':G' . (($index2+0)+($i*$index2_M)))->getAlignment()->setHorizontal('right');
                        $spreadsheet_2->getStyle('B' . (($index2+0)+($i*$index2_M)) . ':E' . (($index2+0)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+0)+($i*$index2_M)) . ':E' . (($index2+0)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+0)+($i*$index2_M)) . ':E' . (($index2+0)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+0)+($i*$index2_M)) . ':E' . (($index2+0)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        $spreadsheet_2->mergeCells('F' . (($index2+0)+($i*$index2_M)) . ':G' . (($index2+0)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('F' . (($index2+0)+($i*$index2_M)), '設備/管線編號：');
                        $spreadsheet_2->mergeCells('H' . (($index2+0)+($i*$index2_M)) . ':L' . (($index2+0)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('H' . (($index2+0)+($i*$index2_M)), $item->pipeline_no);
                        $spreadsheet_2->getStyle('H' . (($index2+0)+($i*$index2_M)))->getAlignment()->setHorizontal('left');
                        $spreadsheet_2->getStyle('F' . (($index2+0)+($i*$index2_M)) . ':L' . (($index2+0)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+0)+($i*$index2_M)) . ':L' . (($index2+0)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+0)+($i*$index2_M)) . ':L' . (($index2+0)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+0)+($i*$index2_M)) . ':L' . (($index2+0)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        

                        // 圖片標題
                        $spreadsheet_2->mergeCells('A' . (($index2+1)+($i*$index2_M)) . ':F' . (($index2+1)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('A' . (($index2+1)+($i*$index2_M)), 'A. 施照位置外觀照片');
                        $spreadsheet_2->getStyle('A' . (($index2+1)+($i*$index2_M)) . ':F' . (($index2+1)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+1)+($i*$index2_M)) . ':F' . (($index2+1)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+1)+($i*$index2_M)) . ':F' . (($index2+1)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+1)+($i*$index2_M)) . ':F' . (($index2+1)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        

                        $spreadsheet_2->mergeCells('G' . (($index2+1)+($i*$index2_M)) . ':L' . (($index2+1)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('G' . (($index2+1)+($i*$index2_M)), 'B. RT檢測片');
                        $spreadsheet_2->getStyle('G' . (($index2+1)+($i*$index2_M)) . ':L' . (($index2+1)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+1)+($i*$index2_M)) . ':L' . (($index2+1)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+1)+($i*$index2_M)) . ':L' . (($index2+1)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+1)+($i*$index2_M)) . ':L' . (($index2+1)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        // 圖片欄位
                        $spreadsheet_2->mergeCells('A' . (($index2+2)+($i*$index2_M)) . ':F' . (($index2+19)+($i*$index2_M)));
                        $spreadsheet_2->getStyle('A' . (($index2+2)+($i*$index2_M)) . ':F' . (($index2+19)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+2)+($i*$index2_M)) . ':F' . (($index2+19)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+2)+($i*$index2_M)) . ':F' . (($index2+19)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+2)+($i*$index2_M)) . ':F' . (($index2+19)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        $spreadsheet_2->mergeCells('G' . (($index2+2)+($i*$index2_M)) . ':L' . (($index2+19)+($i*$index2_M)));
                        $spreadsheet_2->getStyle('G' . (($index2+2)+($i*$index2_M)) . ':L' . (($index2+19)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+2)+($i*$index2_M)) . ':L' . (($index2+19)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+2)+($i*$index2_M)) . ':L' . (($index2+19)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+2)+($i*$index2_M)) . ':L' . (($index2+19)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        //表格標題
                        $spreadsheet_2->setCellValue('A' . (($index2+20)+($i*$index2_M)), '底片編號');
                        $spreadsheet_2->getStyle('A' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        $spreadsheet_2->setCellValue('B' . (($index2+20)+($i*$index2_M)), '公稱厚度');
                        $spreadsheet_2->getStyle('B' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->setCellValue('C' . (($index2+20)+($i*$index2_M)), '剩餘厚度');
                        $spreadsheet_2->getStyle('C' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->setCellValue('D' . (($index2+20)+($i*$index2_M)), '減薄率');
                        $spreadsheet_2->getStyle('D' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->setCellValue('E' . (($index2+20)+($i*$index2_M)), '內腐蝕');
                        $spreadsheet_2->getStyle('E' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('E' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('E' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('E' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        // $spreadsheet_2->mergeCells('F' . (($index2+20)+($i*$index2_M)) . ':G' . (($index2+20)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('F' . (($index2+20)+($i*$index2_M)), '外腐蝕');
                        $spreadsheet_2->getStyle('F' . (($index2+20)+($i*$index2_M)) . ':F' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+20)+($i*$index2_M)) . ':F' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+20)+($i*$index2_M)) . ':F' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+20)+($i*$index2_M)) . ':F' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->setCellValue('G' . (($index2+20)+($i*$index2_M)), 'CUI');
                        $spreadsheet_2->getStyle('G' . (($index2+20)+($i*$index2_M)) . ':G' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+20)+($i*$index2_M)) . ':G' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+20)+($i*$index2_M)) . ':G' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+20)+($i*$index2_M)) . ':G' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->setCellValue('H' . (($index2+20)+($i*$index2_M)), '缺陷描述');
                        $spreadsheet_2->getStyle('H' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('H' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('H' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('H' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->mergeCells('I' . (($index2+20)+($i*$index2_M)) . ':K' . (($index2+20)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('I' . (($index2+20)+($i*$index2_M)), '建議處理方式');
                        $spreadsheet_2->getStyle('I' . (($index2+20)+($i*$index2_M)) . ':K' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+20)+($i*$index2_M)) . ':K' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+20)+($i*$index2_M)) . ':K' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+20)+($i*$index2_M)) . ':K' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        // $spreadsheet_2->mergeCells('L' . (($index2+20)+($i*$index2_M)) . ':M' . (($index2+20)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('L' . (($index2+20)+($i*$index2_M)), '施照日期');
                        $spreadsheet_2->getStyle('L' . (($index2+20)+($i*$index2_M)) . ':L' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('L' . (($index2+20)+($i*$index2_M)) . ':L' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('L' . (($index2+20)+($i*$index2_M)) . ':L' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('L' . (($index2+20)+($i*$index2_M)) . ':L' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        // 表格內文
                        // 底片編號
                        $spreadsheet_2->setCellValue('A' . (($index2+21)+($i*$index2_M)), $item->film_no);
                        $spreadsheet_2->getStyle('A' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        // 公稱厚度
                        $spreadsheet_2->setCellValue('B' . (($index2+21)+($i*$index2_M)), $item->n_thickness);
                        $spreadsheet_2->getStyle('B' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 剩餘厚度
                        $spreadsheet_2->setCellValue('C' . (($index2+21)+($i*$index2_M)), $item->le_thickness);
                        $spreadsheet_2->getStyle('C' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 減薄率
                        $a = (($item->n_thickness - $item->le_thickness) / $item->n_thickness) * 100;
                        $spreadsheet_2->setCellValue('D' . (($index2+21)+($i*$index2_M)), $a . '%');
                        $spreadsheet_2->getStyle('D' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 腐蝕內
                        $_r = explode(',', $tmpReport->corrosion);
                        $spreadsheet_2->setCellValue('E' . (($index2+21)+($i*$index2_M)), in_array(0, $_r)? "✓":'');
                        $spreadsheet_2->getStyle('E' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('E' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('E' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('E' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 腐蝕外
                        $spreadsheet_2->setCellValue('F' . (($index2+21)+($i*$index2_M)), in_array(1, $_r)? "✓":'');
                        $spreadsheet_2->getStyle('F' . (($index2+21)+($i*$index2_M)) . ':F' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+21)+($i*$index2_M)) . ':F' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+21)+($i*$index2_M)) . ':F' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+21)+($i*$index2_M)) . ':F' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // CUI
                        $spreadsheet_2->setCellValue('G' . (($index2+21)+($i*$index2_M)), in_array(1, $_r)? "✓":'');
                        $spreadsheet_2->getStyle('G' . (($index2+21)+($i*$index2_M)) . ':G' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+21)+($i*$index2_M)) . ':G' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+21)+($i*$index2_M)) . ':G' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+21)+($i*$index2_M)) . ':G' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        // 缺陷描述
                        $spreadsheet_2->setCellValue('H' . (($index2+21)+($i*$index2_M)), $item->defect_descript);
                        $spreadsheet_2->getStyle('H' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('H' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('H' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('H' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 建議處理方式
                        $spreadsheet_2->mergeCells('I' . (($index2+21)+($i*$index2_M)) . ':K' . (($index2+21)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('I' . (($index2+21)+($i*$index2_M)), $item->suggested_handling);
                        $spreadsheet_2->getStyle('I' . (($index2+21)+($i*$index2_M)) . ':K' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+21)+($i*$index2_M)) . ':K' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+21)+($i*$index2_M)) . ':K' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+21)+($i*$index2_M)) . ':K' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 施照日期
                        $spreadsheet_2->mergeCells('L' . (($index2+21)+($i*$index2_M)) . ':L' . (($index2+21)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('L' . (($index2+21)+($i*$index2_M)), $item->exce_date);
                        $spreadsheet_2->getStyle('L' . (($index2+21)+($i*$index2_M)) . ':L' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('L' . (($index2+21)+($i*$index2_M)) . ':L' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('L' . (($index2+21)+($i*$index2_M)) . ':L' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('L' . (($index2+21)+($i*$index2_M)) . ':L' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        $i++;
                    }
                }
            }
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(Storage::disk('export')->path($copyPath));
        }
    }

    public function parseReportFpgRT_Oil($function_path, $id)
    {
        $out = [];
        $sample = $this->getFpgRT_Oil();
        $reporter = $this->dispstchRepo->funPathDispatch($function_path, $id);
        
        $realPath = $sample->full_path;
        $serial = Carbon::now()->format('Ymd');
        $copyPath = 'fpg/'.$serial.'/rt/XID-RT-' . $serial .'-' . str_pad($id, 5, "0", STR_PAD_LEFT) . '.xlsx';
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
            /* dispatch */
            // 客戶
            $spreadsheet_0->setCellValue('B4', $reporter->customer);
            // 檢測日期
            $spreadsheet_0->setCellValue('F4', $reporter->exam_date);
            // 工程名稱
            $spreadsheet_0->setCellValue('B6', $reporter->job_name);

            /* report_rt */
            if(isset($reporter->report_rtoil)) {
                $tmpReport = $reporter->report_rtoil;
                // 工程編號
                $spreadsheet_0->setCellValue('B8', $tmpReport->job_no);
                // 設備編號 
                $spreadsheet_0->setCellValue('B10', $tmpReport->equlpment_no);
                // 檢測地點
                $spreadsheet_0->setCellValue('F6', $tmpReport->exam_place);
                // 材質及厚度
                $spreadsheet_0->setCellValue('F8', $tmpReport->thicknes);
                // 檢測規範
                // $spreadsheet_0->setCellValue('F10', $tmpReport->customer);
                //  底片型號 
                $spreadsheet_0->setCellValue('F13', $tmpReport->film_brand);
                //  檢測儀器廠牌 
                $spreadsheet_0->setCellValue('B13', $tmpReport->sn);
                //  射源強度 
                $spreadsheet_0->setCellValue('F15', $tmpReport->energy_strength . ' Ci');
                //  射源類別 
                $radiationEnergy = '';
                if($tmpReport->radiation_energy == 0) {
                    $radiationEnergy = '▓Ir-192      □CO-60                     □X-Ray       □se-75';
                } else if($tmpReport->radiation_energy == 1) {
                    $radiationEnergy = '□Ir-192      ▓CO-60                     □X-Ray       □se-75';
                } else if($tmpReport->radiation_energy == 2) {
                    $radiationEnergy = '□Ir-192      □CO-60                     ▓X-Ray       □se-75';
                } else if($tmpReport->radiation_energy == 3) {
                    $radiationEnergy = '□Ir-192      □CO-60                     □X-Ray       ▓se-75';
                }
                $spreadsheet_0->setCellValue('B15', $radiationEnergy);

                //底片大小 Film Size
                $film_size1 = "";
                $film_size2 = "";
                $listFilmSize = ['10inX12in','8inX10in'];
                $r_report_size = explode(',', $tmpReport->film_size);
                if(is_array($r_report_size)) {
                    for($i = 0; $i < sizeof($listFilmSize); $i++) {
                        if(in_array($i, $r_report_size)) {
                            if($i == 0)
                            $film_size1 .= ('▓' . $listFilmSize[$i] . " ");
                            else if($i == 1)
                            $film_size2 .= ('▓' . $listFilmSize[$i] . " ");
                        } else {
                            if($i == 0)
                            $film_size1 .= ('□' . $listFilmSize[$i] . " ");
                            else if($i == 1)
                            $film_size2 .= ('□' . $listFilmSize[$i] . " ");
                        }
                    }
                }
                $spreadsheet_0->setCellValue('F17', $film_size1);
                $spreadsheet_0->setCellValue('F18', $film_size2);
                // 焦點尺寸 FOCUS SIZE
                $spreadsheet_0->setCellValue('B17', $tmpReport->focal_spot . 'mm');

                //底片黑度範圍 sin_dou
                $sin_dou1 = "";
                $sin_dou2 = "";
                $listSinDou= ['單片SINGLE1.8-4.0','雙片DOUBLE'];
                $r_sin_dou = explode(',', $tmpReport->sin_dou);
                if(is_array($r_sin_dou)) {
                    for($i = 0; $i < sizeof($listSinDou); $i++) {
                        if(in_array($i, $r_sin_dou)) {
                            if($i == 0)
                            $sin_dou1 .= ('▓' . $listSinDou[$i] . " ");
                            else if($i == 1)
                            $sin_dou2 .= ('▓' . $listSinDou[$i] . " ");
                        } else {
                            if($i == 0)
                            $sin_dou1 .= ('□' . $listSinDou[$i] . " ");
                            else if($i == 1)
                            $sin_dou2 .= ('□' . $listSinDou[$i] . " ");
                        }
                    }
                }
                $spreadsheet_0->setCellValue('B19', $sin_dou1);
                $spreadsheet_0->setCellValue('B20', $sin_dou2);

                //增感屏厚度 screen_thick
                $screen_thick1 = "";
                $screen_thick2 = "";
                $listScreenThick= ['前FRONT 鉛Pb-0.005','後BACK  鉛Pb-0.010'];
                $r_screen_thick = explode(',', $tmpReport->screen_thick);
                if(is_array($r_screen_thick)) {
                    for($i = 0; $i < sizeof($listScreenThick); $i++) {
                        if(in_array($i, $r_screen_thick)) {
                            if($i == 0)
                            $screen_thick1 .= ('▓' . $listScreenThick[$i] . " ");
                            else if($i == 1)
                            $screen_thick2 .= ('▓' . $listScreenThick[$i] . " ");
                        } else {
                            if($i == 0)
                            $screen_thick1 .= ('□' . $listScreenThick[$i] . " ");
                            else if($i == 1)
                            $screen_thick2 .= ('□' . $listScreenThick[$i] . " ");
                        }
                    }
                }
                $spreadsheet_0->setCellValue('B21', $screen_thick1);
                $spreadsheet_0->setCellValue('B22', $screen_thick2);

                //判片方式 interpret
                $interpret1 = "";
                $interpret2 = "";
                $listInterpret= ['單壁判讀SINGLE WALL','雙壁判讀DOUBLE WALL'];
                $r_interpret = explode(',', $tmpReport->interpret);
                if(is_array($r_interpret)) {
                    for($i = 0; $i < sizeof($listInterpret); $i++) {
                        if(in_array($i, $r_interpret)) {
                            if($i == 0)
                            $interpret1 .= ('▓' . $listInterpret[$i] . " ");
                            else if($i == 1)
                            $interpret2 .= ('▓' . $listInterpret[$i] . " ");
                        } else {
                            if($i == 0)
                            $interpret1 .= ('□' . $listInterpret[$i] . " ");
                            else if($i == 1)
                            $interpret2 .= ('□' . $listInterpret[$i] . " ");
                        }
                    }
                }
                $spreadsheet_0->setCellValue('F21', $interpret2);
                $spreadsheet_0->setCellValue('F22', $interpret1);

                //檢測人員  obsert
                $spreadsheet_0->setCellValue('B36', $tmpReport->obsert);

                //審查  Approved By
                $spreadsheet_0->setCellValue('F36', "");

                $spreadsheet_1 = $spreadsheet->getSheet(1);
                $spreadsheet_2 = $spreadsheet->getSheet(2);

                $spreadsheet_2->setCellValue('A2', '工程名稱：'. $tmpReport->job_name);
                $spreadsheet_2->setCellValue('A3', '工程編號：'. $tmpReport->job_no);

                $items = json_decode($tmpReport->items);
                $sheet1CellNames = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'];
                if(is_array($items)) {
                    $i = 0; $index = 9; $index2 = 4; $index2_M = 22;
                    foreach($items as $item) {
                        // 項次
                        $spreadsheet_1->setCellValue('A' . ($index+$i), (string)($i + 1));
                        // 底片編號
                        $spreadsheet_1->setCellValue('B' . ($index+$i), $item->film_no);
                        // 管徑
                        $spreadsheet_1->setCellValue('C' . ($index+$i), $item->pipeline_size);
                        // 公稱厚度
                        $spreadsheet_1->setCellValue('D' . ($index+$i), $item->n_thickness);
                        // 剩餘厚度
                        $spreadsheet_1->setCellValue('E' . ($index+$i), $item->le_thickness);
                        // 減薄率
                        $a = ($item->n_thickness - $item->le_thickness / $item->n_thickness) * 100;
                        $spreadsheet_1->setCellValue('F' . ($index+$i), $a . '%');
                        // 腐蝕內
                        $_r = explode(',', $tmpReport->corrosion);
                        $spreadsheet_1->setCellValue('G' . ($index+$i), in_array(0, $_r)? '✓':'');
                        // 腐蝕外
                        $spreadsheet_1->setCellValue('H' . ($index+$i), in_array(1, $_r)? '✓':'');
                        // 缺陷描述
                        $spreadsheet_1->setCellValue('I' . ($index+$i), $item->defect_descript);
                        // 建議處理方式
                        $spreadsheet_1->setCellValue('J' . ($index+$i), $item->suggested_handling);
                        // 施照日期
                        $spreadsheet_1->setCellValue('K' . ($index+$i), $item->exce_date);
                        // 備註
                        $spreadsheet_1->setCellValue('L' . ($index+$i), $item->remark);
                        // 本單編號
                        $spreadsheet_1->setCellValue('M' . ($index+$i), $item->exce_no);
                        // 異常編號
                        $spreadsheet_1->setCellValue('N' . ($index+$i), $item->unusual_no);
                        // 是否管制追蹤
                        $spreadsheet_1->setCellValue('O' . ($index+$i), $item->result == 0? '是':'');

                        // 匡線繪製
                        foreach($sheet1CellNames as $nameValue) {
                            $spreadsheet_1->getStyle($nameValue . ($index+$i))->getBorders()->getTop()->setBorderStyle('thin');
                            $spreadsheet_1->getStyle($nameValue . ($index+$i))->getBorders()->getLeft()->setBorderStyle('thin');
                            $spreadsheet_1->getStyle($nameValue . ($index+$i))->getBorders()->getRight()->setBorderStyle('thin');
                            $spreadsheet_1->getStyle($nameValue . ($index+$i))->getBorders()->getBottom()->setBorderStyle('thin');
                            $spreadsheet_1->getStyle($nameValue . ($index+$i))->applyFromArray(ExcelConfig::fpgOilSheet1ListStyle());
                        }

                        $spreadsheet_2->getStyle((($index2+0)+($i*$index2_M)))->applyFromArray(ExcelConfig::fpgOilSheet2Line4Style());
                        $spreadsheet_2->getRowDimension((($index2+0)+($i*$index2_M)))->setRowHeight(29, 'pt'); //高度
                        $spreadsheet_2->getStyle((($index2+1)+($i*$index2_M)))->applyFromArray(ExcelConfig::fpgOilSheet2Line5Style());
                        $spreadsheet_2->getRowDimension((($index2+1)+($i*$index2_M)))->setRowHeight(29, 'pt'); //高度
                        $spreadsheet_2->getStyle((($index2+20)+($i*$index2_M)))->applyFromArray(ExcelConfig::fpgOilSheet2Line24Style());
                        $spreadsheet_2->getRowDimension((($index2+20)+($i*$index2_M)))->setRowHeight(29, 'pt'); //高度
                        $spreadsheet_2->getStyle((($index2+21)+($i*$index2_M)))->applyFromArray(ExcelConfig::fpgOilSheet2Line25Style());
                        $spreadsheet_2->getRowDimension((($index2+21)+($i*$index2_M)))->setRowHeight(29, 'pt'); //高度

                        $spreadsheet_2->setCellValue('A' . (($index2+0)+($i*$index2_M)), '序號：' . (string)($i + 1));
                        // $spreadsheet_2->getStyle('A' . (($index2+0)+($i*$index2_M)))->applyFromArray(ExcelConfig::fpgOilSheet2Line24Style());
                        $spreadsheet_2->getStyle('A' . (($index2+0)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+0)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+0)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+0)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->mergeCells('B' . (($index2+0)+($i*$index2_M)) . ':G' . (($index2+0)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('B' . (($index2+0)+($i*$index2_M)), '編號/管號：');
                        $spreadsheet_2->getStyle('B' . (($index2+0)+($i*$index2_M)) . ':G' . (($index2+0)+($i*$index2_M)))->getAlignment()->setHorizontal('right');
                        $spreadsheet_2->getStyle('B' . (($index2+0)+($i*$index2_M)) . ':G' . (($index2+0)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+0)+($i*$index2_M)) . ':G' . (($index2+0)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+0)+($i*$index2_M)) . ':G' . (($index2+0)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+0)+($i*$index2_M)) . ':G' . (($index2+0)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        $spreadsheet_2->mergeCells('H' . (($index2+0)+($i*$index2_M)) . ':M' . (($index2+0)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('H' . (($index2+0)+($i*$index2_M)), $item->film_no);
                        $spreadsheet_2->getStyle('H' . (($index2+0)+($i*$index2_M)))->getAlignment()->setHorizontal('left');
                        $spreadsheet_2->getStyle('B' . (($index2+0)+($i*$index2_M)) . ':M' . (($index2+0)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+0)+($i*$index2_M)) . ':M' . (($index2+0)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+0)+($i*$index2_M)) . ':M' . (($index2+0)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+0)+($i*$index2_M)) . ':M' . (($index2+0)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        

                        // 圖片標題
                        $spreadsheet_2->mergeCells('A' . (($index2+1)+($i*$index2_M)) . ':F' . (($index2+1)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('A' . (($index2+1)+($i*$index2_M)), 'A. 施照位置外觀照片');
                        $spreadsheet_2->getStyle('A' . (($index2+1)+($i*$index2_M)) . ':F' . (($index2+1)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+1)+($i*$index2_M)) . ':F' . (($index2+1)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+1)+($i*$index2_M)) . ':F' . (($index2+1)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+1)+($i*$index2_M)) . ':F' . (($index2+1)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        

                        $spreadsheet_2->mergeCells('G' . (($index2+1)+($i*$index2_M)) . ':M' . (($index2+1)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('G' . (($index2+1)+($i*$index2_M)), 'B. RT檢測片');
                        $spreadsheet_2->getStyle('G' . (($index2+1)+($i*$index2_M)) . ':M' . (($index2+1)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+1)+($i*$index2_M)) . ':M' . (($index2+1)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+1)+($i*$index2_M)) . ':M' . (($index2+1)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+1)+($i*$index2_M)) . ':M' . (($index2+1)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        // 圖片欄位
                        $spreadsheet_2->mergeCells('A' . (($index2+2)+($i*$index2_M)) . ':F' . (($index2+19)+($i*$index2_M)));
                        $spreadsheet_2->getStyle('A' . (($index2+2)+($i*$index2_M)) . ':F' . (($index2+19)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+2)+($i*$index2_M)) . ':F' . (($index2+19)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+2)+($i*$index2_M)) . ':F' . (($index2+19)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+2)+($i*$index2_M)) . ':F' . (($index2+19)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        $spreadsheet_2->mergeCells('G' . (($index2+2)+($i*$index2_M)) . ':M' . (($index2+19)+($i*$index2_M)));
                        $spreadsheet_2->getStyle('G' . (($index2+2)+($i*$index2_M)) . ':M' . (($index2+19)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+2)+($i*$index2_M)) . ':M' . (($index2+19)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+2)+($i*$index2_M)) . ':M' . (($index2+19)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('G' . (($index2+2)+($i*$index2_M)) . ':M' . (($index2+19)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        //表格標題
                        $spreadsheet_2->setCellValue('A' . (($index2+20)+($i*$index2_M)), '底片編號');
                        $spreadsheet_2->getStyle('A' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        $spreadsheet_2->setCellValue('B' . (($index2+20)+($i*$index2_M)), '公稱厚度');
                        $spreadsheet_2->getStyle('B' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->setCellValue('C' . (($index2+20)+($i*$index2_M)), '剩餘厚度');
                        $spreadsheet_2->getStyle('C' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->setCellValue('D' . (($index2+20)+($i*$index2_M)), '減薄率');
                        $spreadsheet_2->getStyle('D' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->setCellValue('E' . (($index2+20)+($i*$index2_M)), '內腐蝕');
                        $spreadsheet_2->getStyle('E' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('E' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('E' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('E' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->mergeCells('F' . (($index2+20)+($i*$index2_M)) . ':G' . (($index2+20)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('F' . (($index2+20)+($i*$index2_M)), '外腐蝕');
                        $spreadsheet_2->getStyle('F' . (($index2+20)+($i*$index2_M)) . ':G' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+20)+($i*$index2_M)) . ':G' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+20)+($i*$index2_M)) . ':G' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+20)+($i*$index2_M)) . ':G' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->setCellValue('H' . (($index2+20)+($i*$index2_M)), '缺陷描述');
                        $spreadsheet_2->getStyle('H' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('H' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('H' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('H' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->mergeCells('I' . (($index2+20)+($i*$index2_M)) . ':K' . (($index2+20)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('I' . (($index2+20)+($i*$index2_M)), '建議處理方式');
                        $spreadsheet_2->getStyle('I' . (($index2+20)+($i*$index2_M)) . ':K' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+20)+($i*$index2_M)) . ':K' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+20)+($i*$index2_M)) . ':K' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+20)+($i*$index2_M)) . ':K' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');

                        $spreadsheet_2->mergeCells('L' . (($index2+20)+($i*$index2_M)) . ':M' . (($index2+20)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('L' . (($index2+20)+($i*$index2_M)), '施照日期');
                        $spreadsheet_2->getStyle('L' . (($index2+20)+($i*$index2_M)) . ':M' . (($index2+20)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('L' . (($index2+20)+($i*$index2_M)) . ':M' . (($index2+20)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('L' . (($index2+20)+($i*$index2_M)) . ':M' . (($index2+20)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('L' . (($index2+20)+($i*$index2_M)) . ':M' . (($index2+20)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        // 表格內文
                        // 底片編號
                        $spreadsheet_2->setCellValue('A' . (($index2+21)+($i*$index2_M)), $item->film_no);
                        $spreadsheet_2->getStyle('A' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('A' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        
                        // 公稱厚度
                        $spreadsheet_2->setCellValue('B' . (($index2+21)+($i*$index2_M)), $item->n_thickness);
                        $spreadsheet_2->getStyle('B' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('B' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 剩餘厚度
                        $spreadsheet_2->setCellValue('C' . (($index2+21)+($i*$index2_M)), $item->le_thickness);
                        $spreadsheet_2->getStyle('C' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('C' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 減薄率
                        $a = ($item->n_thickness - $item->le_thickness / $item->n_thickness) * 100;
                        $spreadsheet_2->setCellValue('D' . (($index2+21)+($i*$index2_M)), $a . '%');
                        $spreadsheet_2->getStyle('D' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('D' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 腐蝕內
                        $_r = explode(',', $tmpReport->corrosion);
                        $spreadsheet_2->setCellValue('E' . (($index2+21)+($i*$index2_M)), in_array(0, $_r)? "✓":'');
                        $spreadsheet_2->getStyle('E' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('E' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('E' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('E' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 腐蝕外
                        $spreadsheet_2->setCellValue('F' . (($index2+21)+($i*$index2_M)), in_array(1, $_r)? "✓":'');
                        $spreadsheet_2->mergeCells('F' . (($index2+21)+($i*$index2_M)) . ':G' . (($index2+21)+($i*$index2_M)));
                        $spreadsheet_2->getStyle('F' . (($index2+21)+($i*$index2_M)) . ':G' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+21)+($i*$index2_M)) . ':G' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+21)+($i*$index2_M)) . ':G' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('F' . (($index2+21)+($i*$index2_M)) . ':G' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 缺陷描述
                        $spreadsheet_2->setCellValue('H' . (($index2+21)+($i*$index2_M)), $item->defect_descript);
                        $spreadsheet_2->getStyle('H' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('H' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('H' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('H' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 建議處理方式
                        $spreadsheet_2->mergeCells('I' . (($index2+21)+($i*$index2_M)) . ':K' . (($index2+21)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('I' . (($index2+21)+($i*$index2_M)), $item->suggested_handling);
                        $spreadsheet_2->getStyle('I' . (($index2+21)+($i*$index2_M)) . ':K' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+21)+($i*$index2_M)) . ':K' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+21)+($i*$index2_M)) . ':K' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('I' . (($index2+21)+($i*$index2_M)) . ':K' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        // 施照日期
                        $spreadsheet_2->mergeCells('L' . (($index2+21)+($i*$index2_M)) . ':M' . (($index2+21)+($i*$index2_M)));
                        $spreadsheet_2->setCellValue('L' . (($index2+21)+($i*$index2_M)), $item->exce_date);
                        $spreadsheet_2->getStyle('L' . (($index2+21)+($i*$index2_M)) . ':M' . (($index2+21)+($i*$index2_M)))->getBorders()->getTop()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('L' . (($index2+21)+($i*$index2_M)) . ':M' . (($index2+21)+($i*$index2_M)))->getBorders()->getLeft()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('L' . (($index2+21)+($i*$index2_M)) . ':M' . (($index2+21)+($i*$index2_M)))->getBorders()->getRight()->setBorderStyle('thin');
                        $spreadsheet_2->getStyle('L' . (($index2+21)+($i*$index2_M)) . ':M' . (($index2+21)+($i*$index2_M)))->getBorders()->getBottom()->setBorderStyle('thin');
                        $i++;
                    }
                }
                unset($tmpReport);
            }

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(Storage::disk('export')->path($copyPath));
        }
    }
}