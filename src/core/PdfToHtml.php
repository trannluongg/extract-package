<?php
/**
 * Created by PhpStorm.
 * User: TranLuong
 * Date: 24/2/2020
 * Time: 9:06 AM
 */

namespace WorkableCV\Extract\core;


use Illuminate\Support\Facades\App;

class PdfToHtml extends Pdf
{
    /**
     * PdfToHtml constructor.
     * @param $file
     * @param array $options
     */
    public function __construct($file, array $options = [])
    {
        parent::__construct($file, $options);
    }

    /**
     * @param string $path_tmp
     * @param string $name_file
     * @return bool
     */
    public function checkPdf($path_tmp = '', $name_file = '')
    {
        $content_page       = $this->getHtml()->getAllPages();

        $checkPdf           = 0;

        $count_page         = count($content_page);

        if ($count_page <= 1)
        {
            $html_file = getFile($path_tmp, $name_file);

            $content_page = file_get_contents($html_file);

            $checkPdf = $this->checkOnlyImage($content_page, $checkPdf);

        }else{
            foreach ($content_page as $key => $row_page)
            {
                $checkPdf = $this->checkOnlyImage($row_page, $checkPdf);
            }
        }

        deleteAll($path_tmp);

        if ($checkPdf == $count_page) return true;

        return false;
    }

    /**
     * @param string $name_file
     * @param string $path_file
     * @param string $extension
     */
    public function generateHTML($name_file = '', $path_file = '', $extension = 'pdf')
    {
        $pdf_info = $this->getInfo();

        $title    = 'Xem CV';

        if (isset($pdf_info['title'])) $title = $pdf_info['title'];

        $content_html       = generateHeaderHTML($title, true, $extension);

        $content_html_image = generateHeaderHTML($title, false, $extension);

        $content_page       = $this->getHtml()->getAllPages();

        $html_handle        = handleHtmlAdvanced($content_page, $path_file, $name_file, $content_html, $content_html_image);

        $content_html       = $html_handle['html'];

        $content_html_image = $html_handle['html_image'];

        $content_html       .= closeHTML();

        $content_html_image .= generateScript();

        $content_html_image .= closeHTML();

        $content_html       = replaceAll($content_html);

        $content_html_image = replaceAll($content_html_image);

        $file_name          = $path_file . '.html';

        $file_name_image    = $path_file . '_image.html';

        createFile($file_name, $content_html);

        createFile($file_name_image, $content_html_image);

        $pdf = App::make('dompdf.wrapper');

        $pdf->loadHTML($content_html_image, $path_file . '_image.html');

        exit('Generate Success');
    }

    /**
     * @param string $content
     * @param int $number_of_check
     * @return int
     */
    private function checkOnlyImage($content = '', $number_of_check = 0)
    {
        if (preg_match('/<div\s.*?>(.*?)<\/div>/si', $content, $matches))
        {
            $image = $matches[1];
            if (preg_match('/<img\s.*?>/si', $image, $matches_image))
            {
                $image = str_replace($matches_image[0], '', $image);
                if (trim($image) == '') $number_of_check++;
            }
        }

        return $number_of_check;
    }

    /**
     * @param string $path_tmp
     * @param string $name_file
     * @return bool
     */
    public function checkExitsEmailPhone($path_tmp = '', $name_file = '')
    {
        $content_page       = $this->getHtml()->getAllPages();

        $count_page         = count($content_page);

        $check_exist = 0;

        if ($count_page <= 1)
        {
            $html_file = getFile($path_tmp, $name_file);

            $content_page = file_get_contents($html_file);

            $check_exist = $this->regexEmailPhone($content_page, $check_exist);

        }else{
            foreach ($content_page as $key => $row_page)
            {
                $check_exist = $this->regexEmailPhone($row_page, $check_exist);
            }
        }

        deleteAll($path_tmp);

        if ($check_exist >= 1) return true;

        return false;
    }

    /**
     * @param string $content
     * @param int $number_check
     * @return int
     */
    private function regexEmailPhone($content = '', $number_check = 0)
    {
        if (preg_match('/<div\s.*?>(.*?)<\/div>/si', $content, $matches))
        {
            $content = $matches[1];

            if (preg_match('/<img\s.*?>/si', $matches[0], $matches_image))
            {
                $content = str_replace($matches_image[0], '', $content);
            }

            if(preg_match('/([a-zA-Z0-9\[\]]|\s+)(\.?[a-zA-Z0-9\s\[\]_\/]){5,}@[a-zA-Z<>\s\.]{3,}\.[a-zA-Z\s+\.\/<>][^(<\/p)]{0,}/', $content,$matches_email))
            {
                $number_check++;
            }

            $content = handleHtmlEntities($content);

            if(preg_match('/(\s+|0|\:|(\(*\+*[0-9]{1,2}\)*))\s*[0-9\.\s+]{8,}/', $content,$matches_phone))
            {
                $number_check++;
            }

            if(preg_match('/(0|(\(*\+[0-9]{1,2}\)*))\s*[0-9\.\-]{8,}/', $content,$matches_phone))
            {
                $number_check++;
            }
        }

        return $number_check;
    }
}
