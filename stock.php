<?php   
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// SHOWS THE INFORMATION FOR EACH PEICE OF STOCK AND ITS LOCATIONS ETC. 
// id QUERY STRING IS NEEDED FOR THIS
include 'session.php'; // Session setup and redirect if the session is not active 
// include 'http-headers.php'; // $_SERVER['HTTP_X_*']
?>

<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Stock</title>
</head>
<body>
    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->

    <div class="content">
        <?php // dependency PHP
        $show_inventory = 0; // for nav.php to show the site and area on the banner
        if (isset($_GET['stock_id'])) {
            if (is_numeric($_GET['stock_id'])) {
                $stock_id = $_GET['stock_id'];
            } else {
                if (isset($_GET['modify'])) {
                    echo('<div class="container" style="padding-top:25px"><p class="red">Non-numeric Stock ID: <or class="blue">'.$_GET['stock_id'].'</or>.<br>Please check the URL or <a class="link" onclick="navPage(updateQueryParameter(\'\', \'stock_id\', 0))">add new stock item</a>.</p></div>');
                    exit();
                } else {
                    echo('<div class="container" style="padding-top:25px"><p class="red">Non-numeric Stock ID: <or class="blue">'.$_GET['stock_id'].'</or>.<br>Please check the URL or go back to the <a class="link" href="./">home page</a>.</p></div>');
                    exit();
                }
                
            }
        } elseif (isset($_GET['modify'])) {
            if ($_GET['modify'] == "edit") {
                echo("No Stock ID Selected.");
            }
        } else {
            echo("No Stock ID or Modification Selected.");
        }
        if (isset($_GET['modify'])) {
            $stock_modify = $_GET['modify'];
        }
        if (!isset($_SERVER['HTTP_REFERER'])) {
            $_SERVER['HTTP_REFERER'] = './index.php';
        }
        ?>

        <!-- Get Inventory -->
        <?php
        $stock_modify_values = ['add', 'remove', 'edit', 'move'];
        if (isset($stock_modify) && in_array(strtolower($stock_modify), $stock_modify_values)) {
            echo('<div class="container" style="padding-bottom:25px">
            <h2 class="header-small" style="padding-bottom:5px">Stock - '.ucwords($stock_modify).'</h2>');
            if (isset($_GET['error'])) {
                $error = $_GET['error'];
                if ($_GET['error'] == 'SKUexists') { $error = 'SKU "<or class="blue">'.$_GET['sku'].'</or>" already exists. Please pick another, or leave empty to generate a new one'; }
                if ($_GET['error'] == 'multipleItemsFound') { $error = 'Multiple item rows found in the items table. Something needs corecting in the database. <br>To continue, change one of:<br>&nbsp;<or class="blue">UPC</or>,<br>&nbsp;<or class="blue">Serial Numbers</or>,<br>&nbsp;<or class="blue">Item Cost</or>,<br>&nbsp;<or class="blue">Shelf/Location</or>'; }
                echo('<p class="red" style="margin-bottom:0">ERROR: '.$error.'.</p>');
            }
            echo('</div>');
            include 'includes/stock-'.$stock_modify.'.inc.php';

        } else {
            include 'includes/dbh.inc.php';
            $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, stock.is_cable AS stock_is_cable,
                                stock_img.id AS stock_img_id, stock_img.stock_id AS stock_img_stock_id, stock_img.image AS stock_img_image, stock.deleted AS stock_deleted
                        FROM stock
                        LEFT JOIN stock_img ON stock.id=stock_img.stock_id
                        WHERE stock.id=?";
            $stmt_stock = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt_stock, $sql_stock)) {
                echo("ERROR getting entries");
            } else {
                mysqli_stmt_bind_param($stmt_stock, "s", $stock_id);
                mysqli_stmt_execute($stmt_stock);
                $result_stock = mysqli_stmt_get_result($stmt_stock);
                $rowCount_stock = $result_stock->num_rows;

                if ($rowCount_stock < 1) {
                    echo ('<div class="container" id="no-stock-found">No Stock Found</div>');
                } else {
                    $stock_img_data = [];
                    while ( $row = $result_stock->fetch_assoc() ) {
                        $stock_id                  = $row['stock_id']          ; 
                        $stock_name                = $row['stock_name']        ;
                        $stock_description         = $row['stock_description'] ;
                        $stock_sku                 = $row['stock_sku']         ;
                        $stock_min_stock           = $row['stock_min_stock']   ;
                        $stock_is_cable            = $row['stock_is_cable']   ;
                        $stock_stock_img_id        = $row['stock_img_id']      ;
                        $stock_stock_img_stock_id  = $row['stock_img_stock_id'];
                        $stock_stock_img_image     = $row['stock_img_image']   ;
                        $stock_stock_deleted       = $row['stock_deleted'];

                        if ($stock_stock_img_id !== null) {
                        $stock_img_data[] = array('id' => $stock_stock_img_id, 'stock_id' => $stock_stock_img_stock_id, 'image' => $stock_stock_img_image);
                        }
                    }
                    // $stock_img_data[] = array('id' => $stock_stock_img_id, 'stock_id' => $stock_stock_img_stock_id, 'image' => $stock_stock_img_image);
                    // $stock_img_data[] = array('id' => $stock_stock_img_id, 'stock_id' => $stock_stock_img_stock_id, 'image' => $stock_stock_img_image);
                    // $stock_img_data[] = array('id' => $stock_stock_img_id, 'stock_id' => $stock_stock_img_stock_id, 'image' => $stock_stock_img_image);
                    // print_r('<pre class="theme-divBg">');
                    // print_r($stock_img_data);
                    // print_r('</pre>');
                    if ($stock_stock_deleted == 1) {
                        $delete_disable = ' disabled';
                    } else {
                        $delete_disable = '';
                    }
                    if ($stock_is_cable == 1) {
                        $cable_disable = ' disabled';
                    } else {
                        $cable_disable = '';
                    }
                    echo('
                        <div class="container stock-heading">
                            <h2 class="header-small" style="padding-bottom:5px">Stock</h2>
                            <div class="nav-row " style="margin-top:5px;">
                                <h3 style="font-size:22px;margin-top:20px;margin-bottom:0;width:max-content" id="stock-name">'.$stock_name.' ('.$stock_sku.')</h3>
                                <div class="nav-div nav-right" style="padding-top:5px;margin-right:0px !important">
                                    <div class="nav-row">
                                        <div id="edit-div" class="nav-div nav-right" style="margin-right:5px">
                                            <button id="edit-stock" class="btn btn-info theme-textColor nav-v-b stock-modifyBtn" onclick="navPage(updateQueryParameter(\'./stock.php?stock_id='.$stock_id.'\', \'modify\', \'edit\'))">
                                                <i class="fa fa-pencil"></i><or class="viewport-large-empty"> Edit</or>
                                            </button>
                                        </div> 
                                        <div id="add-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                                            <button id="add-stock" class="btn btn-success theme-textColor nav-v-b stock-modifyBtn" onclick="navPage(updateQueryParameter(\'./stock.php?stock_id='.$stock_id.'\', \'modify\', \'add\'))"'.$cable_disable.$delete_disable.'>
                                                <i class="fa fa-plus"></i><or class="viewport-large-empty"> Add</or>
                                            </button>
                                        </div> 
                                        <div id="remove-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                                            <button id="remove-stock" class="btn btn-danger theme-textColor nav-v-b stock-modifyBtn" onclick="navPage(updateQueryParameter(\'./stock.php?stock_id='.$stock_id.'\', \'modify\', \'remove\'))"'.$cable_disable.$delete_disable.'>
                                                <i class="fa fa-minus"></i><or class="viewport-large-empty"> Remove</or>
                                            </button>
                                        </div> 
                                        <div id="transfer-div" class="nav-div" style="margin-left:5px;margin-right:0px">
                                            <button id="transfer-stock" class="btn btn-warning nav-v-b stock-modifyBtn" style="color:black" onclick="navPage(updateQueryParameter(\'./stock.php?stock_id='.$stock_id.'\', \'modify\', \'move\'))"'.$cable_disable.$delete_disable.'>
                                                <i class="fa fa-arrows-h"></i><or class="viewport-large-empty"> Move</or>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p id=stock-description style="margin-bottom:0px">'.$stock_description.'</p>
                            '); 
                            if ($stock_stock_deleted == 1) { echo('<p class="red" style="margin-top:20px;font-size:20">Stock Deleted. <a class="link" style="font-size:20" href="admin.php#stockmanagement-settings">Restore?</a></p>');} 
                            echo('
                        </div>

                        <!-- Modal Image Div -->
                        <div id="modalDiv" class="modal" onclick="modalClose()">
                            <span class="close" onclick="modalClose()">&times;</span>
                            <img class="modal-content bg-trans modal-imgWidth" id="modalImg">
                            <div id="caption" class="modal-caption"></div>
                        </div>
                        <!-- End of Modal Image Div -->

                    ');

                    if ($stock_is_cable == 0) { // not a cable
                        $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                                    area.id AS area_id, area.name AS area_name,
                                    shelf.id AS shelf_id, shelf.name AS shelf_name, site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                    (SELECT SUM(quantity) 
                                        FROM item 
                                        WHERE item.stock_id = stock.id AND item.shelf_id=shelf.id
                                    ) AS item_quantity,
                                    (SELECT GROUP_CONCAT(DISTINCT manufacturer.name ORDER BY manufacturer.name SEPARATOR ', ') 
                                        FROM item 
                                        INNER JOIN manufacturer ON manufacturer.id = item.manufacturer_id 
                                        WHERE item.stock_id = stock.id
                                    ) AS manufacturer_names,
                                    (SELECT GROUP_CONCAT(DISTINCT manufacturer.id ORDER BY manufacturer.name SEPARATOR ', ') 
                                        FROM item 
                                        INNER JOIN manufacturer ON manufacturer.id = item.manufacturer_id 
                                        WHERE item.stock_id = stock.id
                                    ) AS manufacturer_ids,
                                    (SELECT GROUP_CONCAT(DISTINCT tag.name ORDER BY tag.name SEPARATOR ', ') 
                                        FROM stock_tag 
                                        INNER JOIN tag ON stock_tag.tag_id = tag.id 
                                        WHERE stock_tag.stock_id = stock.id
                                        ORDER BY tag.name
                                    ) AS tag_names,
                                    (SELECT GROUP_CONCAT(DISTINCT tag.id ORDER BY tag.name SEPARATOR ', ') 
                                        FROM stock_tag
                                        INNER JOIN tag ON stock_tag.tag_id = tag.id
                                        WHERE stock_tag.stock_id = stock.id
                                        ORDER BY tag.name
                                    ) AS tag_ids
                                FROM stock
                                LEFT JOIN item ON stock.id=item.stock_id
                                LEFT JOIN shelf ON item.shelf_id=shelf.id 
                                LEFT JOIN area ON shelf.area_id=area.id 
                                LEFT JOIN site ON area.site_id=site.id
                                WHERE stock.id=?
                                GROUP BY 
                                    stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                                    site_id, site_name, site_description, 
                                    area_id, area_name, 
                                    shelf_id, shelf_name
                                ORDER BY site.id, area.name, shelf.name;";
                    } else { // is a cable
                        $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                                    area.id AS area_id, area.name AS area_name,
                                    shelf.id AS shelf_id, shelf.name AS shelf_name, site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                    (SELECT SUM(quantity) 
                                        FROM cable_item 
                                        WHERE cable_item.stock_id = stock.id AND cable_item.shelf_id=shelf.id
                                    ) AS item_quantity
                                FROM stock
                                LEFT JOIN cable_item ON stock.id=cable_item.stock_id
                                LEFT JOIN shelf ON cable_item.shelf_id=shelf.id 
                                LEFT JOIN area ON shelf.area_id=area.id 
                                LEFT JOIN site ON area.site_id=site.id
                                WHERE stock.id=?
                                GROUP BY 
                                    stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                                    site_id, site_name, site_description, 
                                    area_id, area_name, 
                                    shelf_id, shelf_name
                                ORDER BY site.id, area.name, shelf.name;";
                    }
                    $stmt_stock = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt_stock, $sql_stock)) {
                        echo("ERROR getting entries");
                    } else {
                        mysqli_stmt_bind_param($stmt_stock, "s", $stock_id);
                        mysqli_stmt_execute($stmt_stock);
                        $result_stock = mysqli_stmt_get_result($stmt_stock);
                        $rowCount_stock = $result_stock->num_rows;

                        if ($rowCount_stock < 1) {
                            echo ('<div class="container" id="no-stock-found">No Stock Found</div>');
                        } else {
                            $stock_inv_data = [];
                            while ( $row = $result_stock->fetch_assoc() ) {
                                $stock_id = $row['stock_id'];
                                $stock_name = $row['stock_name'];
                                $stock_sku = $row['stock_sku'];
                                $stock_min_stock = $row['stock_min_stock'];
                                $stock_quantity_total = $row['item_quantity'];
                                $stock_shelf_id = $row['shelf_id'];
                                $stock_shelf_name = $row['shelf_name'];
                                $stock_area_id = $row['area_id'];
                                $stock_area_name = $row['area_name'];
                                $stock_site_id = $row['site_id'];
                                $stock_site_name = $row['site_name'];
                                if ($stock_is_cable == 0) {
                                    $stock_manufacturer_ids = $row['manufacturer_ids'];
                                    $stock_manufacturer_names = $row['manufacturer_names'];
                                    $stock_tag_ids = $row['tag_ids'];
                                    $stock_tag_names = $row['tag_names'];
                                    
                                    $stock_tag_data = [];
                                    if ($stock_tag_ids !== null) {
                                        for ($n=0; $n < count(explode(", ", $stock_tag_ids)); $n++) {
                                            $stock_tag_data[$n] = array('id' => explode(", ", $stock_tag_ids)[$n],
                                                                                'name' => explode(", ", $stock_tag_names)[$n]);
                                        }
                                    } else {
                                        $stock_tag_data = '';
                                    }

                                    $stock_manufacturer_data = [];
                                    if ($stock_manufacturer_ids !== null) {
                                        for ($n=0; $n < count(explode(", ", $stock_manufacturer_ids)); $n++) {
                                            $stock_manufacturer_data[$n] = array('id' => explode(", ", $stock_manufacturer_ids)[$n],
                                                                                'name' => explode(", ", $stock_manufacturer_names)[$n]);
                                        }
                                    } else {
                                        $stock_manufacturer_data = '';
                                    }
                                } else {
                                    $stock_manufacturer_data = '';
                                    $stock_tag_data = '';
                                }
                                
                                $stock_inv_data[] = array('id' => $stock_id,
                                                        'name' => $stock_name,
                                                        'sku' => $stock_sku,
                                                        'quantity' => $stock_quantity_total,
                                                        'shelf_id' => $stock_shelf_id,
                                                        'shelf_name' => $stock_shelf_name,
                                                        'area_id' => $stock_area_id,
                                                        'area_name' => $stock_area_name,
                                                        'site_id' => $stock_site_id,
                                                        'site_name' => $stock_site_name,
                                                        'manufacturer' => $stock_manufacturer_data,
                                                        'tag' => $stock_tag_data);
                            }
                            
                            // Inventory Rows
                            echo ('
                            <div class="container well-nopad theme-divBg">
                                <div class="row">
                                    <div class="col-sm-7 text-left" id="stock-info-left">
                                        ');
                                        $totalStock = 0;
                                        for ($st=0; $st<count($stock_inv_data); $st++) {
                                            $totalStock = $totalStock + (int)$stock_inv_data[$st]['quantity'];
                                        }
                                        echo('<table class="" id="stock-info-table" style="max-width:max-content">
                                                <thead>
                                                    <tr>
                                                        <th hidden>id</th>
                                                        <th>Site</th>
                                                        <th style="padding-left: 10px">Location</th>
                                                        <th style="padding-left: 5px">Shelf</th>
                                                        <th style="padding-left: 5px">Stock</th>
                                                    </tr>
                                                </thead>
                                                <tbody>');
                                            $stt = 0; // checker to see if the stock is 0
                                            for ($st=0; $st<count($stock_inv_data); $st++) {
                                                if ($stock_inv_data[$st]['quantity'] !== 0 && $stock_inv_data[$st]['quantity'] !== '0' && $stock_inv_data[$st]['quantity'] !== null && $stock_inv_data[$st]['quantity'] !== 'null') {
                                                    echo('
                                                    <tr id="stock-row-'.$stock_inv_data[$st]['id'].'">
                                                        <td hidden>'.$stock_inv_data[$st]['id'].'</td>
                                                        <td id="site-'.$stock_inv_data[$st]['site_id'].'"><or class="clickable" onclick="window.location.href=\'./?site='.$stock_inv_data[$st]['site_id'].'\'">'.$stock_inv_data[$st]['site_name'].'</or></td>
                                                        <td id="area-'.$stock_inv_data[$st]['area_id'].'" style="padding-left: 10px"><or class="clickable" onclick="window.location.href=\'./?site='.$stock_inv_data[$st]['site_id'].'&area='.$stock_inv_data[$st]['area_id'].'\'">'.$stock_inv_data[$st]['area_name'].'</or>:</td>
                                                        <td id="shelf-'.$stock_inv_data[$st]['shelf_id'].'" style="padding-left: 5px"><button class="btn theme-btn btn-stock-click gold" onclick="window.location.href=\'./?shelf='.str_replace(' ', '+', $stock_inv_data[$st]['shelf_name']).'\'">'.$stock_inv_data[$st]['shelf_name'].'</button></td>
                                                        <td style="padding-left: 5px" class="text-center theme-textColor">'.$stock_inv_data[$st]['quantity'].'</td>
                                                    </tr>
                                                    ');
                                                    $stt ++; // stock found, add one to the checker.
                                                }
                                            }
                                            if ($stt == 0) { // show this only if there is no stock - this is the cleanest solution i could find at the time. Probably a better way but not worth searching for it yet
                                                echo('
                                                <tr id="stock-row-na-'.$st.'">
                                                    <td colspan=100% style="padding-left: 5px" class="text-center">N/A</td>
                                                </tr>
                                                ');
                                            }
                                            echo('</tbody>
                                            </table>
                                        ');

                                        echo('</p>');

                                        // echo('
                                        // <p id="sku-head"><strong>SKU</strong></p>
                                        // <p id="sku">'.$stock_sku.'</p>');
                                        echo('
                                            <p id="min-stock"><strong>Minimum Stock Count:</strong> <or class="specialColor">'.$stock_min_stock.'</or></p>
                                        ');

                                        if ($stock_is_cable == 0) {
                                            echo('
                                            <p class="clickable gold" id="extra-info-dropdown" onclick="toggleSection(this, \'extra-info\')">More Info <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></p> 
                                            <div id="extra-info" hidden>
                                                <p id="tags-head"><strong>Tags</strong></p>
                                                <p id="tags">');
                                                if (is_array($stock_inv_data[0]['tag'])) {
                                                    for ($l=0; $l < count($stock_inv_data[0]['tag']); $l++) {
                                                        echo('<button class="btn theme-btn btn-stock-click gold" id="tag-'.$stock_inv_data[0]['tag'][$l]['id'].'" onclick="window.location.href=\'./?tag='.$stock_inv_data[0]['tag'][$l]['name'].'\'">'.$stock_inv_data[0]['tag'][$l]['name'].'</button> ');
                                                    }
                                                } else {
                                                    echo('None');
                                                }
                                                
                                                echo('
                                                <p id="manufacturer-head"><strong>Manufacturers</strong></p><p id="manufacturers">');
                                                if ( is_array($stock_inv_data[0]['manufacturer'])) {
                                                    for ($m=0; $m < count($stock_inv_data[0]['manufacturer']); $m++) {
                                                        echo('<button class="btn theme-btn btn-stock-click gold" id="manufacturer-'.$stock_inv_data[0]['manufacturer'][$m]['id'].'" onclick="window.location.href=\'./?manufacturer='.$stock_inv_data[0]['manufacturer'][$m]['name'].'\'">'.$stock_inv_data[0]['manufacturer'][$m]['name'].'</button> ');
                                                    }
                                                } else {
                                                    echo('None');
                                                }
                                                echo('</p>');
                                                
                                                $sql_serials = "SELECT DISTINCT serial_number, id FROM item WHERE stock_id=? AND serial_number != '' AND quantity != 0 AND deleted=0 ORDER BY id";
                                                $stmt_serials = mysqli_stmt_init($conn);
                                                if (!mysqli_stmt_prepare($stmt_serials, $sql_serials)) {
                                                    // fails to connect (do nothing this time)
                                                } else {
                                                    mysqli_stmt_bind_param($stmt_serials, "s", $stock_id);
                                                    mysqli_stmt_execute($stmt_serials);
                                                    $result_serials = mysqli_stmt_get_result($stmt_serials);
                                                    $rowCount_serials = $result_serials->num_rows;
                                                    if ($rowCount_serials > 0) {
                                                        // rows found
                                                        echo('<p id="serial-numbers-head"><strong>Serial Numbers</strong></p><p>');
                                                        $sn = 0;
                                                        while ($row_serials = $result_serials->fetch_assoc()) {
                                                            $sn++;
                                                            echo('<a class="serial-bg" id="serialNumber'.$sn.'">'.$row_serials['serial_number'].'</a>');
                                                        }
                                                        echo('</p>');
                                                    }
                                                }  
                                        
                                            
                                            echo('</div>');
                                        }
                                        echo('
                                    </div>
                                    
                                    <div class="col text-right" id="stock-info-right">');  
                                    if (!empty($stock_img_data) && $stock_img_data !== null && $stock_img_data !== '') {
                                        echo('<div class="well-nopad theme-divBg nav-right stock-imageBox">
                                        <div class="nav-row stock-imageMainSolo">');
                                        for ($i=0; $i < count($stock_img_data); $i++) {
                                            $ii = $i+1;
                                            if ($i == 0) {
                                                if ($i+1 === count($stock_img_data)) {
                                                    $imgClass = "stock-imageMainSolo";
                                                } else {
                                                    $imgClass = "stock-imageMain";
                                                }
                                                echo('
                                                <div class="thumb theme-divBg-m text-center '.$imgClass.'" onclick="modalLoadCarousel()">
                                                    <img class="nav-v-c '.$imgClass.'" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-'.$stock_img_data[$i]['id'].'" alt="'.$stock_name.' - image '.$ii.'" src="assets/img/stock/'.$stock_img_data[$i]['image'].'" />
                                                </div>
                                                <span id="side-images" style="margin-left:5px">
                                                ');
                                            } 
                                            if ($i == 1 || $i == 2) {
                                                echo('
                                                <div class="thumb theme-divBg-m stock-imageOther" style="margin-bottom:5px" onclick="modalLoadCarousel()">
                                                    <img class="nav-v-c stock-imageOther" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-'.$stock_img_data[$i]['id'].'" alt="'.$stock_name.' - image '.$ii.'" src="assets/img/stock/'.$stock_img_data[$i]['image'].'"/>
                                                </div>
                                                ');
                                            }
                                            if ($i == 3) {
                                                if ($i < (count($stock_img_data)-1)) {
                                                    echo ('
                                                    <div class="thumb theme-divBg-m stock-imageOther" onclick="modalLoadCarousel()">
                                                    <p class="nav-v-c text-center stock-imageOther" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-more">+'.(count($stock_img_data)-3).'</p>
                                                    ');
                                                } else {
                                                    echo('
                                                    <div class="thumb theme-divBg-m stock-imageOther" onclick="modalLoadCarousel()">
                                                    <img class="nav-v-c stock-imageOther" id="stock-'.$stock_img_data[$i]['stock_id'].'-img-'.$stock_img_data[$i]['id'].'" src="assets/img/stock/'.$stock_img_data[$i]['image'].'" onclick="modalLoad(this)"/>
                                                    ');
                                                }
                                                echo('</div>');
                                            }
                                            if ($i == (count($stock_img_data)-1)) {
                                                echo('<span>');
                                            }
                                        }
                                        echo('</div>
                                        </div>');
                                        // echo('<div id="edit-images-div" class="nav-div-mid">
                                        //     <button id="edit-images" class="btn btn-info cw nav-v-b" style="padding: 3px 6px 3px 6px;font-size: 12px" onclick="navPage(updateQueryParameter(updateQueryParameter(\'\', \'modify\', \'edit\'), \'images\', \'edit\'))">
                                        //         <i class="fa fa-pencil"></i> Edit images
                                        //     </button>
                                        // </div> ');
                                        if (count($stock_img_data) == 1) {
                                            echo('
                                            <!-- Modal Image Div -->
                                            <div id="modalDivCarousel" class="modal" onclick="modalCloseCarousel()">
                                                <span class="close" onclick="modalCloseCarousel()">&times;</span>');
                                                for ($b=0; $b < count($stock_img_data); $b++) {
                                                    echo('
                                                    <img class="modal-content bg-trans modal-imgWidth" id="stock-'.$stock_img_data[$b]['stock_id'].'-img-'.$stock_img_data[$b]['id'].'" src="assets/img/stock/'.$stock_img_data[$b]['image'].'"/>
                                                    ');
                                                }
                                                echo('
                                                <img class="modal-content bg-trans" id="modalImg">
                                                <div id="caption" class="modal-caption"></div>
                                            </div>
                                            <!-- End of Modal Image Div -->
                                            ');
                                        } else {
                                            echo('
                                            <link rel="stylesheet" href="./assets/css/carousel.css">
                                            <script src="assets/js/carousel.js"></script>
                                            <!-- Modal Image Div -->
                                            <div id="modalDivCarousel" class="modal">
                                                <span class="close" onclick="modalCloseCarousel()">&times;</span>
                                                <img class="modal-content bg-trans" id="modalImg">
                                                    <div id="myCarousel" class="carousel slide" data-ride="carousel" align="center" style="margin-left:10vw; margin-right:10vw">
                                                        <!-- Indicators -->
                                                        <ol class="carousel-indicators">');
                                                        for ($a=0; $a < count($stock_img_data); $a++) {
                                                            if ($a == 0) { $active = ' class="active"'; } else { $active = ''; }
                                                            echo('<li data-target="#myCarousel" data-slide-to="'.$a.'"'.$active.'></li>');
                                                        }
                                                        echo('
                                                        </ol>

                                                        <!-- Wrapper for slides -->
                                                        <div class="carousel-inner" align="centre">');
                                                        for ($b=0; $b < count($stock_img_data); $b++) {
                                                            if ($b == 0) { $active = " active"; } else { $active = ''; }
                                                            $bb = $b+1;
                                                            echo('
                                                            <div class="item'.$active.'" align="centre">
                                                            <img class="modal-content bg-trans modal-imgWidth" id="stock-'.$stock_img_data[$b]['stock_id'].'-img-'.$stock_img_data[$b]['id'].'" src="assets/img/stock/'.$stock_img_data[$b]['image'].'"/>
                                                                <div class="carousel-caption">
                                                                    <h3></h3>
                                                                    <p></p>
                                                                </div>
                                                            </div>
                                                            ');
                                                        }
                                                            echo('
                                                        </div>

                                                        <!-- Left and right controls -->
                                                        <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                                                            <i class="fa fa-chevron-left" style="position:absolute; top:50%; margin-top:-5px"></i>
                                                            <span class="sr-only">Previous</span>
                                                        </a>
                                                        <a class="right carousel-control" href="#myCarousel" data-slide="next">
                                                            <i class="fa fa-chevron-right" style="position:absolute; top:50%; margin-top:-5px"></i>
                                                            <span class="sr-only">Next</span>
                                                        </a>
                                                    </div>
                                                <div id="caption" class="modal-caption"></div>
                                            </div>
                                            <!-- End of Modal Image Div -->
                                            ');
                                        }
                                    } else {
                                        echo('<div id="edit-images-div" class="nav-div-mid nav-v-c">
                                            <button id="edit-images" class="btn btn-success theme-textColor nav-v-b" style="padding: 3px 6px 3px 6px" onclick="navPage(updateQueryParameter(updateQueryParameter(\'\', \'modify\', \'edit\'), \'images\', \'edit\'))">
                                                <i class="fa fa-plus"></i> Add images
                                            </button>
                                        </div> ');
                                    }
                                    echo('
                                    </div>
                                </div>
                            </div>
                            <div class="container well-nopad theme-divBg" style="margin-top:5px">
                                <h2 style="font-size:22px">Stock</h2>');

                                if ($stock_is_cable == 0) {
                                    $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                                                    area.id AS area_id, area.name AS area_name,
                                                    shelf.id AS shelf_id, shelf.name AS shelf_name, site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                                    item.serial_number AS item_serial_number, item.upc AS item_upc, item.cost AS item_cost, item.comments AS item_comments, 
                                                    (SELECT SUM(quantity) 
                                                        FROM item 
                                                        WHERE item.stock_id = stock.id AND item.shelf_id=shelf.id AND item.manufacturer_id=manufacturer.id 
                                                            AND item.serial_number=item_serial_number AND item.upc=item_upc AND item.comments=item_comments
                                                    ) AS item_quantity,
                                                    manufacturer.id AS manufacturer_id, manufacturer.name AS manufacturer_name,
                                                    (SELECT GROUP_CONCAT(DISTINCT tag.name ORDER BY tag.name SEPARATOR ', ') 
                                                        FROM stock_tag 
                                                        INNER JOIN tag ON stock_tag.tag_id = tag.id 
                                                        WHERE stock_tag.stock_id = stock.id
                                                        ORDER BY tag.name
                                                    ) AS tag_names,
                                                    (SELECT GROUP_CONCAT(DISTINCT tag.id ORDER BY tag.name SEPARATOR ', ') 
                                                        FROM stock_tag
                                                        INNER JOIN tag ON stock_tag.tag_id = tag.id
                                                        WHERE stock_tag.stock_id = stock.id
                                                        ORDER BY tag.name
                                                    ) AS tag_ids
                                                FROM stock
                                                LEFT JOIN item ON stock.id=item.stock_id
                                                LEFT JOIN shelf ON item.shelf_id=shelf.id 
                                                LEFT JOIN area ON shelf.area_id=area.id 
                                                LEFT JOIN site ON area.site_id=site.id
                                                LEFT JOIN manufacturer ON item.manufacturer_id=manufacturer.id
                                                WHERE stock.id=? AND quantity!=0
                                                GROUP BY 
                                                    stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                                                    site_id, site_name, site_description, 
                                                    area_id, area_name, 
                                                    shelf_id, shelf_name,
                                                    manufacturer_name, manufacturer_id,
                                                    item_serial_number, item_upc, item_comments, item_cost
                                                ORDER BY site.id, area.name, shelf.name;";
                                } else {
                                    $sql_stock = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                                                    area.id AS area_id, area.name AS area_name,
                                                    shelf.id AS shelf_id, shelf.name AS shelf_name, site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                                    cable_item.cost AS item_cost,
                                                    (SELECT SUM(quantity) 
                                                        FROM cable_item 
                                                        WHERE cable_item.stock_id = stock.id AND cable_item.shelf_id=shelf.id
                                                    ) AS item_quantity,
                                                    (SELECT GROUP_CONCAT(DISTINCT tag.name ORDER BY tag.name SEPARATOR ', ') 
                                                        FROM stock_tag 
                                                        INNER JOIN tag ON stock_tag.tag_id = tag.id 
                                                        WHERE stock_tag.stock_id = stock.id
                                                        ORDER BY tag.name
                                                    ) AS tag_names,
                                                    (SELECT GROUP_CONCAT(DISTINCT tag.id ORDER BY tag.name SEPARATOR ', ') 
                                                        FROM stock_tag
                                                        INNER JOIN tag ON stock_tag.tag_id = tag.id
                                                        WHERE stock_tag.stock_id = stock.id
                                                        ORDER BY tag.name
                                                    ) AS tag_ids
                                                FROM stock
                                                LEFT JOIN cable_item ON stock.id=cable_item.stock_id
                                                LEFT JOIN shelf ON cable_item.shelf_id=shelf.id 
                                                LEFT JOIN area ON shelf.area_id=area.id 
                                                LEFT JOIN site ON area.site_id=site.id
                                                WHERE stock.id=? AND quantity!=0
                                                GROUP BY 
                                                    stock.id, stock_name, stock_description, stock_sku, stock_min_stock, 
                                                    site_id, site_name, site_description, 
                                                    area_id, area_name, 
                                                    shelf_id, shelf_name,
                                                    item_cost
                                                ORDER BY site.id, area.name, shelf.name;";
                                }
                                $stmt_stock = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt_stock, $sql_stock)) {
                                    echo("ERROR getting entries");
                                } else {
                                    mysqli_stmt_bind_param($stmt_stock, "s", $stock_id);
                                    mysqli_stmt_execute($stmt_stock);
                                    $result_stock = mysqli_stmt_get_result($stmt_stock);
                                    $rowCount_stock = $result_stock->num_rows;

                                    if ($rowCount_stock < 1) {
                                        echo ('<div class="container" id="no-stock-found">No Stock Found</div>');
                                    } else {
                                        $stock_inv_data = [];
                                        while ( $row = $result_stock->fetch_assoc() ) {
                                            $stock_id = $row['stock_id'];
                                            $stock_name = $row['stock_name'];
                                            $stock_sku = $row['stock_sku'];
                                            $stock_quantity_total = $row['item_quantity'];
                                            $stock_shelf_id = $row['shelf_id'];
                                            $stock_shelf_name = $row['shelf_name'];
                                            $stock_area_id = $row['area_id'];
                                            $stock_area_name = $row['area_name'];
                                            $stock_site_id = $row['site_id'];
                                            $stock_site_name = $row['site_name'];
                                            if ($stock_is_cable == 0) {
                                                $stock_manufacturer_id = $row['manufacturer_id'];
                                                $stock_manufacturer_name = $row['manufacturer_name'];
                                                $item_upc = $row['item_upc'];
                                                $item_comments = $row['item_comments'];
                                                $item_serial_number = $row['item_serial_number'];
                                            }
                                            $item_cost = $row['item_cost'];
                                            $stock_tag_ids = $row['tag_ids'];
                                            $stock_tag_names = $row['tag_names'];
                                            
                                            $stock_tag_data = [];

                                            if ($stock_tag_ids !== null) {
                                                for ($n=0; $n < count(explode(", ", $stock_tag_ids)); $n++) {
                                                    $stock_tag_data[$n] = array('id' => explode(", ", $stock_tag_ids)[$n],
                                                                                        'name' => explode(", ", $stock_tag_names)[$n]);
                                                }
                                            } else {
                                                $stock_tag_data = '';
                                            }
                                            
                                            if ($stock_is_cable == 0) {
                                                $stock_inv_data[] = array('id' => $stock_id,
                                                                    'name' => $stock_name,
                                                                    'sku' => $stock_sku,
                                                                    'quantity' => $stock_quantity_total,
                                                                    'shelf_id' => $stock_shelf_id,
                                                                    'shelf_name' => $stock_shelf_name,
                                                                    'area_id' => $stock_area_id,
                                                                    'area_name' => $stock_area_name,
                                                                    'site_id' => $stock_site_id,
                                                                    'site_name' => $stock_site_name,
                                                                    'manufacturer_id' => $stock_manufacturer_id,
                                                                    'manufacturer_name' => $stock_manufacturer_name,
                                                                    'upc' => $item_upc,
                                                                    'cost' => $item_cost,
                                                                    'comments' => $item_comments,
                                                                    'serial_number' => $item_serial_number,
                                                                    'tag_names' => $stock_tag_names,
                                                                    'tag' => $stock_tag_data);
                                            } else {
                                                $stock_inv_data[] = array('id' => $stock_id,
                                                                    'name' => $stock_name,
                                                                    'sku' => $stock_sku,
                                                                    'quantity' => $stock_quantity_total,
                                                                    'shelf_id' => $stock_shelf_id,
                                                                    'shelf_name' => $stock_shelf_name,
                                                                    'area_id' => $stock_area_id,
                                                                    'area_name' => $stock_area_name,
                                                                    'site_id' => $stock_site_id,
                                                                    'site_name' => $stock_site_name,
                                                                    'cost' => $item_cost);
                                            }
                                            
                                        }
                                        
                                                echo('
                                            <table class="table table-dark theme-table centertable">
                                                <thead>
                                                    <tr class="theme-tableOuter">
                                                        <th hidden>ID</th>
                                                        <th>Site</th>
                                                        <th>Location</th>
                                                        <th>Shelf</th>');
                                                        if ($stock_is_cable == 0) { 
                                                            echo('
                                                            <th class="viewport-large-empty">Manufacturer</th>
                                                            <th class="viewport-small-empty">Manu.</th>
                                                            <th class="viewport-large-empty">UPC</th>
                                                            <th title="Serial Numbers">Serial</th>
                                                            <th>Tags</th>
                                                            <th class="viewport-large-empty">Cost</th>
                                                            <th class="viewport-large-empty">Comments</th>');
                                                        } else { 
                                                            echo('<th class="viewport-large-empty">Cost</th>');
                                                        }
                                                        echo('
                                                        <th>Stock</th>
                                                    </tr>
                                                </thead>
                                                <tbody>                           
                                            ');
                                            for ($i=0; $i<count($stock_inv_data); $i++) {
                                                echo('
                                                    <tr id="item-'.$i.'" ');if ($stock_is_cable == 0) { echo('class="clickable" onclick="toggleHiddenStock(\''.$i.'\')"'); } echo('>
                                                        <td hidden>'.$i.'</td>
                                                        <td id="item-'.$i.'-'.$stock_inv_data[$i]['site_id'].'">'.$stock_inv_data[$i]['site_name'].'</td>
                                                        <td id="item-'.$i.'-'.$stock_inv_data[$i]['site_id'].'-'.$stock_inv_data[$i]['area_id'].'">'.$stock_inv_data[$i]['area_name'].'</td>
                                                        <td id="item-'.$i.'-'.$stock_inv_data[$i]['site_id'].'-'.$stock_inv_data[$i]['area_id'].'-'.$stock_inv_data[$i]['shelf_id'].'">'.$stock_inv_data[$i]['shelf_name'].'</td>
                                                ');   
                                                if ($stock_is_cable == 0) {
                                                    echo('   
                                                        <td id="item-'.$i.'-manu-'.$stock_inv_data[$i]['manufacturer_id'].'">'.$stock_inv_data[$i]['manufacturer_name'].'</td>
                                                        <td id="item-'.$i.'-upc" class="viewport-large-empty">'.$stock_inv_data[$i]['upc'].'</td>
                                                        <td id="item-'.$i.'-sn">'.$stock_inv_data[$i]['serial_number'].'</td>
                                                        <td id="item-'.$i.'-tags">'.$stock_inv_data[$i]['tag_names'].'</td>
                                                        <td id="item-'.$i.'-cost" class="viewport-large-empty">'.$current_currency.$stock_inv_data[$i]['cost'].'</td>
                                                        <td id="item-'.$i.'-comments" class="viewport-large-empty">'.$stock_inv_data[$i]['comments'].'</td>
                                                        <td id="item-'.$i.'-stock">'.$stock_inv_data[$i]['quantity'].'</td>
                                                        ');
                                                } else {
                                                    echo('<td id="item-'.$i.'-cost" class="viewport-large-empty">'.$current_currency.$stock_inv_data[$i]['cost'].'</td>
                                                    <td id="item-'.$i.'-stock"'); if ($stock_inv_data[$i]['quantity'] < $stock_min_stock) { echo (' class="red" title="Below minimum stock count. Please re-order."'); } echo('>'.$stock_inv_data[$i]['quantity'].'</td>');
                                                }
                                                echo('
                                                        
                                                    </tr>
                                                ');
                                                if ($stock_is_cable == 0) { 
                                                    echo ('
                                                    <tr id="item-'.$i.'-hidden" class="row-hide" hidden>
                                                        <td colspan=100%>
                                                            <div style="max-height:50vh;overflow-x: hidden;overflow-y: auto;">
                                                                <table class="table table-dark theme-table centertable" style="border-left: 1px solid #454d55;border-right: 1px solid #454d55;border-bottom: 1px solid #454d55">
                                                                    <thead>
                                                                        <tr class="theme-tableOuter">
                                                                            <th>ID</th>
                                                                            <th hidden>Site</th>
                                                                            <th hidden>Location</th>
                                                                            <th hidden>Shelf</th>
                                                                            <th>Manufacturer</th>
                                                                            <th>UPC</th>
                                                                            <th>Serial</th>
                                                                            <th>Cost ('.$current_currency.')</th>
                                                                            <th>Comments</th>
                                                                            <th>Stock</th>
                                                                            <th></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    ');
                                                                    $hidden_shelf_id = $stock_inv_data[$i]['shelf_id'];
                                                                    $hidden_serial = $stock_inv_data[$i]['serial_number']; 
                                                                    $hidden_cost = $stock_inv_data[$i]['cost'];
                                                                    $hidden_comments = $stock_inv_data[$i]['comments'];
                                                                    $sql_hidden = "SELECT stock.id AS stock_id, stock.name AS stock_name, stock.description AS stock_description, stock.sku AS stock_sku, stock.min_stock AS stock_min_stock, 
                                                                                area.id AS area_id, area.name AS area_name,
                                                                                shelf.id AS shelf_id, shelf.name AS shelf_name, site.id AS site_id, site.name AS site_name, site.description AS site_description,
                                                                                item.id AS item_id, item.serial_number AS item_serial_number, item.upc AS item_upc, item.cost AS item_cost, item.comments AS item_comments, item.quantity AS item_quantity,
                                                                                manufacturer.id AS manufacturer_id, manufacturer.name AS manufacturer_name
                                                                            FROM stock
                                                                            LEFT JOIN item ON stock.id=item.stock_id
                                                                            LEFT JOIN shelf ON item.shelf_id=shelf.id 
                                                                            LEFT JOIN area ON shelf.area_id=area.id 
                                                                            LEFT JOIN site ON area.site_id=site.id
                                                                            LEFT JOIN manufacturer ON item.manufacturer_id=manufacturer.id
                                                                            WHERE stock.id=? AND shelf_id=$hidden_shelf_id AND quantity!=0 AND item.deleted=0 AND item.serial_number='$hidden_serial' AND item.cost='$hidden_cost' AND item.comments='$hidden_comments'
                                                                            ORDER BY item.serial_number, item.upc, item.comments DESC";
                                                                    $stmt_hidden = mysqli_stmt_init($conn);
                                                                    if (!mysqli_stmt_prepare($stmt_hidden, $sql_hidden)) {
                                                                        echo("ERROR getting entries");
                                                                    } else {
                                                                        mysqli_stmt_bind_param($stmt_hidden, "s", $stock_id);
                                                                        mysqli_stmt_execute($stmt_hidden);
                                                                        $result_hidden = mysqli_stmt_get_result($stmt_hidden);
                                                                        $rowCount_hidden = $result_hidden->num_rows;

                                                                        if ($rowCount_hidden < 1) {
                                                                            echo ('<tr><td colpan=100%>No Stock Found</td></tr>');
                                                                        } else {
                                                                            $stock_img_data = [];
                                                                            while ( $row_hidden = $result_hidden->fetch_assoc() ) {
                                                                                echo('
                                                                                <tr class="align-middle">
                                                                                    <form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data">
                                                                                        <input type="hidden" name="submit" value="row"/>
                                                                                        <td class="align-middle"><input type="hidden" name="item-id" value="'.$row_hidden['item_id'].'" />'.$row_hidden['item_id'].'</td>
                                                                                        <td hidden>'.$row_hidden['site_name'].'</td>
                                                                                        <td hidden>'.$row_hidden['area_name'].'</td>
                                                                                        <td hidden>'.$row_hidden['shelf_name'].'</td>
                                                                                        <td class="align-middle">'.$row_hidden['manufacturer_name'].'</td>
                                                                                        <td class="align-middle"><input type="text" class="form-control" style="width:100px" value="'.$row_hidden['item_upc'].'" name="upc" /></td>
                                                                                        <td class="align-middle"><input type="text" class="form-control" style="width:150px" value="'.$row_hidden['item_serial_number'].'" name="serial_number" /></td>
                                                                                        <td class="align-middle"><input type="number" class="form-control" style="width:75px" value="'.$row_hidden['item_cost'].'" name="cost" min=0 /></td>
                                                                                        <td class="align-middle"><input type="text" class="form-control" style="width:150px" value="'.$row_hidden['item_comments'].'" name="comments" /></td>
                                                                                        <td class="align-middle">'.$row_hidden['item_quantity'].'</td>
                                                                                        <td><input type="submit" class="btn btn-success" name="stock-row-submit" value="Save" /> </td>
                                                                                    </form>
                                                                                </tr>
                                                                                ');
                                                                            }
                                                                        }
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
                                            echo('
                                            </tbody>
                                        </table>
                                    
                                    
                                    
                                ');
                                }
                                echo('</div>');
                            }
                            echo('<div class="container well-nopad theme-divBg viewport-large-empty" style="margin-top:5px">
                                <h2 style="font-size:22px">Transactions</h2>');
                                include 'includes/transaction.inc.php';
                            echo('</div>
                                <div class="container well-nopad theme-divBg viewport-small-empty text-center" style="margin-top:5px">
                                    <or class="specialColor clickable" style="font-size:12px" onclick="navPage(\'transactions.php?stock_id='.$stock_id.'\ \')">View Transactions</or>
                                </div>');
                        }
                        
                    }
                }
            }

            
        }

        
    ?>
    </div>
    
    <?php include 'foot.php'; ?>
    <script> // Toggle hide/show section
        function toggleSection(element, section) {
            var div = document.getElementById(section);
            var icon = element.children[0];
            if (div.hidden == false) {
                div.hidden=true;
                icon.classList.remove("fa-chevron-up");
                icon.classList.add("fa-chevron-down");
            } else {
                div.hidden=false;
                icon.classList.remove("fa-chevron-down");
                icon.classList.add("fa-chevron-up");
            }
        }
    </script>

    <script>
        function modalLoadCarousel() {
            var modal = document.getElementById("modalDivCarousel");
            // Get the image and insert it inside the modal - use its "alt" text as a caption
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal or if they click the image.
        modalCloseCarousel = function() { 
            var modal = document.getElementById("modalDivCarousel");
            modal.style.display = "none";
        }
    </script>
    <script>
    function toggleHiddenStock(id) {
        var hiddenID = 'item-'+id+'-hidden';
        var hiddenRow = document.getElementById(hiddenID);
        var allHiddenRows = document.getElementsByClassName('row-hide');
        if (hiddenRow.hidden == false) {
            hiddenRow.hidden=true;
        } else {
            for(var i = 0; i < allHiddenRows.length; i++) {
            allHiddenRows[i].hidden=true;
            }   
            hiddenRow.hidden=false;
        }
    }
    </script>
</body>