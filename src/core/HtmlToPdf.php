<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 24/2/2020
 * Time: 10:50 AM
 */

namespace Luongtv\Extract\core;


use Illuminate\Support\Facades\App;

class HtmlToPdf
{
    /**
     * @param $path_file
     * @param array $options
     * @return mixed
     */
    public function generatePDF($path_file, $options = []){
        $file = storage_path($path_file);
        $file = file_get_contents($file);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($file);
        $pdf->setOptions($options);
        return $pdf->stream();
    }
}