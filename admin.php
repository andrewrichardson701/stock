<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// ADMIN PAGE - SHOWS CONFIGURATION OPTIONS AND ONLY VISIBLE TO ADMIN USERS
include 'session.php'; // Session setup and redirect if the session is not active 
include 'includes/responsehandling.inc.php'; // Used to manage the error / success / sqlerror querystrings.
// include 'http-headers.php'; // $_SERVER['HTTP_X_*'] 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Admin</title>
</head>
<body>
    <?php // dependency PHP   
    
    // \/ \/ This doesnt work due to headers being sent in head.php \/ \/

    // Redirect if the user is not in the admin list in the get-config.inc.php page. - this needs to be after the "include head.php" 
    // if (!in_array($_SESSION['role'], $config_admin_roles_array)) {
    //     header("Location: ./login.php");
    //     exit();
    // }
    ?>
    <script>
        // Redirect if the user is not in the admin list in the get-config.inc.php page. - this needs to be after the "include head.php" 
        if (!<?php echo json_encode(in_array($_SESSION['role'], $config_admin_roles_array)); ?>) {
            window.location.href = './login.php';
        }
    </script>


    <!-- hidden link, commented out as no purpose currently -->
    <!-- <a href="changelog.php" class="skip-nav-link-inv">changelog</a> -->

    <!-- Header and Nav -->
    <?php 
        $navHighlight = 'admin'; // for colouring the nav bar link
        include 'nav.php'; 
    ?>
    <!-- End of Header and Nav -->

    <div class="container" style="padding-top:60px;padding-bottom:20px">
        <h2 class="header-small">Admin</h2>
    </div>

    <div style="padding-bottom:75px">
        <div class="container" style="padding-bottom:0px">
            <?php
            
            if (!isset($_GET['section']) || (isset($_GET['section']) && $_GET['section'] == 'none')) {
                showResponse();
            }
            
            ?>
            <div id="modalDivAdd" class="modal">
            <!-- <div id="modalDivAdd" style="display: block;"> -->
                <span class="close" onclick="modalCloseAdd()">×</span>
                <div class="container well-nopad theme-divBg" style="padding:25px">
                    <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                        <div style="display:block"> 
                            <h2 style="margin-bottom:20px">Add new Site / Area / Shelf</h2>
                            <form id="locationForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                <input type="hidden" name="admin" value="1" />
                                <table class="centertable">
                                    <thead>
                                        <tr>
                                            <th style="padding-left:20px">Type</th>
                                            <th style="padding-left:5px" class="specialInput shelf area" hidden>Parent</th>
                                            <th style="padding-left:5px" class="specialInput shelf area site" hidden>Name</th>
                                            <th style="padding-left:5px" class="specialInput area site" hidden>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding-left:15px;padding-right:15px">
                                                <select id="addLocation-type" class="form-control" name="type" onchange="showInput()">
                                                    <option selected disabled>Select a Type</option>
                                                    <option value="site">Site</option>
                                                    <option value="area">Area</option>
                                                    <option value="shelf">Shelf</option>
                                                </select>
                                            </td>
                                            <td style="padding-right:15px" class="specialInput area shelf" hidden>
                                                <select id="addLocation-parent" class="form-control" name="parent" disabled>
                                                </select>
                                            </td>
                                            <td style="padding-right:15px" class="specialInput area shelf site" hidden><input class="form-control" type="text" name="name" placeholder="Name"/></td>
                                            <td style="padding-right:15px" class="specialInput area site" hidden><input class="form-control" type="text" name="description" placeholder="Description"/></td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%" style="padding-top:10px" class="text-center"><button class="btn btn-success align-bottom" type="submit" name="location-submit" style="margin-left:10px" value="1">Submit</button></td>
                                        </tr>
                                    </tbody>
                                </table>        
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div id="modalDivEdit" class="modal">
            <!-- <div id="modalDivEdit" style="display: block;"> -->
                <span class="close" onclick="modalCloseEdit()">×</span>
                <div class="container well-nopad theme-divBg" style="padding:25px">
                    <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                        <div style="display:block"> 
                            <h2 style="margin-bottom:20px">Edit Location</h2>
                            <form id="editLocationForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                <table class="centertable">
                                    <tbody>
                                        <tr class="align-middle">
                                            <th style="padding-right:15px">Type:</th>
                                            <td>
                                                <input id="location-type-input" type="hidden" name="location-type" value="" />
                                                <label style="margin-bottom:0px" id="location-type-text"></label>
                                            </td>
                                        </tr>
                                        <tr class="align-middle">
                                            <th style="padding-top:15px; padding-right:10px; padding-bottom:10px ">ID:</th>
                                            <td>
                                                <input id="location-id-input" type="hidden" name="location-id" value="" />
                                                <label style="margin-bottom:0px" id="location-id-text"></label>
                                            </td>
                                        </tr>
                                        <tr id="location-parent-site-tr" class="align-middle">
                                            <th id="location-parent-site-th" style="padding-right:15px">Site:</th>
                                            <td>
                                                <select class="form-control" id="location-parent-site-input" name="location-parent-site"></select>
                                            </td>
                                        </tr>
                                        <tr id="location-parent-area-tr" class="align-middle">
                                            <th id="location-parent-area-th" style="padding-right:15px">Area:</th>
                                            <td>
                                                <select class="form-control" id="location-parent-area-input" name="location-parent-area"></select>
                                            </td>
                                        </tr>
                                        <tr class="align-middle">
                                            <th style="padding-right:15px">Name:</th>
                                            <td>
                                                <input type="text" class="form-control" id="location-name-input" name="location-name" value="" />
                                            </td>
                                        </tr>
                                        <tr id="location-description-tr" class="align-middle">
                                            <th style="padding-right:15px">Description:</th>
                                            <td>
                                                <input type="text" class="form-control" style="width:400px" id="location-description-input" name="location-description" value="" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="100%" style="padding-top:10px" class="text-center"><button class="btn btn-success align-bottom" type="submit" name="location-edit-submit" style="margin-left:10px;margin-top:20px" value="1">Save</button></td>
                                        </tr>
                                    </tbody>
                                </table>        
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="font-size:22px" id="global-settings" onclick="toggleSection(this, 'global')">Global Settings <i class="fa-solid fa-chevron-up fa-2xs" style="margin-left:10px"></i></h3>
            <!-- Global Settings -->
            <div style="padding-top: 20px" id="global" hidden>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'global-settings')) {
                    showResponse();
                }
                ?>
                <form id="globalForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <table id="globalTable">
                        <tbody>
                            <tr class="" id="ldap-headings">
                                <th style="width:250px;margin-left:25px;padding-bottom:20px"></th>
                                <th style="width: 250px;padding-bottom:20px">Change</th>
                                <th style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">Current</th>
                                <th style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">Default</th>
                            </tr>
                            <tr class="">
                                <td id="system_name-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="system_name">System Name:</p>
                                </td>
                                <td id="system_name-set" style="width:250px;padding-bottom:20px">
                                    <input class="form-control " type="text" style="width: 150px" id="system_name" name="system_name">
                                </td>
                                <td style="min-width:230px;margin-left:10px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni"><?php echo($current_system_name); ?></span></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni"><?php echo($default_system_name); ?></span></label>
                                </td>
                            </tr>
                            <tr class="" id="banner-color" style="margin-top:20px">
                                <td id="banner-color-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <!-- Custodian Colour: #72BE2A -->
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="banner_color">Banner Colour:</p>
                                </td>
                                <td id="banner-color-picker" style="width:250px;padding-bottom:20px">
                                    <label class="tag-color">
                                        <input class="form-control input-color color" id="banner_color" name="banner_color" placeholder="#XXXXXX" data-value="#xxxxxx" value="<?php echo($current_banner_color); ?>"/>
                                    </label>
                                </td>
                                <td style="min-width:230px;padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni" style="color:<?php echo(getWorB($current_banner_color)); ?>;background-color:<?php echo($current_banner_color); ?>"><?php echo($current_banner_color); ?></span></label>
                                </td>
                                <td style="min-width:230px;padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni" style="color:<?php echo(getWorB($default_banner_color)); ?>;background-color:<?php echo($default_banner_color); ?>"><?php echo($default_banner_color); ?></span></label>
                                </td>
                            </tr>
                            <tr class="" style="margin-top:20px" id="banner-logo">
                                <td id="banner-logo-label" style="width:250px;margin-left:25px;padding-bottom:20px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="logo_image">Banner Logo:</p>
                                </td>
                                <td id="banner-logo-file">
                                    <input class="" type="file" style="width: 250px;padding-bottom:20px" id="logo_image" name="logo_image">
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">
                                    <label class=""><img class="thumb" src="./assets/img/config/<?php echo($current_logo_image); ?>" style="width:50px" onclick="modalLoad(this)" /></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">
                                    <label class=""><img class="thumb" src="./assets/img/config/<?php echo($default_logo_image); ?>" style="width:50px" onclick="modalLoad(this)" /></label>
                                </td>
                            </tr>
                            <tr class="" style="margin-top:20px" id="favicon-image">
                                <td id="favicon-image-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="favicon_image">Favicon Image:</p>
                                </td>
                                <td id="favicon-image-file" style="padding-bottom:20px">
                                    <input class="" type="file" style="width: 250px" id="favicon_image" name="favicon_image">
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">
                                    <label class=""><img class="thumb" src="./assets/img/config/<?php echo($current_favicon_image); ?>" style="width:32px" onclick="modalLoad(this)" /></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px;padding-left:15px">
                                    <label class=""><img class="thumb" src="./assets/img/config/<?php echo($default_favicon_image); ?>" style="width:32px" onclick="modalLoad(this)" /></label>
                                </td>
                            </tr>
                            <tr class="" style="margin-top:20px">
                                <td id="currency-selector-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="currency_selection">Currency:</p>
                                </td>
                                <td id="currency-selector" style="width:250px;padding-bottom:20px">
                                    <select id="currency_selection" name="currency_selection" placeholder="£" class="form-control" style="width:150px">
                                        <option alt="Pounds Sterling" value="£" <?php if ($current_currency == "£") { echo("selected"); } ?>>£ (Pound)</option>
                                        <option alt="Dollar"          value="$" <?php if ($current_currency == "$") { echo("selected"); } ?>>$ (Dollar)</option>
                                        <option alt="Euro"            value="€" <?php if ($current_currency == "€") { echo("selected"); } ?>>€ (Euro)</option>
                                        <option alt="Yen"             value="¥" <?php if ($current_currency == "¥") { echo("selected"); } ?>>¥ (Yen)</option>
                                        <option alt="Franc"           value="₣" <?php if ($current_currency == "₣") { echo("selected"); } ?>>₣ (Franc)</option>
                                        <option alt="Rupee"           value="₹" <?php if ($current_currency == "₹") { echo("selected"); } ?>>₹ (Rupee)</option>
                                        <option alt="Mark"            value="₻" <?php if ($current_currency == "₻") { echo("selected"); } ?>>₻ (Mark)</option>
                                        <option alt="Ruouble"         value="₽" <?php if ($current_currency == "₽") { echo("selected"); } ?>>₽ (Ruouble)</option>
                                        <option alt="Lira"            value="₺" <?php if ($current_currency == "₺") { echo("selected"); } ?>>₺ (Lira)</option>
                                    </select>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni"><?php echo($current_currency); ?></span></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni"><?php echo($default_currency); ?></span></label>
                                </td>
                            </tr>
                            <tr class="" style="margin-top:20px">
                                <td id="sku-prefix-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="sku_prefix"><or class="title" title="Prefix for SKU element on stock. e.g. ITEM-00001 or SKU-00001">SKU Prefix:</or></p>
                                </td>
                                <td id="sku-prefix-set" style="width:250px;padding-bottom:20px">
                                    <input class="form-control " type="text" style="width: 150px" id="sku_prefix" name="sku_prefix">
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni"><?php echo($current_sku_prefix); ?></span></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni"><?php echo($default_sku_prefix); ?></span></label>
                                </td>
                            </tr>

                            <tr class="" style="margin-top:20px">
                                <td id="base-url-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class="align-middle" for="base_url"><or class="title" title="This only changes the URL for any links or emails, not the web connection url. This needs to be changed in the web config file.">Base URL:</or></p>
                                </td>
                                <td id="base-url-set" style="width:250px;padding-bottom:20px">
                                    <input class="form-control " type="text" style="width: 150px" id="base_url" name="base_url">
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni"><?php echo($current_base_url); ?></span></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni"><?php echo($default_base_url); ?></span></label>
                                </td>
                            </tr>

                            <tr class="" style="margin-top:20px">
                                <td id="default-theme-label" style="width:250px;margin-left:25px;padding-bottom:20px">
                                    <p style="min-height:max-content;margin:0px" class=" align-middle" for="default_theme">Default Theme:</p>
                                </td>
                                <td id="default-theme-set" style="width:250px;padding-bottom:20px">
                                    <select id="default_theme_selection" name="default_theme" placeholder="Dark" class="form-control" style="width:150px">
                                        <?php
                                        $sql_theme = "SELECT * FROM theme";
                                        $stmt_theme = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt_theme, $sql_theme)) {
                                            echo("ERROR getting entries");
                                        } else {
                                            mysqli_stmt_execute($stmt_theme);
                                            $result_theme = mysqli_stmt_get_result($stmt_theme);
                                            $rowCount_theme = $result_theme->num_rows;
                                            if ($rowCount_theme < 1) {
                                                echo ("No themes found.");
                                            } else {
                                                while ( $row_theme = $result_theme->fetch_assoc() ) {
                                                    $theme_id = $row_theme['id'];
                                                    $theme_name = $row_theme['name'];
                                                    $theme_file_name = $row_theme['file_name'];
                                                    echo ('<option value="'.$theme_id.'" '); if ($current_default_theme_id == $theme_id) { echo('selected'); } echo('>'.$theme_name.'</option>');
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni"><?php echo($current_default_theme_name); ?></span></label>
                                </td>
                                <td style="min-width:230px;margin-left:25px; padding-left:15px;padding-bottom:20px">
                                    <label class=""><span class="uni"><?php echo($default_default_theme_name); ?></span></label>
                                </td>
                            </tr>


                            <tr class="" style="margin-top:20px;margin-left:25px;padding-bottom:20px">
                                <td style="width:250px">
                                    <input id="global-submit" type="submit" name="global-submit" class="btn btn-success" value="Save" />
                                </td>
                                <td style="width:250px;padding-bottom:20px">
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px">
                                </td>
                                <td style="min-width:230px;margin-left:25px;padding-bottom:20px">
                                    <input id="global-restore-defaults" type="submit" name="global-restore-defaults" class="btn btn-danger" style="margin-left:15px" value="Restore Default" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>

        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="footer-settings" onclick="toggleSection(this, 'footer')">Footer <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Footer -->
            <div style="padding-top: 20px" id="footer" hidden>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'footer')) {
                    showResponse();
                }
                ?>
                <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                    <p id="footer-output" class="last-edit-T" hidden></p>
                    <table>
                        <tbody>
                            <tr>
                                <td class="align-middle" style="margin-left:25px;margin-right:10px" id="normal-footer">
                                    <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable Footer at the bottom of each page.">Enable Footer:</p>
                                </td>
                                <td class="align-middle" style="padding-left:5px;padding-right:20px" id="normal-footer-toggle">
                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                        <input type="checkbox" name="normal-footer" onchange="toggleFooter(this, 1)" <?php if ($current_footer_enable == 1) { echo("checked");} ?>>
                                        <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                    </label>
                                </td>
                                <td class="align-middle" style="margin-left:25px;margin-right:10px" id="left-footer">
                                    <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable GitLab link on the footer.">Enable GitLab Link:</p>
                                </td>
                                <td class="align-middle" style="padding-left:5px;padding-right:20px" id="left-footer-toggle">
                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                        <input type="checkbox" name="left-footer" onchange="toggleFooter(this, 2)" <?php if ($current_footer_left_enable == 1) { echo("checked");} ?>>
                                        <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                    </label>
                                </td>
                                <td class="align-middle" style="margin-left:25px;margin-right:10px" id="right-footer">
                                    <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable Road Map link on the footer.">Enable Road Map Link:</p>
                                </td>
                                <td class="align-middle" style="padding-left:5px;padding-right:20px" id="right-footer-toggle">
                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                        <input type="checkbox" name="right-footer" onchange="toggleFooter(this, 3)" <?php if ($current_footer_right_enable == 1) { echo("checked");} ?>>
                                        <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div style="padding-bottom:0px">
            <div class="container">
                <h3 class="clickable" style="margin-top:50px;font-size:22px" id="users-settings" onclick="toggleSection(this, 'users')">Users <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            </div>
            <!-- Users Settings -->
            <div class="align-middle text-center" style="margin-left:5vw;margin-right:5vw; padding-top: 20px" id="users" hidden>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'users')) {
                    showResponse();
                }
                ?>
                <table id="usersTable" class="table table-dark theme-table centertable" style="max-width:max-content">
                    <thead>
                        <tr id="users_table_info_tr" hidden>
                            <td colspan=8 id="users_table_info_td"></td>
                        </tr>
                        <tr class="text-center theme-tableOuter">
                            <th>ID</th>
                            <th>Username</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Auth</th>
                            <th>Enabled</th>
                            <th>Password</th>
                            <th>2FA</th>
                            <?php if ($_SESSION['role'] == "Root") { echo ("<th></th>"); }?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // GET users from table

                            $sql_users = "SELECT users.id as id, users.username as username, users.first_name as first_name, users.last_name as last_name, users.email as email, users.auth as auth, users_roles.name as role, users.enabled as enabled FROM users 
                                            INNER JOIN users_roles ON users.role_id = users_roles.id";
                            $stmt_users = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_users, $sql_users)) {
                                echo('<td colspan=9><or class="red">SQL Issue with `users` table.</or></td>');
                            } else {
                                mysqli_stmt_execute($stmt_users);
                                $result_users = mysqli_stmt_get_result($stmt_users);
                                $rowCount_users = $result_users->num_rows;
                                if ($rowCount_users < 1) {
                                    echo ('<td colspan=9><or class="red">No Users in table: `users`.</or></td>');
                                } else {
                                    while($row_users = $result_users->fetch_assoc()) {
                                        $user_id = $row_users['id'];
                                        $user_username = $row_users['username'];
                                        $user_first_name = $row_users['first_name'];
                                        $user_last_name = $row_users['last_name'];
                                        $user_email = $row_users['email'];
                                        $user_role = $row_users['role'];
                                        $user_auth = $row_users['auth'];
                                        $user_enabled = $row_users['enabled'];
                                        $user_roles = [];

                                        $sql_roles = "SELECT * FROM users_roles";
                                        $stmt_roles = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt_roles, $sql_roles)) {
                                            echo('SQL Issue with `users` table.');
                                        } else {
                                            mysqli_stmt_execute($stmt_roles);
                                            $result_roles = mysqli_stmt_get_result($stmt_roles);
                                            $rowCount_roles = $result_roles->num_rows;
                                            if ($rowCount_roles < 1) {
                                                echo ('No Roles in table: `users_roles`.');
                                            } else {
                                                $i = 0;
                                                while ($row_roles = $result_roles->fetch_assoc()) {
                                                    $user_roles[$i]['id'] = $row_roles['id'];
                                                    $user_roles[$i]['name'] = $row_roles['name'];
                                                    $i++;
                                                }
                                            }
                                        }


                                        
                                        echo('<tr class="text-center" style="vertical-align: middle;">
                                            <td id="user_'.$user_id.'_id" style="vertical-align: middle;">'.$user_id.'</td>
                                            <td id="user_'.$user_id.'_username" style="vertical-align: middle;">'.$user_username.'</td>
                                            <td id="user_'.$user_id.'_first_name" style="vertical-align: middle;">'.$user_first_name.'</td>
                                            <td id="user_'.$user_id.'_last_name" style="vertical-align: middle;">'.$user_last_name.'</td>
                                            <td id="user_'.$user_id.'_email" style="vertical-align: middle;">'.$user_email.'</td>
                                            <td id="user_'.$user_id.'_role" style="vertical-align: middle;">
                                                <select class="form-control" id="user_'.$user_id.'_role_select" style="min-width:max-content; padding-top:0px; padding-bottom:0px" onchange="userRoleChange(\''.$user_id.'\') "'); if ($user_id == 0 || $user_id == '0') { echo("disabled"); } echo('>');
                                                foreach ($user_roles as $role) {
                                                    echo('<option value="'.$role['id'].'"');
                                                    // check if the user role matches or not, and mark it as selected
                                                    if ($role['name'] == $user_role) {
                                                        echo (' selected');
                                                    }
                                                    // check if the user role is the root user role (0), if it is, disable it so that it cannot be selected.
                                                    if ($role['id'] == 0 || $role['id'] == '0') {
                                                        echo(' disabled');
                                                    }
                                                    echo('>'.ucwords($role['name']).'</option>');
                                                }
                                                echo('
                                                </select>
                                            </td>
                                            <td id="user_'.$user_id.'_auth" style="vertical-align: middle;">'.$user_auth.'</td>
                                            <td style="vertical-align: middle;"><input type="checkbox" id="user_'.$user_id.'_enabled_checkbox"');
                                                if ($user_enabled == 1) {
                                                    echo('checked');
                                                }
                                            echo(' onchange="usersEnabledChange(\''.$user_id.'\')"/>
                                            </td>
                                            <td style="vertical-align: middle;">
                                                <button class="btn btn-warning" style="padding: 2px 6px 2px 6px" id="user_'.$user_id.'_pwreset" onclick="resetPassword(\''.$user_id.'\')"'); if ($user_auth == "ldap" || $user_role == "Admin" || $user_role == "Root") { echo("disabled"); } echo('>Reset</button>
                                            </td>
                                            <td style="vertical-align: middle;">');
                                                if ($user_id !== 0) {
                                                    echo ('<button class="btn btn-primary" id="reset_2fa" style="padding: 2px 6px 2px 6px" onclick="modalLoadReset2FA('.$user_id.')">Reset 2FA</button>');
                                                }
                                            echo('
                                            </td>
                                            ');
                                            if ($_SESSION['role'] == 'Root') {
                                                echo('
                                                <td style="vertical-align: middle;">   
                                                    <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST" style="padding:0px;margin:0px">
                                                        <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                                        <button type="submit" style="padding:2px 8px 2px 8px" class="btn btn-info" id="user_'.$user_id.'_impersonate" title="Impersonate" '); if ($user_id == $_SESSION['user_id']) { echo('disabled'); } echo('><i class="fa fa-user-secret" style="color:black" aria-hidden="true"></i></button>
                                                        <input type="hidden" name="user-impersonate" value="impersonate"/>
                                                        <input type="hidden" name="role" value="Root" />
                                                        <input type="hidden" name="user-id" value="'.$user_id.'" />
                                                    </form>
                                                ');
                                            }
                                    }
                                    echo('<tr class="theme-tableOuter"><td></td><td colspan=100%><button class="btn btn-success" type="button" onclick="navPage(\'addlocaluser.php\');"><i class="fa fa-plus"></i> Add</button></td></tr>');
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <div id="modalDivReset2FA" class="modal" style="display: none;">
                <span class="close" onclick="modalCloseReset2FA()">×</span>
                <div class="container well-nopad theme-divBg" style="padding:25px">
                    <div style="margin:auto;text-align:center;margin-top:10px">
                        <form action="includes/admin.inc.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                            <input type="hidden" name="2fareset_submit" value="admin" />
                            <input type="hidden" name="2fa_user_id" id="2fareset_user_id" value=""/>
                            <p>Are you sure you want to reset the 2FA for <or class="green" id="2fareset_username"></or>?<br>
                            This will prompt a reset on the user's next login.</p>
                            <span>
                                <button class="btn btn-danger" type="submit" name="submit" value="1">Reset</button>
                                <button class="btn btn-warning" type="button" onclick="modalCloseReset2FA()">Cancel</button>
                            </span>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="usersroles-settings" onclick="toggleSection(this, 'usersroles')">User Roles <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Users Roles -->
            <div style="padding-top: 20px" id="usersroles" hidden>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'users-roles')) {
                    showResponse();
                }
                ?>
                <table id="usersTable" class="table table-dark theme-table" style="max-width:max-content">
                    <thead>
                        <tr class="text-center theme-tableOuter">
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th class="title" title="Can access the Optics page">Optics</th>
                            <th>Administrator</th>
                            <th>Root</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $sql_roles = "SELECT * FROM users_roles";
                            $stmt_roles = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_roles, $sql_roles)) {
                                echo('<td colspan=7><or class="red">SQL Issue with `users` table.</or></td>');
                            } else {
                                mysqli_stmt_execute($stmt_roles);
                                $result_roles = mysqli_stmt_get_result($stmt_roles);
                                $rowCount_roles = $result_roles->num_rows;
                                if ($rowCount_roles < 1) {
                                    echo ('<td colspan=7><or class="red">No Roles in table: `users`.</or></td>');
                                } else {
                                    while ($row_roles = $result_roles->fetch_assoc()) {
                                        echo('<tr class="text-center">
                                        <td>'.$row_roles['id'].'</td>
                                        <td>'.$row_roles['name'].'</td>
                                        <td>'.$row_roles['description'].'</td>
                                        <td style="vertical-align: middle;">'); if ($row_roles['is_optic'] == 1) { echo('<i class="fa-solid fa-square-check fa-lg" style="color: #3881ff;"></i>'); } else { echo('<i class="fa-solid fa-xmark" style="color: #ff0000;"></i>'); } echo ('</td>
                                        <td style="vertical-align: middle;">'); if ($row_roles['is_admin'] == 1) { echo('<i class="fa-solid fa-square-check fa-lg" style="color: #3881ff;"></i>'); } else { echo('<i class="fa-solid fa-xmark" style="color: #ff0000;"></i>'); } echo ('</td>
                                        <td style="vertical-align: middle;">'); if ($row_roles['is_root'] == 1) { echo('<i class="fa-solid fa-square-check fa-lg" style="color: #3881ff;"></i>'); } else { echo('<i class="fa-solid fa-xmark" style="color: #ff0000;"></i>'); } echo ('</td>
                                        </tr>');
                                    }
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="authentication-settings" onclick="toggleSection(this, 'authentication')">Authentication <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Authentication -->
            <div style="padding-top: 20px" id="authentication" hidden>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'authentication')) {
                    showResponse();
                }
                ?>
                <p id="authentication-output" class="last-edit-T" hidden></p>

                <?php
                $sql_check = "SELECT signup_allowed, 2fa_enabled, 2fa_enforced FROM config WHERE id=1";
                $stmt_check = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_check, $sql_check)) {
                    echo ('ERRORRRR');
                } else {
                    mysqli_stmt_execute($stmt_check);
                    $result_check = mysqli_stmt_get_result($stmt_check);

                    $row_check = $result_check->fetch_assoc();
                    $signup_allowed = $row_check['signup_allowed'];
                    $enabled_2fa = $row_check['2fa_enabled'];
                    $enforced_2fa = $row_check['2fa_enforced'];

                    if ($signup_allowed == 1) {
                        $signup_checked = 'checked';
                    } else {
                        $signup_checked = '';
                    }

                    if ($enabled_2fa == 1) {
                        $enabled_checked = 'checked';
                    } else {
                        $enabled_checked = '';
                    }
                    
                    if ($enforced_2fa == 1) {
                        $enforced_checked = 'checked';
                    } else {
                        $enforced_checked = '';
                    }
                    
                }
                ?>

                <table>
                    <tbody>
                        <tr>
                            <td class="align-middle" style="margin-left:25px;margin-right:10px">
                                <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable the signup.php self sign up page for locally authenticated users">Self sign up allowed:</p>
                            </td>
                            <td class="align-middle" style="padding-left:5px;padding-right:50px" id="signup_allowed_toggle">
                                <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px" >
                                    <input type="checkbox" name="signup_allowed" onchange="authSettings(this, 'signup_allowed')" <?php echo($signup_checked); ?>>
                                    <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                </label>
                            </td>
                            <td class="align-middle" style="margin-left:25px;margin-right:10px">
                                <p style="min-height:max-content;margin:0px" class="align-middle">Enable 2FA:</p>
                            </td>
                            <td class="align-middle" style="padding-left:5px;padding-right:50px" id="enable_2fa_toggle">
                                <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px" >
                                    <input type="checkbox" name="enable_2fa" onchange="authSettings(this, '2fa_enabled')" <?php echo($enabled_checked); ?>>
                                    <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                </label>
                            </td>
                            <td class="align-middle" style="margin-left:25px;margin-right:10px">
                                <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enforce the use of 2FA for ALL users (except Root)">Enforce 2FA:</p>
                            </td>
                            <td class="align-middle" style="padding-left:5px;padding-right:50px" id="enforce_2fa_toggle">
                                <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px" >
                                    <input type="checkbox" name="enforce_2fa" onchange="authSettings(this, '2fa_enforced')" <?php echo($enforced_checked); ?>>
                                    <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="sessionmanagement-settings" onclick="toggleSection(this, 'sessionmanagement')">Session Management <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Session Management -->
            <div style="padding-top: 20px" id="sessionmanagement" hidden>
            <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'sessionmanagement')) {
                    showResponse();
                }
                ?>
                <table id="sessionsTable" class="table table-dark theme-table" style="max-width:max-content">
                    <thead>
                        <tr id="sessions_table_info_tr" hidden>
                            <td colspan=8 id="sessions_table_info_td"></td>
                        </tr>
                        <tr class="text-center theme-tableOuter">
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>IP</th>
                            <th>Login Time</th>
                            <th hidden>Logout Time</th>
                            <th>Last Activity</th>
                            <th>Browser</th>
                            <th>OS</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // GET users from table

                            $sql_sessions = "SELECT sl.id AS sl_id, sl.user_id AS sl_user_id, 
                                                FROM_UNIXTIME(sl.login_time) AS sl_login_time, IFNULL(FROM_UNIXTIME(sl.logout_time), NULL) AS sl_logout_time, 
                                                COALESCE(INET_NTOA(sl.ipv4), INET6_NTOA(sl.ipv6)) AS sl_ip,
                                                sl.browser AS sl_browser, sl.os AS sl_os, sl.status AS sl_status,
                                                FROM_UNIXTIME(sl.last_activity) AS sl_last_activity,
                                                u.username AS u_username
                                            FROM session_log AS sl
                                            INNER JOIN users AS u ON u.id=sl.user_id";
                            $stmt_sessions = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_sessions, $sql_sessions)) {
                                echo('<td colspan=9><or class="red">SQL Issue with `users` table.</or></td>');
                            } else {
                                mysqli_stmt_execute($stmt_sessions);
                                $result_sessions = mysqli_stmt_get_result($stmt_sessions);
                                $rowCount_sessions = $result_sessions->num_rows;
                                if ($rowCount_sessions < 1) {
                                    echo ('<td colspan=9><or class="red">No Users in table: `users`.</or></td>');
                                } else {
                                    while($row_sessions = $result_sessions->fetch_assoc()) {

                                        $sl_id = $row_sessions['sl_id'];
                                        $sl_user_id = $row_sessions['sl_user_id'];
                                        $sl_login_time = $row_sessions['sl_login_time'];
                                        $sl_logout_time = $row_sessions['sl_logout_time'];
                                        $sl_ip = $row_sessions['sl_ip'];
                                        $sl_browser = $row_sessions['sl_browser'];
                                        $sl_os = $row_sessions['sl_os'];
                                        $sl_status = $row_sessions['sl_status'];
                                        $sl_last_activity = $row_sessions['sl_last_activity'];
                                        $u_username = $row_sessions['u_username'];

                                        if ($sl_logout_time == null ) {
                                            echo('<tr class="text-center" style="vertical-align: middle;">
                                                <form action="includes/admin.inc.php" method="POST">
                                                    <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                                    <input type="hidden" name="session_id" value="'.$sl_id.'" />
                                                    <td id="sessions_'.$sl_id.'_id" style="vertical-align: middle;">'.$sl_id.'</td>
                                                    <td id="sessions_'.$sl_id.'_user_id" style="vertical-align: middle;">'.$sl_user_id.'</td>
                                                    <td id="sessions_'.$sl_id.'_username" style="vertical-align: middle;">'.$u_username.'</td>
                                                    <td id="sessions_'.$sl_id.'_ip" style="vertical-align: middle;">'.$sl_ip.'</td>
                                                    <td id="sessions_'.$sl_id.'_login_time" style="vertical-align: middle;">'.$sl_login_time.'</td>
                                                    <td id="sessions_'.$sl_id.'_logout_time" style="vertical-align: middle;" hidden>'.$sl_logout_time.'</td>
                                                    <td id="sessions_'.$sl_id.'_last_activity" style="vertical-align: middle;">'.$sl_last_activity.'</td>
                                                    <td id="sessions_'.$sl_id.'_browser" style="vertical-align: middle;">'.$sl_browser.'</td>
                                                    <td id="sessions_'.$sl_id.'_os" style="vertical-align: middle;">'.$sl_os.'</td>
                                                    <td id="sessions_'.$sl_id.'_status" style="vertical-align: middle;">'.$sl_status.'</td>
                                                    <td id="sessions_'.$sl_id.'_kill" style="vertical-align: middle;"><input type="submit" class="btn btn-danger" name="session-kill-submit" value="Kill"'); if ($_SESSION['session_id'] == $sl_id) { echo (' title="Current Session" disabled'); } echo('></td>
                                                </form>
                                            </tr>
                                            ');
                                        } 
                                    }
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="imagemanagement-settings" onclick="toggleSection(this, 'imagemanagement')">Image Management <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Image Management Settings -->
            <div style="padding-top: 20px" id="imagemanagement" hidden>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'imagemanagement')) {
                    showResponse();
                }
                ?>
                <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                    <?php 
                    $files = array_values(array_diff(scandir('./assets/img/stock'), array('..', '.'))); 
                    ?>
                    <p>Image Count: <or class="green"><?php echo(count($files));?></or></p>
                    <table class="table table-dark theme-table" style="max-width:max-content">
                        <thead>
                            <tr class="theme-tableOuter">
                                <th class="text-center theme-tableOuter" style="width:130px; position: sticky; top: -1;">Image</th>
                                <th class="text-center theme-tableOuter" style="position: sticky; top: -1;">File</th>
                                <th class="text-center theme-tableOuter" style="position: sticky; top: -1;">Links</th>
                                <th class="text-center theme-tableOuter" style="position: sticky; top: -1; z-index:10">Delete</th>
                                <th class="text-center theme-tableOuter" style="position: sticky; top: -1;">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody id="image-management-tbody">
                            <tr id="loader-tr">
                                <td id="loader-td" colspan=100% class="algin-middle text-center">
                                    <div id="loader-outerdiv">
                                        <button class="btn btn-info" id="show-images" onclick="loadAdminImages(0, 1)">Load Images</button>
                                        <div class="loader" id="loaderDiv" style="margin-top:10px;width:130px;display:none">
                                            <div class="loaderBar"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="attributemanagement-settings" onclick="toggleSection(this, 'attributemanagement')">Attribute Management <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Attribute Management Settings -->
            <div style="padding-top: 20px" id="attributemanagement" hidden>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'attributemanagement')) {
                    showResponse();
                }
                ?>

                <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Tags<a class="align-middle link" style="margin-left:30px;font-size:12px" href="tags.php">View all</a></h4>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'attributemanagement-tag')) {
                    echo('<div style="margin-right: 10px; margin-left: 10px">');
                    showResponse();
                    echo('</div>');
                }
                ?>
                
                        <?php   
                            $tags = [];
                            $tags_links = [];

                            $l_d = 0;

                            $sql_tags = "SELECT 
                                                tag.id AS tag_id, 
                                                tag.name AS tag_name, 
                                                tag.deleted AS tag_deleted,
                                                stock_tag.id AS stock_tag_id, 
                                                stock_tag.stock_id AS stock_id, 
                                                stock_tag.tag_id AS stock_tag_tag_id,
                                                stock.name AS stock_name
                                            FROM 
                                                tag
                                            LEFT JOIN 
                                                stock_tag ON tag.id = stock_tag.tag_id
                                            LEFT JOIN 
                                                stock ON stock_tag.stock_id = stock.id AND stock.deleted=0;";                         
                            $stmt_tags = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_tags, $sql_tags)) {
                                echo("ERROR getting entries");
                            } else {
                                mysqli_stmt_execute($stmt_tags);
                                $result_tags = mysqli_stmt_get_result($stmt_tags);
                                $rowCount_tags = $result_tags->num_rows;
                                while ($row_tags = $result_tags->fetch_assoc()) {
                                    if ($row_tags['tag_deleted'] == 1) {
                                        $l_d++;
                                    }
                                    if (!array_key_exists($row_tags['tag_id'], $tags)) {
                                        $tags[$row_tags['tag_id']] = array('id' =>  $row_tags['tag_id'], 'name' => $row_tags['tag_name'], 'deleted' => $row_tags['tag_deleted']);
                                    }
                                    if (isset($row_tags['stock_tag_id']) && $row_tags['stock_tag_id'] !== NULL && $row_tags['stock_tag_tag_id'] !== '') {
                                        $tags_links[$row_tags['tag_id']][] = array('id' => $row_tags['stock_tag_id'], 'stock_id' => $row_tags['stock_id'], 
                                                                                            'stock_name' => $row_tags['stock_name'], 'tag_id' => $row_tags['stock_tag_tag_id']);
                                    }
                                }
                                ?>
                                <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                                    <table class="table table-dark theme-table" style="max-width:max-content">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">ID</th>
                                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                                                <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                                                    <button id="show-deleted-tag" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('tag', 1)" <?php if ($l_d == 0) { echo "hidden"; } ?> >
                                                        <span class="zeroStockFont">
                                                            <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                                            
                                                        </span>
                                                    </button>
                                                    <button id="hide-deleted-tag" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('tag', 0)" hidden>
                                                        <span class="zeroStockFont">
                                                            <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                                        </span>
                                                    
                                                    </button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                <?php
                                foreach ($tags as $tag) {
                                    $l = $tag['id'];
                                    $tag_deleted = $tag['deleted'];
                                    $tag_deleted_class = '';
                                    if ($tag_deleted == 1) {
                                        $tag_deleted_class = 'red theme-divBg tag-deleted';
                                    }
                                    if (array_key_exists($l, $tags_links)) {
                                        $tag_link_count = count($tags_links[$l]);
                                    } else {
                                        $tag_link_count = 0;
                                    }
                                    
                                    echo('
                                        <tr id="tag-row-'.$l.'" class="align-middle '.$tag_deleted_class.'"'); if ($tag_deleted == 1) { echo(' hidden'); } echo('>
                                            <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                            <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                                <input type="hidden" name="attribute-type" value="tag"/>
                                                <input type="hidden" name="id" value="'.$l.'">
                                                <td id="tag-'.$l.'-id" class="text-center align-middle">'.$l.'</td>
                                                <td id="tag-'.$l.'-name" class="text-center align-middle">'.$tag['name'].'</td>
                                                <td class="text-center align-middle">'.$tag_link_count.'</td>
                                                <td class="text-center align-middle">');
                                                if ($tag_deleted !== 1) {
                                                    echo('<button class="btn btn-danger" type="submit" name="attributemanagement-submit" '); 
                                                        if ($tag_link_count !== 0) { echo('disabled title="tag still linked to stock. Remove these links before deleting."'); } 
                                                        echo('><i class="fa fa-trash"></i></button></td>');
                                                } else {
                                                    echo('<button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>');
                                                }
                                                echo('<td class="text-center align-middle">'); 
                                                    if ($tag_deleted !== 1) {
                                                        if ($tag_link_count !== 0) { echo('<button class="btn btn-warning" id="tag-'.$l.'-links" type="button" onclick="showLinks(\'tag\', \''.$l.'\')">Show Links</button>'); }
                                                    } else {
                                                        echo('<or class="green">Restore?</or>');
                                                    }
                                                echo('</td>');
                                        echo('</form>
                                        </tr>
                                    ');
                                    if ($tag_link_count !== 0) { 
                                        echo('
                                            <tr id="tag-row-'.$l.'-links" class="align-middle" hidden>
                                                <td colspan=100%>
                                                    <div>
                                                        <table class="table table-dark theme-table">
                                                            <thead>
                                                                <tr class="theme-tableOuter">
                                                                    <th>ID</th>
                                                                    <th>Stock ID</th>
                                                                    <th>Stock Name</th>
                                                                    <th>tag ID</th>
                                                                    <th>tag Name</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>');
                                                            $links = $tags_links[$l];
                                                            foreach ($links as $ll) {
                                                                echo('
                                                                    <tr class="clickable" onclick=navPage("stock.php?stock_id='.$ll['stock_id'].'")>
                                                                        <td class="text-center">'.$ll['id'].'</td>
                                                                        <td class="text-center"><a href="stock.php?stock_id='.$ll['stock_id'].'">'.$ll['stock_id'].'</a></td>
                                                                        <td class="text-center"><a href="stock.php?stock_id='.$ll['stock_id'].'">'.$ll['stock_name'].'</a></td>
                                                                        <td class="text-center">'.$ll['tag_id'].'</td>
                                                                        <td class="text-center">'.$tags[$ll['tag_id']]['name'].'</td>
        
                                                                    </tr>
                                                                ');
                                                            }                                                    
                                                            echo('
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        ');
                                    }
                                }
                                ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php
                            }     
                            ?>

                <hr style="border-color:white; margin-left:10px">

                <h4 style="margin-left:10px; margin-right:10px; margin-top:20px; font-size:20px; margin-bottom:10px">Manufacturers</h4>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'attributemanagement-manufacturer')) {
                    echo('<div style="margin-right: 10px; margin-left: 10px">');
                    showResponse();
                    echo('</div>');
                }

                $manufacturers = [];
                $manufacturers_links = [];

                $m_d = 0;

                $sql_manufacturers = "SELECT 
                                        manufacturer.id AS manufacturer_id, 
                                        manufacturer.name AS manufacturer_name, 
                                        manufacturer.deleted AS manufacturer_deleted,
                                        item.id AS item_id, item.stock_id AS item_stock_id, item.manufacturer_id AS item_manufacturer_id,
                                        stock.id AS stock_id, stock.name AS stock_name
                                    FROM 
                                        manufacturer
                                    LEFT JOIN
                                        item ON manufacturer.id=item.manufacturer_id AND item.deleted=0  
                                    LEFT JOIN 
                                        stock ON stock.id=item.stock_id AND stock.deleted=0";                  
                $stmt_manufacturers = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_manufacturers, $sql_manufacturers)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_execute($stmt_manufacturers);
                    $result_manufacturers = mysqli_stmt_get_result($stmt_manufacturers);
                    $rowCount_manufacturers = $result_manufacturers->num_rows;
                    while ($row_manufacturers = $result_manufacturers->fetch_assoc()) {
                        if ($row_manufacturers['manufacturer_deleted'] == 1) {
                            $m_d++;
                        }
                        if (!array_key_exists($row_manufacturers['manufacturer_id'], $manufacturers)) {
                            $manufacturers[$row_manufacturers['manufacturer_id']] = array('id' =>  $row_manufacturers['manufacturer_id'], 'name' => $row_manufacturers['manufacturer_name'], 'deleted' => $row_manufacturers['manufacturer_deleted']);
                        }
                        if (isset($row_manufacturers['item_manufacturer_id']) && $row_manufacturers['item_manufacturer_id'] !== NULL && $row_manufacturers['item_manufacturer_id'] !== '') {
                            $manufacturers_links[$row_manufacturers['manufacturer_id']][] = array('id' => $row_manufacturers['item_manufacturer_id'], 'item_id' => $row_manufacturers['item_id'], 'stock_id' => $row_manufacturers['stock_id'], 
                                                                                'stock_name' => $row_manufacturers['stock_name'], 'manufacturer_id' => $row_manufacturers['item_manufacturer_id']);
                        }
                    }
                    ?>
                    <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                        <table class="table table-dark theme-table" style="max-width:max-content">
                            <thead>
                                <tr class="theme-tableOuter">
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Item ID</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                                        <button id="show-deleted-manufacturer" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('manufacturer', 1)" <?php if($m_d == 0) { echo "hidden"; } ?> >
                                            <span class="zeroStockFont">
                                                <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                                
                                            </span>
                                        </button>
                                        <button id="hide-deleted-manufacturer" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('manufacturer', 0)" hidden>
                                            <span class="zeroStockFont">
                                                <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                            </span>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php
                            foreach ($manufacturers as $manufacturer) {
                                $m = $manufacturer['id'];
                                $manufacturer_deleted = $manufacturer['deleted'];
                                $manufacturer_deleted_class = '';
                                if ($manufacturer_deleted == 1) {
                                    $manufacturer_deleted_class = 'red theme-divBg manufacturer-deleted';
                                }
                                if (array_key_exists($m, $manufacturers_links)) {
                                    $manufacturer_link_count = count($manufacturers_links[$m]);
                                } else {
                                    $manufacturer_link_count = 0;
                                }
                                
                                echo('
                                    <tr id="manufacturer-row-'.$m.'" class="align-middle '.$manufacturer_deleted_class.'"'); if ($manufacturer_deleted == 1) { echo(' hidden'); } echo('>
                                        <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                            <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                            <input type="hidden" name="attribute-type" value="manufacturer"/>
                                            <input type="hidden" name="id" value="'.$m.'">
                                            <td id="manufacturer-'.$m.'-id" class="text-center align-middle">'.$m.'</td>
                                            <td id="manufacturer-'.$m.'-name" class="text-center align-middle">'.$manufacturer['name'].'</td>
                                            <td class="text-center align-middle">'.$manufacturer_link_count.'</td>
                                            <td class="text-center align-middle">');
                                            if ($manufacturer_deleted !== 1) {
                                                echo('<button class="btn btn-danger" type="submit" name="attributemanagement-submit" '); 
                                                    if ($manufacturer_link_count !== 0) { echo('disabled title="manufacturer still linked to stock. Remove these links before deleting."'); } 
                                                    echo('><i class="fa fa-trash"></i></button></td>');
                                            } else {
                                                echo('<button class="btn btn-success" type="submit" name="attributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>');
                                            }
                                            echo('<td class="text-center align-middle">'); 
                                                if ($manufacturer_deleted !== 1) {
                                                    if ($manufacturer_link_count !== 0) { echo('<button class="btn btn-warning" id="manufacturer-'.$m.'-links" type="button" onclick="showLinks(\'manufacturer\', \''.$m.'\')">Show Links</button>'); }
                                                } else {
                                                    echo('<or class="green">Restore?</or>');
                                                }
                                            echo('</td>');
                                    echo('</form>
                                    </tr>
                                ');
                                if ($manufacturer_link_count !== 0) { 
                                    echo('
                                        <tr id="manufacturer-row-'.$m.'-links" class="align-middle" hidden>
                                            <td colspan=100%>
                                                <div>
                                                    <table class="table table-dark theme-table">
                                                        <thead>
                                                            <tr class="theme-tableOuter">
                                                                <th>ID</th>
                                                                <th>Stock ID</th>
                                                                <th>Stock Name</th>
                                                                <th>manufacturer ID</th>
                                                                <th>manufacturer Name</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>');
                                                        $links = $manufacturers_links[$m];
                                                        foreach ($links as $mm) {
                                                            echo('
                                                                <tr class="clickable" onclick=navPage("stock.php?stock_id='.$mm['stock_id'].'")>
                                                                    <td class="text-center">'.$mm['item_id'].'</td>
                                                                    <td class="text-center"><a href="stock.php?stock_id='.$mm['stock_id'].'">'.$mm['stock_id'].'</a></td>
                                                                    <td class="text-center"><a href="stock.php?stock_id='.$mm['stock_id'].'">'.$mm['stock_name'].'</a></td>
                                                                    <td class="text-center">'.$mm['manufacturer_id'].'</td>
                                                                    <td class="text-center">'.$manufacturers[$mm['manufacturer_id']]['name'].'</td>
                                                                </tr>
                                                            ');
                                                        }                                                    
                                                        echo('
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    ');
                                }
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            <?php
                }  
            ?>                         
            </div>
        </div>

        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="opticattributemanagement-settings" onclick="toggleSection(this, 'opticattributemanagement')">Optic Attribute Management <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Optic Attribute Management Settings -->
            <div style="padding-top: 20px" id="opticattributemanagement" hidden>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement')) {
                    showResponse();
                }
                ?>

                <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Vendors</h4>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement-optic_vendors')) {
                    echo('<div style="margin-right: 10px; margin-left: 10px">');
                    showResponse();
                    echo('</div>');
                }

                $vendors = [];
                $vendors_links = [];

                $v_d = 0;

                $sql_vendors = "SELECT 
                                    V.id AS vendor_id, 
                                    V.name AS vendor_name, 
                                    V.deleted AS vendor_deleted,
                                    I.id AS item_id, I.vendor_id AS item_vendor_id, I.serial_number AS item_serial_number, I.model AS item_model
                                FROM 
                                    optic_vendor AS V
                                LEFT JOIN
                                    optic_item AS I ON V.id=I.vendor_id AND I.deleted=0";                
                $stmt_vendors = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_vendors, $sql_vendors)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_execute($stmt_vendors);
                    $result_vendors = mysqli_stmt_get_result($stmt_vendors);
                    $rowCount_vendors = $result_vendors->num_rows;
                    while ($row_vendors = $result_vendors->fetch_assoc()) {
                        if ($row_vendors['vendor_deleted'] == 1) {
                            $v_d++;
                        }
                        if (!array_key_exists($row_vendors['vendor_id'], $vendors)) {
                            $vendors[$row_vendors['vendor_id']] = array('id' =>  $row_vendors['vendor_id'], 'name' => $row_vendors['vendor_name'], 'deleted' => $row_vendors['vendor_deleted']);
                        }
                        if (isset($row_vendors['item_vendor_id']) && $row_vendors['item_vendor_id'] !== NULL && $row_vendors['item_vendor_id'] !== '') {
                            $vendors_links[$row_vendors['vendor_id']][] = array('id' => $row_vendors['item_vendor_id'], 'item_id' => $row_vendors['item_id'],
                                                                                    'item_serial' => $row_vendors['item_serial_number'], 'item_model' => $row_vendors['item_model']);
                        }
                    }
                    ?>
                    <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                        <table class="table table-dark theme-table" style="max-width:max-content">
                            <thead>
                                <tr class="theme-tableOuter">
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Item ID</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                                        <button id="show-deleted-optic_vendors" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('optic_vendors', 1)" <?php if($v_d == 0) { echo "hidden"; } ?> >
                                            <span class="zeroStockFont">
                                                <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                                
                                            </span>
                                        </button>
                                        <button id="hide-deleted-optic_vendors" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('optic_vendors', 0)" hidden>
                                            <span class="zeroStockFont">
                                                <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                            </span>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php
                            foreach ($vendors as $vendor) {
                                $m = $vendor['id'];
                                $vendor_deleted = $vendor['deleted'];
                                $vendor_deleted_class = '';
                                if ($vendor_deleted == 1) {
                                    $vendor_deleted_class = 'red theme-divBg optic_vendors-deleted';
                                }
                                if (array_key_exists($m, $vendors_links)) {
                                    $vendor_link_count = count($vendors_links[$m]);
                                } else {
                                    $vendor_link_count = 0;
                                }
                                
                                echo('
                                    <tr id="vendor-row-'.$m.'" class="align-middle '.$vendor_deleted_class.'"'); if ($vendor_deleted == 1) { echo(' hidden'); } echo('>
                                        <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                            <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                            <input type="hidden" name="attribute-type" value="optic_vendors"/>
                                            <input type="hidden" name="id" value="'.$m.'">
                                            <td id="vendor-'.$m.'-id" class="text-center align-middle">'.$m.'</td>
                                            <td id="vendor-'.$m.'-name" class="text-center align-middle">'.$vendor['name'].'</td>
                                            <td class="text-center align-middle">'.$vendor_link_count.'</td>
                                            <td class="text-center align-middle">');
                                            if ($vendor_deleted !== 1) {
                                                echo('<button class="btn btn-danger" type="submit" name="opticattributemanagement-submit" '); 
                                                    if ($vendor_link_count !== 0) { echo('disabled title="vendor still linked to stock. Remove these links before deleting."'); } 
                                                    echo('><i class="fa fa-trash"></i></button></td>');
                                            } else {
                                                echo('<button class="btn btn-success" type="submit" name="opticattributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>');
                                            }
                                            echo('<td class="text-center align-middle">'); 
                                                if ($vendor_deleted !== 1) {
                                                    if ($vendor_link_count !== 0) { echo('<button class="btn btn-warning" id="vendor-'.$m.'-links" type="button" onclick="showLinks(\'vendor\', \''.$m.'\')">Show Links</button>'); }
                                                } else {
                                                    echo('<or class="green">Restore?</or>');
                                                }
                                            echo('</td>');
                                    echo('</form>
                                    </tr>
                                ');
                                if ($vendor_link_count !== 0) { 
                                    echo('
                                        <tr id="vendor-row-'.$m.'-links" class="align-middle" hidden>
                                            <td colspan=100%>
                                                <div>
                                                    <table class="table table-dark theme-table">
                                                        <thead>
                                                            <tr class="theme-tableOuter">
                                                                <th>Optic ID</th>
                                                                <th>Optic Model</th>
                                                                <th>Optic Serial</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>');
                                                        $links = $vendors_links[$m];
                                                        foreach ($links as $mm) {
                                                            echo('
                                                                <tr class="">
                                                                    <td class="text-center">'.$mm['item_id'].'</td>
                                                                    <td class="text-center">'.$mm['item_model'].'</td>
                                                                    <td class="text-center">'.$mm['item_serial'].'</td>
                                                                </tr>
                                                            ');
                                                        }                                                    
                                                        echo('
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    ');
                                }
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
                <?php
                    }  
                ?>  

                <hr style="border-color:white; margin-left:10px"> 

                <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Types</h4>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement-optic_types')) {
                    echo('<div style="margin-right: 10px; margin-left: 10px">');
                    showResponse();
                    echo('</div>');
                }

                $types = [];
                $types_links = [];

                $t_d = 0;

                $sql_types = "SELECT 
                                    T.id AS type_id, 
                                    T.name AS type_name, 
                                    T.deleted AS type_deleted,
                                    I.id AS item_id, I.type_id AS item_type_id, I.serial_number AS item_serial_number, I.model AS item_model
                                FROM 
                                    optic_type AS T
                                LEFT JOIN
                                    optic_item AS I ON T.id=I.type_id AND I.deleted=0";                
                $stmt_types = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_types, $sql_types)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_execute($stmt_types);
                    $result_types = mysqli_stmt_get_result($stmt_types);
                    $rowCount_types = $result_types->num_rows;
                    while ($row_types = $result_types->fetch_assoc()) {
                        if ($row_types['type_deleted'] == 1) {
                            $t_d++;
                        }
                        if (!array_key_exists($row_types['type_id'], $types)) {
                            $types[$row_types['type_id']] = array('id' =>  $row_types['type_id'], 'name' => $row_types['type_name'], 'deleted' => $row_types['type_deleted']);
                        }
                        if (isset($row_types['item_type_id']) && $row_types['item_type_id'] !== NULL && $row_types['item_type_id'] !== '') {
                            $types_links[$row_types['type_id']][] = array('id' => $row_types['item_type_id'], 'item_id' => $row_types['item_id'],
                                                                                    'item_serial' => $row_types['item_serial_number'], 'item_model' => $row_types['item_model']);
                        }
                    }
                    ?>
                    <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                        <table class="table table-dark theme-table" style="max-width:max-content">
                            <thead>
                                <tr class="theme-tableOuter">
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Item ID</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                                        <button id="show-deleted-optic_types" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('optic_types', 1)" <?php if($t_d == 0) { echo "hidden"; } ?> >
                                            <span class="zeroStockFont">
                                                <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                                
                                            </span>
                                        </button>
                                        <button id="hide-deleted-optic_types" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('optic_types', 0)" hidden>
                                            <span class="zeroStockFont">
                                                <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                            </span>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php
                            foreach ($types as $type) {
                                $m = $type['id'];
                                $type_deleted = $type['deleted'];
                                $type_deleted_class = '';
                                if ($type_deleted == 1) {
                                    $type_deleted_class = 'red theme-divBg optic_types-deleted';
                                }
                                if (array_key_exists($m, $types_links)) {
                                    $type_link_count = count($types_links[$m]);
                                } else {
                                    $type_link_count = 0;
                                }
                                
                                echo('
                                    <tr id="type-row-'.$m.'" class="align-middle '.$type_deleted_class.'"'); if ($type_deleted == 1) { echo(' hidden'); } echo('>
                                        <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                            <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                            <input type="hidden" name="attribute-type" value="optic_types"/>
                                            <input type="hidden" name="id" value="'.$m.'">
                                            <td id="type-'.$m.'-id" class="text-center align-middle">'.$m.'</td>
                                            <td id="type-'.$m.'-name" class="text-center align-middle">'.$type['name'].'</td>
                                            <td class="text-center align-middle">'.$type_link_count.'</td>
                                            <td class="text-center align-middle">');
                                            if ($type_deleted !== 1) {
                                                echo('<button class="btn btn-danger" type="submit" name="opticattributemanagement-submit" '); 
                                                    if ($type_link_count !== 0) { echo('disabled title="type still linked to stock. Remove these links before deleting."'); } 
                                                    echo('><i class="fa fa-trash"></i></button></td>');
                                            } else {
                                                echo('<button class="btn btn-success" type="submit" name="opticattributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>');
                                            }
                                            echo('<td class="text-center align-middle">'); 
                                                if ($type_deleted !== 1) {
                                                    if ($type_link_count !== 0) { echo('<button class="btn btn-warning" id="type-'.$m.'-links" type="button" onclick="showLinks(\'type\', \''.$m.'\')">Show Links</button>'); }
                                                } else {
                                                    echo('<or class="green">Restore?</or>');
                                                }
                                            echo('</td>');
                                    echo('</form>
                                    </tr>
                                ');
                                if ($type_link_count !== 0) { 
                                    echo('
                                        <tr id="type-row-'.$m.'-links" class="align-middle" hidden>
                                            <td colspan=100%>
                                                <div>
                                                    <table class="table table-dark theme-table">
                                                        <thead>
                                                            <tr class="theme-tableOuter">
                                                                <th>Optic ID</th>
                                                                <th>Optic Model</th>
                                                                <th>Optic Serial</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>');
                                                        $links = $types_links[$m];
                                                        foreach ($links as $mm) {
                                                            echo('
                                                                <tr class="">
                                                                    <td class="text-center">'.$mm['item_id'].'</td>
                                                                    <td class="text-center">'.$mm['item_model'].'</td>
                                                                    <td class="text-center">'.$mm['item_serial'].'</td>
                                                                </tr>
                                                            ');
                                                        }                                                    
                                                        echo('
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    ');
                                }
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
                <?php
                    }  
                ?>  

                <hr style="border-color:white; margin-left:10px"> 

                <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Speeds</h4>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement-optic_speeds')) {
                    echo('<div style="margin-right: 10px; margin-left: 10px">');
                    showResponse();
                    echo('</div>');
                }

                $speeds = [];
                $speeds_links = [];

                $t_d = 0;

                $sql_speeds = "SELECT 
                                    C.id AS speed_id, 
                                    C.name AS speed_name, 
                                    C.deleted AS speed_deleted,
                                    I.id AS item_id, I.speed_id AS item_speed_id, I.serial_number AS item_serial_number, I.model AS item_model
                                FROM 
                                    optic_speed AS C
                                LEFT JOIN
                                    optic_item AS I ON C.id=I.speed_id AND I.deleted=0";                
                $stmt_speeds = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_speeds, $sql_speeds)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_execute($stmt_speeds);
                    $result_speeds = mysqli_stmt_get_result($stmt_speeds);
                    $rowCount_speeds = $result_speeds->num_rows;
                    while ($row_speeds = $result_speeds->fetch_assoc()) {
                        if ($row_speeds['speed_deleted'] == 1) {
                            $t_d++;
                        }
                        if (!array_key_exists($row_speeds['speed_id'], $speeds)) {
                            $speeds[$row_speeds['speed_id']] = array('id' =>  $row_speeds['speed_id'], 'name' => $row_speeds['speed_name'], 'deleted' => $row_speeds['speed_deleted']);
                        }
                        if (isset($row_speeds['item_speed_id']) && $row_speeds['item_speed_id'] !== NULL && $row_speeds['item_speed_id'] !== '') {
                            $speeds_links[$row_speeds['speed_id']][] = array('id' => $row_speeds['item_speed_id'], 'item_id' => $row_speeds['item_id'],
                                                                                    'item_serial' => $row_speeds['item_serial_number'], 'item_model' => $row_speeds['item_model']);
                        }
                    }
                    ?>
                    <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                        <table class="table table-dark theme-table" style="max-width:max-content">
                            <thead>
                                <tr class="theme-tableOuter">
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Item ID</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                                        <button id="show-deleted-optic_speeds" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('optic_speeds', 1)" <?php if($t_d == 0) { echo "hidden"; } ?> >
                                            <span class="zeroStockFont">
                                                <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                                
                                            </span>
                                        </button>
                                        <button id="hide-deleted-optic_speeds" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('optic_speeds', 0)" hidden>
                                            <span class="zeroStockFont">
                                                <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                            </span>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php
                            foreach ($speeds as $speed) {
                                $m = $speed['id'];
                                $speed_deleted = $speed['deleted'];
                                $speed_deleted_class = '';
                                if ($speed_deleted == 1) {
                                    $speed_deleted_class = 'red theme-divBg optic_speeds-deleted';
                                }
                                if (array_key_exists($m, $speeds_links)) {
                                    $speed_link_count = count($speeds_links[$m]);
                                } else {
                                    $speed_link_count = 0;
                                }
                                
                                echo('
                                    <tr id="speed-row-'.$m.'" class="align-middle '.$speed_deleted_class.'"'); if ($speed_deleted == 1) { echo(' hidden'); } echo('>
                                        <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                            <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                            <input type="hidden" name="attribute-type" value="optic_speeds"/>
                                            <input type="hidden" name="id" value="'.$m.'">
                                            <td id="speed-'.$m.'-id" class="text-center align-middle">'.$m.'</td>
                                            <td id="speed-'.$m.'-name" class="text-center align-middle">'.$speed['name'].'</td>
                                            <td class="text-center align-middle">'.$speed_link_count.'</td>
                                            <td class="text-center align-middle">');
                                            if ($speed_deleted !== 1) {
                                                echo('<button class="btn btn-danger" type="submit" name="opticattributemanagement-submit" '); 
                                                    if ($speed_link_count !== 0) { echo('disabled title="speed still linked to stock. Remove these links before deleting."'); } 
                                                    echo('><i class="fa fa-trash"></i></button></td>');
                                            } else {
                                                echo('<button class="btn btn-success" type="submit" name="opticattributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>');
                                            }
                                            echo('<td class="text-center align-middle">'); 
                                                if ($speed_deleted !== 1) {
                                                    if ($speed_link_count !== 0) { echo('<button class="btn btn-warning" id="speed-'.$m.'-links" type="button" onclick="showLinks(\'speed\', \''.$m.'\')">Show Links</button>'); }
                                                } else {
                                                    echo('<or class="green">Restore?</or>');
                                                }
                                            echo('</td>');
                                    echo('</form>
                                    </tr>
                                ');
                                if ($speed_link_count !== 0) { 
                                    echo('
                                        <tr id="speed-row-'.$m.'-links" class="align-middle" hidden>
                                            <td colspan=100%>
                                                <div>
                                                    <table class="table table-dark theme-table">
                                                        <thead>
                                                            <tr class="theme-tableOuter">
                                                                <th>Optic ID</th>
                                                                <th>Optic Model</th>
                                                                <th>Optic Serial</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>');
                                                        $links = $speeds_links[$m];
                                                        foreach ($links as $mm) {
                                                            echo('
                                                                <tr class="">
                                                                    <td class="text-center">'.$mm['item_id'].'</td>
                                                                    <td class="text-center">'.$mm['item_model'].'</td>
                                                                    <td class="text-center">'.$mm['item_serial'].'</td>
                                                                </tr>
                                                            ');
                                                        }                                                    
                                                        echo('
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    ');
                                }
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
                <?php
                    }  
                ?>

                <hr style="border-color:white; margin-left:10px"> 

                <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Connectors</h4>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement-optic_connectors')) {
                    echo('<div style="margin-right: 10px; margin-left: 10px">');
                    showResponse();
                    echo('</div>');
                }

                $connectors = [];
                $connectors_links = [];

                $t_d = 0;

                $sql_connectors = "SELECT 
                                    C.id AS connector_id, 
                                    C.name AS connector_name, 
                                    C.deleted AS connector_deleted,
                                    I.id AS item_id, I.connector_id AS item_connector_id, I.serial_number AS item_serial_number, I.model AS item_model
                                FROM 
                                    optic_connector AS C
                                LEFT JOIN
                                    optic_item AS I ON C.id=I.connector_id AND I.deleted=0";                
                $stmt_connectors = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_connectors, $sql_connectors)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_execute($stmt_connectors);
                    $result_connectors = mysqli_stmt_get_result($stmt_connectors);
                    $rowCount_connectors = $result_connectors->num_rows;
                    while ($row_connectors = $result_connectors->fetch_assoc()) {
                        if ($row_connectors['connector_deleted'] == 1) {
                            $t_d++;
                        }
                        if (!array_key_exists($row_connectors['connector_id'], $connectors)) {
                            $connectors[$row_connectors['connector_id']] = array('id' =>  $row_connectors['connector_id'], 'name' => $row_connectors['connector_name'], 'deleted' => $row_connectors['connector_deleted']);
                        }
                        if (isset($row_connectors['item_connector_id']) && $row_connectors['item_connector_id'] !== NULL && $row_connectors['item_connector_id'] !== '') {
                            $connectors_links[$row_connectors['connector_id']][] = array('id' => $row_connectors['item_connector_id'], 'item_id' => $row_connectors['item_id'],
                                                                                    'item_serial' => $row_connectors['item_serial_number'], 'item_model' => $row_connectors['item_model']);
                        }
                    }
                    ?>
                    <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                        <table class="table table-dark theme-table" style="max-width:max-content">
                            <thead>
                                <tr class="theme-tableOuter">
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Item ID</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                                        <button id="show-deleted-optic_connectors" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('optic_connectors', 1)" <?php if($t_d == 0) { echo "hidden"; } ?> >
                                            <span class="zeroStockFont">
                                                <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                                
                                            </span>
                                        </button>
                                        <button id="hide-deleted-optic_connectors" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('optic_connectors', 0)" hidden>
                                            <span class="zeroStockFont">
                                                <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                            </span>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php
                            foreach ($connectors as $connector) {
                                $m = $connector['id'];
                                $connector_deleted = $connector['deleted'];
                                $connector_deleted_class = '';
                                if ($connector_deleted == 1) {
                                    $connector_deleted_class = 'red theme-divBg optic_connectors-deleted';
                                }
                                if (array_key_exists($m, $connectors_links)) {
                                    $connector_link_count = count($connectors_links[$m]);
                                } else {
                                    $connector_link_count = 0;
                                }
                                
                                echo('
                                    <tr id="connector-row-'.$m.'" class="align-middle '.$connector_deleted_class.'"'); if ($connector_deleted == 1) { echo(' hidden'); } echo('>
                                        <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                            <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                            <input type="hidden" name="attribute-type" value="optic_connectors"/>
                                            <input type="hidden" name="id" value="'.$m.'">
                                            <td id="connector-'.$m.'-id" class="text-center align-middle">'.$m.'</td>
                                            <td id="connector-'.$m.'-name" class="text-center align-middle">'.$connector['name'].'</td>
                                            <td class="text-center align-middle">'.$connector_link_count.'</td>
                                            <td class="text-center align-middle">');
                                            if ($connector_deleted !== 1) {
                                                echo('<button class="btn btn-danger" type="submit" name="opticattributemanagement-submit" '); 
                                                    if ($connector_link_count !== 0) { echo('disabled title="connector still linked to stock. Remove these links before deleting."'); } 
                                                    echo('><i class="fa fa-trash"></i></button></td>');
                                            } else {
                                                echo('<button class="btn btn-success" type="submit" name="opticattributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>');
                                            }
                                            echo('<td class="text-center align-middle">'); 
                                                if ($connector_deleted !== 1) {
                                                    if ($connector_link_count !== 0) { echo('<button class="btn btn-warning" id="connector-'.$m.'-links" type="button" onclick="showLinks(\'connector\', \''.$m.'\')">Show Links</button>'); }
                                                } else {
                                                    echo('<or class="green">Restore?</or>');
                                                }
                                            echo('</td>');
                                    echo('</form>
                                    </tr>
                                ');
                                if ($connector_link_count !== 0) { 
                                    echo('
                                        <tr id="connector-row-'.$m.'-links" class="align-middle" hidden>
                                            <td colspan=100%>
                                                <div>
                                                    <table class="table table-dark theme-table">
                                                        <thead>
                                                            <tr class="theme-tableOuter">
                                                                <th>Optic ID</th>
                                                                <th>Optic Model</th>
                                                                <th>Optic Serial</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>');
                                                        $links = $connectors_links[$m];
                                                        foreach ($links as $mm) {
                                                            echo('
                                                                <tr class="">
                                                                    <td class="text-center">'.$mm['item_id'].'</td>
                                                                    <td class="text-center">'.$mm['item_model'].'</td>
                                                                    <td class="text-center">'.$mm['item_serial'].'</td>
                                                                </tr>
                                                            ');
                                                        }                                                    
                                                        echo('
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    ');
                                }
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
                <?php
                    }  
                ?>   
                
                <hr style="border-color:white; margin-left:10px"> 

                <h4 style="margin-left:10px; margin-right:10px; font-size:20px; margin-bottom:10px">Distances</h4>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'opticattributemanagement-optic_distances')) {
                    echo('<div style="margin-right: 10px; margin-left: 10px">');
                    showResponse();
                    echo('</div>');
                }

                $distances = [];
                $distances_links = [];

                $t_d = 0;

                $sql_distances = "SELECT 
                                    C.id AS distance_id, 
                                    C.name AS distance_name, 
                                    C.deleted AS distance_deleted,
                                    I.id AS item_id, I.distance_id AS item_distance_id, I.serial_number AS item_serial_number, I.model AS item_model
                                FROM 
                                    optic_distance AS C
                                LEFT JOIN
                                    optic_item AS I ON C.id=I.distance_id AND I.deleted=0";                
                $stmt_distances = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_distances, $sql_distances)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_execute($stmt_distances);
                    $result_distances = mysqli_stmt_get_result($stmt_distances);
                    $rowCount_distances = $result_distances->num_rows;
                    while ($row_distances = $result_distances->fetch_assoc()) {
                        if ($row_distances['distance_deleted'] == 1) {
                            $t_d++;
                        }
                        if (!array_key_exists($row_distances['distance_id'], $distances)) {
                            $distances[$row_distances['distance_id']] = array('id' =>  $row_distances['distance_id'], 'name' => $row_distances['distance_name'], 'deleted' => $row_distances['distance_deleted']);
                        }
                        if (isset($row_distances['item_distance_id']) && $row_distances['item_distance_id'] !== NULL && $row_distances['item_distance_id'] !== '') {
                            $distances_links[$row_distances['distance_id']][] = array('id' => $row_distances['item_distance_id'], 'item_id' => $row_distances['item_id'],
                                                                                    'item_serial' => $row_distances['item_serial_number'], 'item_model' => $row_distances['item_model']);
                        }
                    }
                    ?>
                    <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                        <table class="table table-dark theme-table" style="max-width:max-content">
                            <thead>
                                <tr class="theme-tableOuter">
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Item ID</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Links</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1; z-index:10">Delete</th>
                                    <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">
                                        <button id="show-deleted-optic_distances" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes('optic_distances', 1)" <?php if($t_d == 0) { echo "hidden"; } ?> >
                                            <span class="zeroStockFont">
                                                <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                                
                                            </span>
                                        </button>
                                        <button id="hide-deleted-optic_distances" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes('optic_distances', 0)" hidden>
                                            <span class="zeroStockFont">
                                                <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                            </span>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php
                            foreach ($distances as $distance) {
                                $m = $distance['id'];
                                $distance_deleted = $distance['deleted'];
                                $distance_deleted_class = '';
                                if ($distance_deleted == 1) {
                                    $distance_deleted_class = 'red theme-divBg optic_distances-deleted';
                                }
                                if (array_key_exists($m, $distances_links)) {
                                    $distance_link_count = count($distances_links[$m]);
                                } else {
                                    $distance_link_count = 0;
                                }
                                
                                echo('
                                    <tr id="distance-row-'.$m.'" class="align-middle '.$distance_deleted_class.'"'); if ($distance_deleted == 1) { echo(' hidden'); } echo('>
                                        <form enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                            <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                            <input type="hidden" name="attribute-type" value="optic_distances"/>
                                            <input type="hidden" name="id" value="'.$m.'">
                                            <td id="distance-'.$m.'-id" class="text-center align-middle">'.$m.'</td>
                                            <td id="distance-'.$m.'-name" class="text-center align-middle">'.$distance['name'].'</td>
                                            <td class="text-center align-middle">'.$distance_link_count.'</td>
                                            <td class="text-center align-middle">');
                                            if ($distance_deleted !== 1) {
                                                echo('<button class="btn btn-danger" type="submit" name="opticattributemanagement-submit" '); 
                                                    if ($distance_link_count !== 0) { echo('disabled title="distance still linked to stock. Remove these links before deleting."'); } 
                                                    echo('><i class="fa fa-trash"></i></button></td>');
                                            } else {
                                                echo('<button class="btn btn-success" type="submit" name="opticattributemanagement-restore"><i class="fa fa-trash-restore"></i></button></td>');
                                            }
                                            echo('<td class="text-center align-middle">'); 
                                                if ($distance_deleted !== 1) {
                                                    if ($distance_link_count !== 0) { echo('<button class="btn btn-warning" id="distance-'.$m.'-links" type="button" onclick="showLinks(\'distance\', \''.$m.'\')">Show Links</button>'); }
                                                } else {
                                                    echo('<or class="green">Restore?</or>');
                                                }
                                            echo('</td>');
                                    echo('</form>
                                    </tr>
                                ');
                                if ($distance_link_count !== 0) { 
                                    echo('
                                        <tr id="distance-row-'.$m.'-links" class="align-middle" hidden>
                                            <td colspan=100%>
                                                <div>
                                                    <table class="table table-dark theme-table">
                                                        <thead>
                                                            <tr class="theme-tableOuter">
                                                                <th>Optic ID</th>
                                                                <th>Optic Model</th>
                                                                <th>Optic Serial</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>');
                                                        $links = $distances_links[$m];
                                                        foreach ($links as $mm) {
                                                            echo('
                                                                <tr class="">
                                                                    <td class="text-center">'.$mm['item_id'].'</td>
                                                                    <td class="text-center">'.$mm['item_model'].'</td>
                                                                    <td class="text-center">'.$mm['item_serial'].'</td>
                                                                </tr>
                                                            ');
                                                        }                                                    
                                                        echo('
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    ');
                                }
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
                <?php
                    }  
                ?>
            </div>

            
        </div>

        <div class="container" style="padding-bottom:0px">       
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="stockmanagement-settings" onclick="toggleSection(this, 'stockmanagement')">Stock Management <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Stock Management Settings -->
            <div style="padding-top: 20px" id="stockmanagement" hidden>
                <h4 style="margin-left:10px; margin-right:10px; margin-top:5px; font-size:20px; margin-bottom:10px">Cost Enablement</h4>
                <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                    <p id="cost-output" class="last-edit-T" hidden></p>
                    <table>
                        <tbody>
                            <tr>
                                <td class="align-middle" style="margin-left:25px;margin-right:10px" id="normal-cost">
                                    <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable Cost/Pricing for normal stock items.">Normal Stock Cost:</p>
                                </td>
                                <td class="align-middle" style="padding-left:5px;padding-right:20px" id="normal-cost-toggle">
                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                        <input type="checkbox" name="normal-cost" onchange="toggleCost(this, 1)" <?php if ($current_cost_enable_normal == 1) { echo("checked");} ?>>
                                        <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                    </label>
                                </td>
                                <td class="align-middle" style="margin-left:25px;margin-right:10px" id="cable-cost">
                                    <p style="min-height:max-content;margin:0px" class="align-middle title" title="Enable Cost/Pricing for cable stock items.">Cable Stock Cost:</p>
                                </td>
                                <td class="align-middle" style="padding-left:5px;padding-right:20px" id="cable-cost-toggle">
                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                        <input type="checkbox" name="cable-cost" onchange="toggleCost(this, 2)" <?php if ($current_cost_enable_cable == 1) { echo("checked");} ?>>
                                        <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <h4 style="margin-left:10px; margin-right:10px; margin-top:20px; font-size:20px; margin-bottom:10px">Deleted Stock</h4>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'stockmanagement')) {
                    echo('<div style="margin-right: 10px; margin-left: 10px">');
                    showResponse();
                    echo('</div>');
                }
                // cost/price toggles for both normal stock and cable stock
                ?>
                <?php
                $sql_stock = "SELECT 
                                stock.id AS stock_id, 
                                stock.name AS stock_name, 
                                stock.deleted AS stock_deleted,
                                stock.description AS stock_description,
                                stock.is_cable AS stock_is_cable,
                                stock.sku AS stock_sku
                            FROM 
                                stock
                            WHERE
                                stock.deleted=1";                  
                $stmt_stock = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_stock, $sql_stock)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_execute($stmt_stock);
                    $result_stock = mysqli_stmt_get_result($stmt_stock);
                    $rowCount_stock = $result_stock->num_rows;
                    if ($rowCount_stock !== 0) {
                        ?>
                        <div style="max-height:60vh;overflow-x: hidden;overflow-y: auto; margin-left:10px; margin-right:10px">
                        <?php echo('<p class="margin-left:10px">Count: <or class="green">'.$rowCount_stock.'</or></p>'); ?>
                            <table class="table table-dark theme-table" style="max-width:max-content">
                                <thead>
                                    <tr class="theme-tableOuter">
                                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">ID</th>
                                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Name</th>
                                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">SKU</th>
                                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Description</th>
                                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Cable?</th>
                                        <th class="text-center theme-tableOuter align-middle" style="position: sticky; top: -1;">Restore</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    while ($row_stock = $result_stock->fetch_assoc()) {
                                        $s_id = $row_stock['stock_id'];
                                        $s_name = $row_stock['stock_name'];
                                        $s_sku = $row_stock['stock_sku'];
                                        $s_description = $row_stock['stock_description'];
                                        $s_short_description = strlen($s_description) > 30 ? substr($s_description,0,27).'...' : $s_description;
                                        $s_is_cable = $row_stock['stock_is_cable'] == 1 ? '<or class="green">Yes</or>' : '<or class="red">No</or>';
                                        $s_d_l = 0; // this is used to determine if the stock description length is too long.
                                        if (strlen($s_description) > 30) {
                                            $s_d_l = 1;
                                        }

                                        echo('
                                            <tr id="deleted-stock-row-'.$s_id.' class="align-middle">
                                                <form enctype="multipart/form-data" action="./includes/stock-modify.inc.php" method="POST">
                                                    <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                                    <input type="hidden" name="stockmanagement-type" value="deleted"/>
                                                    <input type="hidden" name="id" value="'.$s_id.'">
                                                    <td class="align-middle text-center">'.$s_id.'</td>
                                                    <td class="align-middle text-center"><a class="link" href="stock.php?stock_id='.$s_id.'">'.$s_name.'</a></td>
                                                    <td class="align-middle text-center">'.$s_sku.'</td>
                                                    <td class="align-middle text-center"><or'); if ($s_d_l == 1) {echo (' title="'.$s_description.'" class="title"');} echo('>'.$s_short_description.'</td>
                                                    <td class="align-middle text-center">'.$s_is_cable.'</td>
                                                    <td class="align-middle text-center"><button class="btn btn-success" type="submit" name="stockmanagement-restore"><i class="fa fa-trash-restore"></i></button></td>
                                                </form>
                                            </tr>
                                        ');
                                    }
                                ?>
                                </tbody>
                            </table>
                        </div>
                <?php
                    } else {
                        echo('<p style="margin-left:10px; margin-right:10px">No deleted stock found.</p>');
                    }
                }
                ?>
                
            </div>
        </div>

        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="stocklocations-settings" onclick="toggleSection(this, 'stocklocations')">Stock Location Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            <!-- Stock Location Settings -->
            <div style="padding-top: 20px" id="stocklocations" hidden>
                <style>
                    .location-deleted{
                        color: red !important;
                    }
                </style>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'stocklocation-settings')) {
                    showResponse();
                }
            
                $locations = [];
                $sql_locations = "SELECT site.id AS site_id, site.name AS site_name, site.description AS site_description, site.deleted AS site_deleted,
                                        area.id AS area_id, area.name AS area_name, area.description AS area_description, area.site_id AS area_site_id, area.deleted AS area_deleted,
                                        shelf.id AS shelf_id, shelf.name AS shelf_name, shelf.area_id AS shelf_area_id, shelf.deleted AS shelf_deleted
                                    FROM site
                                    LEFT JOIN area ON site.id = area.site_id
                                    LEFT JOIN shelf ON area.id = shelf.area_id
                                    ORDER BY site.id, area.id, shelf.id";
                $stmt_locations = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_locations, $sql_locations)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_execute($stmt_locations);
                    $result_locations = mysqli_stmt_get_result($stmt_locations);
                    $rowCount_locations = $result_locations->num_rows;
                    if ($rowCount_locations < 1) {
                        echo ("No sites found");
                    } else {
                        $locations_deleted_count = 0;
                        while( $row_locations = $result_locations->fetch_assoc() ) {  
                            if ($row_locations['site_deleted'] == 1) {
                                $locations_deleted_count ++;
                            }
                            if ($row_locations['area_deleted'] == 1) {
                                $locations_deleted_count ++;
                            }
                            if ($row_locations['shelf_deleted'] == 1) {
                                $locations_deleted_count ++;
                            }
                            $locations[$row_locations['site_id']]['site_id'] = $row_locations['site_id'];
                            $locations[$row_locations['site_id']]['site_name'] = $row_locations['site_name'];
                            $locations[$row_locations['site_id']]['site_description'] = $row_locations['site_description'];
                            $locations[$row_locations['site_id']]['site_deleted'] = $row_locations['site_deleted'];
                            $locations[$row_locations['site_id']]['areas'][$row_locations['area_id']]['area_id'] = $row_locations['area_id'];
                            $locations[$row_locations['site_id']]['areas'][$row_locations['area_id']]['area_name'] = $row_locations['area_name'];
                            $locations[$row_locations['site_id']]['areas'][$row_locations['area_id']]['area_description'] = $row_locations['area_description'];
                            $locations[$row_locations['site_id']]['areas'][$row_locations['area_id']]['area_site_id'] = $row_locations['area_site_id'];
                            $locations[$row_locations['site_id']]['areas'][$row_locations['area_id']]['area_deleted'] = $row_locations['area_deleted'];
                            $locations[$row_locations['site_id']]['areas'][$row_locations['area_id']]['shelves'][$row_locations['shelf_id']]['shelf_id'] = $row_locations['shelf_id'];
                            $locations[$row_locations['site_id']]['areas'][$row_locations['area_id']]['shelves'][$row_locations['shelf_id']]['shelf_name'] = $row_locations['shelf_name'];
                            $locations[$row_locations['site_id']]['areas'][$row_locations['area_id']]['shelves'][$row_locations['shelf_id']]['shelf_area_id'] = $row_locations['shelf_area_id'];
                            $locations[$row_locations['site_id']]['areas'][$row_locations['area_id']]['shelves'][$row_locations['shelf_id']]['shelf_deleted'] = $row_locations['shelf_deleted'];
                        }

                        $l = 0;
                        echo('<table class="table table-dark theme-table text-center" style="max-width:max-content; vertical-align: middle;">
                                <thead>
                                    <tr class="theme-tableOuter">
                                        <th>site_id</th>
                                        <th>site_name</th>
                                        <th hidden>site_description</th>
                                        <th style="border-left:2px solid #95999c">area_id</th>
                                        <th>area_name</th>
                                        <th hidden>area_description</th>
                                        <th hidden>area_site_id</th>
                                        <th hidden>area_parent_id</th>
                                        <th style="border-left:2px solid #95999c">shelf_id</th>
                                        <th>shelf_name</th>
                                        <th hidden>shelf_area_id</th>
                                        <th style="border-left:2px solid #95999c" colspan=3>
                                            <button id="show-deleted-location" class="btn btn-success" style="opacity:90%;color:black;" onclick="toggleDeletedAttributes(\'location\', 1)" '); if ($locations_deleted_count == 0) { echo('hidden'); } echo('>
                                            <span class="zeroStockFont">
                                                <p style="margin:0px;padding:0px"><i class="fa fa-plus"></i> Show Deleted</p>
                                            </span>
                                            </button>
                                            <button id="hide-deleted-location" class="btn btn-danger" style="opacity:80%;color:black;" onclick="toggleDeletedAttributes(\'location\', 0)" hidden>
                                                <span class="zeroStockFont">
                                                    <p style="margin:0px;padding:0px"><i class="fa fa-minus"></i> Hide Deleted</p>
                                                </span>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>');
                                foreach ($locations as $site) {
                                    $l++;
                                    if ($l % 2 == 0) {
                                        $color1 = '#F4BB44';
                                        $color2 = '#ffe47a';
                                        $color3 = '#FFDEAD';
                                    } else {
                                        $color1 = '#6abad6';
                                        $color2 = '#99d4ef';
                                        $color3 = '#c1e9fc';
                                        
                                    }
                                    

                                    $site_id_check = $site['site_id'];

                                    $sql_site_check = "SELECT * FROM area WHERE site_id=$site_id_check;";
                                    $stmt_site_check = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_site_check, $sql_site_check)) {
                                        echo('SQL Failure at '.__LINE__.' in includes/stock-'.$_GET['modify'].'.php');
                                    } else {
                                        mysqli_stmt_execute($stmt_site_check);
                                        $result_site_check = mysqli_stmt_get_result($stmt_site_check);
                                        $rowCount_site_check = $result_site_check->num_rows;
                                    }

                                    if ($site['site_deleted'] == 1) {
                                        $site_deleted_class = ' location-deleted';
                                        $site_deleted_hidden = ' hidden';
                                        $color = '#7e1515';
                                    } else {
                                        $site_deleted_class = '';
                                        $site_deleted_hidden = '';
                                        $color = $color1;
                                    }
                                    
                                    if ($l > 1) {
                                        echo('<tr class="theme-tableOuter'.$site_deleted_class.'"'.$site_deleted_hidden.'><td colspan=9></td></tr>');
                                    }
                                    echo('<tr class="'.$site_deleted_class.'" style="background-color:'.$color.' !important; color:black"'.$site_deleted_hidden.'>
                                            <form id="siteForm-'.$site['site_id'].'" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                                <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                                <input type="hidden" id="site-'.$site['site_id'].'-type" name="type" value="site" />
                                                <input type="hidden" id="site-'.$site['site_id'].'-id" name="id" value="'.$site['site_id'].'" />
                                                <td class="stockTD" style="">'.$site['site_id'].'</td>
                                                <td class="stockTD" style=""><input id="site-'.$site['site_id'].'-name" class="form-control stockTD-input" name="name" type="text" value="'.htmlspecialchars($site['site_name'], ENT_QUOTES, 'UTF-8').'" style="width:150px"/></td>
                                                <td hidden><input id="site-'.$site['site_id'].'-description" class="form-control stockTD-input" type="text" name="description" value="'.htmlspecialchars($site['site_description'], ENT_QUOTES, 'UTF-8').'" /></td>
                                                <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden></td> <td hidden></td> <td hidden></td> 
                                                <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden></td>
                                                <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55; ">
                                                    <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="stocklocation-submit" value="1" type="submit">
                                                        <i class="fa fa-save"></i>
                                                    </button>
                                                </td>
                                                <td class="stockTD theme-table-blank" ">
                                                    <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button" onclick="modalLoadEdit(\''.$site['site_id'].'\', \'site\')">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>
                                                </td>
                                            </form>
                                            <form id="siteForm-delete-'.$site['site_id'].'" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                                <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                                <input type="hidden" name="location-id" value="'.$site_id_check.'" />
                                                <td class="stockTD theme-table-blank">');
                                                if ($site['site_deleted'] !== 1) {
                                                    echo('
                                                    <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="site" type="submit" '); 
                                                    if ($rowCount_site_check > 0 ) { echo('disabled title="Dependencies exist for this object."'); } else { echo('title="Delete object"'); } 
                                                    echo('>
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                    ');
                                                } else {
                                                    echo('
                                                    <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-restore-submit" value="site" type="submit" title="Restore object">
                                                        <i class="fa fa-trash-restore"></i>
                                                    </button>
                                                    ');
                                                }
                                                echo('
                                                </td>
                                            </form>
                                        </tr>');
                                    foreach ($site['areas'] as $area) {
                                        if ($area['area_id'] !== '' && $area['area_id'] !== null) {
                                            $area_id_check = $area['area_id'];

                                            $sql_area_check = "SELECT * FROM shelf WHERE area_id=$area_id_check;";
                                            $stmt_area_check = mysqli_stmt_init($conn);
                                            if (!mysqli_stmt_prepare($stmt_area_check, $sql_area_check)) {
                                                echo('SQL Failure at '.__LINE__.' in includes/stock-'.$_GET['modify'].'.php');
                                            } else {
                                                mysqli_stmt_execute($stmt_area_check);
                                                $result_area_check = mysqli_stmt_get_result($stmt_area_check);
                                                $rowCount_area_check = $result_area_check->num_rows;
                                            }

                                            if ($area['area_deleted'] == 1) {
                                                $area_deleted_class = ' location-deleted';
                                                $area_deleted_hidden = ' hidden';
                                                $color = '#7e1515';
                                            } else {
                                                $area_deleted_class = '';
                                                $area_deleted_hidden = '';
                                                $color = $color2;
                                            }

                                            

                                            echo('<tr class="'.$area_deleted_class.'" style="background-color:'.$color.' !important; color:black"'.$area_deleted_hidden.'>
                                                    <form id="areaForm-'.$area['area_id'].'" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                                        <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                                        <input type="hidden" id="area-'.$area['area_id'].'-type" name="type" name="type" value="area" />
                                                        <input type="hidden" id="area-'.$area['area_id'].'-id" name="id" value="'.$area['area_id'].'" />
                                                        <td class="stockTD theme-table-blank"></td> <td class="theme-table-blank"></td> <td hidden></td>
                                                        <td class="stockTD" style="border-left:2px solid #454d55; ">'.$area['area_id'].'</td>
                                                        <td class="stockTD" style=""><input id="area-'.$area['area_id'].'-name" class="form-control stockTD-input" type="text" name="name" value="'.htmlspecialchars($area['area_name'], ENT_QUOTES, 'UTF-8').'" style="width:150px"/></td>
                                                        <td class="stockTD" hidden><input id="area-'.$area['area_id'].'-description" class="form-control stockTD-input" type="text" name="description" value="'.htmlspecialchars($area['area_description'], ENT_QUOTES, 'UTF-8').'" /></td>
                                                        <td class="stockTD" hidden><input id="area-'.$area['area_id'].'-parent" type="hidden" name="area-site-id" value="'.$area['area_site_id'].'" /></td>
                                                        <td class="stockTD" style="border-left:2px solid #454d55; "></td> <td></td> <td hidden></td>
                                                        <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55; ">
                                                            <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="stocklocation-submit" value="1" type="submit">
                                                                <i class="fa fa-save"></i>
                                                            </button>
                                                        </td>
                                                        <td class="stockTD theme-table-blank">
                                                            <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button" onclick="modalLoadEdit(\''.$area['area_id'].'\', \'area\')">
                                                                <i class="fa fa-pencil"></i>
                                                            </button>
                                                        </td>
                                                    </form>
                                                    <form id="areaForm-delete-'.$area['area_id'].'" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                                        <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                                        <input type="hidden" name="location-id" value="'.$area_id_check.'" />
                                                        <td class="stockTD theme-table-blank">');
                                                        if ($area['area_deleted'] !== 1) {
                                                            echo('
                                                            <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="area" type="submit" '); 
                                                            if ($rowCount_area_check > 0 ) { echo('disabled title="Dependencies exist for this object."'); } else { echo('title="Delete object"'); } 
                                                            echo('>
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                            ');
                                                        } else {
                                                            echo('
                                                            <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-restore-submit" value="area" type="submit" title="Restore object">
                                                                <i class="fa fa-trash-restore"></i>
                                                            </button>
                                                            ');
                                                        }
                                                        echo('
                                                        </td>
                                                    </form>
                                                </tr>');
                                            foreach ($area['shelves'] as $shelf) {
                                                if ($shelf['shelf_id'] !== '' && $shelf['shelf_id'] !== null) {
                                                    $shelf_id_check = $shelf['shelf_id'];

                                                    $sql_shelf_check = "SELECT * FROM item WHERE shelf_id=$shelf_id_check;";
                                                    $stmt_shelf_check = mysqli_stmt_init($conn);
                                                    if (!mysqli_stmt_prepare($stmt_shelf_check, $sql_shelf_check)) {
                                                        echo('SQL Failure at '.__LINE__.' in includes/stock-'.$_GET['modify'].'.php');
                                                    } else {
                                                        mysqli_stmt_execute($stmt_shelf_check);
                                                        $result_shelf_check = mysqli_stmt_get_result($stmt_shelf_check);
                                                        $rowCount_shelf_check = $result_shelf_check->num_rows;
                                                    }
                                                    if ($shelf['shelf_deleted'] == 1) {
                                                        $shelf_deleted_class = ' location-deleted';
                                                        $shelf_deleted_hidden = ' hidden';
                                                        $color = '#7e1515';
                                                    } else {
                                                        $shelf_deleted_class = '';
                                                        $shelf_deleted_hidden = '';
                                                        $color = $color3;
                                                    }

                                                    echo('<tr class="'.$shelf_deleted_class.'" style="background-color:'.$color.' !important; color:black"'.$shelf_deleted_hidden.'>
                                                            <form id="shelfForm-'.$shelf['shelf_id'].'" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                                            <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                                                <input type="hidden" id="shelf-'.$shelf['shelf_id'].'-site" name="site" value="'.$site['site_id'].'" />
                                                                <input type="hidden" id="shelf-'.$shelf['shelf_id'].'-type" name="type" value="shelf" />
                                                                <input type="hidden" id="shelf-'.$shelf['shelf_id'].'-id" name="id" value="'.$shelf['shelf_id'].'" />
                                                                <td class="stockTD theme-table-blank"></td> <td class="theme-table-blank"></td> <td hidden></td> 
                                                                <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55;"></td> <td class="theme-table-blank"></td> <td hidden></td> <td hidden></td> <td hidden></td>
                                                                <td class="stockTD" style="border-left:2px solid #454d55; ">'.$shelf['shelf_id'].'</td>
                                                                <td class="stockTD" style=""><input id="shelf-'.$shelf['shelf_id'].'-name" class="form-control stockTD-input" type="text" name="name" value="'.htmlspecialchars($shelf['shelf_name'], ENT_QUOTES, 'UTF-8').'" style="width:150px"/></td>
                                                                <td class="stockTD" hidden><input id="shelf-'.$shelf['shelf_id'].'-parent" type="hidden" name="shelf-area-id" value="'.$shelf['shelf_area_id'].'" /></td>
                                                                <td class="stockTD theme-table-blank" style="border-left:2px solid #454d55; ">
                                                                    <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="stocklocation-submit" value="1" type="submit">
                                                                        <i class="fa fa-save"></i>
                                                                    </button>
                                                                </td>
                                                                <td class="stockTD theme-table-blank">
                                                                    <button class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" type="button" onclick="modalLoadEdit(\''.$shelf['shelf_id'].'\', \'shelf\')" >
                                                                        <i class="fa fa-pencil"></i>
                                                                    </button>
                                                                </td>
                                                            </form>
                                                            <form id="shelfForm-delete-'.$shelf['shelf_id'].'" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                                                                <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                                                <input type="hidden" name="location-id" value="'.$shelf_id_check.'" />
                                                                <td class="stockTD theme-table-blank">');
                                                                if ($shelf['shelf_deleted'] !== 1) {
                                                                    echo('
                                                                    <button class="btn btn-danger cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-delete-submit" value="shelf" type="submit" '); 
                                                                    if ($rowCount_shelf_check > 0 ) { echo('disabled title="Dependencies exist for this object."'); } else { echo('title="Delete object"'); } 
                                                                    echo('>
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                    ');
                                                                } else {
                                                                    echo('
                                                                    <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" name="location-restore-submit" value="shelf" type="submit" title="Restore object">
                                                                        <i class="fa fa-trash-restore"></i>
                                                                    </button>
                                                                    ');
                                                                }
                                                                echo('
                                                                </td>
                                                            </form>
                                                        </tr>');
                                                }
                                            }
                                        }
                                    }
                                    echo('<tr class="theme-table-blank'.$site_deleted_class.'"'.$site_deleted_hidden.'>
                                        <td colspan=6 class="stockTD">
                                            <button class="btn btn-success cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px; width: 50px" onclick="modalLoadAdd(\''.$site['site_id'].'\')">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </td>
                                        <td colspan=3 style="border-left:2px solid #454d55">  
                                        </td>
                                    </tr>');
                                }
                        echo('  </tbody>
                            </table>');
                    }
                }   





                ?>

            </div>
        </div>

        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="ldap-settings" onclick="toggleSection(this, 'ldap')">LDAP Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 

            <!-- LDAP Settings -->
            <div style="padding-top: 20px" id="ldap" hidden>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'ldap-settings')) {
                    showResponse();
                }
        
                if (isset($_GET['ldapUpload'])) {
                    echo ('<p id="success-output" class="green" style="margin-left:25px">');
                    if ($_GET['ldapUpload'] == 'success') { echo('LDAP config uploaded!'); }
                    if ($_GET['ldapUpload'] == 'configRestored') { echo('LDAP config restored to defaults!'); }
                    echo('</p>');
                }
                ?>
                <form id="ldapToggleForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="ldap-toggle-submit" value="set" />
                    <table id="ldapToggleTable">
                        <tbody>
                            <tr class="nav-row" id="ldap-headings" style="margin-bottom:10px">
                                <td style="width:150px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-username">Enable LDAP</p>
                                    </td>
                                <td class="align-middle">
                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                        <input type="checkbox" name="ldap-enabled" id="ldap-enabled-toggle" <?php if($current_ldap_enabled == 1) { echo("checked"); } ?> >
                                        <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
                
                <form id="ldapForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST" <?php if($current_ldap_enabled == 0) { echo("hidden"); } ?>>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <hr style="border-color:white; margin-left:10px">
                    <table id="ldapTable">
                        <tbody>
                            <tr class="nav-row" id="ldap-headings" style="margin-bottom:10px; margin-right:10px">
                                <th style="width:250px;margin-left:25px"></th>
                                <th style="width: 250px">Custom</th>
                                <th style="margin-left:25px">Default</th>
                            </tr>
                            <tr class="nav-row" id="ldap-auth-username">
                                <td id="ldap-auth-username-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-username">Authentication Username:</p>
                                </td>
                                <td id="ldap-auth-username-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-username" name="auth-username" value="<?php echo(isset($_GET['auth-username']) ? $_GET['auth-username'] : $current_ldap_username); ?>" required>
                                </td>
                                <td id="ldap-auth-username-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-username-default"><?php echo($default_ldap_username); ?></p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-password">
                                <td id="ldap-auth-password-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-password">Authentication Password:</p>
                                </td>
                                <td id="ldap-auth-password-input">
                                    <input class="form-control nav-v-c" type="password" style="width: 250px" id="auth-password" name="auth-password" value="password" required>
                                </td>
                                <td id="ldap-auth-password-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-password-default" <?php echo(($config_d_ldap_password !== '') ? 'type="password"' : ''); ?>><?php echo($default_ldap_password); ?></p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-password-confirm">
                                <td id="ldap-auth-password-confirm-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-password-confirm">Confirm Password:</p>
                                </td>
                                <td id="ldap-auth-passowrd-confirm-input">
                                    <input class="form-control nav-v-c" type="password" style="width: 250px" id="auth-password-confirm" name="auth-password-confirm" value="password" required>
                                </td>
                                <td>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-domain">
                                <td id="ldap-auth-domain-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-domain">Domain:</p>
                                </td>
                                <td id="ldap-auth-domain-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-domain" name="auth-domain" value="<?php echo(isset($_GET['auth-domain']) ? $_GET['auth-domain'] : $current_ldap_domain); ?>" required>
                                </td>
                                <td id="ldap-auth-domain-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-domain-default"><?php echo($default_ldap_domain); ?></p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-host">
                                <td id="ldap-auth-host-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-host">Host:</p>
                                </td>
                                <td id="ldap-auth-host-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-host" name="auth-host" value="<?php echo(isset($_GET['auth-host']) ? $_GET['auth-host'] : $current_ldap_host); ?>" required>
                                </td>
                                <td id="ldap-auth-host-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-host-default"><?php echo($default_ldap_host); ?></p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-host">
                                <td id="ldap-auth-host-secondary-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-host-secondary">Secondary Host:</p>
                                </td>
                                <td id="ldap-auth-host-secondary-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-host-secondary" name="auth-host-secondary" value="<?php echo(isset($_GET['auth-host-secondary']) ? $_GET['auth-host-secondary'] : $current_ldap_host_secondary); ?>">
                                </td>
                                <td id="ldap-auth-host-secondary-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-host-secondary-default"><?php echo($default_ldap_host_secondary); ?></p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-port">
                                <td id="ldap-auth-port-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-port">Port:</p>
                                </td>
                                <td id="ldap-auth-port-input">
                                    <input class="form-control nav-v-c" type="number" style="width: 250px" id="auth-port" name="auth-port" value="<?php echo(isset($_GET['auth-port']) ? $_GET['auth-port'] : $current_ldap_port); ?>" required>
                                </td>
                                <td id="ldap-auth-port-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-port-default"><?php echo($default_ldap_port); ?></p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-basedn">
                                <td id="ldap-auth-basedn-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-basedn">Base DN:</p>
                                </td>
                                <td id="ldap-auth-basedn-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-basedn" name="auth-basedn" value="<?php echo(isset($_GET['auth-basedn']) ? $_GET['auth-basedn'] : $current_ldap_basedn); ?>">
                                </td>
                                <td id="ldap-auth-basedn-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-basedn-default"><?php echo($default_ldap_basedn); ?></p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-usergroup">
                                <td id="ldap-auth-usergroup-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-usergroup">User Group:</p>
                                </td>
                                <td id="ldap-auth-usergroup-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-usergroup" name="auth-usergroup" value="<?php echo(isset($_GET['auth-usergroup']) ? $_GET['auth-usergroup'] : $current_ldap_usergroup); ?>">
                                </td>
                                <td id="ldap-auth-usergroup-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-usergroup-default"><?php echo($default_ldap_usergroup); ?></p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px" id="ldap-auth-userfilter">
                                <td id="ldap-auth-userfilter-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-userfilter">User Filter:</p>
                                </td>
                                <td id="ldap-auth-userfilter-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="auth-userfilter" name="auth-userfilter" value="<?php echo(isset($_GET['auth-userfilter']) ? $_GET['auth-userfilter'] : $current_ldap_userfilter); ?>">
                                </td>
                                <td id="ldap-auth-userfilter-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="auth-userfilter-default"><?php echo($default_ldap_userfilter); ?></p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px">
                                <td style="width:250px">
                                    <input id="ldap-submit" type="submit" name="ldap-submit" class="btn btn-success" style="margin-left:25px" value="Save" />
                                </td>
                                <td style="width:250px">
                                    <a id="test-config" name="test-config" class="btn btn-info" style="margin-left:25px;color:white !important" onclick="testLDAP()">Test config</a>
                                </td>
                                <td style="margin-left:25px">
                                    <input id="ldap-restore-defaults" type="submit" name="ldap-restore-defaults" class="btn btn-danger" style="margin-left:25px" value="Restore Default" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>

        <div class="container" style="padding-bottom:0px">   
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="smtp-settings" onclick="toggleSection(this, 'smtp')">SMTP Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 

            <!-- SMTP Settings -->
            <div style="padding-top: 20px" id="smtp" hidden>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'smtp-settings')) {
                    showResponse();
                }
                ?>
                <form id="smtpToggleForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="smtp-toggle-submit" value="set" />
                    <table id="smtpToggleTable">
                        <tbody>
                            <tr class="nav-row" id="smtp-headings" style="margin-bottom:10px">
                                <td style="width:150px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="auth-username">Enable SMTP</p>
                                    </td>
                                <td class="align-middle">
                                    <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px">
                                        <input type="checkbox" name="smtp-enabled" id="smtp-enabled-toggle" <?php if($current_smtp_enabled == 1) { echo("checked"); } ?> >
                                        <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
                <form id="smtpForm" enctype="multipart/form-data" action="./includes/admin.inc.php" method="POST" <?php if($current_smtp_enabled == 0) { echo("hidden"); } ?>>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <hr style="border-color:white; margin-left:10px">
                    <table id="smtpTable">
                        <tbody>
                            <tr class="nav-row" id="smtp-headings" style="margin-bottom:10px">
                                <th style="width:250px;margin-left:25px"></th>
                                <th style="width: 250px">Custom</th>
                                <th style="margin-left:25px">Default</th>
                            </tr>
                            <tr class="nav-row" id="smtp-host-row">
                                <td id="smtp-host-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-host">SMTP Host:</p>
                                </td>
                                <td id="smtp-host-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-host" name="smtp-host" value="<?php echo $current_smtp_host; ?>" required>
                                </td>
                                <td id="smtp-host-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-host-default"><?php echo $default_smtp_host; ?></p>
                                </td>
                            </tr>
                            <tr class="nav-row" id="smtp-port-row" style="margin-top:20px">
                                <td id="smtp-port-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-port">SMTP Port:</p>
                                </td>
                                <td id="smtp-port-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-port" name="smtp-port" value="<?php echo $current_smtp_port; ?>" required>
                                </td>
                                <td id="smtp-port-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-port-default"><?php echo $default_smtp_port; ?></p>
                                </td>
                            </tr>
                            <tr class="nav-row" id="smtp-encryption-row" style="margin-top:20px">
                                <td id="smtp-encryption-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-encryption">SMTP Encryption:</p>
                                </td>
                                <td id="smtp-encryption-input">
                                    <select id="smtp-encryption" name="smtp-encryption" style="width:250px" class="form-control nav-v-c" required>
                                        <option value="none" <?php if ($current_smtp_encryption == 'starttls' || $current_smtp_encryption == '' || $current_smtp_encryption == null) { echo 'selected'; } ?>>None</option>
                                        <option value="starttls" <?php if ($current_smtp_encryption == 'starttls') { echo 'selected'; } ?>>STARTTLS</option>
                                        <option value="tls" <?php if ($current_smtp_encryption == 'tls') { echo 'selected'; } ?>>Transport Layer Security (TLS)</option>
                                        <option value="ssl" <?php if ($current_smtp_encryption == 'ssl') { echo 'selected'; } ?>>Secure Sockets Layer (SSL)</option>
                                    </select>
                                </td>
                                <td id="smtp-encryption-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-encryption-default">
                                        <?php 
                                            switch ($default_smtp_encryption) {
                                                case 'none':
                                                case '':
                                                case null:
                                                    echo 'None';
                                                    break;
                                                case 'starttls':
                                                    echo 'STARTTLS';
                                                    break;
                                                case 'tls':
                                                    echo 'Transport Layer Security (TLS)';
                                                    break;
                                                case 'ssl':
                                                    echo 'Secure Sockets Layer (SSL)';
                                                    break;
                                                default:
                                                    echo 'None';
                                            }
                                        ?>
                                    </p>
                                </td>
                            </tr>
                            <tr class="nav-row" id="smtp-username-row" style="margin-top:20px">
                                <td id="smtp-username-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-username">SMTP Username:</p>
                                </td>
                                <td id="smtp-username-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-username" name="smtp-username" value="<?php echo $current_smtp_username; ?>">
                                </td>
                                <td id="smtp-username-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-username-default"><?php echo $default_smtp_username; ?></p>
                                </td>
                            </tr>
                            <tr class="nav-row" id="smtp-password-row" style="margin-top:20px">
                                <td id="smtp-password-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-password">SMTP Password:</p>
                                </td>
                                <td id="smtp-password-input">
                                    <input class="form-control nav-v-c" type="password" style="width: 250px" id="smtp-password" name="smtp-password" value="password">
                                </td>
                                <td id="smtp-password-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-password-default"><or class="green"><?php echo $default_smtp_password; ?></or></p>
                                </td>
                            </tr>
                            <tr class="nav-row" id="smtp-from-email-row" style="margin-top:20px">
                                <td id="smtp-from-email-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-from-email">SMTP From Email:</p>
                                </td>
                                <td id="smtp-from-email-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-from-email" name="smtp-from-email" value="<?php echo $current_smtp_from_email; ?>" required>
                                </td>
                                <td id="smtp-from-email-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-from-email-default"><?php echo $default_smtp_from_email; ?></p>
                                </td>
                            </tr>
                            <tr class="nav-row" id="smtp-from-name-row" style="margin-top:20px">
                                <td id="smtp-from-name-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-from-name">SMTP From Name:</p>
                                </td>
                                <td id="smtp-from-name-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-from-name" name="smtp-from-name" value="<?php echo $current_smtp_from_name; ?>" required>
                                </td>
                                <td id="smtp-from-name-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-from-name-default"><?php echo $default_smtp_from_name; ?></p>
                                </td>
                            </tr>
                            <tr class="nav-row" id="smtp-backup-to-row" style="margin-top:20px">
                                <td id="smtp-backup-to-label" style="width:250px;margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" for="smtp-backup-to">SMTP To Email (Backup):</p>
                                </td>
                                <td id="smtp-backup-to-input">
                                    <input class="form-control nav-v-c" type="text" style="width: 250px" id="smtp-backup-to" name="smtp-backup-to" value="<?php echo $current_smtp_to_email; ?>" required>
                                </td>
                                <td id="smtp-backup-to-default-cell" style="margin-left:25px">
                                    <p style="min-height:max-content;margin:0px" class="nav-v-c align-middle" id="smtp-backup-to-default"><?php echo $default_smtp_to_email; ?></p>
                                </td>
                            </tr>
                            <tr class="nav-row" style="margin-top:20px">
                                <td style="width:250px">
                                    <input id="smtp-submit" type="submit" name="smtp-submit" class="btn btn-success" style="margin-left:25px" value="Save" />
                                </td>
                                <td style="width:250px">
                                    <a id="test-config" name="test-config" class="btn btn-info" style="margin-left:25px;color:white !important" onclick="testSMTP()">Test config</a>
                                    <i id="smtp-success-icon" class="fa-solid fa-check fa-lg" style="color: lime; margin-left:10px; display: none;" ></i>
                                    <i id="smtp-fail-icon" class="fa-solid fa-xmark fa-lg" style="color: red; margin-left:10px; display: none;" ></i>
                                    <i id="smtp-loading-icon" class="fa-solid fa-spinner fa-spin fa-lg" style="color: cyan; margin-left:10px; display: none;" ></i>
                                </td>
                                <td style="margin-left:25px">
                                    <input id="smtp-restore-defaults" type="submit" name="smtp-restore-defaults" class="btn btn-danger" style="margin-left:25px" value="Restore Default" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>

        <div class="container" style="padding-bottom:0px">
            <h3 class="clickable" style="margin-top:50px;font-size:22px" id="notification-settings" onclick="toggleSection(this, 'notification')">Email Notification Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 

            <!-- Notification Settings -->
            <div style="padding-top: 20px" id="notification" hidden>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'notification-settings')) {
                    showResponse();
                }
    
                if ($current_smtp_enabled == 1) {
                    $sql_notif = "SELECT * FROM notifications
                                    WHERE id!=0;"; 
                    $stmt_notif = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_notif, $sql_notif)) {
                        // show error for no connection
                        echo('<p id="notification-output"><or class="red">Error: MYSQL connection issue.</or></p>');
                    } else {
                        mysqli_stmt_execute($stmt_notif);
                        $result_notif = mysqli_stmt_get_result($stmt_notif);
                        $rowCount_notif = $result_notif->num_rows;
                        if ($rowCount_notif != 0) {

                            echo('<p id="notification-output" class="last-edit-T" hidden></p>
                                <table><tbody>');

                            $n = 0;
                            while($row_notif = $result_notif->fetch_assoc()){
                                if ($n == 0) {
                                    echo('<tr>');
                                }

                                $notif_id = $row_notif['id'];
                                $notif_name = $row_notif['name'];
                                $notif_title = $row_notif['title'];
                                $notif_description = $row_notif['description'];
                                $notif_enabled = $row_notif['enabled'];
                                $checked = $row_notif['enabled'] == 1 ? 'checked' : '';

                                if ($n % 4 == 0) {
                                    echo('</tr><tr>');
                                }

                                echo('
                                    <td class="align-middle" style="margin-left:25px;margin-right:10px" id="notif-'.$notif_id.'">
                                        <p style="min-height:max-content;margin:0px" class="align-middle title" title="'.$notif_description.'">'.$notif_title.':</p>
                                    </td>
                                    <td class="align-middle" style="padding-left:5px;padding-right:20px" id="notif-'.$notif_id.'-toggle">
                                        <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px" >
                                            <input type="checkbox" name="'.$notif_name.'" onchange="mailNotification(this, '.$notif_id.')" '.$checked.'>
                                            <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                                        </label>
                                    </td>
                                ');

                                if ($n == $rowCount_notif-1) {
                                    echo('</tr>');
                                }
                                $n++;
                            }
                            echo('</tbody></table>');
                            
                        } else {
                            // show an error for no rows
                            echo('<p id="notification-output"><or class="red">No notifications settings found in table...</or></p>');
                        }
                    }
                } else {
                    echo ('<p class="blue">SMTP is disabled. All email notifications have been disabled.</p>');
                }
                ?>
                <div class="well-nopad theme-divBg" style="margin-top:20px">
                    <?php 
                    $example_body = urlencode("<p style='color:black !important'>Cable stock added, for <strong><a class='link' style='color: #0000EE !important;' href='stock.php?stock_id=1'>Stock Name</a></strong> in <strong>Site 1</strong>, <strong>Store 1</strong>, <strong>Shelf 1</strong>!<br>New stock count: <strong>12</strong>.</p>");
                    ?>
                    <h4>Email example</h4>
                    <input type="hidden" value="<?php echo($example_body); ?>" id="email-template-body" />
                    <div id="email-template" style="margin-top:20px;margin-bottom:10px">
                    </div>
                    <a style="margin-left:5px" href="includes/smtp.inc.php?template=echo&body=<?php echo ($example_body); ?>" target="_blank">View in new tab</a>
                </div>

            </div>
        </div>
        <div style="padding-bottom:0px">
            <div class="container">
                <h3 class="clickable" style="margin-top:50px;font-size:22px" id="changelog-settings" onclick="toggleSection(this, 'changelog')">Changelog <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 
            </div>
            <!-- Changelog -->
            <div class="text-center align-middle" style="margin-left:5vw; margin-right:5vw; padding-top: 20px" id="changelog" hidden>
                <?php
                if ((isset($_GET['section']) && $_GET['section'] == 'changelog')) {
                    showResponse();
                }
                ?>
                <div class="content">
                    <?php 
                    include 'includes/dbh.inc.php';
                    $sql = "SELECT * FROM changelog WHERE table_name NOT LIKE '%login%' ORDER BY id DESC, timestamp DESC LIMIT 10";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        echo("<p class='red'>Error reaching changelog table</p>");
                    } else {
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $rowCount = $result->num_rows;
                        if ($rowCount < 1) {
                            echo("<p>No entries found.</p>");
                        } else {
                            ?>
                            <table id="changelogTable" class="table table-dark theme-table centertable" style="max-width:max-content">
                                <thead>
                                    <tr class="theme-tableOuter">
                                        <th>id</th>
                                        <th style="min-width: 110px;">timestamp</th>
                                        <th>user_id</th>
                                        <th>user_username</th>
                                        <th>action</th>
                                        <th>table_name</th>
                                        <th>record_id</th>
                                        <th>field_name</th>
                                        <th>value_old</th>
                                        <th>value_new</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $result->fetch_assoc()) {
                                        echo('
                                        <tr>
                                            <td>'.$row['id'].'</td>
                                            <td>'.$row['timestamp'].'</td>
                                            <td>'.$row['user_id'].'</td>
                                            <td>'.$row['user_username'].'</td>
                                            <td>'.$row['action'].'</td>
                                            <td>'.$row['table_name'].'</td>
                                            <td>'.$row['record_id'].'</td>
                                            <td>'.$row['field_name'].'</td>
                                            <td>'.$row['value_old'].'</td>
                                            <td>'.$row['value_new'].'</td>
                                        </tr>
                                        ');
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <?php
                        }
                    }
                    ?>
                    <a class="clickable" href="changelog.php">Full Changelog</a>
                </div>
                
            </div>
        </div>

        <div id="modalDivResetPW" class="modal" style="display: none;">
            <span class="close" onclick="modalCloseResetPW()">×</span>
            <div class="container well-nopad theme-divBg" style="padding:25px">
                <div style="margin:auto;text-align:center;margin-top:10px">
                    <form action="includes/admin.inc.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="admin-pwreset-submit" value="set" />
                        <input type="hidden" name="user-id" id="modal-user-id" value=""/>
                        <table class="centertable">
                            <tbody>
                                <tr>
                                    <td class="align-middle" style="padding-right:20px">
                                        New Password:
                                    </td>
                                    <td class="align-middle" style="padding-right:20px">
                                        <input type="password" name="password" id="reset-password" required>
                                    </td>
                                    <td class="align-middle">
                                        <input type="submit" name="submit" class="btn btn-success" value="Change">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Image Div -->
    <div id="modalDiv" class="modal" onclick="modalClose()">
        <span class="close" onclick="modalClose()">&times;</span>
        <img class="modal-content bg-trans" id="modalImg">
        <div id="caption" class="modal-caption"></div>
    </div>
    <!-- End of Modal Image Div -->
    
    
    <script>
    // php reliant scripts

    // scripts for users modifications
    function userRoleChange(id) {
        var select = document.getElementById("user_"+id+"_role_select");
        var selectedValue = select.value;

        $.ajax({
            type: "POST",
            url: "./includes/admin.inc.php",
            data: {
                user_id: id,
                user_new_role: selectedValue,
                user_role_submit: 'yes',
                csrf_token: '<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>'
            },
            dataType: "html",
            success: function(response) {
                console.log(response);
                var tr = document.getElementById('users_table_info_tr');
                var td = document.getElementById('users_table_info_td');
                tr.hidden = false;
                var result = response;
                if (result.startsWith("Error:")) {
                    td.classList.add("red");
                } else {
                    td.classList.add("green");
                }
                td.textContent = result;
            },
            async: true
        });
    }
    function usersEnabledChange(id) {
        var checkbox = document.getElementById("user_"+id+"_enabled_checkbox");
        if (checkbox.checked == true) {
            var checkboxValue = 1;
        } else {
            var checkboxValue = 0;
        }

        $.ajax({
            type: "POST",
            url: "./includes/admin.inc.php",
            data: {
                user_id: id,
                user_new_enabled: checkboxValue,
                user_enabled_submit: 'yes',
                csrf_token: '<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>'
            },
            dataType: "html",
            success: function(response) {
                var tr = document.getElementById('users_table_info_tr');
                var td = document.getElementById('users_table_info_td');
                tr.hidden = false;
                var result = response;
                if (result.startsWith("Error:")) {
                    td.classList.add("red");
                } else {
                    td.classList.add("green");
                }
                td.textContent = result;
            },
            async: true
        });
    }
    </script>
    
    <!-- Add the JS for the file -->
    <script src="assets/js/admin.js"></script>
    
<?php include 'foot.php'; ?>


</body>
