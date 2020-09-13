<?php
/**
 * Created by PhpStorm.
 * User: TranLuong
 * Date: 23/2/2020
 * Time: 9:37 PM
 */

namespace WorkableCV\Extract\Http\Controllers;

use App\Http\Controllers\Controller;
use WorkableCV\Extract\core\HtmlToPdf;
use WorkableCV\Extract\core\PdfOCR;
use WorkableCV\Extract\core\PdfProtected;
use WorkableCV\Extract\core\PdfToHtml;

class ExtractController extends Controller
{
    public function convertDocToPdf()
    {
        //        $doc = new DocToPdf();
        //        $result = $doc->generatePDF(storage_path('doc/cv23.docx'), storage_path('cv/cv100.pdf'));
        //        if (!$result) exit('Convert error. Try again');
        //        exit('Convert Doc to Pdf successfully');
    }

    public function extract()
    {
        $name_file = 'cv12';

        $file = public_path('upload_cv/2020/08/28/' . $name_file . '.pdf');

        $options_check = config('extract.options_extract');

        $pdf           = new PdfToHtml($file, $options_check);

        $output_dir    = config('extract.options_extract.outputDir');

        $checkPdf      = $pdf->checkPdf($output_dir, $name_file);

        if ($checkPdf)
        {
            $pdfOCR = new PdfOCR();

            $result_pdfOCR = $pdfOCR->pdfOCR($file);

            if (!$result_pdfOCR) exit('File not convert. Try again');

            $path_file_ocr = $result_pdfOCR[1];

            $pdfProtected = new PdfProtected($path_file_ocr, $options_check);

            $path_cv_protected = $pdfProtected->pdfProtected($result_pdfOCR[0], $output_dir, true, false, 'pdf', 1, config('extract.output_cv_protected'));

            unlink($path_file_ocr);
        }
        else
        {
            $checkExistEmailPhone = $pdf->checkExitsEmailPhone($output_dir, $name_file);

            if (!$checkExistEmailPhone)
            {
                $pdfOCR = new PdfOCR();

                $result_pdfOCR = $pdfOCR->pdfOCR($file);

                if (!$result_pdfOCR) exit('File not convert. Try again');

                $path_file_ocr = $result_pdfOCR[1];

                $pdfProtected = new PdfProtected($path_file_ocr, $options_check);

                $path_cv_protected = $pdfProtected->pdfProtected($result_pdfOCR[0], $output_dir, true, false, 'pdf', 1, config('extract.output_cv_protected'));

                unlink($path_file_ocr);
            }
            else
            {
                $pdfProtected = new PdfProtected($file, $options_check);

                $path_cv_protected = $pdfProtected->pdfProtected($name_file, $output_dir, false, false, 'pdf', 1, config('extract.output_cv_protected'));
            }
        }

        return $path_cv_protected;
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
