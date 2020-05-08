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
        'pdftohtml_path' => 'C:\Users\ASUS\Desktop\poppler-0.68.0\bin\pdftohtml.exe',
        'pdfinfo_path'   => 'C:\Users\ASUS\Desktop\poppler-0.68.0\bin\pdfinfo.exe',
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
    'protected' => '[123cv.net protected]'
];
