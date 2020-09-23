<?php
/**
 * Created by PhpStorm.
 * User: TranLuong
 * Date: 24/2/2020
 * Time: 8:29 AM
 */

use Illuminate\Support\Facades\App;

/**
 * @param $string
 * @param $number_page
 * @return string|string[]|null
 */
if (!function_exists('regex'))
{
    function regex($string, $extension = 'pdf', $number_page = 2)
    {
        $string = handleHtmlEntities($string);

        $string = preg_replace('/(\s+|0|\:|(\(*\+*[0-9]{1,2}\)*))\s*[0-9\.\s+]{8,}/', config('extract.protected'), $string);
        $string = preg_replace('/(0|(\(*\+[0-9]{1,2}\)*))\s*[0-9\.\-]{8,}/', config('extract.protected'), $string);
        $string = html_entity_decode($string);

        $string = regexSkype($string);

        $string = preg_replace('/<a.*?>/', '', $string);
        $string = str_replace('</a>', '', $string);
        $string = str_replace('<br/>', 'noiuytrewq', $string);
        $string = str_replace('<br>', 'asdfghjkln', $string);

        $string = preg_replace('/(\s+)?(?:https?:\/\/)?(?:www\.)?(facebook|fb)\.[a-z]{1,13}\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[\w\-<>]*\/[^(\/p)])*([\w\-\.<>]*)[\?a-zA-Z0-9=\s+\/<>][^(\/p)]{1,}/', config('extract.protected') . '<', $string);
        $string = preg_replace('/([a-zA-Z0-9\[\]]|\s+)(\.?[a-zA-Z0-9\s\[\]_\/]){5,100}@[a-zA-Z<>\s\.]{3,}\.[a-zA-Z\s+\.\/<>][^(<\/p)]*/', config('extract.protected'), $string);
        $string = preg_replace('/([a-zA-Z0-9\[\]]|\s+)(\.?[a-zA-Z0-9\[\]_\/]){5,100}@/', config('extract.protected'), $string);
        $string = preg_replace('/id=[0-9]*/', config('extract.protected'), $string);
        $string = preg_replace('/([a-zA-Z0-9]|\s+)(\.?[a-zA-Z0-9]){5, 100}@[a-zA-Z\s\.]{3,}/', config('extract.protected'), $string);
        $string = preg_replace('~(?:(?:https:\/\/)|(?:http:\/\/)|(?:www\.)|([a-zA-Z]{1,}(?:\.(com|net))))(?![^" ]*(?:jpg|png|gif|"))[^" <>]+~', config('extract.protected'), $string);
        $string = preg_replace('/font-family.+?;/', '', $string);

        $string = preg_replace('~line\-height: ?([\d]+)px;~', '', $string);
        $string = str_replace('po;iu&&ytr--ewq', '       ', $string);
        $string = str_replace('po;iu&&ytr---ewq', '      ', $string);
        $string = str_replace('noiuytrewq', '<br/>', $string);
        $string = str_replace('asdfghjkln', '<br>', $string);
        $string = str_replace('<protected]', '', $string);
        $string = str_replace('protected]protected]', 'protected]', $string);
        $string = str_replace('.com', '', $string);

        $string = regexFBLinkedIn($string);
        $string = regexFBLinkedIn($string, 'LinkedIn');

        if ($number_page == 1 && $extension == 'doc') $string = replaceSpace($string);

        return $string;
    }
}

/**
 * @param $string
 * @return string
 */
if (!function_exists('handleHtmlEntities'))
{
    function handleHtmlEntities($string)
    {
        $string = str_replace('&#160;', ' ', $string);

        $string = htmlentities($string, ENT_QUOTES);
        $string = str_replace('&nbsp;', ' ', $string);
        $string = str_replace('       ', 'po;iu&&ytr--ewq', $string);
        $string = str_replace('      ', 'po;iu&&ytr---ewq', $string);

        return $string;
    }
}

/**
 * @param $string
 * @return string
 */
