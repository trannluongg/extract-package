<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 23/2/2020
 * Time: 9:37 PM
 */

namespace Luongtv\Extract\Http\Controllers;

use App\Http\Controllers\Controller;
use Luongtv\Extract\core\DocToPdf;
use Luongtv\Extract\core\HtmlToPdf;
use Luongtv\Extract\core\PdfToHtml;

class ExtractController extends Controller
{
    public function convertDocToPdf(){
        $doc = new DocToPdf();
        $doc->generatePDF('test.doc', 'test.pdf');
        exit('Convert Doc to Pdf successfully');
    }
    public function extract()
    {
        $name_file = 'test';
        $file = $name_file.'.pdf';

        $name_folder = uniqid();

        $options = [
            'pdftohtml_path' => 'path pdftohtml.exe',
            'pdfinfo_path' => 'path pdfinfo.exe',
            'generate' => [
                'singlePage' => false,
                'imageJpeg' => true,
                'ignoreImages' => false,
                'zoom' => 1.5 . ' -nodrm',
                'noFrames' => false,
            ],
            'clearAfter' => false,
            'outputDir' => storage_path('files/'.$name_folder),
        ];
        $pdf = new PdfToHtml($file, $options);
        $pdf->generateHTML($name_file, 'files/'.$name_folder);
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