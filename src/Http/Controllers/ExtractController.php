<?php
/**
 * Created by PhpStorm.
 * User: TranLuong
 * Date: 23/2/2020
 * Time: 9:37 PM
 */

namespace WorkableCV\Extract\Http\Controllers;

use App\Http\Controllers\Controller;
use WorkableCV\Extract\core\DocToPdf;
use WorkableCV\Extract\core\HtmlToPdf;
use WorkableCV\Extract\core\PdfOCR;
use WorkableCV\Extract\core\PdfProtected;
use WorkableCV\Extract\core\PdfToHtml;

class ExtractController extends Controller
{
    public function convertDocToPdf()
    {
        $doc = new DocToPdf();
        $result = $doc->generatePDF(storage_path('doc/cv23.docx'), storage_path('cv/cv100.pdf'));
        if (!$result) exit('Convert error. Try again');
        exit('Convert Doc to Pdf successfully');
    }

    public function extract()
    {
        $name_file = 'cv105';
        $file      = storage_path('cv1/' . $name_file . '.pdf');

        $options_check = config('extract.options_extract');

        $pdf           = new PdfToHtml($file, $options_check);

        $output_dir = config('extract.options_extract.outputDir');

        $checkPdf = $pdf->checkPdf($output_dir, $name_file);

        if ($checkPdf)
        {
            $pdfOCR = new PdfOCR();

            $result_pdfOCR = $pdfOCR->pdfOCR($file);

            if (!$result_pdfOCR) exit('File not convert. Try again');

            $pdfProtected = new PdfProtected($result_pdfOCR[1], $options_check);

            $pdfProtected->pdfProtected($result_pdfOCR[0], $output_dir, true);

        }
        else
        {
            $checkExistEmailPhone = $pdf->checkExitsEmailPhone($output_dir, $name_file);

            if (!$checkExistEmailPhone)
            {
                $pdfOCR = new PdfOCR();

                $result_pdfOCR = $pdfOCR->pdfOCR($file);

                if (!$result_pdfOCR) exit('File not convert. Try again');

                $pdfProtected = new PdfProtected($result_pdfOCR[1], $options_check);

                $pdfProtected->pdfProtected($result_pdfOCR[0], $output_dir, true, true);
            }
            else
            {
                $pdfProtected = new PdfProtected($file, $options_check);

                $pdfProtected->pdfProtected($name_file, $output_dir, false, false, 'doc');
            }
        }
    }

    public function generate()
    {
        $html         = new HtmlToPdf();
        $file         = '5e534622c3df8.html';
        $option       = [
            'dpi' => 120
        ];
        $pdf_generate = $html->generatePDF($file, $option);
        return $pdf_generate;
    }
}
