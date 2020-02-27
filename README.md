**Convert PDF to HTML, HTML to PDF, HTML to Image For Laravel**

Require this package in your composer.json and update composer.
```
    composer require luongtv/extract
```
**Installation**

After updating composer, add the ServiceProvider to the providers array in config/app.php

```php
    Luongtv\Extract\ExtractServiceProvider::class
```
**Laravel 5.x:**

**I. PDF to HTML**

**1. Install Poppler-Utils**

**Debian/Ubuntu**
```
    sudo apt-get install poppler-utils
```
**Mac OS X**
```
    brew install poppler
```
**Window**

For those who need this package in windows, there is a way. First download poppler-utils for windows here http://blog.alivate.com.au/poppler-windows/. And download the latest binary.

After download it, extract it.

**2. We need to know where is utilities**

**Debian/Ubuntu**
```
    $ whereis pdftohtml
    pdftohtml: /usr/bin/pdftohtml
    
    $ whereis pdfinfo
    pdfinfo: /usr/bin/pdfinfo
```
**Mac OS X**
```$ which pdfinfo
   /usr/local/bin/pdfinfo
   
   $ which pdftohtml
   /usr/local/bin/pdfinfo
```
**Window**

Go in extracted directory. There will be a directory called ```bin```. We will need this one.

**3. PHP Configuration with shell access enabled**

**Usage**

Create files folder in storage folder

**Example**

```php
    use Luongtv\Extract\core\PdfToHtml;

    $name_file = 'cv24';
    $file = $name_file . ".pdf";
    $id = uniqid();
    $options = [
        'pdftohtml_path' => '/usr/bin/pdftohtml',
        'pdfinfo_path' => '/usr/bin/pdfinfo'
        'clearAfter' => false,
        'outputDir' => storage_path('files/'.$id),
    ];
    //example for Window
    //$options = [
    //            'pdftohtml_path' => '/path/to/poppler/bin/pdftohtml.exe',
    //            'pdfinfo_path' => '/path/to/poppler/bin/pdfinfo.exe',
    //            'clearAfter' => false,
    //            'outputDir' => storage_path('files/'.$id),
    //        ];
    $pdf = new PdfToHtml($file, $options);
    $pdf->generateHTML($name_file, $id);
```
**Full options**

```php
    $full_settings = [
        'pdftohtml_path' => '/usr/bin/pdftohtml', // path to pdftohtml
        'pdfinfo_path' => '/usr/bin/pdfinfo', // path to pdfinfo
    
        'generate' => [ // settings for generating html
            'singlePage' => false, // we want separate pages
            'imageJpeg' => false, // we want png image
            'ignoreImages' => false, // we need images
            'zoom' => 1.5, // scale pdf
            'noFrames' => false, // we want separate pages
        ],
    
        'clearAfter' => true, // auto clear output dir (if removeOutputDir==false then output dir will remain)
        'removeOutputDir' => true, // remove output dir
        'outputDir' => '/tmp/'.uniqid(), // output dir
    
        'html' => [ // settings for processing html
            'inlineCss' => true, // replaces css classes to inline css rules
            'inlineImages' => true, // looks for images in html and replaces the src attribute to base64 hash
            'onlyContent' => true, // takes from html body content only
        ]
    ]
```
**II. HTML to PDF**

**Usage**

**Example**
```php
    use Luongtv\Extract\core\HtmlToPdf;

    $html = new HtmlToPdf();
    $file = 'test.html';
    $option = [
        'dpi' => 120
    ];
    $pdf_generate =  $html->generatePDF($file, $option);
    return $pdf_generate;
```

**Full options**

```
    rootDir: "{app_directory}/vendor/dompdf/dompdf"
    tempDir: "/tmp" (available in config/dompdf.php)
    fontDir: "{app_directory}/storage/fonts/" (available in config/dompdf.php)
    fontCache: "{app_directory}/storage/fonts/" (available in config/dompdf.php)
    chroot: "{app_directory}" (available in config/dompdf.php)
    logOutputFile: "/tmp/log.htm"
    defaultMediaType: "screen" (available in config/dompdf.php)
    defaultPaperSize: "a4" (available in config/dompdf.php)
    defaultFont: "serif" (available in config/dompdf.php)
    dpi: 96 (available in config/dompdf.php)
    fontHeightRatio: 1.1 (available in config/dompdf.php)
    isPhpEnabled: false (available in config/dompdf.php)
    isRemoteEnabled: true (available in config/dompdf.php)
    isJavascriptEnabled: true (available in config/dompdf.php)
    isHtml5ParserEnabled: false (available in config/dompdf.php)
    isFontSubsettingEnabled: false (available in config/dompdf.php)
    debugPng: false
    debugKeepTemp: false
    debugCss: false
    debugLayout: false
    debugLayoutLines: true
    debugLayoutBlocks: true
    debugLayoutInline: true
    debugLayoutPaddingBox: true
    pdfBackend: "CPDF" (available in config/dompdf.php)
    pdflibLicense: ""
    adminUsername: "user"
    adminPassword: "password"
```

**III. HTML to IMAGE**

**Usage**

Copy the html file path under the ```$id .'_image.html ' ($id generated in pdf to html)``` structure on the browser and run the file. Turn on F12, switch to the console tab will have the image as a base64
**IV. DOC to PDF**

**Usage**

Refer to https://www.php.net/manual/en/book.com.php

**Requirement**

Add to php.ini

```
   [PHP_COM_DOTNET]
   extension=php_com_dotnet.dll 
```

**Example**

```php
    use Luongtv\Extract\core\DocToPdf;
    
    //test.doc is a convert to pdf file.
    //test.pdf is the path to save the test.pdf file
    $doc = new DocToPdf();
    $doc->generatePDF('test.doc', 'test.pdf');  
```

**V. License**

This DOMPDF Wrapper for Laravel is open-sourced software licensed under the MIT license