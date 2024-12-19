<?php 
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.
?>

<!-- Navigation Bar for the top of the page, using the config settings for logo and colour -->
<a href="./" class="nav-head" style="position:fixed;z-index:999;color:<?php echo($current_banner_text_color); ?> !important"><?php echo($current_system_name); ?></a>
<header class="nav inv-nav" style="position:fixed;width:100%;z-index:900">
    <div id="nav-row" class="nav-row viewport-large">
        <div class="logo-div">
            <a href="./">
                <img class="logo" src="assets/img/config/<?php echo($current_logo_image); ?>" />
            </a>
        </div>
        <?php 
        // check if plink highlighting has been set in the parent php file
        if (isset($navHighlight)) {
            switch($navHighlight) {
                case 'index':
                    $highlight = 1;
                    break;
                case 'cables':
                    $highlight = 2;
                    break;
                case 'assets':
                    $highlight = 3;
                    break;
                case 'containers':
                    $highlight = 4;
                    break;
                case 'admin':
                    $highlight = 5;
                    break;
                case 'favourites':
                    $highlight = 6;
                    break;
		        case 'profile':
                    $highlight = 7;
                    break;
                default:
                    $highlight = 0;
                    break;
            }
        } else {
            $highlight = 0;
        }

        if ((isset($_SESSION['username'])) && ($_SESSION['username'] !== '')) {
            echo('
            <div id="add-div" class="nav-div" style="margin-right:5px">
                <button id="add-stock" class="btn btn-success cw nav-v-c btn-nav" style="'); if (isset($navBtnDim) && $navBtnDim == 1) { echo('opacity:60%'); } else { echo('opacity:90%'); } echo('" onclick="navPage(updateQueryParameter(\'stock.php\', \'modify\', \'add\'))">
                    <i class="fa fa-plus"></i> Add 
                </button>
            </div> 
            <div id="remove-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                <button id="remove-stock" class="btn btn-danger cw nav-v-c btn-nav" '); if (isset($navBtnDim) && $navBtnDim == 1) { echo('style="opacity:60%" '); } echo('onclick="navPage(updateQueryParameter(\'stock.php\', \'modify\', \'remove\'))">
                    <i class="fa fa-minus"></i> Remove 
                </button>
            </div>
            <div id="transfer-div" class="nav-div" style="margin-left:5px;margin-right:0px">
                <button id="transfer-stock" class="btn btn-warning nav-v-c btn-nav" style="color:black;'); if (isset($navBtnDim) && $navBtnDim == 1) { echo('opacity:60%'); } echo('" onclick="navPage(updateQueryParameter(\'./stock.php\', \'modify\', \'move\'))">
                    <i class="fa fa-arrows-h"></i> Move 
                </button>
            </div>
            ');
            
            $nav_right_set = 0;

            if (isset($_SESSION['impersonate'])) {
                $impersonate = $_SESSION['impersonate'];
                if ($impersonate == 1) {
                    echo('
                    <div id="impersonate-div" class="'); if ($nav_right_set == 0) { echo('nav-right'); $nav_right_set = 1; } echo(' nav-div">
                        <form enctype="multipart/form-data" class="nav-trans" action="./includes/admin.inc.php" method="POST" style="margin:0px;padding:0px">
                            <!-- Include CSRF token in the form -->
                            <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                            <input type="hidden" name="user-stop-impersonate" value="1"/>
                            <button type="submit" id="impersonate" style="border-radius: 8px;padding-left:10px;padding-right:10px;margin-top:2.5%;height:80%;color:'.getWorB(getComplement($current_banner_color)).';background-color:'.getComplement($current_banner_color).' !important;margin-bottom:10%">Stop Impersonating</button>
                        </form>
                    </div> 
                    ');
                }
            }
            $n = 0;
            if (isset($loggedin_role)) {
                $n = 1;
                echo('
                <div id="stock-div" class="'); if ($nav_right_set == 0) { echo('nav-right'); $nav_right_set = 1; } echo(' nav-div">
                    <a id="stock" class="nav-v-c nav-trans" style="padding-left:6px;padding-right:6px;align-items:center;display:flex;height:100%;color:'.$current_banner_text_color.' !important;'); if ($highlight == $n) { echo('text-decoration: underline !important;'); }  echo('" href="./">Stock</a>
                </div> 
                ');
                
            }
            if (isset($loggedin_role)) {
                $n = 2;
                echo('
                <div id="stock-div" class="'); if ($nav_right_set == 0) { echo('nav-right'); $nav_right_set = 1; } echo(' nav-div">
                    <a id="stock" class="nav-v-c nav-trans" style="padding-left:6px;padding-right:6px;align-items:center;display:flex;height:100%;color:'.$current_banner_text_color.' !important;'); if ($highlight == $n) { echo('text-decoration: underline !important;'); }  echo('" href="./cablestock.php">Cables</a>
                </div> 
                ');
                
            }
            if (isset($loggedin_role)) {
                $n = 3;
                echo('
                <div id="assets-div" class="'); if ($nav_right_set == 0) { echo('nav-right'); $nav_right_set = 1; } echo(' nav-div">
                    <a id="assets" class="nav-v-c nav-trans" style="padding-left:6px;padding-right:6px;align-items:center;display:flex;height:100%;color:'.$current_banner_text_color.' !important;'); if ($highlight == $n) { echo('text-decoration: underline !important;'); }  echo('" href="./assets.php">Assets</a>
                </div> 
                ');
            }
            if (isset($loggedin_role)) {
                $n = 4;
                echo('
                <div id="stock-div" class="'); if ($nav_right_set == 0) { echo('nav-right'); $nav_right_set = 1; } echo(' nav-div">
                    <a id="stock" class="nav-v-c nav-trans" style="padding-left:6px;padding-right:6px;align-items:center;display:flex;height:100%;color:'.$current_banner_text_color.' !important;'); if ($highlight == $n) { echo('text-decoration: underline !important;'); }  echo('" href="./containers.php">Containers</a>
                </div> 
                ');
                
            }
            if (isset($profile_name)) {
                echo ('
                    <div id="menu-div" class="nav-menu theme-burger '); if ($nav_right_set == 0) { echo('nav-right'); $nav_right_set = 1; } echo(' nav-div" style="cursor:pointer; color:'.$current_banner_text_color.' !important" '); if (!isset($profile_name)) { echo ('hidden'); } echo('>
                        <a id="profile" class="nav-v-c nav-trans" style="padding-left:6px;padding-right:6px;align-items:center;display:flex;height:100%;color:'.$current_banner_text_color.' !important">'.$profile_name.'<i class="fa fa-chevron-down" style="margin-left:5px; font-size:12px"></i></a>
                    </div>
                ');
                
            }
            
        }
        
        ?>
    </div>
    <?php 
    if (isset($profile_name)) {
        echo('
        <div style="width:100%">
            <div class="nav-div float-right nav-float" style="width:120px;">
                <ul class="nav-links align-middle" style="max-width:max-content; padding-left: 30px; padding-right:30px">');
                    if (in_array($loggedin_role, $config_admin_roles_array)) { 
                        echo('<li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-wrench"></i></span><a class="clickable link" style="margin-left:5px" href="./admin.php"'); if ($highlight == 5) { echo(' style="text-decoration: underline !important;"'); } echo('>Admin</a></li>
                                <li class="align-middle text-center divider" style="margin-top:5px;height: 6px;">&nbsp;</li>');
                    }
                echo('
                    <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-user"></i></span><a class="clickable link" style="margin-left:5px" href="./profile.php"'); if ($highlight == 7) { echo(' style="text-decoration: underline !important;"'); } echo('>Profile</a></li>
                    <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-star"></i></span><a class="clickable link" style="margin-left:5px" href="./favourites.php"'); if ($highlight == 6) { echo(' style="text-decoration: underline !important;"'); } echo('>Favourites</a></li>
                    <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-right-from-bracket"></i></span><a class="clickable link" style="margin-left:5px" href="./logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
        ');
    }
    ?>
    <div id="nav-row" class="nav-row viewport-small">
        <div class="logo-div">
            <a href="./">
                <img class="logo" src="assets/img/config/<?php echo($current_logo_image); ?>" />
            </a>
        </div>
        
        <?php
            $nav_right = 0;
            if (isset($_SESSION['impersonate'])) {
                $impersonate = $_SESSION['impersonate'];
                if ($impersonate == 1) {
                    echo('<div id="impersonate-div" class="'); if($nav_right == 0) { echo('nav-right'); $nav_right = 1; } echo(' nav-div" style="margin-right:0px">
                            <form enctype="multipart/form-data" class="nav-v-c nav-trans" action="./includes/admin.inc.php" method="POST" style="margin:0px;padding:0px">
                                <!-- Include CSRF token in the form -->
                                <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                <input type="hidden" name="user-stop-impersonate" value="1"/>
                                <button type="submit" style="border-radius: 8px; height:90%;color:'.getWorB(getComplement($current_banner_color)).';background-color:'.getComplement($current_banner_color).' !important; >Stop <i class="fa fa-user-secret" style="color:black" aria-hidden="true"></i></button>
                            </form>
                        </div>');
                }
            }
            echo('<div class="nav-div '); if($nav_right == 0) { echo('nav-right'); $nav_right = 1; } echo('">');
            if (isset($profile_name)) { 
                echo('
                    <ul class="burger-links">
                        <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-boxes-stacked"></i></span><a href="./"'); if ($highlight == 1) { echo(' style="text-decoration: underline !important;"'); } echo('>Stock</a></li>
                        <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-ethernet"></i></span><a href="./cablestock.php"'); if ($highlight == 2) { echo(' style="text-decoration: underline !important;"'); } echo('>Cables</a></li>');
                        if (isset($loggedin_role)) {
                            echo('<li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-wave-square"></i></span><a href="./assets.php"'); if ($highlight == 3) { echo(' style="text-decoration: underline !important;"'); } echo('>Assets</a></li>');
                        }
                        echo('
                        <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-box-open"></i></span><a href="./containers.php"'); if ($highlight == 4) { echo(' style="text-decoration: underline !important;"'); } echo('>Containers</a></li>
                        <li class="align-middle text-center divider" style="margin-top:5px;height: 6px;">&nbsp</li>
			            <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-user"></i></span><a href="./profile.php"'); if ($highlight == 7) { echo(' style="text-decoration: underline !important;"'); } echo('>Profile</a></li>
                        <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-star"></i></span><a href="./favourites.php"'); if ($highlight == 6) { echo(' style="text-decoration: underline !important;"'); } echo('>Favourites</a></li>
                        <li><span class="text-center" style="display:inline-block;width:25px"><i class="fa-solid fa-right-from-bracket"></i></span><a href="./logout.php">Logout</a></li>
                    </ul>
                    ');
            }
            echo('
                    <div class="burger-menu nav-v-c theme-burger" style="color:'.$current_banner_text_color.' !important" '); if(!isset($profile_name)) { echo ('hidden'); } echo('><i class="fa-solid fa-bars"></i></div>
                </div>
            ');
        ?>
    </div>
</header>
<?php
if ((isset($_SESSION['username'])) && ($_SESSION['username'] !== '')) {
?>
<header class="nav inv-nav-secondary viewport-small" style="position: relative;top:60px">
    <table class="centertable">
        <tbody>
            <tr>
                <td>
                    <div style="margin-right:5vw">
                        <button id="add-stock" class="btn btn-success cw nav-v-b btn-nav scale_1-15" style="width:80px;opacity:90%" onclick="navPage(updateQueryParameter('./stock.php', 'modify', 'add'))">
                            <i class="fa fa-plus"></i> Add 
                        </button>
                    </div>
                </td>
                <td>
                    <div>
                        <button id="remove-stock" class="btn btn-danger cw btn-nav scale_1-15" style="width:80px;" onclick="navPage(updateQueryParameter('./stock.php', 'modify', 'remove'))">
                            <i class="fa fa-minus"></i> Remove 
                        </button>
                    </div>
                </td>
                <td>
                    <div style="margin-left:5vw">
                        <button id="transfer-stock" class="btn btn-warning btn-nav scale_1-15" style="width:80px;color:black" onclick="navPage(updateQueryParameter('./stock.php', 'modify', 'move'))">
                            <i class="fa fa-arrows-h"></i> Move 
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</header>
<?php
}
?>
<!-- Add the JS for the file -->
<script src="assets/js/nav.js"></script>
