<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// Container View Page. Shows all containers and what they contain
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
        $navHighlight = 'favourites'; // for colouring the nav bar link
        $navBtnDim = 0;
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
            <h2 class="header-small" style="padding-bottom:5px"><?php if (isset($_GET['return'])) { echo('<button class="btn btn-dark" style="margin-right:20px" onclick="window.location.href=\''.urldecode($_GET['return']).'\'"><i class="fa fa-chevron-left"></i> Back</button>'); } ?>Favourites</h2>
        </div>

        <?php

        $sql = "SELECT si.image AS image, s.id AS id, s.name AS name, s.sku AS sku, tag_ids.tag_ids AS tag_ids, tag_ids.tag_names AS tag_names, area_ids.area_ids AS area_ids, area_ids.area_names as area_names
                FROM stock AS s
                INNER JOIN favourites AS f ON s.id = f.stock_id
                LEFT JOIN (
                    SELECT stock_img.stock_id, MIN(stock_img.image) AS image
                    FROM stock_img
                    GROUP BY stock_img.stock_id
                ) AS si ON si.stock_id = s.id
                LEFT JOIN (
                    SELECT stock_tag.stock_id, GROUP_CONCAT(DISTINCT tag_id SEPARATOR ', ') AS tag_ids, GROUP_CONCAT(DISTINCT tag.name SEPARATOR ', ') AS tag_names
                    FROM stock_tag
                    INNER JOIN tag ON tag.id=stock_tag.tag_id
                    GROUP BY stock_tag.stock_id
                ) AS tag_ids ON tag_ids.stock_id = s.id
                LEFT JOIN (
                    SELECT item.stock_id, GROUP_CONCAT(DISTINCT area_id SEPARATOR ', ') AS area_ids, GROUP_CONCAT(DISTINCT area.name SEPARATOR ', ') AS area_names
                    FROM shelf
                    INNER JOIN area ON shelf.area_id = area.id
                    INNER JOIN item ON item.shelf_id = shelf.id
                    GROUP BY item.stock_id
                ) AS area_ids ON area_ids.stock_id = s.id
                WHERE f.user_id=?
                GROUP BY si.image, s.id, s.name, s.sku, tag_ids.tag_ids, area_ids.area_ids
                ORDER BY name, sku, id;
                ";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../login.php?sqlerror=getLDAPconfig");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $_SESSION['user_id']);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rowCount = $result->num_rows;
            ?>
            <div style="padding-bottom:75px">
            <?php
            if ($rowCount > 0) {
                ?>
                <table class="table table-dark theme-table centertable" style="max-width:max-content;margin-bottom:0px;">
                    <thead style="text-align: center; white-space: nowrap;">
                        <tr class="theme-tableOuter align-middle text-center">
                            <th></th>
                            <th class="align-middle">ID</th>
                            <th class="align-middle">Name</th>
                            <th class="align-middle">SKU</th>
                            <th class="align-middle">Area(s)</th>
                            <th class="align-middle">Tags</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    $img_folder = 'assets/img/stock/';
                    $stock_img = $row['image'];
                    $stock_id = $row['id'];
                    $stock_name = $row['name'];
                    $stock_sku = $row['sku'];
                    $stock_tag_ids = $row['tag_ids'];
                    $stock_tag_names = $row['tag_names'];
                    $stock_area_ids = $row['area_ids'];
                    $stock_area_names = $row['area_names'];

                    // split out the tags and areas 
                    $stock_tag_ids_array = explode(', ', $stock_tag_ids);
                    $stock_tag_names_array = explode(', ', $stock_tag_names);
                    $stock_area_ids_array = explode(', ', $stock_area_ids);
                    $stock_area_names_array = explode(', ', $stock_area_names);
                    
                    // do the printing
                    echo ('
                        <tr id="stock-'.$stock_id.'">
                            <td class="text-center align-middle">'); if ($stock_img !== '' && $stock_id !== null) { echo('<img id="image-'.$stock_id.'" class="inv-img-main thumb" style="cursor:default !important" src="'.$img_folder.$stock_img.'">'); } echo('</td>
                            <td class="text-center align-middle">'.$stock_id.'</td>
                            <td id="stock-'.$stock_id.'-name" class="text-center align-middle" style="width:300px"><a href="stock.php?stock_id='.$stock_id.'">'.$stock_name.'</a></td>
                            <td class="text-center align-middle">'.$stock_sku.'</td>
                            <td class="text-center align-middle">');
                            for ($i = 0; $i < count($stock_area_ids_array); $i++) {
                                if ($i+1 < count($stock_area_ids_array)) {
                                    $delimiter = ', ';
                                } else {
                                    $delimiter = '';
                                }
                                $area_id = $stock_area_ids_array[$i];
                                $area_name = $stock_area_names_array[$i];
                                echo('<or id="stock-'.$stock_id.'-area-'.$area_id.'" class="gold link" onclick="navPage(updateQueryParameter(\'.\', \'area\', \''.$area_name.'\'))">'.$area_name.'</or>');
                                echo($delimiter);
                            }
                            echo('</td>
                            <td class="text-center align-middle">');
                            for ($i = 0; $i < count($stock_tag_ids_array); $i++) {
                                if ($i+1 < count($stock_tag_ids_array)) {
                                    $delimiter = ', ';
                                } else {
                                    $delimiter = '';
                                }
                                $tag_id = $stock_tag_ids_array[$i];
                                $tag_name = $stock_tag_names_array[$i];
                                echo('<or id="stock-'.$stock_id.'-tag-'.$tag_id.'" class="gold link" onclick="navPage(updateQueryParameter(\'.\', \'tag\', \''.$tag_name.'\'))">'.$tag_name.'</or>');
                                echo($delimiter);
                            }
                            echo('</td>
                            <td class="text-center align-middle">
                                <button onclick="favouriteStockReload('.$stock_id.')" class="btn btn-danger" style="padding:3px 6px 3px 6px; color:black" title="Remove Favourite">
                                    <i id="favouriteIcon" class="fa-regular fa-star"></i>
                                </button>
                            </td>
                        </tr>
                    ');
                }
                ?>
                    </tbody>
                </table>
                <?php
            } else {
                ?>
                <p class="container red" style="margin-top:20px">No favourites found.</p>
                <?php
            }
            ?>
            </div>
            <?php
        }
        ?>
    </div>

    <script src="assets/js/favourites.js"></script>

    <?php include 'foot.php'; ?>
</body>
