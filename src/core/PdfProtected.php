<?php
/**
 * Created by PhpStorm.
 * User: TranLuong
 * Date: 3/3/2020
 * Time: 2:09 PM
 */

namespace WorkableCV\Extract\core;


use Illuminate\Support\Facades\App;

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
     *
     * @param null $name_file
     * @param null $path_tmp
     * @param bool $ocr
     * @param bool $flag_text
     * @param string $extension
     * @param int $path_save
     * @param null $folder_save
     * @return string
     * User: TranLuong
     * Date: 13/09/2020
     */
    public function pdfProtected($name_file = null, $path_tmp = null, $ocr = false, $flag_text = false, $extension = 'pdf',
                                 $path_save = 0, $folder_save = null)
    {
        if ($folder_save)
        {
            $path = $folder_save . '/' . date('Y') . '/' . date('m') . '/' . date('d');
        }
        else
        {
            $path = 'files/' . date('Y') . '/' . date('m') . '/' . date('d');
        };
        createFolder($path, $path_save);

        $file_pdf_name      = generateNewFileName($name_file);
        $content_html       = $this->handelHtml($name_file, $path_tmp, $ocr, $flag_text, $extension);
        $path_pdf_protected = $path . '/' . $file_pdf_name . '.pdf';

        saveProtected($path_save, $path_pdf_protected, $content_html);
        deleteAll($path_tmp);

        return $path_pdf_protected;
    }

    /**
     * Note:
     * @param null $name_file
     * @param null $path_tmp
     * @param bool $ocr
     * @param bool $flag_text
     * @param string $extension
     * @return string
     * User: TranLuong
     * Date: 17/11/2020
     */
    public function handelHtml($name_file = null, $path_tmp = null, $ocr = false, $flag_text = false, $extension = 'pdf')
    {
        $cv_info      = $this->handelInfoCV($name_file, $ocr, $flag_text, $extension);
        $content_page = $cv_info['content_page'];
        $content_html = $cv_info['content_html'];
        $content_text = $cv_info['content_text'];

        $content_html = handleHtmlBasic($content_page, $path_tmp, $name_file, $content_html, $ocr, $content_text, $extension);
        return $content_html;
    }

    /**
     * Note:
     * @param null $name_file
     * @param bool $ocr
     * @param bool $flag_text
     * @param string $extension
     * @return array
     * User: TranLuong
     * Date: 17/11/2020
     */
    public function handelInfoCV($name_file = null, $ocr = false, $flag_text = false, $extension = 'pdf')
    {
        $pdf_info = $this->getInfo();
        $title    = 'Xem CV';

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

        return [
            'content_html' => $content_html,
            'content_page' => $content_page,
            'content_text' => $content_text
        ];
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

        $rand                 = uniqid();
        $output_dir           = storage_path($rand);
        $options['outputDir'] = $output_dir;
        $pdf                  = new self($file, $options);

        $count_page = count($pdf->getHtml()->getAllPages());
        if ($count_page <= 1)
        {
            $html_file = getFile($output_dir, $name_file);
            $content   = file_get_contents($html_file);
        }
        else
        {
            $content = $pdf->getHtml()->getAllPages();
        }
        deleteAll($output_dir);
        return $content;
    }

    /**
     * Note:
     * @param null $name_file
     * @param null $path_tmp
     * @param bool $ocr
     * @param bool $flag_text
     * @param string $extension
     * @param int $path_save
     * @param null $folder_save
     * @param array $options
     * @return string
     * User: TranLuong
     * Date: 17/11/2020
     */
    public function pdfProtectedWKH($name_file = null, $path_tmp = null, $ocr = false, $flag_text = false, $extension = 'pdf',
                                    $path_save = 0, $folder_save = null, $options = [])
    {
        if ($folder_save)
        {
            $path = $folder_save . '/' . date('Y') . '/' . date('m') . '/' . date('d');
        }
        else
        {
            $path = 'files/' . date('Y') . '/' . date('m') . '/' . date('d');
        }
        createFolder($path, $path_save);

        $file_pdf_name      = generateNewFileName($name_file);
        $path_pdf_protected = $path . '/' . $file_pdf_name . '.pdf';

        $cv_info            = $this->handelInfoCV($name_file, $ocr, $flag_text, $extension);
        $content_page       = $cv_info['content_page'];
        $content_html       = $cv_info['content_html'];
        $content_text       = $cv_info['content_text'];

        $content_html = handleHtmlWKH($content_page, $path_tmp, $name_file, $content_html, $ocr, $content_text, $extension);;
        $pdfWithHtml  = App::make('dompdf.wrapper');
        $content_html = $pdfWithHtml->convertEntities($content_html, null);

        $snappy = new  \Knp\Snappy\Pdf(config('extract.path_wkhtmltopdf'));
        //option default
        $snappy->setOption('margin-bottom', '0mm');
        $snappy->setOption('margin-left', '0mm');
        $snappy->setOption('margin-top', '0mm');
        $snappy->setOption('margin-right', '0mm');
        $snappy->setOption('zoom', '1.2');

        try
        {
            $snappy->generateFromHtml($content_html, $path_pdf_protected, $options);
            deleteAll($path_tmp);
            return $path_pdf_protected;
        } catch (\Exception $exception)
        {
            deleteAll($path_tmp);
            return $exception->getMessage();
        }
    }
}
