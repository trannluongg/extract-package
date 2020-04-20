<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 3/3/2020
 * Time: 2:09 PM
 */

namespace Luongtv\Extract\core;

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
     * @param null $name_file
     * @param null $path_tmp
     * @param string $extension
     * @param int $path_save
     * @param null $folder_save
     * @param bool $ocr
     */
    public function pdfProtected($name_file = null, $path_tmp = null, $ocr = false, $extension = 'pdf', $path_save = 0, $folder_save = null)
    {
        if ($folder_save) $path = $folder_save;
        else $path = 'files/' . date('Y') . '/' . date('m') . '/' . date('d');

        createFolder($path, $path_save);

        $file_pdf_name = date('d_m_Y') . '__________' . uniqid();

        $pdf_info = $this->getInfo();

        $title    = 'Xem CV';

        if (isset($pdf_info['title']))
        {
            $title = $pdf_info['title'];
        }

        $content_html = generateHeaderHTML($title, true, $extension, $ocr);

        $content_page = $this->getHtml()->getAllPages();

        $content_html = handleHtmlBasic($content_page, $path_tmp, $name_file, $content_html, $ocr);

        $path_pdf_protected = $path . '/' . $file_pdf_name . '.pdf';

        saveProtected($path_save, $path_pdf_protected, $content_html);

        deleteAll($path_tmp);
    }


}