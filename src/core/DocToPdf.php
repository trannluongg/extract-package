<?php
/**
 * Created by PhpStorm.
 * User: TranLuong
 * Date: 27/2/2020
 * Time: 9:59 AM
 */

namespace WorkableCV\Extract\core;


//use COM;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DocToPdf
{
    /**
     * @param $path_doc
     * @param $path_pdf
     * @return bool
     */
//    public function generatePDF($path_doc, $path_pdf)
//    {
//        if (!file_exists($path_doc)) return 'File not exist';
//        $word = new COM("word.application") or die ("Could not initialise MS Word object.");
//        $word->Visible         = 0;
//        $readOnly              = true;
//        $wdOpenFormatAuto      = 0;
//        $msoEncodingAutoDetect = 50001;
//        $word->Documents->OpenNoRepairDialog($path_doc, false, $readOnly, false, "", "", true, "", "", $wdOpenFormatAuto, $msoEncodingAutoDetect);
//        $word->ActiveDocument->ExportAsFixedFormat($path_pdf, 17, false, 0, 0, 0, 0, 7, true, true, 2, true, true, false);
//        $word->Quit(false);
//        return true;
//    }

    /**
     * @param string $path_file_doc
     * @param string $output_dir_pdf
     * @return string
     */
    public function generatePDFLinux($path_file_doc = '', $output_dir_pdf = '')
    {
        if (file_exists($path_file_doc))
        {
            $process = new Process(['lowriter',  '--convert-to', 'pdf', $path_file_doc, '--outdir', $output_dir_pdf]);
            try {
                $process->setTimeout(180);
                $process->mustRun();
                return $process->getOutput();
            } catch (ProcessFailedException $exception) {
                return $exception->getMessage();
            }
        }
        return 'File not exist';
    }
}
