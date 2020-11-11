<?php
/**
 * Created by PhpStorm.
 * User: TranLuong
 * Date: 8/5/2020
 * Time: 1:28 PM
 */
$name_folder = uniqid();
$output_dir = public_path('files/' . $name_folder);
$output_protected = public_path('cv_protected');

return [
    'options_extract'       => [
        'pdftohtml_path' => '/usr/bin/pdftohtml',
        'pdfinfo_path'   => '/usr/bin/pdfinfo',
        'generate'       => [
            'singlePage'   => false,
            'imageJpeg'    => true,
            'ignoreImages' => false,
            'zoom'         => 1.5 . ' -hidden -nodrm',
            'noFrames'     => false,
        ],
        'clearAfter'     => false,
        'outputDir'      => $output_dir
    ],
    'protected' => '[123cv.net protected]',
    'output_cv_protected' => 'cv_protected',//folder nằm trong thư mục public
    'path_ocrmypdf' => '/home/tranluong/.local/bin/ocrmypdf',
    'ocrmypdf' => '',
    'change_cv_from' => 'offerjob.vn'
];
