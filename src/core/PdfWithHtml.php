<?php

namespace WorkableCV\Extract\core;

use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Response;

/**
 * A Laravel wrapper for Dompdf
 *
 * @package laravel-dompdf
 * @author Barry vd. Heuvel
 */
class PdfWithHtml
{

    /** @var Dompdf */
    protected $dompdf;

    /** @var \Illuminate\Contracts\Config\Repository */
    protected $config;

    /** @var \Illuminate\Filesystem\Filesystem */
    protected $files;

    /** @var \Illuminate\Contracts\View\Factory */
    protected $view;

    protected $rendered = false;
    protected $showWarnings;
    protected $public_path;

    /**
     * @param Dompdf $dompdf
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param \Illuminate\Contracts\View\Factory $view
     */
    public function __construct(Dompdf $dompdf, ConfigRepository $config, Filesystem $files, ViewFactory $view)
    {
        $this->dompdf = $dompdf;
        $this->config = $config;
        $this->files  = $files;
        $this->view   = $view;

        $this->showWarnings = $this->config->get('dompdf.show_warnings', false);
    }

    /**
     * Get the DomPDF instance
     *
     * @return Dompdf
     */
    public function getDomPDF()
    {
        return $this->dompdf;
    }

    /**
     * Set the paper size (default A4)
     *
     * @param string $paper
     * @param string $orientation
     * @return $this
     */
    public function setPaper($paper, $orientation = 'portrait')
    {
        $this->dompdf->setPaper($paper, $orientation);
        return $this;
    }

    /**
     * Show or hide warnings
     *
     * @param bool $warnings
     * @return $this
     */
    public function setWarnings($warnings)
    {
        $this->showWarnings = $warnings;
        return $this;
    }

    /**
     * Load a HTML file
     *
     * @param string $file
     * @return static
     */
    public function loadFile($file)
    {
        $this->dompdf->loadHtmlFile($file);
        $this->rendered = false;
        return $this;
    }

    /**
     * Add metadata info
     *
     * @param array $info
     * @return static
     */
    public function addInfo($info)
    {
        foreach ($info as $name => $value)
        {
            $this->dompdf->add_info($name, $value);
        }
        return $this;
    }

    /**
     * Load a View and convert to HTML
     *
     * @param string $view
     * @param array $data
     * @param array $mergeData
     * @param string $encoding Not used yet
     * @return static
     */
    public function loadView($view, $data = array(), $mergeData = array(), $encoding = null)
    {
        $html = $this->view->make($view, $data, $mergeData)->render();
        return $this->loadHTML($html, $encoding);
    }

    /**
     * Load a HTML string
     *
     * @param string $string
     * @param string $encoding Not used yet
     * @return static
     */
    public function loadHTML($string, $path_file = null, $encoding = null)
    {
        $string = $this->convertEntities($string, $path_file);
        $this->dompdf->loadHtml($string, $encoding);
        $this->rendered = false;
        return $this;
    }

