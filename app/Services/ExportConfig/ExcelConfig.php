<?php
namespace App\Services\ExportConfig;

class ExcelConfig
{
    public static function fpgOilSheet1ListStyle()
    {
        return array(  
            'font' => array(  
            //    'bold' => true,  
               'color'=>array(  
                   'rgb' => '000000',
               ),  
               'size' => '12',  
            ),  
            'alignment' => array(  
                'horizontal' => 'center',  
                'vertical' => 'center',  
            ),
        );
    }

    public static function fpgOilSheet2Line4Style()
    {
        return array(  
            'font' => array(  
            //    'bold' => true,  
               'color'=>array(  
                   'rgb' => '000000',
               ),  
               'size' => '14',  
            ),  
            'alignment' => array(  
                'horizontal' => 'center',  
                'vertical' => 'center',  
            ),
        );
    } 
    public static function fpgOilSheet2Line5Style()
    {
        return array(  
            'font' => array(  
            //    'bold' => true,  
               'color'=>array(  
                   'rgb' => '000000',
               ),  
               'size' => '14',  
            ),  
            'alignment' => array(  
                'horizontal' => 'center',  
                'vertical' => 'center',  
            ),  
        );
    } 

    public static function fpgOilSheet2Line24Style()
    {
        return array(  
            'font' => array(  
            //    'bold' => true,  
               'color'=>array(  
                   'rgb' => '000000',
               ),  
               'size' => '14',  
            ),  
            'alignment' => array(  
                'horizontal' => 'center',  
                'vertical' => 'center',  
            ),
            // 'borders' => array(
            //     'left' => array(
            //         'style' => "thin",
            //     ),
            //     'right' => array(
            //         'style' => "thin",
            //     ),
            //     'top' => array(
            //         'style' => "thin",
            //     ),
            //     'bottom' => array(
            //         'style' => "thin",
            //     )
            // )
        );
    }
    public static function fpgOlllSheet2Line24Style()
    {
        return array(  
            'font' => array(  
            //    'bold' => true,  
               'color'=>array(  
                   'rgb' => '000000',
               ),  
               'size' => '12',  
            ),  
            'alignment' => array(  
                'horizontal' => 'center',  
                'vertical' => 'center',  
            ),
            // 'borders' => array(
            //     'left' => array(
            //         'style' => "thin",
            //     ),
            //     'right' => array(
            //         'style' => "thin",
            //     ),
            //     'top' => array(
            //         'style' => "thin",
            //     ),
            //     'bottom' => array(
            //         'style' => "thin",
            //     )
            // )
        );
    }
    public static function fpgOilSheet2Line25Style()
    {
        return array(  
            'font' => array(  
            //    'bold' => true,  
               'color'=>array(  
                   'rgb' => '000000',
               ),  
               'size' => '12',  
            ),  
            'alignment' => array(  
                'horizontal' => 'center',  
                'vertical' => 'center',  
            ),
        );
    }

    public static function fpgThreeSheet1ListStyle()
    {
        return array(  
            'font' => array(  
                'italic' => false,
            ),  
            'alignment' => array(  
                'horizontal' => 'center',  
                'vertical' => 'center',  
            ),
        );
    }
}