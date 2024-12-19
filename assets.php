<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// Assets Selection Page. Shows all Asset types to be viewed.

include 'session.php'; // Session setup and redirect if the session is not active 
// include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?></title>
</head>
<body>

    <!-- Header and Nav -->
    <?php 
        $navHighlight = 'assets'; // for colouring the nav bar link
        $navBtnDim = 1;
        include 'nav.php'; 
    ?>
    <!-- End of Header and Nav -->



    <div class="content">
        <?php // Error section
        $errorPprefix = '<div class="container"><p class="red" style="padding-top:10px">Error: ';
        $errorPsuffix = '</p></div>';
        $successPprefix = '<div class="container"><p class="green" style="padding-top:10px">';
        $successPsuffix = '</p></div>';

        include 'includes/responsehandling.inc.php';
        showResponse(); // 

        ?>

        <div class="container">
            <h2 class="header-small" style="padding-bottom:5px"><?php if (isset($_GET['return'])) { echo('<button class="btn btn-dark" style="margin-right:20px" onclick="window.location.href=\''.urldecode($_GET['return']).'\'"><i class="fa fa-chevron-left"></i> Back</button>'); } ?>Assets</h2>
        </div>

        <div class="container">
            <div class="row ">
                <div class="col text-center well-nopad theme-divBg
                    <?php
                    if (in_array($loggedin_role, $config_optics_roles_array)) { echo(" clickable\" onclick=\"navPage('optics.php')\""); } else { echo(' no-perms" title="Not permitted"');}
                    ?>
                style="margin:5px">
                    <h4>Optics</h4>
                    <img style="max-width:100px;overflow:hidden;" src="assets/img/stock/SFP.png">
                </div>
                <div class="col text-center well-nopad theme-divBg clickable" style="margin:5px" onclick="navPage('CPUs.php')">
                    <h4>CPUs</h4> 
                    <img style="max-width:100px;overflow:hidden;" src="assets/img/stock/CPU.png">
                </div>
                <div class="col text-center well-nopad theme-divBg no-perms" style="margin:5px" onclick="navPage('optics.php')" title="Coming soon...">
                    <h4>Memory</h4> 
                    <img style="max-width:100px;overflow:hidden;" src="assets/img/stock/RAM.png">
                </div>
            </div>
            <div class="row ">
                <div class="col text-center well-nopad theme-divBg no-perms" style="margin:5px" onclick="navPage('optics.php')" title="Coming soon...">
                    <h4>Disks</h4>
                    <img style="max-width:100px;overflow:hidden;" src="assets/img/stock/HDD.png">
                </div>
                <div class="col text-center well-nopad theme-divBg no-perms" style="margin:5px" onclick="navPage('optics.php')" title="Coming soon...">
                    <h4>Fans</h4> 
                    <img style="max-width:100px;overflow:hidden;" src="assets/img/stock/Fan.png">
                </div>
                <div class="col text-center well-nopad theme-divBg no-perms" style="margin:5px" onclick="navPage('optics.php')" title="Coming soon...">
                    <h4>PSUs</h4> 
                    <img style="max-width:100px;overflow:hidden;" src="assets/img/stock/PSU.png">
                </div>
            </div>
        </div>
        
        <?php
        ?>

    </div>
    

    <!-- Add the JS for the file -->
    <!-- <script src="assets/js/assets.js"></script> -->

    <?php include 'foot.php'; ?>
</body>
