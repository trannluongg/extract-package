<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 23/2/2020
 * Time: 9:37 PM
 */

namespace Luongtv\Extract\Http\Controllers;

use App\Http\Controllers\Controller;
use Luongtv\Extract\core\HtmlToPdf;
use Luongtv\Extract\core\PdfToHtml;

class ExtractController extends Controller
{
    public function extract()
    {
        $name_file = 'cv24';
        $file = storage_path("cv/".$name_file.".pdf");

        $id = uniqid();

        $options = [
            'pdftohtml_path' => 'C:\Users\ASUS\Desktop\poppler-0.68.0\bin\pdftohtml.exe',
            'pdfinfo_path' => 'C:\Users\ASUS\Desktop\poppler-0.68.0\bin\pdfinfo.exe',
            'generate' => [
                'singlePage' => false,
                'imageJpeg' => true,
                'ignoreImages' => false,
                'zoom' => 1.5 . ' -nodrm',
                'noFrames' => false,
            ],
            'clearAfter' => false,
            'outputDir' => storage_path('files/'.$id),
        ];
        $pdf = new PdfToHtml($file, $options);
        $pdf->generateHTML($name_file, $id);
    }

    public function generate(){
        $html = new HtmlToPdf();
        $file = '5e534622c3df8.html';
        $option = [
            'dpi' => 120
        ];
        $pdf_generate =  $html->generatePDF($file, $option);
        return $pdf_generate;
    }
}