if (!function_exists('replaceSpace'))
{
    function replaceSpace($string)
    {
        preg_match_all('/<p\s.*?>(.*?)<\/p>/si', $string, $matches);

        $regex_value = $matches[1];

        if (isset($regex_value) && !empty($regex_value))
        {
            foreach ($regex_value as $key => $value)
            {
                $value_new = str_replace(' ', '&#160;', $value);

                $tag_p = $matches[0][$key];

                if ($value == ' ')
                {
                    $position  = strripos($tag_p, ' ');
                    $tag_p_new = substr_replace($tag_p, '&#160;', $position, 1);
                }
                else
                {
                    $tag_p_new = str_replace($value, $value_new, $tag_p);
                }
                $string = str_replace($tag_p, $tag_p_new, $string);
            }
        }

        return $string;
    }
}
/**
 * @param $string
 * @return string
 */
if (!function_exists('regexSkype'))
{
    function regexSkype($string)
    {
        $xpath_p      = "//p[contains(text(),'Skype')]";
        $array_result = xpath($string, $xpath_p, true);
        if ($array_result[1] == $xpath_p && $array_result[2]) return $array_result[0];


        $xpath_pa     = "//p/a[contains(text(),'Skype')]";
        $array_result = xpath($string, $xpath_pa, true);
        if ($array_result[1] == $xpath_pa && $array_result[2]) return $array_result[0];


        $xpath_pb     = "//p/b[contains(text(),'Skype')]/parent::*/following-sibling::p[1]";
        $array_result = xpath($string, $xpath_pb);
        if ($array_result[1] == $xpath_pb && $array_result[2]) return $array_result[0];

        $xpath_ps     = "//p[contains(text(),'Skype')]/following-sibling::p[1]";
        $array_result = xpath($string, $xpath_ps);
        if ($array_result[1] == $xpath_ps && $array_result[2]) return $array_result[0];

        return $string;
    }
}

/**
 * @param $string
 * @return string
 */
if (!function_exists('regexFBLinkedIn'))
{
    function regexFBLinkedIn($string, $txt = 'Facebook')
    {
        $xpath_p      = "//p[contains(text(), '" . $txt . "')]";
        $array_result = xpath($string, $xpath_p, true, $txt);
        if ($array_result[1] == $xpath_p && $array_result[2]) return $array_result[0];

        return $string;
    }
}


/**
 * @param $string
 * @param $string
 * @param boolean
 * @return string
 */

if (!function_exists('xpath'))
{
    function xpath($string = '', $query = '', $flag = false, $obj = 'skype')
    {
        $html_dom = new DOMDocument();
        @$html_dom->loadHTML($string);
        $x_path = new DOMXPath($html_dom);

        $nodes_p = $x_path->query($query);

        $is_replace = false;

        if ($nodes_p->length > 0)
        {
            foreach ($nodes_p as $node_p)
            {
                $value_node = $node_p->nodeValue;

                if ($flag) $value_node = substr($value_node, ($obj == 'skype' ? 5 : 8));

                $string = str_replace($value_node, config('extract.protected'), $string);

                $is_replace = true;
            }
        }
        return [$string, $query, $is_replace];
    }
}

/**
 * @param $string
 * @return string|string[]|null
 */
