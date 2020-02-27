<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 27/2/2020
 * Time: 9:59 AM
 */

namespace Luongtv\Extract\core;


use COM;

class DocToPdf
{
    /**
     * @param $path_doc
     * @param $path_pdf
     */
    public function generatePDF($path_doc, $path_pdf){
        $word = new COM("word.application") or die ("Could not initialise MS Word object.");
        $word->Visible = 0;
        $readOnly = true;
        $wdOpenFormatAuto = 0;
        $msoEncodingAutoDetect = 50001;
        $word->Documents->OpenNoRepairDialog($path_doc, false, $readOnly, false, "", "", true,"", "", $wdOpenFormatAuto, $msoEncodingAutoDetect);
        $word->ActiveDocument->ExportAsFixedFormat($path_pdf, 17, false, 0, 0, 0, 0, 7, true, true, 2, true, true, false);
        $word->Quit(false);
    }
}