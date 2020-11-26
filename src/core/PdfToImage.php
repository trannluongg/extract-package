<?php
/**
 * Created by PhpStorm.
 * User: TranLuong
 * Date: 20/11/2020
 * Time: 10:29
 */

namespace WorkableCV\Extract\core;


use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PdfToImage
{
    public function generatePdfImage($path_pdf = '', $output_dir = '', $image_extension = 'jpeg')
    {
        if (!file_exists($path_pdf)) throw new \Exception('File do not exit');

        $process = new Process([config('extract.path_pdftoimage'), $path_pdf , $output_dir, '-' . $image_extension]);
        try {
            $process->mustRun();
            return $process->getOutput();
        } catch (ProcessFailedException $exception) {
            return $exception->getMessage();
        }

    }
}
