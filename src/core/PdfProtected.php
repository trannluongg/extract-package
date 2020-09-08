<?php
/**
 * Created by PhpStorm.
 * User: TranLuong
 * Date: 3/3/2020
 * Time: 2:09 PM
 */

namespace WorkableCV\Extract\core;

class PdfProtected extends Pdf
{
    private $file;
    private $options;

    /**
     * PdfToHtml constructor.
     * @param $file
     * @param array $options
     */
    public function __construct($file, array $options = [])
    {
        $this->file    = $file;
        $this->options = $options;
        parent::__construct($file, $options);
    }

    /**
     * @param null $name_file
     * @param null $path_tmp
     * @param bool $ocr
     * @param bool $flag_text
     * @param string $extension
     * @param int $path_save
     * @param null $folder_save
     */
    public function pdfProtected($name_file = null, $path_tmp = null, $ocr = false, $flag_text = false, $extension = 'pdf',
                                 $path_save = 0, $folder_save = null)
    {
        if ($folder_save) $path = $folder_save . '/' . date('Y') . '/' . date('m') . '/' . date('d');
        else $path = 'files/' . date('Y') . '/' . date('m') . '/' . date('d');

        createFolder($path, $path_save);

        $file_pdf_name = date('Y_m_d') . '__________' . uniqid();

        $pdf_info = $this->getInfo();

        $title = 'Xem CV';

        if (isset($pdf_info['title']))
        {
            $title = $pdf_info['title'];
        }

        $content_html = generateHeaderHTML($title, true, $extension, $ocr);

        $content_page = $this->getHtml()->getAllPages();

        $content_text = '';

        if ($flag_text)
        {
            $content_text = $this->getTextNoOCR($this->file, $this->options, $name_file);
        }

        $content_html = handleHtmlBasic($content_page, $path_tmp, $name_file, $content_html, $ocr, $content_text, $extension);

        $path_pdf_protected = $path . '/' . $file_pdf_name . '.pdf';

        saveProtected($path_save, $path_pdf_protected, $content_html);

        deleteAll($path_tmp);

        return $path_pdf_protected;
    }

    /**
     * @param string $file
     * @param array $options
     * @param string $name_file
     * @return array|false|string
     */
    private function getTextNoOCR($file = '', $options = [], $name_file = '')
    {
        $options['generate']['zoom'] = '1.5 -nodrm';

        $rand = uniqid();

        $output_dir = storage_path($rand);

        $options['outputDir'] = $output_dir;

        $pdf = new self($file, $options);

        $count_page = count($pdf->getHtml()->getAllPages());

        if ($count_page <= 1)
        {
            $html_file = getFile($output_dir, $name_file);

            $content = file_get_contents($html_file);
        }
        else
        {
            $content = $pdf->getHtml()->getAllPages();
        }
        deleteAll($output_dir);
        return $content;

    }
}
