<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// OPTICS INVENTORY PAGE

include 'session.php'; // Session setup and redirect if the session is not active 
// include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Optics</title>
</head>
<body>
    <script>
        // Redirect if the user is not in the admin list in the get-config.inc.php page. - this needs to be after the "include head.php" 
        if (!<?php echo json_encode(in_array($_SESSION['role'], $config_optics_roles_array)); ?>) {
            window.location.href = './login.php';
        }
    </script>
    <!-- Header and Nav -->
    <?php 
        $navHighlight = 'assets'; // for colouring the nav bar link
        $navBtnDim = 1;
        include 'nav.php'; 
    ?>
    <!-- End of Header and Nav -->
    
    <div class="content viewport-content" style="padding-top:80px">
        <div id="selection" class="viewport-selection" style="margin-bottom:15px">
            <?php
            $site = isset($_GET['site']) ? $_GET['site'] : "0";
            $search = isset($_GET['search']) ? $_GET['search'] : "";
            $sort = isset($_GET['sort']) ? $_GET['sort'] : "";
            $deleted = isset($_GET['deleted']) ? $_GET['deleted'] : "";

            // get types from table to create buttons

            $optic_type = isset($_GET['type']) ? $_GET['type'] : 0;
            $optic_speed = isset($_GET['speed']) ? $_GET['speed'] : 0;
            $optic_mode = isset($_GET['mode']) ? $_GET['mode'] : 0;
            $optic_connector = isset($_GET['connector']) ? $_GET['connector'] : 0;
            $optic_distance = isset($_GET['distance']) ? $_GET['distance'] : 0;
            $site = isset($_GET['site']) ? $_GET['site'] : 0;

            if (isset($_GET['rows'])) {
                if ($_GET['rows'] == 50 || $_GET['rows'] == 100) {
                    $rowSelectValue = htmlspecialchars($_GET['rows']);
                } else {
                    $rowSelectValue = 20;
                }
            } else {
                $rowSelectValue = 20;
            }
            echo('<div class="centertable viewport-small" style="max-width:max-content">
                <table class="centertable">
                    <tbody>
                        <tr>
                            <td>Site:</td>
                            <td>Search:</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <select name="site" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter(\'\', \'site\', this.value))">');
                                $sql_site = "SELECT id, name
                                            FROM site
                                            WHERE site.deleted != 1";
                                $stmt_site = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_site, $sql_site)) {
                                    echo('<option value="0" selected>ERROR</option>');
                                } else {
                                    mysqli_stmt_execute($stmt_site);
                                    $result_site = mysqli_stmt_get_result($stmt_site);
                                    $rowCount_site = $result_site->num_rows;
                                    if ($rowCount_site < 1) {
                                        // error
                                        echo('<option value="0" selected>No Sites Found...</option>');
                                    } else {
                                        echo('<option value="0" '); if ($site == '' || $site == 0) { echo('selected'); } echo('>All</option>');
                                        while ($row_site = $result_site->fetch_assoc()) {
                                            $id = $row_site['id'];
                                            $name = $row_site['name'];

                                            echo('<option value="'.$id.'"'); if ($site == $id) { echo('selected'); } echo('>'.$name.'</option>');
                                        }
                                    }
                                }
                            echo('</select>
                            </td>
                            <td>
                                <form action="" method="GET" style="display:inline-block">
                                    <span style="">
                                        <input type="text" id="search" name="search" placeholder="Search" class="form-control" style="display:inline !important; width:100px;padding-right:0px"'); if (isset($_GET['search'])) { echo('value="'.$_GET['search'].'"');} echo('>
                                        <button id="search-submit" class="btn btn-info" style="vertical-align:middle;margin-top: 0px !important;padding: 5px 6px 5px 6px !important;opacity:80%;color:black;" type="submit">
                                            <i class="fa fa-search" style="padding-top:4px"></i>
                                        </button>
                                    </span>
                                </form>
                            </td>
                            <td>
                                <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black" onclick="navPage(\'optics.php\')">
                                    <i class="fa fa-ban fa-rotate-90" style="padding-top:4px"></i>
                                </button>
                            </td>
                            <td>
                                <button id="add-optic-small" class="btn btn-success nav-v-b" style="opacity:80%;color:white;padding:6px 2px 5px 2px" onclick="toggleAddDivSmall()" '); if (isset($_GET['add-form']) && $_GET['add-form'] == 1) { echo ('hidden'); } else { } echo('>
                                    <i class="fa fa-plus" style="padding-top:4px"></i> Add
                                </button>
                                <button id="add-optic-hide-small" class="btn btn-danger nav-v-b viewport-small" style="opacity:80%;color:black;padding:6px 2px 5px 2px" onclick="toggleAddDivSmall()" '); if (isset($_GET['add-form']) && $_GET['add-form'] == 1) { } else { echo ('hidden'); } echo('>
                                    Hide Add
                                </button>
                            </td>
                                
                            <td>
                                <button id="show-deleted-optics-small" class="btn btn-success nav-v-b" style="padding:6px 2px 5px 2px;opacity:80%;color:white" onclick="navPage(updateQueryParameter(\'\', \'deleted\', 1))" '); if((isset($deleted) && $deleted == 1)) { echo('hidden'); } echo('>
                                    Deleted
                                </button>
                                <button id="hide-deleted-optics-small" class="btn btn-danger nav-v-b viewport-small" style="padding:6px 2px 5px 2px;opacity:80%;color:black" onclick="navPage(updateQueryParameter(\'\', \'deleted\', 0))" '); if(isset($deleted) && ($deleted == 0 || $deleted == '')) { echo('hidden'); } echo('>
                                    Deleted
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            ');
            echo('<div class="row centertable viewport-large" style="max-width:max-content">
                    <div class="col align-middle viewport-large" style="max-width:max-content">
                        <label class="align-middle" style="padding-right:15px;padding-top:7px">Site:</label>
                        <select name="site" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter(\'\', \'site\', this.value))">');
                        $sql_site = "SELECT id, name
                                    FROM site
                                    WHERE site.deleted != 1";
                        $stmt_site = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt_site, $sql_site)) {
                            echo('<option value="0" selected>ERROR</option>');
                        } else {
                            mysqli_stmt_execute($stmt_site);
                            $result_site = mysqli_stmt_get_result($stmt_site);
                            $rowCount_site = $result_site->num_rows;
                            if ($rowCount_site < 1) {
                                // error
                                echo('<option value="0" selected>No Sites Found...</option>');
                            } else {
                                echo('<option value="0" '); if ($site == '' || $site == 0) { echo('selected'); } echo('>All</option>');
                                while ($row_site = $result_site->fetch_assoc()) {
                                    $id = $row_site['id'];
                                    $name = $row_site['name'];

                                    echo('<option value="'.$id.'"'); if ($site == $id) { echo('selected'); } echo('>'.$name.'</option>');
                                }
                            }
                        }
                    echo('</select>
                    </div>');
                echo('<div class="col align-middle" style="display:inline-block;max-width:max-content">
                        <form action="" method="GET" style="display:inline-block">
                            <label class="align-middle" style="padding-top:7px;padding-right:15px;">Search:</label>
                            <span style="">
                                <input type="text" id="search" name="search" placeholder="Search" class="form-control" style="display:inline !important; width:200px;padding-right:0px"'); if (isset($_GET['search'])) { echo('value="'.$_GET['search'].'"');} echo('>
                                <button id="search-submit" class="btn btn-info" style="margin-top:-3px;vertical-align:middle;padding: 6px 6px 6px 6px;opacity:80%;color:black" type="submit">
                                    <i class="fa fa-search" style="padding-top:4px"></i>
                                </button>
                            </span>
                        </form>
                    </div>');
                echo('<div class="col align-middle" style="max-width:max-content">
                        <button id="clear-filters" class="btn btn-warning nav-v-b" style="opacity:80%;color:black" onclick="navPage(\'optics.php\')">
                            <i class="fa fa-ban fa-rotate-90" style="padding-top:4px"></i>
                        </button>
                    </div>');
                echo('<div class="col align-middle" style="max-width:max-content">
                        <button id="add-optic" class="btn btn-success nav-v-b" style="opacity:80%;color:white" onclick="toggleAddDiv()" '); if (isset($_GET['add-form']) && $_GET['add-form'] == 1) { echo ('hidden'); } else { } echo('>
                            <i class="fa fa-plus" style="padding-top:4px"></i> Add Optic
                        </button>
                        <button id="add-optic-hide" class="btn btn-danger nav-v-b" style="opacity:80%;color:black" onclick="toggleAddDiv()" '); if (isset($_GET['add-form']) && $_GET['add-form'] == 1) { } else { echo ('hidden'); } echo('>
                            Hide Add Optic
                        </button>
                    </div>');
                echo('<div class="col align-middle" style="max-width:max-content">
                        <button id="show-deleted-optics" class="btn btn-success nav-v-b" style="opacity:80%;color:white" onclick="navPage(updateQueryParameter(\'\', \'deleted\', 1))" '); if((isset($deleted) && $deleted == 1)) { echo('hidden'); } echo('>
                            View Deleted
                        </button>
                        <button id="hide-deleted-optics" class="btn btn-danger nav-v-b" style="opacity:80%;color:black" onclick="navPage(updateQueryParameter(\'\', \'deleted\', 0))" '); if(isset($deleted) && ($deleted == 0 || $deleted == '')) { echo('hidden'); } echo('>
                            Hide Deleted
                        </button>
                    </div>');
                echo('</div>
                    <div class="row centertable" style="max-width:max-content; margin-top:10px">');
                    $sql_type = "SELECT id, name FROM optic_type WHERE deleted=0";
                    $stmt_type = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_type, $sql_type)) {
                        echo("ERROR no optic types found");
                    } else {
                        mysqli_stmt_execute($stmt_type);
                        $result_type = mysqli_stmt_get_result($stmt_type);
                        $rowCount_type = $result_type->num_rows;
                        echo ('<div class="col align-middle" style="max-width:max-content">');
                            echo('<label class="align-middle" style="padding-right:15px;padding-top:7px">Type:</label>');
                            echo ('<select name="type" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter(\'\', \'type\', this.value))">');
                                echo ('<option value="0"'); if ($optic_type == 0 || $optic_type == '') { echo(' selected'); } echo('>All</option>');
                        if ($rowCount_site > 0) {
                            while ($row_type = $result_type->fetch_assoc()) {
                                $type_id = $row_type['id'];
                                $type_name = $row_type['name'];
                                echo ('<option value="'.$type_id.'"'); if ($type_id == $optic_type) { echo(' selected'); } echo('>'.$type_name.'</option>');
                            }
                        }
                            echo('</select>');
                        echo('</div>');
                    }
            
                    $sql_speed = "SELECT id, name FROM optic_speed";
                    $stmt_speed = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_speed, $sql_speed)) {
                        echo("ERROR no optic speeds found");
                    } else {
                        mysqli_stmt_execute($stmt_speed);
                        $result_speed = mysqli_stmt_get_result($stmt_speed);
                        $rowCount_speed = $result_speed->num_rows;
                        echo ('<div class="col align-middle" style="max-width:max-content">');
                            echo('<label class="align-middle" style="padding-right:15px;padding-top:7px">Speed:</label>');
                            echo ('<select name="speed" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter(\'\', \'speed\', this.value))">');
                                echo ('<option value="0"'); if ($optic_speed == 0 || $optic_speed == '') { echo(' selected'); } echo('>All</option>');
                        if ($rowCount_site > 0) {
                            while ($row_speed = $result_speed->fetch_assoc()) {
                                $speed_id = $row_speed['id'];
                                $speed_name = $row_speed['name'];
                                echo ('<option value="'.$speed_id.'"'); if ($speed_id == $optic_speed) { echo(' selected'); } echo('>'.$speed_name.'</option>');
                            }
                        }
                            echo('</select>');
                        echo('</div>');
                    }

                        echo ('<div class="col align-middle" style="max-width:max-content">');
                            echo('<label class="align-middle" style="padding-right:15px;padding-top:7px">Mode:</label>');
                            echo ('<select name="mode" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter(\'\', \'mode\', this.value))">');
                                echo ('<option value="0"'); if ($optic_mode == 0 || $optic_mode == '') { echo(' selected'); } echo('>All</option>');
                                echo ('<option value="SM"'); if ($optic_mode == 'SM') { echo(' selected'); } echo('>SM</option>');
                                echo ('<option value="MM"'); if ($optic_mode == 'MM') { echo(' selected'); } echo('>MM</option>');
                                echo ('<option value="Copper"'); if ($optic_mode == 'Copper') { echo(' selected'); } echo('>Copper</option>');
                                echo ('<option value="N/A"'); if ($optic_mode == 'N/A') { echo(' selected'); } echo('>N/A</option>');
                            echo('</select>');
                        echo('</div>');

                    $sql_connector = "SELECT id, name FROM optic_connector WHERE deleted=0";
                    $stmt_connector = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_connector, $sql_connector)) {
                        echo("ERROR no optic connectors found");
                    } else {
                        mysqli_stmt_execute($stmt_connector);
                        $result_connector = mysqli_stmt_get_result($stmt_connector);
                        $rowCount_connector = $result_connector->num_rows;
                        echo ('<div class="col align-middle" style="max-width:max-content">');
                            echo('<label class="align-middle" style="padding-right:15px;padding-top:7px">Connector:</label>');
                            echo ('<select name="connector" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter(\'\', \'connector\', this.value))">');
                                echo ('<option value="0"'); if ($optic_connector == 0 || $optic_connector == '') { echo(' selected'); } echo('>All</option>');
                        if ($rowCount_site > 0) {
                            while ($row_connector = $result_connector->fetch_assoc()) {
                                $connector_id = $row_connector['id'];
                                $connector_name = $row_connector['name'];
                                echo ('<option value="'.$connector_id.'"'); if ($connector_id == $optic_connector) { echo(' selected'); } echo('>'.$connector_name.'</option>');
                            }
                        }
                            echo('</select>');
                        echo('</div>');
                    }
                    
                    $sql_distance = "SELECT id, name FROM optic_distance WHERE deleted=0";
                    $stmt_distance = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_distance, $sql_distance)) {
                        echo("ERROR no optic distances found");
                    } else {
                        mysqli_stmt_execute($stmt_distance);
                        $result_distance = mysqli_stmt_get_result($stmt_distance);
                        $rowCount_distance = $result_distance->num_rows;
                        echo ('<div class="col align-middle" style="max-width:max-content">');
                            echo('<label class="align-middle" style="padding-right:15px;padding-top:7px">Distance:</label>');
                            echo ('<select name="distance" class="form-control" style="display:inline !important; max-width:max-content" onchange="navPage(updateQueryParameter(\'\', \'distance\', this.value))">');
                                echo ('<option value="0"'); if ($optic_distance == 0 || $optic_distance == '') { echo(' selected'); } echo('>All</option>');
                        if ($rowCount_site > 0) {
                            while ($row_distance = $result_distance->fetch_assoc()) {
                                $distance_id = $row_distance['id'];
                                $distance_name = $row_distance['name'];
                                echo ('<option value="'.$distance_id.'"'); if ($distance_id == $optic_distance) { echo(' selected'); } echo('>'.$distance_name.'</option>');
                            }
                        }
                            echo('</select>');
                        echo('</div>');
                    }
                
            echo('</div>');
            
            ?>
        </div>

        <!-- Add optic form section -->
        <?php 
        // url example for return info: https://inventory-dev.ajrich.co.uk/optics.php?add-form=1form-site=1&form-type=1&form-speed=4&form-distance=1&form-connector=2&form-mode=SM&form-vendor=1&form-model=TTYTRED
        echo('
        <div class="container" id="add-optic-section" style="margin-bottom:20px" '); if (isset($_GET['add-form']) && $_GET['add-form'] == 1) { } else { echo ('hidden'); } echo('>
            <div class="well-nopad theme-divBg text-center">
                <h3 style="font-size:22px">Add new optic</h3>
                <hr style="border-color:#9f9d9d; margin-left:10px">
                <p id="optic-add-response" hidden></p>
                <form id="add-optic-form" action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                    <!-- Include CSRF token in the form -->
                    <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                    <div class="row" style="margin-right:25px;margin-top:5px">
                        <div class="col">
                            <div>Serial Number</div>
                            <div><input class="form-control text-center" type="text" id="serial" name="serial" style="min-width:120px" placeholder="Serial" oninput="searchSerial(this.value)"required/></div>
                        </div>
                        <div class="col">
                            <div>Model</div>
                            <div>
                                <input class="form-control text-center" id="model" type="text" list="names" name="model" placeholder="Model" style="min-width:120px" '); if (isset($_GET['form-model'])) { echo ('value="'.$_GET['form-model'].'"'); } echo (' required/>
                                <datalist id="names">');
                                    $sql_model = "SELECT DISTINCT model from optic_item WHERE deleted=0 AND quantity=1 ORDER BY model";
                                    $stmt_model = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_model, $sql_model)) {
                                        echo("ERROR getting entries");
                                    } else {
                                        mysqli_stmt_execute($stmt_model);
                                        $result_model = mysqli_stmt_get_result($stmt_model);
                                        $rowCount_model = $result_model->num_rows;
                                        if ($rowCount_model < 1) {
                                        } else {
                                            while( $row_model = $result_model->fetch_assoc() ) {
                                                echo("<option>".$row_model['model']."</option>");
                                            }
                                        }
                                    }
                                echo('
                                </datalist>
                            </div>
                        </div>
                        <div class="col">
                            <div>Spectrum</div>
                            <div><input class="form-control text-center" type="text" id="spectrum" name="spectrum" style="min-width:120px" placeholder="1310nm" '); if (isset($_GET['form-spectrum'])) { echo ('value="'.$_GET['form-spectrum'].'"'); } echo ('required/></div>
                        </div>
                        <div class="col">
                            <div>Vendor</div>
                            <div>
                                <select id="vendor" name="vendor" class="form-control text-center" style="border-color:black;" required>');

                                    $sql_vendor = "SELECT * FROM optic_vendor WHERE deleted = 0 ORDER BY name";
                                    $stmt_vendor = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_vendor, $sql_vendor)) {
                                        echo("ERROR getting entries");
                                    } else {
                                        mysqli_stmt_execute($stmt_vendor);
                                        $result_vendor = mysqli_stmt_get_result($stmt_vendor);
                                        $rowCount_vendor = $result_vendor->num_rows;
                                        if ($rowCount_vendor < 1) {
                                            echo ("<option selected disabled>No Vendors Found</option> ");
                                        } else {
                                            echo ("<option selected disabled>Select Vendor</option>");
                                            while( $row_vendor = $result_vendor->fetch_assoc() ) {
                                                echo("<option value='".$row_vendor['id']."'"); if (isset($_GET['form-vendor']) && $_GET['form-vendor'] == $row_vendor['id']) { echo ('selected'); } echo (">".$row_vendor['name']."</option>");
                                            }
                                        }
                                    }   

                                echo('      
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewVendor()">Add New</a>
                            </div>
                        </div>
                        <div class="col">
                            <div>Type</div>
                            <div>
                                <select id="type" name="type" class="form-control text-center" style="border-color:black;"  required>');
                                
                                    $sql_type = "SELECT * FROM optic_type WHERE deleted=0 ORDER BY name";
                                    $stmt_type = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_type, $sql_type)) {
                                        echo("ERROR getting entries");
                                    } else {
                                        mysqli_stmt_execute($stmt_type);
                                        $result_type = mysqli_stmt_get_result($stmt_type);
                                        $rowCount_type = $result_type->num_rows;
                                        if ($rowCount_type < 1) {
                                            echo ("<option selected disabled>No Types Found</option> ");
                                        } else {
                                            echo ('<option value="" selected disabled hidden>Select Type</option>');
                                            while( $row_type = $result_type->fetch_assoc() ) {
                                                echo("<option value='".$row_type['id']."'"); if (isset($_GET['form-type']) && $_GET['form-type'] == $row_type['id']) { echo ('selected'); } echo (">".$row_type['name']."</option>");
                                            }
                                        }
                                    }   

                                echo(' 
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewType()">Add New</a>
                            </div>
                        </div>
                    </div> 
                
                    <div class="row" style="margin-right:25px">
                        <div class="col">
                            <div>Speed</div>
                            <div>
                                <select id="speed" name="speed" class="form-control text-center" style="border-color:black;"  required>');
                                
                                    $sql_speed = "SELECT * FROM optic_speed WHERE deleted = 0 
                                                    ORDER BY  
                                                        CASE 
                                                            WHEN name LIKE '%M' THEN 0  -- First sort by 'M'
                                                            ELSE 1  -- Then 'G'
                                                        END,
                                                        CASE 
                                                            WHEN name LIKE '%M' THEN CAST(SUBSTRING(name, 1, LENGTH(name) - 1) AS SIGNED)
                                                            WHEN name LIKE '%G' THEN CAST(SUBSTRING(name, 1, LENGTH(name) - 1) AS SIGNED) * 1000
                                                            ELSE 0
                                                        END; "; // this orders the items based on M and G, so it will be 100M, 200M, 1G, 2.5G, 100G etc
                                    $stmt_speed = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_speed, $sql_speed)) {
                                        echo("ERROR getting entries");
                                    } else {
                                        mysqli_stmt_execute($stmt_speed);
                                        $result_speed = mysqli_stmt_get_result($stmt_speed);
                                        $rowCount_speed = $result_speed->num_rows;
                                        if ($rowCount_speed < 1) {
                                            echo ("<option selected disabled>No Speeds Found</option> ");
                                        } else {
                                            echo ('<option value="" selected disabled hidden>Select Speed</option>');
                                            while( $row_speed = $result_speed->fetch_assoc() ) {
                                                echo("<option value='".$row_speed['id']."'"); if (isset($_GET['form-speed']) && $_GET['form-speed'] == $row_speed['id']) { echo ('selected'); } echo (">".$row_speed['name']."</option>");
                                            }
                                        }
                                    }   

                                echo(' 
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewSpeed()">Add New</a>
                            </div>
                        </div>
                        <div class="col">
                            <div>Connector</div>
                            <div>
                                <select id="connector" name="connector" class="form-control text-center" style="border-color:black;"  required>');
                                
                                $sql_connector = "SELECT * FROM optic_connector WHERE deleted=0 ORDER BY name";
                                $stmt_connector = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_connector, $sql_connector)) {
                                    echo("ERROR getting entries");
                                } else {
                                    mysqli_stmt_execute($stmt_connector);
                                    $result_connector = mysqli_stmt_get_result($stmt_connector);
                                    $rowCount_connector = $result_connector->num_rows;
                                    if ($rowCount_connector < 1) {
                                        echo ("<option selected disabled>No Connectors Found</option> ");
                                    } else {
                                        echo ('<option value="" selected disabled hidden>Select Connector</option>');
                                        while( $row_connector = $result_connector->fetch_assoc() ) {
                                            echo("<option value='".$row_connector['id']."'"); if (isset($_GET['form-connector']) && $_GET['form-connector'] == $row_connector['id']) { echo ('selected'); } echo (">".$row_connector['name']."</option>");
                                        }
                                    }
                                }   

                            echo(' 
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewConnector()">Add New</a>
                            </div>
                        </div>
                        <div class="col">
                            <div>Distance</div>
                            <div>
                                <select id="distance" name="distance" class="form-control text-center" style="border-color:black;"  required>');
                                
                                $sql_distance = "SELECT * FROM optic_distance WHERE deleted=0 ORDER BY name";
                                $stmt_distance = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_distance, $sql_distance)) {
                                    echo("ERROR getting entries");
                                } else {
                                    mysqli_stmt_execute($stmt_distance);
                                    $result_distance = mysqli_stmt_get_result($stmt_distance);
                                    $rowCount_distance = $result_distance->num_rows;
                                    if ($rowCount_distance < 1) {
                                        echo ("<option selected disabled>No Distances Found</option> ");
                                    } else {
                                        echo ('<option value="" selected disabled hidden>Select Distance</option>');
                                        while( $row_distance = $result_distance->fetch_assoc() ) {
                                            echo("<option value='".$row_distance['id']."'"); if (isset($_GET['form-distance']) && $_GET['form-distance'] == $row_distance['id']) { echo ('selected'); } echo (">".$row_distance['name']."</option>");
                                        }
                                    }
                                }   

                            echo(' 
                                </select>
                            </div>
                            <div class="text-center">
                                <label class="gold clickable" style="margin-top:5px;font-size:14px" onclick="modalLoadNewDistance()">Add New</a>
                            </div>
                        </div>
                        <div class="col">
                            <div>Mode</div>
                            <div>
                                <select id="mode" name="mode" class="form-control text-center" style="border-color:black;"  required>
                                    <option value="" selected disabled hidden>Select Mode</option>
                                    <option value="SM"'); if (isset($_GET['form-mode']) && $_GET['form-mode'] == 'SM') { echo ('selected'); } echo ('>Single-Mode (SM)</option>
                                    <option value="MM"'); if (isset($_GET['form-mode']) && $_GET['form-mode'] == 'MM') { echo ('selected'); } echo ('>Multi-Mode (MM)</option>
                                    <option value="Copper"'); if (isset($_GET['form-mode']) && $_GET['form-mode'] == 'Copper') { echo ('selected'); } echo ('>Copper</option>
                                    <option value="N/A"'); if (isset($_GET['form-mode']) && $_GET['form-mode'] == 'N/A') { echo ('selected'); } echo ('>N/A</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div>Site</div>
                            <div>
                                <select id="site" name="site" class="form-control text-center" style="border-color:black;" required>');

                                    $sql_site_cable = "SELECT * FROM site";
                                    $stmt_site_cable = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_site_cable, $sql_site_cable)) {
                                        echo("ERROR getting entries");
                                    } else {
                                        mysqli_stmt_execute($stmt_site_cable);
                                        $result_site_cable = mysqli_stmt_get_result($stmt_site_cable);
                                        $rowCount_site_cable = $result_site_cable->num_rows;
                                        if ($rowCount_site_cable < 1) {
                                            echo ("<option selected disabled>No Sites Found</option> ");
                                        } else {
                                            echo ("<option selected disabled>Select Site</option>");
                                            while( $row_site_cable = $result_site_cable->fetch_assoc() ) {
                                                echo("<option value='".$row_site_cable['id']."'"); if (isset($_GET['form-site']) && $_GET['form-site'] == $row_site_cable['id']) { echo ('selected'); } echo (">".$row_site_cable['name']."</option>");
                                            }
                                        }
                                    }   

                                echo('      
                                </select>
                            </div>
                        </div> 
                    </div> 
                    <div class="row align-middle" style="margin-right:25px">
                        <div class="col-sm" style="margin-top:10px"> 
                        </div>
                        <div class="col" style="margin-top:10px">
                            <button id="optic-add-single" class="btn btn-success align-bottom" type="submit" name="add-optic-submit" style="" value="1">Add</button>
                        </div>
                        <div class="col-sm text-right" style="margin-top:10px">
                            <a href="optic-import.php" class="link" style="font-size:12px; padding-bottom:10px">Import from CSV</a>
                        </div>
                    </div>        
                </form>
            </div>
        </div>
        ');
        ?>

        <div class="container">

            <!-- Get Inventory -->
            <?php
            $order = " ORDER BY T.id, V.name, D.name, C.id, I.model, I.serial_number";
            switch ($sort) {
                case 'type':
                    $order = " ORDER BY T.name, V.name, D.name, C.id, I.model, I.serial_number";
                    break;
                case 'connector':
                    $order = " ORDER BY C.name, T.name, V.name, I.model, I.serial_number";
                    break;
                case 'distance':
                    $order = " ORDER BY D.name, T.name, C.id, V.name, I.model, I.serial_number";
                    break;
                case 'model':
                    $order = " ORDER BY I.model, T.name, V.name, D.name, C.id, I.serial_number";
                    break;
                case 'speed':
                    $order = " ORDER BY S.id, T.name, V.name, D.name, C.id, I.model, I.serial_number";
                    break;
                case 'mode':
                    $order = " ORDER BY I.mode, T.name, V.name, D.name, C.id, I.model, I.serial_number";
                    break;
                case 'serial':
                    $order = " ORDER BY I.serial_number, T.name, V.name, D.name, C.id, I.model";
                    break;
                case 'vendor':
                    $order = " ORDER BY V.name, T.name, D.name, C.id, I.model, I.serial_number";
                    break;
                default:
                    $order = " ORDER BY T.id, V.name, D.name, C.id, I.model, I.serial_number";
                    break;
            }

            if (isset($deleted)) { 
                if ($deleted == 0 || $deleted == '') {
                    $sql_where = "WHERE I.deleted=0";
                } else {
                    $sql_where = "WHERE I.deleted=1";
                }
            }

            include 'includes/dbh.inc.php';
            $s = 0;
            $sql_inv_count = "SELECT I.id AS i_id, I.model AS i_model, I.serial_number AS i_serial_number, I.mode AS i_mode, I.quantity AS i_quantity, I.spectrum as i_spectrum,
                            V.id AS v_id, V.name AS v_name, 
                            T.id AS t_id, T.name AS t_name, 
                            C.id AS c_id, C.name AS c_name,
                            D.id AS d_id, D.name AS d_name,
                            S.id AS s_id, S.name AS s_name,
                            (SELECT count(id) AS count
                                FROM optic_comment
                                WHERE optic_comment.item_id = I.id AND optic_comment.deleted=0) AS comments,
                            site.id AS site_id, site.name AS site_name 
                        FROM optic_item AS I 
                        INNER JOIN optic_vendor AS V on I.vendor_id = V.id 
                        INNER JOIN optic_type AS T ON I.type_id=T.id 
                        INNER JOIN optic_connector AS C ON I.connector_id=C.id 
                        INNER JOIN optic_speed AS S ON I.speed_id=S.id
                        INNER JOIN optic_distance AS D on I.distance_id=D.id
                        INNER JOIN site ON I.site_id=site.id ";
            $sql_inv_count .= $sql_where;
            $sql_inv_add = '';
            if ((int)$site !== 0) { 
                $sql_inv_add  .= " AND site.id=$site"; 
            } 
            if ((int)$optic_type !== 0 ) { 
                if (is_numeric($optic_type)) {
                    $sql_inv_add  .= " AND T.id=$optic_type";
                }
            } 
            if ((int)$optic_speed !== 0 ) { 
                if (is_numeric($optic_speed)) {
                    $sql_inv_add  .= " AND S.id=$optic_speed";
                }
            } 
            if ($optic_mode !== 0 && $optic_mode !== '0') { 
                $sql_inv_add  .= " AND I.mode='$optic_mode'";
            } 
            if ((int)$optic_connector !== 0 ) { 
                if (is_numeric($optic_connector)) {
                    $sql_inv_add  .= " AND C.id=$optic_connector";
                }
            } 
            if ((int)$optic_distance !== 0 ) { 
                if (is_numeric($optic_distance)) {
                    $sql_inv_add  .= " AND D.id=$optic_distance";
                }
            } 
            
            if ($search !== '') { 
                $search = mysqli_real_escape_string($conn, $search); // escape the special characters
                $sql_inv_add  .= " 
                            AND (I.serial_number LIKE '%$search%' 
                                OR I.model LIKE '%$search%' 
                                OR I.spectrum LIKE '%$search%' 
                                OR V.name LIKE '%$search%' 
                                OR T.name LIKE '%$search%' 
                                OR C.name LIKE '%$search%'
                                OR D.name LIKE '%$search%'
                                OR I.mode LIKE '%$search%' 
                                OR S.name LIKE '%$search%'
                                )
                        ";
            }
            $sql_inv_count .= $sql_inv_add;
            $sql_inv_count .= $order;

            $stmt_inv_count = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_inv_count, $sql_inv_count)) {
                echo("ERROR getting entries");
            } else {
                mysqli_stmt_execute($stmt_inv_count);
                $result_inv_count = mysqli_stmt_get_result($stmt_inv_count);
                $totalRowCount = $result_inv_count->num_rows;
                
                // Pagination settings
                $results_per_page = $rowSelectValue; // Number of rows per page - based no the querystring (or 10 by default)
                $total_pages = ceil($totalRowCount / $results_per_page);

                $current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                if ($current_page < 1) {
                    $current_page = 1;
                } elseif ($current_page > $total_pages) {
                    $current_page = $total_pages;
                } 

                // Calculate the offset for the query
                $offset = ($current_page - 1) * $results_per_page;
                if ($offset < 0) {
                    $offset = $results_per_page;
                }

                $sql_inv_pagination = " LIMIT $results_per_page OFFSET $offset;";

                $sql_inv = $sql_inv_count .= $sql_inv_pagination;

                echo '<pre hidden>'.$sql_inv.'</pre>';
                $stmt_inv = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_inv, $sql_inv)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_execute($stmt_inv);
                    $result_inv = mysqli_stmt_get_result($stmt_inv);
                    $rowCount_inv = $result_inv->num_rows;

                    $rowdump = []; // testing

                    echo('
                        <div class="container">
                            <hr class="viewport-hr" style="border-color:#9f9d9d; margin-left:10px">
                            <div class="row centertable">
                                <div class="col-3 float-left viewport-font" >
                                    Count: <or class="green">'.$totalRowCount.'</or>
                                </div>
                                <div class="col">');
                                if (isset($_GET['error'])) { echo('<p class="red">Error: '.htmlspecialchars($_GET['error']).'</p>'); } 
                                if (isset($_GET['success'])) { echo('<p class="green">Success: '.htmlspecialchars($_GET['success']).'</p>'); } 
                                echo('</div>
                                <div class="col align-middle viewport-padding-0-lr" style="max-width:max-content;white-space: nowrap;padding-bottom:10px">
                                    <table class="viewport-font viewport-table">
                                        <tr class="align-middle">
                                            <td class="align-middle" style="padding-right:10px">
                                                Sort By:
                                            </td>
                                            <td class="align-middle">
                                                <select name="sort" class="form-control row-dropdown viewport-width-50" style="width:max-content;height:25px; padding:0px" onchange="navPage(updateQueryParameter(\'\', \'sort\', this.value))">
                                                    <option value="type"'); if ($sort == "type" || $sort == '') { echo(' selected'); } echo('>Type</option>
                                                    <option value="connector"'); if ($sort == "connector") { echo(' selected'); } echo('>Connector</option>
                                                    <option value="distance"'); if ($sort == "distance") { echo(' selected'); } echo('>Distance</option>
                                                    <option value="model"'); if ($sort == "model") { echo(' selected'); } echo('>Model</option>
                                                    <option value="speed"'); if ($sort == "speed") { echo(' selected'); } echo('>Speed</option>
                                                    <option value="mode"'); if ($sort == "mode") { echo(' selected'); } echo('>Mode</option>
                                                    <option value="serial"'); if ($sort == "serial") { echo(' selected'); } echo('>Serial</option>
                                                    <option value="vendor"'); if ($sort == "vendor") { echo(' selected'); } echo('>Vendor</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="optics-table" class="text-center" style="max-width:max-content; margin:auto">
                        <table class="table table-dark theme-table centertable viewport-font" style="max-width:max-content;padding-bottom:0px;margin-bottom:0px;">
                            <thead>
                                <tr class="align-middle text-center theme-tableOuter viewport-large-empty">
                                    <th hidden>ID</th>
                                    <th>Type</th>
                                    <th>Connector</th>
                                    <th>Model</th>
                                    <th>Speed</th>
                                    <th>Mode</th>
                                    <th>Spectrum</th>
                                    <th>Distance</th>
                                    <th>Serial</th>
                                    <th>Vendor</th>
                                    <th'); if ((int)$site !== 0) { echo(' hidden'); } echo('>Site</th>
                                    <th>Comments</th>
                                    <th hidden>Quantity</th>
                                    <th colspan=2></th>
                                <tr>
                                <tr class="align-middle text-center theme-tableOuter viewport-small-empty">
                                    <th hidden>ID</th>
                                    <th>Type</th>
                                    <th>Conn.</th>
                                    <th>Model</th>
                                    <th>Speed</th>
                                    <th>Mode</th>
                                    <th>Spect.</th>
                                    <th>Dist.</th>
                                    <th>S/N</th>
                                    <th>Vendor</th>
                                    <th'); if ((int)$site !== 0) { echo(' hidden'); } echo('>Site</th>
                                    <th>Comm.</th>
                                    <th hidden>Quantity</th>
                                    <th colspan=2></th>
                                <tr>
                            </thead>
                            <tbody>
                    ');
                    if ($rowCount_inv == 0) {
                        echo('<tr><td colspan=100% class="text-center align-middle">No Optics Found.</td></td>');
                    } else {

                        while ($row_inv = $result_inv->fetch_assoc()) {

                            $rowdump[] = $row_inv; // testing

                            $i_id = $row_inv['i_id'];
                            $i_model = $row_inv['i_model'];
                            $i_serial_number = $row_inv['i_serial_number'];
                            $i_model = $row_inv['i_model'];
                            $i_mode = $row_inv['i_mode'];
                            $i_spectrum = $row_inv['i_spectrum'];
                            $i_quantity = $row_inv['i_quantity'];
                            $v_id = $row_inv['v_id'];
                            $v_name = $row_inv['v_name'];
                            $t_id = $row_inv['t_id'];
                            $t_name = $row_inv['t_name'];
                            $c_id = $row_inv['c_id'];
                            $c_name = $row_inv['c_name'];
                            $d_id = $row_inv['d_id'];
                            $d_name = $row_inv['d_name'];
                            $s_id = $row_inv['s_id'];
                            $s_name = $row_inv['s_name'];
                            $i_comments = mysqli_real_escape_string($conn, $row_inv['comments']);
                            $site_id = $row_inv['site_id'];
                            $site_name = $row_inv['site_name'];

                            echo('
                                <tr id="item-'.$i_id.'" class="row-show align-middle text-center'); if ($deleted == 1) { echo(' red'); } echo('">
                                    <form id="opticForm-'.$i_id.'"action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                                        <!-- Include CSRF token in the form -->
                                        <input type="hidden" form="opticForm-'.$i_id.'" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                        <input type="hidden" form="opticForm-'.$i_id.'" value="'.$i_id.'" name="id"/>
                                    </form>
                                    <td class="align-middle" hidden>'.$i_id.'</td>
                                    <td class="align-middle">'.$t_name.'</td>
                                    <td class="align-middle">'.$c_name.'</td>
                                    <td class="align-middle">'.$i_model.'</td>
                                    <td class="align-middle">'.$s_name.'</td>
                                    <td class="align-middle">'.$i_mode.'</td>
                                    <td class="align-middle">'.$i_spectrum.'</td>
                                    <td class="align-middle">'.$d_name.'</td>
                                    <td class="align-middle" id="optic-serial-'.$i_id.'">'.$i_serial_number.'</td>
                                    <td class="align-middle">'.$v_name.'</td>
                                    <td class="align-middle link gold" style="white-space: nowrap !important;" onclick="navPage(updateQueryParameter(\'\', \'site\', \''.$site_id.'\'))"'); if ((int)$site !== 0) { echo(' hidden'); } echo('>'.$site_name.'</td>
                                    <td hidden class="align-middle text-right'); if ((int)$i_comments > 0) { echo(' clickable gold link" onclick="toggleAddComment(\''.$i_id.'\', 1)"'); } else { echo('" style="color:#8f8f8f"'); } echo('>'.$i_comments.'</or></td>
                                    <td hidden class="align-middle text-left"><button class="btn btn-success" type="button" style="padding: 2px 4px 2px 4px" onclick="toggleAddComment(\''.$i_id.'\', '); if ((int)$i_comments > 0) { echo('1'); } else { echo('0'); } echo(')"><i class="fa fa-plus"></i></button></td>
                                    <td class="align-middle">
                                        <div style="position: relative; display: inline-block;">
                                        <i class="'); if ((int)$i_comments > 0) { echo('fa-solid fa-message clickable gold" style="font-size:20; padding:5px"'); } else { echo('fa-regular fa-message clickable gold" style="font-size:18px; padding:5px"'); } echo(' onclick="toggleAddComment(\''.$i_id.'\', '); if ((int)$i_comments > 0) { echo('1'); } else { echo('0'); } echo(')"></i>');
                                            if ((int)$i_comments > 0) { 
                                                echo('<span class="uni theme-inv-textColor" style="pointer-events: none; font-size:10px; position: absolute; top: 3px; right: 5px; border-radius: 50%; padding: 2px 5px;" onclick="toggleAddComment(\''.$i_id.'\', '); if ((int)$i_comments > 0) { echo('1'); } else { echo('0'); } echo(')">'.$i_comments.'</span>');
                                            } else {
                                                echo('<span class="uni gold" style="pointer-events: none; font-size:12px; position: absolute; top: 1px; right: 6px; border-radius: 50%; padding: 2px 5px;" onclick="toggleAddComment(\''.$i_id.'\', '); if ((int)$i_comments > 0) { echo('1'); } else { echo('0'); } echo(')">+</span>');
                                            }
                                    echo('
                                        </div>
                                    </td>
                                    <td class="align-middle" hidden>'.$i_quantity.'</td>
                                    <td class="align-middle" style="padding-right:5px">
                                        <button id="move-btn-'.$i_id.'" class="btn btn-warning" style="padding-left:10px;padding-right:10px" type="button" value="move" title="Move?" onclick="modalLoadMoveOptic(\''.$i_id.'\')">
                                            <i class="fa fa-arrows-h" style="color:black"></i>
                                        </button>
                                    </td>
                                    <td class="align-middle" style="padding-left:5px">');
                                    if ($deleted == 1) { 
                                        echo('<button class="btn btn-success" type="submit" form="opticForm-'.$i_id.'" name="optic-restore-submit" value="1" title="Restore?">
                                                <i class="fa fa-trash-restore"></i>
                                            </button>');
                                    } else {
                                        echo('<button class="btn btn-danger" type="button" value="1" title="Delete?" onclick="modalLoadDeleteOptic(\''.$i_id.'\')">
                                                <i class="fa fa-trash"></i>
                                            </button>');
                                    }
                                echo('
                                    </td>
                                </tr>
                                <tr id="item-'.$i_id.'-add-comments" class="row-add-hide align-middle text-center" hidden>
                                    <td colspan="100%">
                                        <div class="container">
                                            <form action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                                                <!-- Include CSRF token in the form -->
                                                <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                                <div class="row centertable" style="max-width:max-content">
                                                    <div class="col" style="max-width:max-content">
                                                        <label class="nav-v-c">Comment:</label>
                                                    </div>
                                                    <div class="col" style="max-width:max-content">
                                                        <input type="hidden" name="id" value="'.$i_id.'" />
                                                        <input name="comment" class="form-control row-dropdown" type="text" style="padding: 2px 7px 2px 7px; width:250px" placeholder="Comment..."/>
                                                    </div>
                                                    <div class="col" style="max-width:max-content">
                                                        <button class="btn btn-success align-bottom" type="submit" name="optic-comment-add" style="margin-left:10px" value="1">Add</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            ');
                            if ((int)$i_comments > 0) {
                                echo('
                                <tr id="item-'.$i_id.'-comments" class="row-hide align-middle text-center" hidden>
                                    <td colspan="100%">
                                        <div class="container">
                                            <table class="centertable" style="border: 1px solid #454d55;">
                                                <thead>
                                                    <tr class="row-show align-middle text-center">
                                                        <th hidden>ID</th>
                                                        <th>Username</th>
                                                        <th>Comment</th>
                                                        <th>Timestamp</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>');
                                                $sql_comment = "SELECT oc.id AS id, oc.item_id AS item_id, oc.comment AS comment, oc.user_id AS user_id, oc.timestamp AS timestamp, u.username AS username
                                                            FROM optic_comment AS oc
                                                            INNER JOIN users AS u ON u.id=oc.user_id
                                                            WHERE oc.deleted != 1 AND oc.item_id = '$i_id'
                                                            ORDER BY timestamp";
                                                $stmt_comment = mysqli_stmt_init($conn);
                                                if (!mysqli_stmt_prepare($stmt_comment, $sql_comment)) {
                                                    //error
                                                    echo('<tr><td colspan=100%>Error getting comments...</td></tr>');
                                                } else {
                                                    mysqli_stmt_execute($stmt_comment);
                                                    $result_comment = mysqli_stmt_get_result($stmt_comment);
                                                    $rowCount_comment = $result_comment->num_rows;
                                                    if ($rowCount_comment < 1) {
                                                        // somehow no entries found
                                                        echo('<tr><td colspan=100%>No Entries Found...</td></tr>');
                                                    } else {
                                                        while ($row_comment = $result_comment->fetch_assoc()) {
                                                            $com_id = $row_comment['id'];
                                                            $com_user_id = $row_comment['user_id'];
                                                            $com_username = $row_comment['username'];
                                                            $com_comment = $row_comment['comment'];
                                                            $com_timestamp = $row_comment['timestamp'];

                                                            echo ('
                                                                <tr id="comment-'.$com_id.'" class="row-show align-middle text-center">
                                                                    <form action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                                                                        <!-- Include CSRF token in the form -->
                                                                        <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">
                                                                        <input type="hidden" value="'.$com_id.'" name="id"/>
                                                                        <td class="align-middle" hidden>'.$com_id.'</td>
                                                                        <td class="align-middle">'.$com_username.'</td>
                                                                        <td class="align-middle">'.$com_comment.'</td>
                                                                        <td class="align-middle">'.$com_timestamp.'</td>
                                                                        <td class="align-middle"><button class="btn btn-danger" type="submit" name="optic-comment-delete" value="1"><i class="fa fa-trash"></i></button></td>
                                                                    </form>
                                                                </tr>
                                                            ');
                                                        }
                                                    }
                                                }
                                            echo('</tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                ');
                            }

                            // print_r('<div hidden>');
                            // print_r($row_inv);
                            // print_r('</div>');
                        }
                    
                    } 
                    echo('
                            </tbody>
                        </table>
                        <table class="table table-dark theme-table centertable">
                            <tbody>
                                <tr class="theme-tableOuter">
                                    <td colspan="100%" style="margin:0px;padding:0px" class="invTablePagination">
                                    <div class="row">
                                        <div class="col text-center"></div>
                                        <div id="inv-page-numbers" class="col-6 text-center align-middle" style="overflow-y:auto; display:flex;justify-content:center;align-items:center;">
                                        ');
                                        if ($total_pages > 1) {
                                            if ($current_page > 1) {
                                                echo '<or class="gold clickable" style="padding-right:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page - 1).'\') + \'\')"><</or>';
                                            }
                                            if ($total_pages > 5) {
                                                for ($i = 1; $i <= $total_pages; $i++) {
                                                    if ($i == $current_page) {
                                                        echo '<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>';
                                                        // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                                                    } elseif ($i == 1 && $current_page > 5) {
                                                        echo '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or><or style="padding-left:5px;padding-right:5px">...</or>';  
                                                    } elseif ($i < $current_page && $i >= $current_page-2) {
                                                        echo '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                                                    } elseif ($i > $current_page && $i <= $current_page+2) {
                                                        echo '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                                                    } elseif ($i == $total_pages) {
                                                        echo '<or style="padding-left:5px;padding-right:5px">...</or><or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';  
                                                    }
                                                }
                                            } else {
                                                for ($i = 1; $i <= $total_pages; $i++) {
                                                    if ($i == $current_page) {
                                                        echo '<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>';
                                                        // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                                                    } else {
                                                        echo '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                                                    }
                                                }
                                            }
                        
                                            if ($current_page < $total_pages) {
                                                echo '<or class="gold clickable" style="padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page + 1).'\') + \'\')">></or>';
                                            }  
                                        }
                                        echo('
                                        </div>
                                        <div class="col text-center">
                                            <table style="margin-left:auto; margin-right:20px">
                                                <tbody>
                                                    <tr>
                                                        <td class="theme-textColor align-middle" style="border:none;padding-top:4px;padding-bottom:4px">
                                                            Rows: 
                                                        </td>
                                                        <td class="align-middle" style="border:none;padding-top:4px;padding-bottom:4px">
                                                            <select id="tableRowCount" class="form-control row-dropdown" style="width:50px;height:25px; padding:0px" name="rows" onchange="navPage(updateQueryParameter(\'\', \'rows\', this.value))">
                                                                <option id="rows-20"  value="20"');  if($rowSelectValue == 20)  { echo('selected'); } echo('>20</option>
                                                                <option id="rows-50"  value="50"');  if($rowSelectValue == 50)  { echo('selected'); } echo('>50</option>
                                                                <option id="rows-100" value="100"'); if($rowSelectValue == 100) { echo('selected'); } echo('>100</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    ');
                    // testing
                    // print_r('<pre style="margin-top:100px">');// testing
                    // print_r($sql_inv);// testing
                    // print_r('<br><br>');// testing
                    // print_r($rowdump);// testing
                    // print_r('</pre>');// testing
                }
            }

            ?>
    </div>    
    <!-- Modal NewType Div -->
    <div id="modalDivNewType" class="modal">
    <!-- <div id="modalDivNewType" style="display: block;"> -->
        <span class="close" onclick="modalCloseNewType()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                <form id="add-optic-type-form" action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                    <!-- Include CSRF token in the form -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <?php 
                    if (is_array($_GET) && count($_GET) > 1) {
                        foreach (array_keys($_GET) AS $key) {
                            echo('<input type="hidden" name="QUERY['.$key.']" value="'.$_GET[$key].'"/>');
                        }
                    }
                    ?>
                    <table class="centertable">
                        <tbody>
                            <tr class="nav-row">
                                <td style="width: 150px"><label for="type_name" class="nav-v-c align-middle">Type Name:</label></td>
                                <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="type_name" name="type_name" /></td>
                                <td></td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width:150px"></td>
                                <td style="margin-top:10px;margin-left:10px"><button type="submit" name="optic-type-add" value="Add Type" class="btn btn-success">Add Type</button></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div> 
    </div>
    <!-- End of Modal NewType Div -->
    <!-- Modal NewVendor Div -->
    <div id="modalDivNewVendor" class="modal">
    <!-- <div id="modalDivNewVendor" style="display: block;"> -->
        <span class="close" onclick="modalCloseNewVendor()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                <form id="add-optic-vendor-form" action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                    <!-- Include CSRF token in the form -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <?php 
                    if (is_array($_GET) && count($_GET) > 1) {
                        foreach (array_keys($_GET) AS $key) {
                            echo('<input type="hidden" name="QUERY['.$key.']" value="'.$_GET[$key].'"/>');
                        }
                    }
                    ?>
                    <table class="centertable">
                        <tbody>
                            <tr class="nav-row">
                                <td style="width: 150px"><label for="vendor_name" class="nav-v-c align-middle">Vendor Name:</label></td>
                                <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="vendor_name" name="vendor_name" /></td>
                                <td></td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width:150px"></td>
                                <td style="margin-top:10px;margin-left:10px"><button type="submit" name="optic-vendor-add" value="Add Vendor" class="btn btn-success">Add Vendor</button></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div> 
    </div>
    <!-- End of Modal NewVendor Div -->
    <!-- Modal NewSpeed Div -->
    <div id="modalDivNewSpeed" class="modal">
    <!-- <div id="modalDivNewSpeed" style="display: block;"> -->
        <span class="close" onclick="modalCloseNewSpeed()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                <form id="add-optic-speed-form" action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                    <!-- Include CSRF token in the form -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <?php 
                    if (is_array($_GET) && count($_GET) > 1) {
                        foreach (array_keys($_GET) AS $key) {
                            echo('<input type="hidden" name="QUERY['.$key.']" value="'.$_GET[$key].'"/>');
                        }
                    }
                    ?>
                    <table class="centertable">
                        <tbody>
                            <tr class="nav-row">
                                <td style="width: 150px"><label for="speed_name" class="nav-v-c align-middle">Speed:</label></td>
                                <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="speed_name" name="speed_name" /></td>
                                <td></td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width:150px"></td>
                                <td style="margin-top:10px;margin-left:10px"><button type="submit" name="optic-speed-add" value="Add Speed" class="btn btn-success">Add Speed</button></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div> 
    </div>
    <!-- Modal NewConnector Div -->
    <div id="modalDivNewConnector" class="modal">
    <!-- <div id="modalDivNewConnector" style="display: block;"> -->
        <span class="close" onclick="modalCloseNewConnector()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                <form id="add-optic-connector-form" action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                    <!-- Include CSRF token in the form -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <?php 
                    if (is_array($_GET) && count($_GET) > 1) {
                        foreach (array_keys($_GET) AS $key) {
                            echo('<input type="hidden" name="QUERY['.$key.']" value="'.$_GET[$key].'"/>');
                        }
                    }
                    ?>
                    <table class="centertable">
                        <tbody>
                            <tr class="nav-row">
                                <td style="width: 150px"><label for="connector_name" class="nav-v-c align-middle">Connector Name:</label></td>
                                <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="connector_name" name="connector_name" /></td>
                                <td></td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width:150px"></td>
                                <td style="margin-top:10px;margin-left:10px"><button type="submit" name="optic-connector-add" value="Add Connector" class="btn btn-success">Add Connector</button></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div> 
    </div>
    <!-- Modal NewDistance Div -->
    <div id="modalDivNewDistance" class="modal">
    <!-- <div id="modalDivNewDistance" style="display: block;"> -->
        <span class="close" onclick="modalCloseNewDistance()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                <form id="add-optic-distance-form" action="includes/optics.inc.php" method="POST" enctype="multipart/form-data" style="margin-bottom:0px">
                    <!-- Include CSRF token in the form -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <?php 
                    if (is_array($_GET) && count($_GET) > 1) {
                        foreach (array_keys($_GET) AS $key) {
                            echo('<input type="hidden" name="QUERY['.$key.']" value="'.$_GET[$key].'"/>');
                        }
                    }
                    ?>
                    <table class="centertable">
                        <tbody>
                            <tr class="nav-row">
                                <td style="width: 150px"><label for="distance_name" class="nav-v-c align-middle">Distance Name:</label></td>
                                <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="distance_name" name="distance_name" /></td>
                                <td></td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width:150px"></td>
                                <td style="margin-top:10px;margin-left:10px"><button type="submit" name="optic-distance-add" value="Add Distance" class="btn btn-success">Add Distance</button></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div> 
    </div>
    <!-- End of Modal NewDistance Div -->
    <!-- Modal DeleteOptic Div -->
    <div id="modalDivDeleteOptic" class="modal">
        <span class="close" onclick="modalCloseDeleteOptic()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                <form action="includes/optics.inc.php" method="POST" enctype="multipart/form-data">
                    <!-- Include CSRF token in the form -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <table class="centertable" style="border:none">
                        <tbody style="border:none">
                            <tr>
                                <td class="align-middle text-center" colspan=100% style="border:none"><h3 id="delete-optic-serial" style="margin-bottom:20px"></h3></td>
                            </tr>
                            <tr>
                                <td class="align-middle text-center" colspan=100% style="border:none">
                                <p style="margin-bottom:5px">Reason for Deletion:</p></td>
                            </tr>
                            <tr>
                                <td class="align-middle text-center" style="border:none; padding-right:0px;">
                                    <input id="delete-reason" type="text" class="form-control" placeholder="Reason..." name="reason" required/>
                                    <input type="hidden" id="delete-id" name="id" />
                                </td>
                                <td class="align-middle text-center" style="border:none"><input type="submit" value="Delete" class="btn btn-danger" name="optic-delete-submit" /></td>
                                <td class="align-middle text-center" style="border:none"><button type="button" style="margin-left:20px"class="btn btn-warning" onclick="modalCloseDeleteOptic()">Cancel</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- <h3 id="delete-optic-serial" style="margin-bottom:20px"></h3>
                    <p style="margin-bottom:5px">Reason for Deletion:</p>
                    <input id="delete-reason" type="text" class="form-control" placeholder="Reason..." name="reason" style="margin-bottom:10px" required/>
                    <input type="hidden" id="delete-id" name="id" />
                    <input type="submit" value="Delete" class="btn btn-danger" name="optic-delete-submit" /> -->
                </form>
            </div>  
        </div>
    </div>
    <!-- End of DeleteOptic Div -->
    <!-- Modal MoveOptic Div -->
    <div id="modalDivMoveOptic" class="modal">
        <span class="close" onclick="modalCloseMoveOptic()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg property" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;">
                <form action="includes/optics.inc.php" method="POST" enctype="multipart/form-data">
                    <!-- Include CSRF token in the form -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <table class="centertable" style="border:none">
                        <tbody style="border:none">
                            <tr>
                                <td class="align-middle text-center" colspan=100% style="border:none"><h3 id="move-optic-serial" style="margin-bottom:20px"></h3></td>
                            </tr>
                            <tr>
                                <td class="align-middle text-center" colspan=100% style="border:none">
                                <p style="margin-bottom:5px">Move location:</p></td>
                            </tr>
                            <tr>
                                <td class="align-middle text-center" style="border:none; padding-right:0px;">
                                    <select name="move-site" class="form-control" style="display:inline !important; max-width:max-content">');
                                    <?php
                                        $sql_site = "SELECT id, name
                                                    FROM site
                                                    WHERE site.deleted != 1";
                                        $stmt_site = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($stmt_site, $sql_site)) {
                                            echo('<option value="0" selected>ERROR</option>');
                                        } else {
                                            mysqli_stmt_execute($stmt_site);
                                            $result_site = mysqli_stmt_get_result($stmt_site);
                                            $rowCount_site = $result_site->num_rows;
                                            if ($rowCount_site < 1) {
                                                // error
                                                echo('<option value="0" selected>No Sites Found...</option>');
                                            } else {
                                                echo('<option disabled selected>Select</option>');
                                                while ($row_site = $result_site->fetch_assoc()) {
                                                    $id = $row_site['id'];
                                                    $name = $row_site['name'];

                                                    echo('<option value="'.$id.'">'.$name.'</option>');
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                    <input type="hidden" id="move-id" name="id" />
                                </td>
                                <td class="align-middle text-center" style="border:none"><input type="submit" value="Move" class="btn btn-success" name="optic-move-submit" /></td>
                                <td class="align-middle text-center" style="border:none"><button type="button" style="margin-left:20px"class="btn btn-warning" onclick="modalCloseMoveOptic()">Cancel</button></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>  
        </div>
    </div>
    <!-- End of MoveOptic Div -->
    <!-- Add the JS for the file -->
    <script src="assets/js/optics.js"></script>

    <?php include 'foot.php'; ?>

</body>
