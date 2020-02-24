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

    /**
     * @param string $name_file
     * @param string $id
     */
    public function generateHTML($name_file = '', $id = ''){
        $pdf_info = $this->getInfo();
        $title = 'Xem CV';
        if (isset($pdf_info['title'])){
            $title = $pdf_info['title'];
        }
        $content_html = generateHeaderHTML($title);
        $content_html_image = generateHeaderHTML($title, false);
        $content_page = $this->getHtml()->getAllPages();
        if (count($content_page) <= 1){
            $html_file = null;
            if (file_exists(storage_path('files/'.$id.'/'.$name_file.'.html'))) $html_file = storage_path('files/'.$id.'/'.$name_file.'.html');
            if (file_exists(storage_path('files/'.$id.'/'.$name_file.'-1.html'))) $html_file = storage_path('files/'.$id.'/'.$name_file.'-1.html');
            $content_page = file_get_contents($html_file);
            if(preg_match_all('/<style\s.*?>(.*?)<\/style>/si', $content_page, $matches)){
                if (count($matches[1]) == 2){
                    $matches[1] = implode(' ', $matches[1]);
                }else{
                    $matches[1] = $matches[1][0];
                }
                $matches[1] = replaceCss($matches[1]);
                $content_html .= $matches[1];
                $content_html_image .= $matches[1];
            }
            $content_html .= closeHeader();
            $content_html_image .= closeHeader(false);
            $content_page_image = $content_page;
            if(preg_match('/<img\s.*?\bsrc="(.*?)".*?>/si', $content_page, $matches)){
                $image = storage_path("files/".$id. "/" .$name_file."001.jpg");
                removeBorderImage($image, $id, $name_file.'001.jpg', 10);
                $img = str_replace($matches[1], $image, $matches[0]);
                $content_page = str_replace($matches[0], $img, $content_page);
            }
            if(preg_match('/<div\s.*?>(.*?)<\/div>/si', $content_page, $matches)){
                $content_page = $matches[1];
            }
            if(preg_match('/<div\s.*?>(.*?)<\/div>/si', $content_page_image, $matches)){
                $content_page_image = $matches[1];
            }
            $content_page = regex($content_page);
            $content_page_image = regex($content_page_image);
            $content_html .= $content_page;
            $content_html_image .= $content_page_image;
        }else{
            $content_html .= closeHeader();
            $content_html_image .= closeHeader(false);
            foreach ($content_page as $key => $row_page){
                $row_page_v2 = $row_page;
                if(preg_match('/<img\s.*?\bsrc="(.*?)".*?>/si', $row_page, $matches)){
                    $image = storage_path("files/".$id. "/" .$name_file."00".$key.".jpg");
                    if ($key < count($content_page)){
                        removeBorderImage($image, $id, $name_file."00".$key.".jpg", 2);
                    }else{
                        removeBorderImage($image, $id, $name_file."00".$key.".jpg", 50);
                    }
                    $img = str_replace($matches[1], $image, $matches[0]);
                    $row_page = str_replace($matches[0], $img, $row_page);
                }
                if(preg_match('/<div\s.*?>(.*?)<\/div>/si', $row_page, $matches)){
                    $row_page = $matches[1];
                }
                if(preg_match('/<div\s.*?>(.*?)<\/div>/si', $row_page_v2, $matches)){
                    $row_page_v2 = $matches[0];
                }
                $row_page = regex($row_page);
                $row_page_v2 = regex($row_page_v2);
                $content_html .= $row_page;
                $content_html_image .= $row_page_v2;
            }
        }
        $content_html .= closeHTML();
        $content_html_image .= generateScript();
        $content_html_image .= closeHTML();
        $content_html = replaceAll($content_html);
        $content_html_image = replaceAll($content_html_image);
        $file_name = storage_path('files/'.$id.'.html');
        $file_name_image = storage_path('files/'.$id.'_image.html');
        createFile($file_name, $content_html);
        createFile($file_name_image, $content_html_image);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($content_html_image, $id.'_image.html');
        exit('Generate Success');
    }
}