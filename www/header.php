<?php
include_once(__DIR__ . "/../config.php");

?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
            if (isset($pagename)) {
                echo $pagename;
            }
            ?></title>
    
    <link rel="stylesheet" href="/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link href="/css/css.css?v=<?php echo filemtime(__DIR__ . '/css/css.css'); ?>" rel="stylesheet">

    <link href='/node_modules/@fortawesome/fontawesome-free/css/all.min.css' rel='stylesheet' type='text/css'>
    <script src="/node_modules/jquery/dist/jquery.min.js"></script>
    <script src="/js/js.js?v=<?php echo filemtime(__DIR__ . '/js/js.js'); ?>"></script>

    <?php
    if (strrpos($_SERVER["REQUEST_URI"], "/admin/") !== false) {
        echo "<script src='/js/admin.js?time=";
        echo filemtime(__DIR__ . '/js/admin.js');
        echo "'></script>\n";
    }
    if (isset($tinymce)) {
        echo "<script src='/node_modules/tinymce/tinymce.min.js'></script>\n";
        echo "<script src='/js/tinymce.js?time=";
        echo filemtime(__DIR__ . '/js/tinymce.js');
        echo "' defer></script>\n";
    }
    /*
    if ($site['custom_css'] != '') {
        echo "<style>{$site['custom_css']}</style>";
    }
    if ($site['custom_js'] != '') {
        echo "<script>{$site['custom_js']}</script>";
    }
    */
    ?>

</head>

<body>
    <div class="container">
        <div class="row mb-4">
            <div class="col-1 text-left p-1 pt-3"><img src='/logos/hrlogo.small.jpg' style="max-width:100%;min-width:50px;" /></div>
            <h2 class="text-center col-10" style="line-height: 5rem;">Like dislike administration</h2>
            <div class="col-1 text-left p-1 pt-3"><img src='/logos/ldlogo.png' style="max-width:100%;min-width:50px;" /></div>
        </div>