if (!function_exists('regexOCR'))
{
    function regexOCR($string)
    {
        preg_match_all('/<p\s.*?>(.*?)<\/p>/si', $string, $matches);

        if (!empty($matches[0]))
        {
            foreach ($matches[0] as $tag_p)
            {
                if (preg_match('/<p\s.*?>(.*?)<\/p>/si', $tag_p, $matches_p))
                {
                    if (
                        preg_match('/((.*?){1,3}|\s+|0|\:|(\(*\+[0-9]{1,2}\)*))\s*[0-9\.\s+]{9,}/', $matches_p[1], $matches_c) ||
                        preg_match('/((.*?){1,3}|[a-zA-Z0-9]|\s+)(\.?[a-zA-Z0-9]){5,}@[a-zA-Z]{3,}\.[a-zA-Z\s+\.\/<>][^(<\/p)]{0,}/', $matches_p[1], $matches_c) ||
                        preg_match('~(.*?){1,3}(?:(?:https:\/\/)|(?:http:\/\/)|(?:www\.))(?![^" ]*(?:jpg|png|gif|"))[^" <>]+~', $matches_p[1], $matches_c) ||
                        preg_match('/(.*?){1,3}(\s+)?(?:https?:\/\/)?(?:www\.)?(facebook|fb)\.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[\w\-<>]*\/)*([\w\-\.<>]*)[\?a-z0-9=\s+\/<>][^(\/p)]{1,}/', $matches_p[1], $matches_c)
                    )
                    {
                        $matches_p_new = str_replace($matches_c[1], '', $matches_c[0]);
                        $tag_p_new     = str_replace($matches_c[0], $matches_p_new, $tag_p);
                        $string        = str_replace($tag_p, $tag_p_new, $string);
                    }
                    else
                    {
                        $string = str_replace($tag_p, '', $string);
                    }
                }
            }
        }
        $string = str_replace("\n", "", $string);
        $string = str_replace("\r", "", $string);
        return $string;
    }
}

/**
 * @param $path
 */
if (!function_exists('deleteAll'))
{
    function deleteAll($path)
    {
        $files = glob($path . '/*');
        foreach ($files as $file)
        {
            if (is_file($file))
                unlink($file);
        }
        rmdir($path);
    }
}

/**
 * @param string $path
 * @param int $path_where
 */
if (!function_exists('createFolder'))
{
    function createFolder($path = '', $path_where = 0)
    {
        if ($path_where == 0)
        {
            if (!file_exists(storage_path($path)))
            {
                mkdir(storage_path($path), 0777, true);
            }
        }
        else
        {
            if (!file_exists(public_path($path)))
            {
                mkdir(public_path($path), 0777, true);
            }
        }
    }
}

/**
 * @param $image
 * @param $path_file
 * @param $name_file
 * @param $minus_width
 * @param $minus_height
 */
if (!function_exists('removeBorderImage'))
{
    function removeBorderImage($image, $path_file, $name_file, $minus_width, $minus_height)
    {
        $im            = imagecreatefromjpeg($image);
        $ini_x_size    = getimagesize($image)[0];
        $ini_y_size    = getimagesize($image)[1];
        $to_crop_array = array('x' => 1, 'y' => 1, 'width' => $ini_x_size - $minus_width, 'height' => $ini_y_size - $minus_height);
        $image_im      = imagecrop($im, $to_crop_array);
        unlink($image);
        imagejpeg($image_im, $path_file . "/" . $name_file, 100);
    }
}

/**
 * @param $string
 * @return mixed
 */
if (!function_exists('getTextBetweenTags'))
{
    function getTextBetweenTags($string)
    {
        $string  = str_replace('<br>', 'zxcvbnm', $string);
        $string  = str_replace('<br/>', 'zxcvbnm', $string);
        $pattern = "/<?.*>(.*)<\/?.*>/";
        preg_match_all($pattern, $string, $matches);
        return $string;
    }
}

if (!function_exists('generateNewFileName'))
{
    function generateNewFileName($filename)
    {
        $ipClient = time() . uniqid() . rand(111111, 999999) . rand(111111, 999999);

        $prefix      = date("Y_m_d") . '___' . strtotime(date("Y_m_d")) . '___';
        $nFilename   = str_replace('.', '--', $filename);
        $nFilename   = \Illuminate\Support\Str::slug($nFilename);
        $filenameMd5 = $prefix . md5($nFilename . $ipClient);

        return $filenameMd5;
    }
}

/**
 * @param $file_name
 * @param $content
 */
if (!function_exists('createFile'))
{
    function createFile($file_name, $content)
    {
        if (!file_exists($file_name))
        {
            $my_file = fopen($file_name, 'w');
            fwrite($my_file, $content);
            fclose($my_file);
        }
        else
        {
            $my_file = fopen($file_name, 'w');
            fwrite($my_file, $content);
            fclose($my_file);
        }
    }
}

