<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 24/2/2020
 * Time: 8:29 AM
 */

/**
 * Regex phone, email, date of birth, link facebook, all link
 * @param $string
 * @return $string
 */
if (!function_exists('regex')){
    function regex($string){
        $string = preg_replace('/(\s+)?(?:https?:\/\/)?(?:www\.)?(facebook|fb)\.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[\w\-<>]*\/)*([\w\-\.<>]*)[\?a-z0-9=\s+\/<>][^(\/p)]{1,}/', '[123cv.net protected]<', $string);
        $string = preg_replace('/([a-z0-9]|\s+)(\.?[a-z0-9]){5,}@[a-z]{3,}\.[a-z\s+\.\/<>][^(<\/p)]{2,}/', '[123cv.net protected]', $string);
        $string = preg_replace('/(\s+|0|\:|(\(*\+[0-9]{1,2}\)*))\s*[0-9\.\s+]{9,}/', '[123cv.net protected]', $string);
        $string = preg_replace('~(?:(?:https://)|(?:http://)|(?:www\.))(?![^" ]*(?:jpg|png|gif|"))[^" <>]+~', '[123cv.net protected]', $string);
        $string = preg_replace('~line\-height: ?([\d]+)px;~', '', $string);
        return $string;
    }
}

/**
 * Remove border image
 * @param $path_image, $name_folder, $name_file, $minus_height
 * @return new image
 */
if (!function_exists('removeBorderImage')){
    function removeBorderImage($image, $id,  $name_file, $minus_height){
        $im = imagecreatefromjpeg($image);
        $ini_x_size = getimagesize($image )[0];
        $ini_y_size = getimagesize($image )[1];
        $to_crop_array = array('x' => 1 , 'y' => 1, 'width' => $ini_x_size-2, 'height'=> $ini_y_size-$minus_height);
        $image_im = imagecrop($im, $to_crop_array);
        unlink($image);
        imagejpeg($image_im, storage_path("files/".$id. "/" . $name_file), 100);
    }
}

/**
 * Regex get all match
 * @param $string
 * @return $array match
 */
if (!function_exists('getTextBetweenTags')){
    function getTextBetweenTags($string) {
        $string = str_replace('<br>', 'zxcvbnm', $string);
        $string = str_replace('<br/>', 'zxcvbnm', $string);
        $pattern = "/<?.*>(.*)<\/?.*>/";
        preg_match_all($pattern, $string, $matches);
        return $string;
    }
}

/**
 * Create file
 * @param $path_file, $content
 * @return new file
 */
if (!function_exists('createFile')){
    function createFile($file_name, $content){
        if (!file_exists($file_name)){
            $my_file = fopen($file_name, 'w');
            fwrite($my_file, $content);
            fclose($my_file);
        }else{
            $my_file = fopen($file_name, 'w');
            fwrite($my_file, $content);
            fclose($my_file);
        }
    }
}

/**
 * Generate header HTML
 * @param $title
 * @return $string
 */
if (!function_exists('generateHeaderHTML')){
    function generateHeaderHTML($title, $flag = true){
        if ($flag) {
            return '<!DOCTYPE html>
                <html xmlns="http://www.w3.org/1999/xhtml" lang="vi" xml:lang="vi">
                <head>
                <title>' . $title . '</title>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <link rel="stylesheet" href="'.public_path('fontawesome-free-5.12.1-web/css/all.css').'">
                <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese" rel="stylesheet">
                <style type="text/css">
                    body{font-family: "Roboto", sans-serif}
                    i.fas, i.far{margin-top: 2px}
                    ';
        }else {
            return '<!DOCTYPE html>
                <html xmlns="http://www.w3.org/1999/xhtml" lang="vi" xml:lang="vi">
                <head>
                    <title>'.$title.'</title>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    <link rel="stylesheet" href="'.public_path('fontawesome-free-5.12.1-web/css/all.css').'">
                    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.js"></script>
                    <style type="text/css">
                        body{font-family: "Open Sans", sans-serif; background: #ffffff}
                        i.fas, i.far{margin-top: 2px}
                        p{line-height: 20px}
                        div{page-break-after: always; margin: 0 auto 15px auto}
        ';
        }
    }
}

/**
 * Replace HTML
 * @param $content
 * @return $content
 */
if (!function_exists('replaceAll')){
    function replaceAll($content){
        $content = str_replace('font-family: Times;', '', $content);
        $content = str_replace('font-family:Times;', '', $content);
        $content = preg_replace('/<a.*?>/', '', $content);
        $content = str_replace('</a>', '', $content);
        return $content;
    }
}
/**
 * Replace CSS
 * @param $string
 * @return $string
 */
if (!function_exists('replaceCss')){
    function replaceCss($string){
        $string = trim(preg_replace('/\s\s+/', ' ', $string));
        $string = str_replace('<!--', '', $string);
        $string = str_replace('-->', '', $string);
        $string = preg_replace('~line\-height: ?([\d]+)px;~', '', $string);
        return $string;
    }
}

/**
 * Close Header
 * @param $flag
 * @return $string
 */
if (!function_exists('closeHeader')){
    function closeHeader($flag = true){
        if ($flag === true){
            return '
                </style>
                <script src="'.public_path('fontawesome-free-5.12.1-web/js/all.js').'"></script>
                </head>
                <body>
                ';
        }else{
            return '
                </style>
                <script src="'.public_path('fontawesome-free-5.12.1-web/js/all.js').'"></script>
                </head>
                <body id="body">
                ';
        }
    }
}
/**
 * Close HTML
 * @return $string
 */
if (!function_exists('closeHTML')){
    function closeHTML(){
            return '
               </body></html>
                ';
    }
}
/**
 * Script
 */
if (!function_exists('generateScript')){
    function generateScript(){
        return
        '<script>
                    window.addEventListener("DOMContentLoaded", () => {
                        const node = document.getElementById("body");
                        domtoimage.toJpeg(node, { quality: 0.95 })
                            .then(function (dataUrl) {
                                console.log(dataUrl);
                            });
                    });
                </script>';
    }
}
