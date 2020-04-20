<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 24/2/2020
 * Time: 9:06 AM
 */

namespace Luongtv\Extract\core;


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

    public function checkPdf($path_dir = '')
    {
        $content_page       = $this->getHtml()->getAllPages();

        $checkPdf           = 0;

        $count_page         = count($content_page);

        if ($count_page <= 1)
        {
            if (preg_match('/<div\s.*?>(.*?)<\/div>/si', $content_page[1], $matches))
            {
                $image = $matches[1];

                if (preg_match('/<img\s.*?>/si', $image, $matches_image))
                {
                    $checkPdf++;
                }
            }
        }else{
            foreach ($content_page as $key => $row_page)
            {
                if (preg_match('/<div\s.*?>(.*?)<\/div>/si', $row_page, $matches))
                {
                    $image = $matches[1];
                    if (preg_match('/<img\s.*?>/si', $image, $matches_image))
                    {
                        $image = str_replace($matches_image[0], '', $image);
                        if (trim($image) == '') $checkPdf++;
                    }
                }
            }
        }

        deleteAll($path_dir);

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

        $html_handle = handleHtmlAdvanced($content_page, $path_file, $name_file, $content_html, $content_html_image);

        $content_html = $html_handle['html'];

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
}