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
use Luongtv\Extract\core\PdfOCR;
use Luongtv\Extract\core\PdfProtected;
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
        $name_file = 'cv7';
        $file = storage_path('cv/'.$name_file.'.pdf');

        $name_folder = uniqid();

        $options_check = [
            'pdftohtml_path' => 'pdftohtml.exe',
            'pdfinfo_path' => 'pdfinfo.exe',
            'generate' => [
                'singlePage' => false,
                'imageJpeg' => true,
                'ignoreImages' => false,
                'zoom' => 1.5 . ' -hidden -nodrm',
                'noFrames' => false,
            ],
            'clearAfter' => false
        ];
        $pdf = new PdfToHtml($file, $options_check);
        $checkPdf = $pdf->checkPdf();
        if ($checkPdf)
        {
            $pdfOCR = new PdfOCR();
            $path_pdfOCR = storage_path('cv/cv1.ocr.pdf');
            try
            {
                $pdfOCR->generatePdfOCR($file, $path_pdfOCR);
            } catch (\Exception $e)
            {
                echo $e;
            }
            $options_check['outputDir'] = storage_path('files/'.$name_folder);
            $pdfProtected = new PdfProtected($file, $options_check);
            $pdfProtected->pdfProtected('cv1.ocr', storage_path('files/'.$name_folder), true);
        }else{
            $options_check['outputDir'] = storage_path('files/'.$name_folder);
            $pdfProtected = new PdfProtected($file, $options_check);
            $pdfProtected->pdfProtected($name_file, storage_path('files/'.$name_folder));
        }
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