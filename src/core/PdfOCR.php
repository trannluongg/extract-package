<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 17/4/2020
 * Time: 1:51 PM
 */

namespace Luongtv\Extract\core;

class PdfOCR
{
    public function generatePdfOCR($path_pdf = '', $path_pdf_ocr = '')
    {
        if (!file_exists($path_pdf)) throw new \Exception('File do not exit');
        return exec('ocrmypdf -l eng+vie --remove-background --deskew ' . $path_pdf . ' ' . $path_pdf_ocr);
    }
}