/**
 * @param int $save_path
 * @param string $path_file
 * @param string $content_html
 */
if (!function_exists('saveProtected'))
{
    function saveProtected($save_path = 0, $path_file = '', $content_html = '')
    {
        if ($save_path == 0) $path_save = storage_path($path_file);
        else $path_save = public_path($path_file);
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($content_html);
        $pdf->setOptions(['dpi' => 120]);
        $pdf->save($path_save);
    }
}

/**
 * @param array $content_page
 * @param string $path_tmp
 * @param string $name_file
 * @param string $content_html
 * @param boolean $ocr
 * @param string $content_text
 * @param boolean $extension
 * @param boolean $img_base64
 * @return string
 */
if (!function_exists('handleHtmlBasic'))
{
    function handleHtmlBasic($content_page = [], $path_tmp = '', $name_file = '', $content_html = '', $ocr = false,
                             $content_text = '', $extension = 'pdf', $img_base64 = false)
    {
        if (count($content_page) <= 1)
        {
            $html_file = getFile($path_tmp, $name_file);

            $content_page = file_get_contents($html_file);

            if (preg_match_all('/<style\s.*?>(.*?)<\/style>/si', $content_page, $matches))
            {
                if (count($matches[1]) == 2) $matches[1] = implode(' ', $matches[1]);
                else $matches[1] = $matches[1][0];

                $matches[1]   = replaceCss($matches[1], $ocr);
                $content_html .= $matches[1];
            }
            $content_html .= closeHeader();

            if (preg_match('/<img\s.*?\bsrc="(.*?)".*?>/si', $content_page, $matches))
            {
                $image = $path_tmp . "/" . $name_file . "001.jpg";
                removeBorderImage($image, $path_tmp, $name_file . '001.jpg', 2, 10);
                if ($img_base64)
                {
                    $image = base64Image($image);
                }
                $img          = str_replace($matches[1], $image, $matches[0]);
                $content_page = str_replace($matches[0], $img, $content_page);
            }

            $content_page = checkWidthImage($content_page);

            if (preg_match('/<div\s.*?>(.*?)<\/div>/si', $content_page, $matches)) $content_page = $matches[1];

            if ($ocr) $content_page = regexOCR($content_page);

            $content_page = regex($content_page, $extension, 1);

            $content_html .= $content_page;

            if ($content_text != '')
            {
                $content_text_p = handleContentOCR($content_text);
                $content_html   .= $content_text_p;
            }

        }
        else
        {
            $content_html .= closeHeader();

            foreach ($content_page as $key => $row_page)
            {
                if (preg_match('/<img\s.*?\bsrc="(.*?)".*?>/si', $row_page, $matches))
                {
                    if ($key < 10)
                    {
                        $image = $path_tmp . "/" . $name_file . "00" . $key . ".jpg";
                        removeBorderImage($image, $path_tmp, $name_file . "00" . $key . ".jpg", 2, 2);
                    }
                    else
                    {
                        $image = $path_tmp . "/" . $name_file . "0" . $key . ".jpg";
                        removeBorderImage($image, $path_tmp, $name_file . "0" . $key . ".jpg", 2, 2);
                    }
                    if ($img_base64)
                    {
                        $image = base64Image($image);
                    }
                    $img      = str_replace($matches[1], $image, $matches[0]);
                    $row_page = str_replace($matches[0], $img, $row_page);
                }

                $row_page = checkWidthImage($row_page);

                if (preg_match('/<div\s.*?>(.*?)<\/div>/si', $row_page, $matches)) $row_page = $matches[1];

                if ($ocr) $row_page = regexOCR($row_page);

                $row_page = regex($row_page, $extension);

                $content_html .= $row_page;

                if ($content_text != '')
                {
                    $content_text_p = handleContentOCR($content_text[$key]);
                    $content_html   .= $content_text_p;
                }
            }
        }
        $content_html .= closeHTML();
        $content_html = replaceAll($content_html, $ocr);

        return $content_html;
    }
}

/**
 * @param array $content_page
 * @param string $path_file
 * @param string $name_file
 * @param string $content_html
 * @param string $content_html_image
 * @param boolean $img_base64
 * @return array
 */
