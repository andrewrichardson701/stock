<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// PAGE HEADER SETUP - SETS UP CSS, BOOTSTRAP AND OTHER STYLES AND SCRIPTS
$versionNumber = 'v1.3.0';

include './includes/get-config.inc.php'; // get config options

// anti clickjacking defense
header("X-Frame-Options: DENY");
// Set a cookie with the Secure flag for defense against cookie attacks
setcookie("stockbase_cookie", bin2hex(random_bytes(32)), [ 'expires' => time() + 3600, 'path' => "/", 'domain' => $current_base_url, 'secure' => true, 'httponly' => true, 'samesite' => 'Strict' ]);
?>

<meta charset="utf-8">
<meta name="theme-color" content="#ffffff">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!-- CSP headers -->
<meta http-equiv="Content-Security-Policy" content="
    default-src 'self';
    script-src 'self' https://ajax.googleapis.com https://cdnjs.cloudflare.com 'unsafe-inline';
    style-src 'self' https://stackpath.bootstrapcdn.com https://fonts.googleapis.com https://cdnjs.cloudflare.com https://adobe-fonts.github.io https://use.fontawesome.com 'unsafe-inline';
    font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com https://adobe-fonts.github.io https://use.fontawesome.com;
    img-src 'self' https://api.qrserver.com data: blob:;
">

<link rel="icon" type="image/png" href="./assets/img/config/<?php echo($current_favicon_image); ?>">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" id="google-font">
<link rel="stylesheet" href="./assets/css/main.css">
<link rel="stylesheet" href="./assets/css/inv.css">
<?php
if (isset($loggedin_theme_file_name) && $loggedin_theme_file_name !== '') {
    echo('<link id="theme-css" rel="stylesheet" href="./assets/css/'.$loggedin_theme_file_name.'">');
} elseif (isset($current_default_theme_file_name) && $current_default_theme_file_name !== ''){
    echo('<link id="theme-css" rel="stylesheet" href="./assets/css/'.$current_default_theme_file_name.'">');
}
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="https://adobe-fonts.github.io/source-code-pro/source-code-pro.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.4.0/css/all.css">

<style>
.inv-nav {
    background-color: <?php echo($current_banner_color);?> ;
    z-index:0px;
}
.inv-nav-secondary {
    background-color: <?php echo(adjustBrightness($current_banner_color, -0.2));?> ;
    z-index:0px;
}

.favouriteBtn {
    background-color: <?php echo $current_banner_color; ?> !important;
    color: <?php echo getWorB($current_banner_color); ?> !important;
}

.favouriteBtn:hover {
    background-color: <?php echo adjustBrightness($current_banner_color, -0.1); ?> !important;
    color: <?php echo getWorB(adjustBrightness($current_banner_color, -0.1)); ?> !important;
}

</style>

<?php
// HTTP Headers, from httpe-headers.php, now truncated to here.

// HEADERS FOR PROXY AND REQUESTED URL INFO
$requestedUrl = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? (isset(explode(', ', $_SERVER['HTTP_X_FORWARDED_HOST'])[1]) ? explode(', ', $_SERVER['HTTP_X_FORWARDED_HOST'])[1] : '') : '';
$requestedHttp = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : ''; // IP of host server
$requestedPort = isset($_SERVER['HTTP_X_FORWARDED_PORT']) ? $_SERVER['HTTP_X_FORWARDED_PORT'] : '';
$requestedHost = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : '';
$requestedServer = isset($_SERVER['HTTP_X_FORWARDED_SERVER']) ? $_SERVER['HTTP_X_FORWARDED_SERVER'] : '';
$remoteIP = isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : (isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '')));
if (str_contains($remoteIP, ',')) {
    $remoteIP = strtok($remoteIP, ',');
}

$requestedUri = isset($_SERVER['HTTP_X_REQUEST_URI']) ? $_SERVER['HTTP_X_REQUEST_URI'] : '';

$serverendhttp = isset($_SERVER['HTTPS']) ? 'https' : 'http';
if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '') {
    $queryString = $_SERVER['QUERY_STRING'];
    $queryStringUrl = '?'.$queryString;
} else {
    $queryString = '';
    $queryStringUrl = '';
}

$fullRequestedURL = $requestedHttp.'://'.$requestedUrl.$requestedUri.$queryStringUrl;
$platform = $_SERVER["HTTP_USER_AGENT"];
?>

<!-- Add the JS for the file -->
<script src="assets/js/head.js"></script>
