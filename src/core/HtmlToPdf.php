<?php
/**
 * Created by PhpStorm.
 * User: TranLuong
 * Date: 24/2/2020
 * Time: 10:50 AM
 */

namespace WorkableCV\Extract\core;


use Illuminate\Support\Facades\App;

class HtmlToPdf
{
    /**
     * @param $path_file
     * @param array $options
     * @return mixed
     */
    public function generatePDF($path_file, $options = [])
    {
        $file = file_get_contents($path_file);
        $pdf  = App::make('dompdf.wrapper');
        $pdf->loadHTML($file);
        $pdf->setOptions($options);
        return $pdf->stream();
    }
}
