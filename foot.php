<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

$WorB_banner_color = getWorB($current_banner_color);
$complemenent_banner_color = getComplement($current_banner_color);
$WorB_complement_banner_color = getWorB($complemenent_banner_color);
?>
<style>
    .scrollBtn {
        background-color: <?php echo $current_banner_color; ?>;
        color: <?php echo $WorB_banner_color; ?>;
    }

    .scrollBtn:hover {
        background-color: <?php echo $complemenent_banner_color; ?>;
        color: <?php echo $WorB_complement_banner_color; ?>;
    }

</style>
<div id="scrollTop" class="hideTranslate">
    <button onclick="topFunction()" class="scrollBtn" id="scrollBtn" title="Go to top.">
        <i class="fa fa-chevron-up scrollIcon"></i> &nbsp;<span id="scrollText">Scroll to Top</span>
    </button>
</div>

<?php
if ($current_footer_enable == 1) {
    ?>
    <div class="footer">
        <div class="container">
            <div class="row">
                
                <div class="col text-center viewport-large-empty">
                    <?php
                    if ($current_footer_left_enable == 1) {
                        echo('<a href="https://gitlab.com/andrewrichardson701/stockbase" class="link" style="font-size:12px" target="_blank">GitLab</a>');
                    }
                    ?>
                </div>                
                <div class="col-6 text-center viewport-large-block" style="font-size:12px;cursor:pointer;" onclick="navPage('about.php')">
                    Copyright &copy; <?php echo date("Y"); ?> StockBase. All rights reserved.
                </div>
                <div class="col text-center viewport-small-block" style="font-size:10px;cursor:pointer;" onclick="navPage('about.php')">
                    &copy; <?php echo date("Y"); ?> StockBase
                </div> 
                <div class="col text-center viewport-large-empty">
                    <?php
                    if ($current_footer_right_enable == 1) {
                        echo('<a href="https://gitlab.com/andrewrichardson701/stockbase#roadmap" class="link" style="font-size:12px" target="_blank">Road Map</a>');
                    }
                    ?>
                </div>

            </div>
        </div> 
        
        <?php include 'includes/updatecheck.inc.php'; ?>
        <div class="align-right popupBox-owner" style="display: block;position: absolute;bottom: 4px;right: 20px;z-index: 99;font-size: 18px;border: none;outline: none;cursor: pointer;overflow: hidden;">
            <a href="./about.php" style="font-size:12px" id="version-about"><?php if (isset($update_available) && $update_available == 1) { echo('<i class="fa-solid fa-circle-exclamation" style="color: #ff3000; margin-right:7px"></i>');} echo $versionNumber; ?></a>
        </div>
        
    </div>
    <?php 
    if (isset($_SESSION['user_id']) && ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Root')) {
        echo('<span id="version-check" class="popupBox well-nopad text-center theme-divBg">');
            if (isset($update_text)) { echo($update_text); } else { echo('Unable to check for updates.'); }
        echo('</span>');
    }
}
?>

<!-- Add the JS for the file -->
<script src="assets/js/foot.js"></script>