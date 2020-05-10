<?php
/**
 * Created by PhpStorm.
 * User: TranLuong
 * Date: 8/5/2020
 * Time: 1:28 PM
 */
$name_folder = uniqid();
$output_dir = storage_path('files/' . $name_folder);

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
    'cv_protected' => '[123cv.net protected]',
    'path_ocrmypdf' => '/home/uuu/.local/bin/ocrmypdf'
];
