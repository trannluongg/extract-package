<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 3/3/2020
 * Time: 2:09 PM
 */

namespace Luongtv\Extract\core;


use Illuminate\Support\Facades\App;

class PdfProtected extends Pdf
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
     * @param $name_file
     * @param $path_tmp
     */
    public function pdfProtected($name_file, $path_tmp){
        $path = 'files/'.date('Y') . '/' . date('m') . '/' . date('d');
        createFolder($path);
        $file_pdf_name = date('d_m_Y'). '__________' . uniqid();

        $pdf_info = $this->getInfo();
        $title = 'Xem CV';
        if (isset($pdf_info['title'])){
            $title = $pdf_info['title'];
        }
        $content_html = generateHeaderHTML($title);
        $content_page = $this->getHtml()->getAllPages();
        if (count($content_page) <= 1){
            $html_file = null;
            if (file_exists(storage_path($path_tmp.'/'.$name_file.'.html'))) $html_file = storage_path($path_tmp.'/'.$name_file.'.html');
            if (file_exists(storage_path($path_tmp.'/'.$name_file.'-1.html'))) $html_file = storage_path($path_tmp.'/'.$name_file.'-1.html');
            $content_page = file_get_contents($html_file);
            if(preg_match_all('/<style\s.*?>(.*?)<\/style>/si', $content_page, $matches)){
                if (count($matches[1]) == 2){
                    $matches[1] = implode(' ', $matches[1]);
                }else{
                    $matches[1] = $matches[1][0];
                }
                $matches[1] = replaceCss($matches[1]);
                $content_html .= $matches[1];
            }
            $content_html .= closeHeader();
            if(preg_match('/<img\s.*?\bsrc="(.*?)".*?>/si', $content_page, $matches)){
                $image = storage_path($path_tmp. "/" .$name_file."001.jpg");
                removeBorderImage($image, $path_tmp, $name_file.'001.jpg', 2, 10);
                $img = str_replace($matches[1], $image, $matches[0]);
                $content_page = str_replace($matches[0], $img, $content_page);
            }
            if(preg_match('/<div\s.*?>(.*?)<\/div>/si', $content_page, $matches)){
                $content_page = $matches[1];
            }
            $content_page = regex($content_page);
            $content_html .= $content_page;
        }else{
            $content_html .= closeHeader();
            foreach ($content_page as $key => $row_page){
                if(preg_match('/<img\s.*?\bsrc="(.*?)".*?>/si', $row_page, $matches)){
                    $image = storage_path($path_tmp ."/" .$name_file."00".$key.".jpg");
                    removeBorderImage($image, $path_tmp, $name_file."00".$key.".jpg", 2,2);
                    $img = str_replace($matches[1], $image, $matches[0]);
                    $row_page = str_replace($matches[0], $img, $row_page);
                }
                if(preg_match('/<div\s.*?>(.*?)<\/div>/si', $row_page, $matches)){
                    $row_page = $matches[1];
                }
                $row_page = regex($row_page);
                $content_html .= $row_page;
            }
        }
        $content_html .= closeHTML();
        $content_html = replaceAll($content_html);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($content_html);
        $pdf->setOptions(['dpi' => 120]);
        $pdf->save(storage_path($path.'/'.$file_pdf_name.'.pdf'));
        deleteAll(storage_path($path_tmp));
    }
}