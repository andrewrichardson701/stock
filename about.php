<?php 
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// ABOUT PAGE
// SHOWS INFO ABOUT THE SYSTEM AND WHERE TO FIND IT ON GITLAB ETC.

// include 'http-headers.php'; // $_SERVER['HTTP_X_*'] 
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - About</title>
</head>
<body>
    <?php include 'nav.php';?>
    <div class="content">
        <div class="container">
            <h2 class="header-small">About</h2>
        </div>
        <div class="container" style="margin-top:25px;">
            <div class="row">
                <div class="col">
                    <h3 style="font-size:22px">StockBase (<?php echo $versionNumber ; ?>)</h3>
                    <div style="padding-top: 20px;">
                        <p>StockBase, an inventory and stock system, with less of the <i>bloat</i>. </p>
                        <p style="margin-top:30px"><?php echo ucwords($current_system_name);?> is powered by StockBase, an open source, minimalist stock management system.<br>
                        Learn more at the <a href="https://gitlab.com/andrewrichardson701/stockbase">GitLab page</a>.</p>
                        <p>StockBase is licenced under the <a href="https://www.gnu.org/licenses/gpl-3.0.txt">GNU GPL licence</a>.</p>
                        <p>StockBase Copyright © <?php echo(date("Y"));?> Andrew Richardson. All rights reserved.</p>
                    </div>
                </div>
                <div class="col-1"></div>
                <div class="col-4" id="version-changelog" style="max-height:60vh;overflow-x: hidden;overflow-y: auto; ">
                    <?php 
                    $file = 'CHANGELOG.md';
                    $changelog = file_get_contents($file);
                    echo $changelog;
                    ?>
                </div>
            </div>
        </div>
    </div>
        
<?php include 'foot.php'; ?>

</body>