    protected function convertEntities($subject, $path_file)
    {
        $entities = array(
            '€' => '&#0128;',
            '£' => '&pound;',
            '' => '<i class="fas fa-angle-double-right"></i>',
            '' => '<i class="fas fa-circle"></i>',
            '' => '<i class="far fa-bookmark"></i>',
            '' => '<i class="fas fa-phone-alt"></i>',
            '' => '<i class="fas fa-envelope"></i>',
            '' => '<i class="fas fa-map-marker-alt"></i>',
            '' => '<i class="far fa-calendar-alt"></i>',
            '' => '<i class="fas fa-mobile-alt"></i>',
            '' => '<i class="fas fa-chart-bar"></i>',
            '' => '<i class="fas fa-folder"></i>',
            '' => '<i class="fas fa-briefcase"></i>',
            '' => '<i class="fas fa-graduation-cap"></i>',
            '' => '<i class="fas fa-laptop"></i>',
            '' => '<i class="fas fa-check-circle"></i>',
            '' => '<i class="fas fa-headphones"></i>',
            '' => '<i class="far fa-circle"></i>',
            '' => '<i class="fas fa-envelope"></i>',
            '' => '<i class="fas fa-info"></i>',
            '' => '<i class="fas fa-user"></i>',
            '' => '<i class="far fa-calendar-alt"></i>',
            '' => '<i class="fas fa-road"></i>',
            '' => '<i class="far fa-lightbulb"></i>',
            '' => '<i class="fas fa-book"></i>',
            '' => '<i class="fas fa-trophy"></i>',
            '' => '<i class="fas fa-star"></i>',
            '' => '<i class="far fa-file-alt"></i>',
            '' => '<i class="fas fa-link"></i>',
            '' => '<i class="fas fa-globe-asia"></i>',
            '' => '<i class="fas fa-rocket"></i>',
            '' => '<i class="fas fa-cog"></i>',
            ''  => '<i class="fas fa-check-square"></i>',
            '' => '<i class="fas fa-check-square"></i>',
            '' => '<i class="fas fa-globe-asia"></i>',
            '' => '<i class="fas fa-briefcase"></i>',
            '' => '<i class="fab fa-gratipay"></i>',
            '' => '<i class="fas fa-male"></i>',
            '' => '<i class="fas fa-chart-pie"></i>',
            '●' => '<i class="fas fa-circle"></i>',
            '➢' => '<i class="fas fa-long-arrow-alt-right"></i>',
            '❖' => '<i class="fas fa-angle-right"></i>',
            '' => '<i class="fas fa-envelope"></i>',
            '' => '<i class="fas fa-mobile-alt"></i>',
            '✓' => '<i class="fas fa-check"></i>',
            '' => '<i class="fas fa-book-open"></i>',
            '' => '<i class="fas fa-music"></i>',
            '' => '<i class="fas fa-laptop"></i>',
            '' => '<i class="fas fa-map-marker-alt"></i>',
            '' => '<i class="fas fa-male"></i>',
            '' => '<i class="fas fa-check-circle"></i>',
            '' => '<i class="fas fa-user"></i>',
            '' => '<i class="fas fa-map-marker-alt"></i>',
            '' => '<i class="fas fa-mobile-alt"></i>',
            '' => '<i class="fas fa-bars"></i>',
            '' => '<i class="far fa-envelope"></i>',
            '' => '<i class="fas fa-users"></i>',
            '' => '<i class="fas fa-pencil-alt"></i>',
            '' => '<i class="fas fa-award"></i>',
            '' => '<i class="fas fa-atom"></i>',
            '' => '<i class="far fa-star"></i>',
            '' => '<i class="fas fa-check"></i>',
            '' => '<i class="fas fa-calendar-day"></i>',
            '' => '<i class="fas fa-user-edit"></i>',
            '' => '<i class="fas fa-user-tag"></i>',
            '' => '<i class="fas fa-user-graduate"></i>',
            '' => '<i class="fas fa-id-card-alt"></i>',
            '' => '<i class="fas fa-hand-holding-heart"></i>',
            '' => '<i class="fas fa-user-edit"></i>',
            '' => '<i class="fas fa-language"></i>',
            '' => '<i class="fas fa-briefcase"></i>',
            '' => '<i class="fas fa-user-tie"></i>',
            '' => '<i class="fas fa-phone-alt"></i>',
            '' => '<i class="fas fa-map-marker-alt"></i>',
            '◼' => '<i class="fas fa-square"></i>',
            '' => '<i class="far fa-square"></i>',
            '' => '<i class="fas fa-calendar-day"></i>',
            '' => '<i class="fas fa-map-marker-alt"></i>',
            '' => '<i class="fas fa-circle"></i>',
            '' => '<i class="fas fa-map-marker-alt"></i>',
            '' => '<i class="far fa-money-bill-alt"></i>',
            '' => '<i class="fas fa-info"></i>',
            '' => '<i class="fas fa-bolt"></i>',
            '' => '<i class="fas fa-volume-down"></i>',
            '' => '<i class="fas fa-heart-broken"></i>',
            '' => '<i class="fas fa-map-marker-alt"></i>',
            '' => '<i class="fas fa-calendar-day"></i>',
            '' => '<i class="fas fa-glasses"></i>',
            '' => '<i class="fas fa-circle"></i>',
            '' => '<i class="fas fa-user"></i>',
            '' => '<i class="fas fa-university"></i>'
        );

        foreach ($entities as $search => $replace)
        {
            $subject = str_replace($search, $replace, $subject);
        }
        if ($path_file != null)
        {
            unlink($path_file);
            if (!file_exists($path_file))
            {
                $my_file = fopen($path_file, 'w');
                fwrite($my_file, $subject);
                fclose($my_file);
            }
        }
        return $subject;
    }

    /**
     * Set/Change an option in DomPdf
     *
     * @param array $options
     * @return static
     */
    public function setOptions(array $options)
    {
        $options = new Options($options);
        $this->dompdf->setOptions($options);
        return $this;
    }

    /**
     * Save the PDF to a file
     *
     * @param $filename
     * @return static
     */
    public function save($filename)
    {
        $this->files->put($filename, $this->output());
        return $this;
    }

    /**
     * Output the PDF as a string.
     *
     * @return string The rendered PDF as string
     */
    public function output()
    {
        if (!$this->rendered)
        {
            $this->render();
        }
        return $this->dompdf->output();
    }

    /**
     * Render the PDF
     */
    protected function render()
    {
        if (!$this->dompdf)
        {
            throw new Exception('DOMPDF not created yet');
        }

        $this->dompdf->render();

        if ($this->showWarnings)
        {
            global $_dompdf_warnings;
            if (!empty($_dompdf_warnings) && count($_dompdf_warnings))
            {
                $warnings = '';
                foreach ($_dompdf_warnings as $msg)
                {
                    $warnings .= $msg . "\n";
                }
                // $warnings .= $this->dompdf->get_canvas()->get_cpdf()->messages;
                if (!empty($warnings))
                {
                    throw new Exception($warnings);
                }
            }
        }
        $this->rendered = true;
    }

    /**
     * Make the PDF downloadable by the user
     *
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    public function download($filename = 'document.pdf')
    {
        $output = $this->output();
        return new Response($output, 200, array(
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length'      => strlen($output),
        ));
    }

    /**
     * Return a response with the PDF to show in the browser
     *
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    public function stream($filename = 'document.pdf')
    {
        $output = $this->output();
        return new Response($output, 200, array(
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ));
    }

    public function setEncryption($password)
    {
        if (!$this->dompdf)
        {
            throw new Exception("DOMPDF not created yet");
        }
        $this->render();
        return $this->dompdf->getCanvas()->get_cpdf()->setEncryption("pass", $password);
    }

}
