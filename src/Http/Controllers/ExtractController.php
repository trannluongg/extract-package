<?php
/**
 * Created by PhpStorm.
 * User: TranLuong
 * Date: 23/2/2020
 * Time: 9:37 PM
 */

namespace WorkableCV\Extract\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use WorkableCV\Extract\core\DocToPdf;
use WorkableCV\Extract\core\HtmlToPdf;
use WorkableCV\Extract\core\PdfOCR;
use WorkableCV\Extract\core\PdfProtected;
use WorkableCV\Extract\core\PdfToHtml;
use WorkableCV\Extract\core\PdfToImage;

class ExtractController extends Controller
{
    public function convertDocToPdf(Request $request)
    {
        $name_file = $request->get('name');
        $file = public_path('upload_cv/2020/08/29/' . $name_file . '.doc');
        if (!file_exists($file))
        {
            $file = public_path('upload_cv/2020/08/29/' . $name_file . '.docx');
        }

        $doc = new DocToPdf();
        $result = $doc->generatePDFLinux($file, public_path('upload_cv/2020/08/29/'));
        if (!$result) exit('Convert error. Try again');

        return $this->extractWkh($request);
    }

    public function extractWkh(Request $request)
    {
        $name_file = $request->get('name');
        $file = public_path('upload_cv/2020/08/30/' . $name_file . '.pdf');

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

            $path_cv_protected = $pdfProtected->pdfProtectedWKH(
                $result_pdfOCR[0],
                $output_dir,
                true,
                false,
                'pdf',
                1,
                config('extract.output_cv_protected')
            );

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

                $path_cv_protected = $pdfProtected->pdfProtectedWKH(
                    $result_pdfOCR[0],
                    $output_dir,
                    true,
                    true,
                    'pdf',
                    1,
                    config('extract.output_cv_protected'));

                unlink($path_file_ocr);
            }
            else
            {
                $pdfProtected = new PdfProtected($file, $options_check);

                $path_cv_protected = $pdfProtected->pdfProtectedWKH(
                    $name_file,
                    $output_dir,
                    false,
                    false,
                    'pdf',
                    1,
                    config('extract.output_cv_protected')
                );
            }
        }

        $folder_image = uniqid();
        $short_name_image = $folder_image;
        $folder_dir_image     = 'image_cv_protected/' . $folder_image;
        createFolder($folder_dir_image, 1);
        $output_dir_image     = public_path('image_cv_protected/' . $folder_image . '/' . $short_name_image);
        $pdfToImage           = new PdfToImage();
        try
        {
            $result_convert_image = $pdfToImage->generatePdfImage(public_path($path_cv_protected), $output_dir_image);
        } catch (\Exception $e)
        {
            Log::info($e->getMessage());
            $result_convert_image = [];
        }

        return json_encode(
            [
                'data'         => 'http://127.0.0.1:8001/' . $path_cv_protected,
                'status_image' => $result_convert_image
            ]);
    }

    public function extract()
    {

        $name_file = 'cv1';
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

            $path_cv_protected = $pdfProtected->pdfProtected(
                $result_pdfOCR[0],
                $output_dir,
                true,
                false,
                'pdf',
                1,
                config('extract.output_cv_protected')
            );

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

                $path_cv_protected = $pdfProtected->pdfProtected(
                    $result_pdfOCR[0],
                    $output_dir,
                    true,
                    true,
                    'pdf',
                    1,
                    config('extract.output_cv_protected'));

                unlink($path_file_ocr);
            }
            else
            {
                $pdfProtected = new PdfProtected($file, $options_check);

                $path_cv_protected = $pdfProtected->pdfProtected(
                    $name_file,
                    $output_dir,
                    false,
                    false,
                    'pdf',
                    1,
                    config('extract.output_cv_protected')
                );
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