if (!function_exists('handleHtmlAdvanced'))
{
    function handleHtmlAdvanced($content_page = [], $path_file = '', $name_file = '', $content_html = '', $content_html_image = '', $img_base64 = false)
    {
        if (count($content_page) <= 1)
        {
            $html_file = getFile($path_file, $name_file);

            $content_page = file_get_contents($html_file);

            if (preg_match_all('/<style\s.*?>(.*?)<\/style>/si', $content_page, $matches))
            {
                if (count($matches[1]) == 2) $matches[1] = implode(' ', $matches[1]);
                else $matches[1] = $matches[1][0];

                $matches[1]         = replaceCss($matches[1]);
                $content_html       .= $matches[1];
                $content_html_image .= $matches[1];
            }

            $content_html       .= closeHeader();
            $content_html_image .= closeHeader(false);

            $content_page_image = $content_page;

            if (preg_match('/<img\s.*?\bsrc="(.*?)".*?>/si', $content_page, $matches))
            {
                $image = $path_file . "/" . $name_file . "001.jpg";
                removeBorderImage($image, $path_file, $name_file . '001.jpg', 2, 10);
                if ($img_base64)
                {
                    $image = base64Image($image);
                }
                $img          = str_replace($matches[1], $image, $matches[0]);
                $content_page = str_replace($matches[0], $img, $content_page);
            }

            $content_page = checkWidthImage($content_page);

            if (preg_match('/<div\s.*?>(.*?)<\/div>/si', $content_page, $matches)) $content_page = $matches[1];

            if (preg_match('/<div\s.*?>(.*?)<\/div>/si', $content_page_image, $matches)) $content_page_image = $matches[1];

            $content_page       = regex($content_page, 1);
            $content_page_image = regex($content_page_image, 1);
            $content_html       .= $content_page;
            $content_html_image .= $content_page_image;
        }
        else
        {
            $content_html       .= closeHeader();
            $content_html_image .= closeHeader(false);

            foreach ($content_page as $key => $row_page)
            {
                $row_page_v2 = $row_page;
                if (preg_match('/<img\s.*?\bsrc="(.*?)".*?>/si', $row_page, $matches))
                {
                    if ($key < 10)
                    {
                        $image = $path_file . "/" . $name_file . "00" . $key . ".jpg";
                        removeBorderImage($image, $path_file, $name_file . "00" . $key . ".jpg", 2, 2);
                    }
                    else
                    {
                        $image = $path_file . "/" . $name_file . "0" . $key . ".jpg";
                        removeBorderImage($image, $path_file, $name_file . "0" . $key . ".jpg", 2, 2);
                    }
                    if ($img_base64)
                    {
                        $image = base64Image($image);
                    }
                    $img      = str_replace($matches[1], $image, $matches[0]);
                    $row_page = str_replace($matches[0], $img, $row_page);
                }

                $row_page = checkWidthImage($row_page);

                if (preg_match('/<div\s.*?>(.*?)<\/div>/si', $row_page, $matches)) $row_page = $matches[1];

                if (preg_match('/<div\s.*?>(.*?)<\/div>/si', $row_page_v2, $matches)) $row_page_v2 = $matches[0];

                $row_page           = regex($row_page);
                $row_page_v2        = regex($row_page_v2);
                $content_html       .= $row_page;
                $content_html_image .= $row_page_v2;
            }
        }
        return [
            'html'       => $content_html,
            'html_image' => $content_html_image
        ];
    }
}


/**
 * @param $string
 * @return $string
 */
if (!function_exists('checkWidthImage'))
{
    function checkWidthImage($string = '')
    {
        if (preg_match('/<img\s.*?\bwidth="(.*?)".*?>/si', $string, $matches))
        {
            $width = $matches[1];
            if ($width < 880)
            {
                $image_new = substr_replace($matches[0], ' style="padding-right:70px" ', 4, 1);
                $string    = str_replace($matches[0], $image_new, $string);
            }
        }
        return $string;
    }
}
/**
 * @param string $content_text
 * @return string
 */
