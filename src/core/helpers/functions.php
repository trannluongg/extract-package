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

if (!function_exists('deleteAll')){
    function deleteAll($path){
        $files = glob($path.'/*');
        foreach($files as $file){
            if(is_file($file))
                unlink($file);
        }
        rmdir($path);
    }
}

/**
 * @param $path
 */
if(!function_exists('createFolder')){
    function createFolder($path){
        if (!file_exists(storage_path($path))) {
            mkdir(storage_path($path), 0777, true);
        }
    }
}
/**
 * Remove border
 * @param $image, $id, $name_file, $minus_width, $minus_height
 */
if (!function_exists('removeBorderImage')){
    function removeBorderImage($image, $path_file,  $name_file, $minus_width, $minus_height){
        $im = imagecreatefromjpeg($image);
        $ini_x_size = getimagesize($image )[0];
        $ini_y_size = getimagesize($image )[1];
        $to_crop_array = array('x' => 1 , 'y' => 1, 'width' => $ini_x_size-$minus_width, 'height'=> $ini_y_size-$minus_height);
        $image_im = imagecrop($im, $to_crop_array);
        unlink($image);
        imagejpeg($image_im, storage_path($path_file. "/" . $name_file), 100);
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
 * @param $file_name, $content
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
                <style type="text/css">
                     '.generateFont().'
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
                         '.generateFont().'
                        body{font-family: "Roboto", sans-serif;  background: white}
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

if (!function_exists('generateFont')){
    function generateFont(){
        return
        '@font-face {
            font-family: \'Roboto\';
            font-style: normal;
            font-weight: 100;
            src: url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100.eot").'"); /* IE9 Compat Modes */
            src: local(\'Roboto Thin\'), local(\'Roboto-Thin\'),
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100.eot?#iefix").'") format("embedded-opentype"), /* IE6-IE8 */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100.woff2").'") format(\'woff2\'), /* Super Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100.woff").'") format(\'woff\'), /* Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100.ttf").'") format(\'truetype\'), /* Safari, Android, iOS */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100.svg#Roboto").'") format(\'svg\'); /* Legacy iOS */
            }
        @font-face {
            font-family: \'Roboto\';
            font-style: italic;
            font-weight: 100;
            src: url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100italic.eot").'"); /* IE9 Compat Modes */
            src: local(\'Roboto Thin Italic\'), local(\'Roboto-ThinItalic\'),
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100italic.eot?#iefix").'") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100italic.woff2").'") format(\'woff2\'), /* Super Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100italic.woff").'") format(\'woff\'), /* Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100italic.ttf").'") format(\'truetype\'), /* Safari, Android, iOS */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100italic.svg#Robot").'") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: normal;
            font-weight: 300;
            src: url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300.eot").'"); /* IE9 Compat Modes */
            src: local(\'Roboto Light\'), local(\'Roboto-Light\'),
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300.eot?#iefix").'") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300.woff2").'") format(\'woff2\'), /* Super Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300.woff").'") format(\'woff\'), /* Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300.ttf").'") format(\'truetype\'), /* Safari, Android, iOS */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300.svg#Roboto").'") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: italic;
            font-weight: 300;
            src: url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300italic.eot").'"); /* IE9 Compat Modes */
            src: local(\'Roboto Light Italic\'), local(\'Roboto-LightItalic\'),
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300italic.eot?#iefix").'\") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300italic.woff2").'") format(\'woff2\'), /* Super Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300italic.woff").'") format(\'woff\'), /* Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300italic.ttf").'") format(\'truetype\'), /* Safari, Android, iOS */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300italic.svg#Roboto").'")format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: normal;
            font-weight: 400;
            src: url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-regular.eot").'"); /* IE9 Compat Modes */
            src: local(\'Roboto\'), local(\'Roboto-Regular\'),
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-regular.eot?#iefix").'") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-regular.woff2").'") format(\'woff2\'), /* Super Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-regular.woff").'") format(\'woff\'), /* Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-regular.ttf").'") format(\'truetype\'), /* Safari, Android, iOS */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-regular.svg#Roboto").'") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: italic;
            font-weight: 400;
            src: url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-italic.eot").'"); /* IE9 Compat Modes */
            src: local(\'Roboto Italic\'), local(\'Roboto-Italic\'),
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-italic.eot?#iefix").'") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-italic.woff2").'") format(\'woff2\'), /* Super Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-italic.woff").'") format(\'woff\'), /* Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-italic.ttf").'") format(\'truetype\'), /* Safari, Android, iOS */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-italic.svg#Roboto").'\") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: normal;
            font-weight: 500;
            src: url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500.eot").'"); /* IE9 Compat Modes */
            src: local(\'Roboto Medium\'), local(\'Roboto-Medium\'),
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500.eot?#iefix").'") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500.woff2").'") format(\'woff2\'), /* Super Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500.woff").'") format(\'woff\'), /* Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500.ttf").'") format(\'truetype\'), /* Safari, Android, iOS */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500.svg#Roboto").'") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: italic;
            font-weight: 500;
            src: url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500italic.eot").'"); /* IE9 Compat Modes */
            src: local(\'Roboto Medium Italic\'), local(\'Roboto-MediumItalic\'),
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500italic.eot?#iefix").'") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500italic.woff2").'") format(\'woff2\'), /* Super Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500italic.woff").'") format(\'woff\'), /* Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500italic.ttf").'") format(\'truetype\'), /* Safari, Android, iOS */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500italic.svg#Roboto").'") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: normal;
            font-weight: 700;
            src: url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700.eot").'"); /* IE9 Compat Modes */
            src: local(\'Roboto Bold\'), local(\'Roboto-Bold\'),
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700.eot?#iefix").'") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700.woff2").'") format(\'woff2\'), /* Super Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700.woff").'") format(\'woff\'), /* Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700.ttf").'") format(\'truetype\'), /* Safari, Android, iOS */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700.svg#Roboto").'") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: italic;
            font-weight: 700;
            src: url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700italic.eot").'"); /* IE9 Compat Modes */
            src: local(\'Roboto Bold Italic\'), local(\'Roboto-BoldItalic\'),
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700italic.eot?#iefix").'") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700italic.woff2").'") format(\'woff2\'), /* Super Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700italic.woff").'") format(\'woff\'), /* Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700italic.ttf").'") format(\'truetype\'), /* Safari, Android, iOS */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700italic.svg#Roboto").'") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: normal;
            font-weight: 900;
            src: url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900.eot").'"); /* IE9 Compat Modes */
            src: local(\'Roboto Black\'), local(\'Roboto-Black\'),
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900.eot?#iefix").'") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900.woff2").'") format(\'woff2\'), /* Super Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900.woff").'") format(\'woff\'), /* Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900.ttf").'") format(\'truetype\'), /* Safari, Android, iOS */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900.svg#Roboto").'") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: italic;
            font-weight: 900;
            src: url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900italic.eot").'"); /* IE9 Compat Modes */
            src: local(\'Roboto Black Italic\'), local(\'Roboto-BlackItalic\'),
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900italic.eot?#iefix").'") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900italic.woff2").'") format(\'woff2\'), /* Super Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900italic.woff").'") format(\'woff\'), /* Modern Browsers */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900italic.ttf").'") format(\'truetype\'), /* Safari, Android, iOS */
               url("'. storage_path("fonts/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900italic.svg#Roboto").'") format(\'svg\'); /* Legacy iOS */
        }';
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
