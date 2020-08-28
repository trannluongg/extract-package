<?php
/**
 * Created by PhpStorm.
 * User: TranLuong
 * Date: 17/4/2020
 * Time: 1:51 PM
 */

namespace WorkableCV\Extract\core;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PdfOCR
{
    /**
     * @param string $file_pdf_ocr
     * @return array|bool
     */
    public function pdfOCR($file_pdf_ocr = '')
    {
        $name_file   = uniqid();
        $path_pdfOCR = storage_path( $name_file . '.pdf');

        $result = $this->handlePdfOCR($file_pdf_ocr, $path_pdfOCR);

        if ($result > 0)
        {
            return false;
        }
        return [
            $name_file, $path_pdfOCR
        ];
    }

    /**
     * @param string $file_pdf_ocr
     * @param string $path_pdf_ocr
     * @return string
     */
    private function handlePdfOCR($file_pdf_ocr = '', $path_pdf_ocr = '')
    {
        try
        {
            return $this->generatePdfOCR($file_pdf_ocr, $path_pdf_ocr);
        } catch (\Exception $e)
        {
            return $e;
        }
    }

    /**
     * @param string $path_pdf
     * @param string $path_pdf_ocr
     * @return string
     * @throws \Exception
     */
    private function generatePdfOCR($path_pdf = '', $path_pdf_ocr = '')
    {
        if (!file_exists($path_pdf)) throw new \Exception('File do not exit');

        $process = new Process([config('extract.path_ocrmypdf'), '-l', 'eng+vie', '--redo-ocr', $path_pdf, $path_pdf_ocr], null, [
            'LC_ALL' => 'C.UTF-8',
            'LANG' => 'C.UTF-8'
        ]);

        try {
            $process->mustRun();
            return $process->getOutput();
        } catch (ProcessFailedException $exception) {
            return $exception->getMessage();
        }
    }
}
