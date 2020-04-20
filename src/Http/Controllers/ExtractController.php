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
        $name_file = 'cv8';
        $file = storage_path('cv/'.$name_file.'.pdf');

        $name_folder = uniqid();

        $output_dir = storage_path('files/'.$name_folder);
        $options_check = [
            'pdftohtml_path' => 'C:\Users\ASUS\Desktop\poppler-0.68.0\bin\pdftohtml.exe',
            'pdfinfo_path' => 'C:\Users\ASUS\Desktop\poppler-0.68.0\bin\pdfinfo.exe',
            'generate' => [
                'singlePage' => false,
                'imageJpeg' => true,
                'ignoreImages' => false,
                'zoom' => 1.5 . ' -hidden -nodrm',
                'noFrames' => false,
            ],
            'clearAfter' => false,
            'outputDir' => $output_dir
        ];
        $pdf = new PdfToHtml($file, $options_check);

        $checkPdf = $pdf->checkPdf($output_dir, $name_file);

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
            $pdfProtected = new PdfProtected($file, $options_check);
            $pdfProtected->pdfProtected('cv1.ocr', $output_dir, true);
        }else{
            $pdfProtected = new PdfProtected($file, $options_check);
            $pdfProtected->pdfProtected($name_file, $output_dir, true);
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