if (!function_exists('handleContentOCR'))
{
    function handleContentOCR($content_text = '')
    {
        preg_match_all('/<p\s.*?>(.*?)<\/p>/si', $content_text, $matches_text);

        $content_p = $matches_text[0];

        if (!empty($content_p))
        {
            foreach ($content_p as $tag_p)
            {
                preg_match('/style="(.*?)"/i', $tag_p, $matches_style);

                $style = 'style="' . $matches_style[1] . "; background: transparent !important; line-height: 18px" . '"';

                $tag_p_new = str_replace($matches_style[0], $style, $tag_p);

                $content_p = str_replace($tag_p, $tag_p_new, $content_p);
            }
        }
        $content_text_p = implode("", $content_p);

        return $content_text_p;
    }
}
/**
 * @param $title
 * @param bool $flag
 * @param string $extension
 * @return string
 */
if (!function_exists('generateHeaderHTML'))
{
    function generateHeaderHTML($title, $flag = true, $extension = 'pdf', $ocr = false)
    {
        if ($flag)
        {
            return '<!DOCTYPE html>
                <html xmlns="http://www.w3.org/1999/xhtml" lang="vi" xml:lang="vi">
                <head>
                <title>' . $title . '</title>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <link rel="stylesheet" href="' . public_path('fonts/extract_cv/fontawesome-free-5.12.1-web/css/all.css') . '"> 
                <style type="text/css">
                     ' . generateFont() . '
                    body{font-family: "Roboto", sans-serif}
                    i.fas, i.far{margin-top: 2px}
                    ' . (($extension == 'pdf') ? '' . (($ocr) ? 'p{background: white; padding:4px 6px !important}' : 'p{line-height: 14px}') . '' : 'p{line-height: 14px}');
        }
        else
        {
            return '<!DOCTYPE html>
                <html xmlns="http://www.w3.org/1999/xhtml" lang="vi" xml:lang="vi">
                <head>
                    <title>' . $title . '</title>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    <link rel="stylesheet" href="' . public_path('fonts/extract_cv/fontawesome-free-5.12.1-web/css/all.css') . '">
                    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.js"></script>
                    <style type="text/css">
                         ' . generateFont() . '
                        body{font-family: "Roboto", sans-serif;  background: white}
                        i.fas, i.far{margin-top: 2px}
                        ' . (($extension == 'pdf') ? 'p{line-height: 20px; background: white}' : 'p{line-height: 14px}') . '
                        div{page-break-after: always; margin: 0 auto 15px auto}
        ';
        }
    }
}

/**
 * @param $content
 * @return mixed|string|string[]|null
 */
if (!function_exists('replaceAll'))
{
    function replaceAll($content, $ocr = false)
    {
        if ($ocr) $content = replaceFontSize($content);
        return $content;
    }
}

/**
 * @param string $path_tmp
 * @param string $name_file
 * @return string
 */
if (!function_exists('getFile'))
{
    function getFile($path_tmp = '', $name_file = '')
    {
        $html_file = '';

        if (file_exists($path_tmp . '/' . $name_file . '.html')) $html_file = $path_tmp . '/' . $name_file . '.html';
        if (file_exists($path_tmp . '/' . $name_file . '-1.html')) $html_file = $path_tmp . '/' . $name_file . '-1.html';

        return $html_file;
    }
}

/**
 * @param $string
 * @return mixed|string|string[]|null
 */
if (!function_exists('replaceCss'))
{
    function replaceCss($string, $ocr = false)
    {
        $string = trim(preg_replace('/\s\s+/', ' ', $string));
        $string = str_replace('<!--', '', $string);
        $string = str_replace('-->', '', $string);
        if ($ocr) $string = replaceFontSize($string);
        $string = preg_replace('~line\-height: ?([\d]+)px;~', '', $string);
        return $string;
    }
}

/**
 * @param $string
 * @return $string
 */

if (!function_exists('replaceFontSize'))
{
    function replaceFontSize($string)
    {
        $string = str_replace('font-size:14px', 'font-size:16px', $string);
        $string = str_replace('font-size:13px', 'font-size:16px', $string);
        $string = str_replace('font-size:12px', 'font-size:16px', $string);
        $string = str_replace('font-size:11px', 'font-size:16px', $string);
        $string = str_replace('font-size:10px', 'font-size:16px', $string);
        $string = str_replace('font-size:9px', 'font-size:16px', $string);

        return $string;
    }
}

/**
 * @return string
 */
if (!function_exists('generateFont'))
{
    function generateFont()
    {
        return
            '
            @font-face {
            font-family: \'Roboto\';
            font-style: normal;
            font-weight: 100;
            src: url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100.eot") . '"); /* IE9 Compat Modes */
            src: local(\'Roboto Thin\'), local(\'Roboto-Thin\'),
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100.eot?#iefix") . '") format("embedded-opentype"), /* IE6-IE8 */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100.woff2") . '") format(\'woff2\'), /* Super Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100.woff") . '") format(\'woff\'), /* Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100.ttf") . '") format(\'truetype\'), /* Safari, Android, iOS */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100.svg#Roboto") . '") format(\'svg\'); /* Legacy iOS */
            }
        @font-face {
            font-family: \'Roboto\';
            font-style: italic;
            font-weight: 100;
            src: url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100italic.eot") . '"); /* IE9 Compat Modes */
            src: local(\'Roboto Thin Italic\'), local(\'Roboto-ThinItalic\'),
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100italic.eot?#iefix") . '") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100italic.woff2") . '") format(\'woff2\'), /* Super Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100italic.woff") . '") format(\'woff\'), /* Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100italic.ttf") . '") format(\'truetype\'), /* Safari, Android, iOS */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-100italic.svg#Robot") . '") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: normal;
            font-weight: 300;
            src: url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300.eot") . '"); /* IE9 Compat Modes */
            src: local(\'Roboto Light\'), local(\'Roboto-Light\'),
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300.eot?#iefix") . '") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300.woff2") . '") format(\'woff2\'), /* Super Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300.woff") . '") format(\'woff\'), /* Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300.ttf") . '") format(\'truetype\'), /* Safari, Android, iOS */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300.svg#Roboto") . '") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: italic;
            font-weight: 300;
            src: url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300italic.eot") . '"); /* IE9 Compat Modes */
            src: local(\'Roboto Light Italic\'), local(\'Roboto-LightItalic\'),
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300italic.eot?#iefix") . '") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300italic.woff2") . '") format(\'woff2\'), /* Super Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300italic.woff") . '") format(\'woff\'), /* Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300italic.ttf") . '") format(\'truetype\'), /* Safari, Android, iOS */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-300italic.svg#Roboto") . '")format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: normal;
            font-weight: 400;
            src: url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-regular.eot") . '"); /* IE9 Compat Modes */
            src: local(\'Roboto\'), local(\'Roboto-Regular\'),
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-regular.eot?#iefix") . '") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-regular.woff2") . '") format(\'woff2\'), /* Super Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-regular.woff") . '") format(\'woff\'), /* Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-regular.ttf") . '") format(\'truetype\'), /* Safari, Android, iOS */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-regular.svg#Roboto") . '") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: italic;
            font-weight: 400;
            src: url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-italic.eot") . '"); /* IE9 Compat Modes */
            src: local(\'Roboto Italic\'), local(\'Roboto-Italic\'),
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-italic.eot?#iefix") . '") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-italic.woff2") . '") format(\'woff2\'), /* Super Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-italic.woff") . '") format(\'woff\'), /* Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-italic.ttf") . '") format(\'truetype\'), /* Safari, Android, iOS */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-italic.svg#Roboto") . '") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: normal;
            font-weight: 500;
            src: url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500.eot") . '"); /* IE9 Compat Modes */
            src: local(\'Roboto Medium\'), local(\'Roboto-Medium\'),
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500.eot?#iefix") . '") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500.woff2") . '") format(\'woff2\'), /* Super Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500.woff") . '") format(\'woff\'), /* Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500.ttf") . '") format(\'truetype\'), /* Safari, Android, iOS */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500.svg#Roboto") . '") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: italic;
            font-weight: 500;
            src: url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500italic.eot") . '"); /* IE9 Compat Modes */
            src: local(\'Roboto Medium Italic\'), local(\'Roboto-MediumItalic\'),
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500italic.eot?#iefix") . '") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500italic.woff2") . '") format(\'woff2\'), /* Super Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500italic.woff") . '") format(\'woff\'), /* Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500italic.ttf") . '") format(\'truetype\'), /* Safari, Android, iOS */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-500italic.svg#Roboto") . '") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: normal;
            font-weight: 700;
            src: url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700.eot") . '"); /* IE9 Compat Modes */
            src: local(\'Roboto Bold\'), local(\'Roboto-Bold\'),
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700.eot?#iefix") . '") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700.woff2") . '") format(\'woff2\'), /* Super Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700.woff") . '") format(\'woff\'), /* Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700.ttf") . '") format(\'truetype\'), /* Safari, Android, iOS */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700.svg#Roboto") . '") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: italic;
            font-weight: 700;
            src: url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700italic.eot") . '"); /* IE9 Compat Modes */
            src: local(\'Roboto Bold Italic\'), local(\'Roboto-BoldItalic\'),
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700italic.eot?#iefix") . '") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700italic.woff2") . '") format(\'woff2\'), /* Super Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700italic.woff") . '") format(\'woff\'), /* Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700italic.ttf") . '") format(\'truetype\'), /* Safari, Android, iOS */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-700italic.svg#Roboto") . '") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: normal;
            font-weight: 900;
            src: url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900.eot") . '"); /* IE9 Compat Modes */
            src: local(\'Roboto Black\'), local(\'Roboto-Black\'),
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900.eot?#iefix") . '") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900.woff2") . '") format(\'woff2\'), /* Super Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900.woff") . '") format(\'woff\'), /* Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900.ttf") . '") format(\'truetype\'), /* Safari, Android, iOS */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900.svg#Roboto") . '") format(\'svg\'); /* Legacy iOS */
        }
        @font-face {
            font-family: \'Roboto\';
            font-style: italic;
            font-weight: 900;
            src: url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900italic.eot") . '"); /* IE9 Compat Modes */
            src: local(\'Roboto Black Italic\'), local(\'Roboto-BlackItalic\'),
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900italic.eot?#iefix") . '") format(\'embedded-opentype\'), /* IE6-IE8 */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900italic.woff2") . '") format(\'woff2\'), /* Super Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900italic.woff") . '") format(\'woff\'), /* Modern Browsers */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900italic.ttf") . '") format(\'truetype\'), /* Safari, Android, iOS */
               url("' . public_path("fonts/extract_cv/roboto-v20-greek-ext_latin_cyrillic-ext_cyrillic_latin-ext_vietnamese_greek-900italic.svg#Roboto") . '") format(\'svg\'); /* Legacy iOS */
        }';
    }
}

/**
 * @param bool $flag
 * @return string
 */
if (!function_exists('closeHeader'))
{
    function closeHeader($flag = true)
    {
        if ($flag === true)
        {
            return '
                </style>
                <script src="' . public_path('fontawesome-free-5.12.1-web/js/all.js') . '"></script>
                </head>
                <body>
                ';
        }
        else
        {
            return '
                </style>
                <script src="' . public_path('fontawesome-free-5.12.1-web/js/all.js') . '"></script>
                </head>
                <body id="body">
                ';
        }
    }
}

/**
 * @return string
 */
if (!function_exists('closeHTML'))
{
    function closeHTML()
    {
        return '
               </body></html>
                ';
    }
}

/**
 * @return string
 */
if (!function_exists('generateScript'))
{
    function generateScript()
    {
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


/**
 * @return string
 */
if (!function_exists('base64Image'))
{
    function base64Image($image_path)
    {
        $type   = pathinfo($image_path, PATHINFO_EXTENSION);
        $data   = file_get_contents($image_path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        return $base64;
    }
}
