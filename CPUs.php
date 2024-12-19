<?php  
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

// Assets > CPU page - for viewing all CPUs stored.
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
            <h2 class="header-small" style="padding-bottom:5px"><?php if (isset($_GET['return'])) { echo('<button class="btn btn-dark" style="margin-right:20px" onclick="window.location.href=\''.urldecode($_GET['return']).'\'"><i class="fa fa-chevron-left"></i> Back</button>'); } ?>CPUs</h2>
        </div>

        
                <div style="padding-bottom:75px">
                    <table class="table table-dark theme-table centertable" style="max-width:max-content;margin-bottom:0px;">
                        <thead style="text-align: center; white-space: nowrap;">
                            <tr class="theme-tableOuter align-middle text-center">
                                <th></th>
                                <th class="align-middle">ID</th>
                                <th class="align-middle">Vendor</th>
                                <th class="align-middle">Model</th>
                                <th class="align-middle">Cores</th>
                                <th class="align-middle">Frequency</th>
                                <th class="align-middle">Socket</th>
                                <th class="align-middle">Site</th>
                                <th class="align-middle">Area</th>
                                <th class="align-middle">Shelf</th>
                                <th colspan=3 class="align-middle"><button type="button" style="padding: 3px 6px 3px 6px" class="btn btn-success" onclick="no_modalLoadAddContainer()">+ Add New</button></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="">
                                <td class="text-center align-middle"><img id="image-'.$container_img_id.'" class="inv-img-main thumb" style="cursor:default !important" src="assets/img/stock/CPU.png"></td>
                                <td class="text-center align-middle">69</td>
                                <td id="" class="text-center align-middle">Intel</td>
                                <td class="text-center align-middle">i7-6700K</td>
                                <td class="text-center align-middle">4</td>
                                <td class="text-center align-middle">4.2GHz</td>
                                <td class="text-center align-middle">LGA 1151</td>
                                <td class="text-center align-middle"><a href="">DC1</a></td>
                                <td class="text-center align-middle"><a href="">Location 1</a></td>
                                <td class="text-center align-middle"><a href="">Shelf 1</a></td>
                                <td class="text-center align-middle" style="padding-right:2.5px"><button class="btn btn-info"><i class="fa fa-pencil"></i></button></td>
                                <td class="text-center align-middle" style="padding-left:2.5px;padding-right:2.5px"><button class="btn btn-warning"><i class="fa fa-arrows-h"></i></button></td>
                                <td class="text-center align-middle" style="padding-left:2.5px"><button class="btn btn-danger"><i class="fa fa-trash"></i></button></td>
                            </tr>  
                            <tr id="">
                                <td class="text-center align-middle"><img id="image-'.$container_img_id.'" class="inv-img-main thumb" style="cursor:default !important" src="assets/img/stock/CPU.png"></td>
                                <td class="text-center align-middle">420</td>
                                <td id="" class="text-center align-middle">AMD</td>
                                <td class="text-center align-middle">Ryzen 5 7600X</td>
                                <td class="text-center align-middle">6</td>
                                <td class="text-center align-middle">5.2GHz</td>
                                <td class="text-center align-middle">AM5</td>
                                <td class="text-center align-middle"><a href="">DC1</a></td>
                                <td class="text-center align-middle"><a href="">Location 1</a></td>
                                <td class="text-center align-middle"><a href="">Shelf 1</a></td>
                                <td class="text-center align-middle" style="padding-right:2.5px"><button class="btn btn-info"><i class="fa fa-pencil"></i></button></td>
                                <td class="text-center align-middle" style="padding-left:2.5px;padding-right:2.5px"><button class="btn btn-warning"><i class="fa fa-arrows-h"></i></button></td>
                                <td class="text-center align-middle" style="padding-left:2.5px"><button class="btn btn-danger"><i class="fa fa-trash"></i></button></td>
                            </tr>   
                        </tbody>
                    </table>
                </div>
            <?php

        include 'includes/stock-new-properties.inc.php';
        ?>

    </div>
    <!-- Start Modal for uninking from container -->
    <div id="modalDivUnlinkContainer" class="modal">
        <span class="close" onclick="modalCloseUnlinkContainer()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;" id="property-container">
                <form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data">
                    <!-- Include CSRF token in the form -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="hidden" id="form-unlink-container-item-id" name="item_id" value=""  />
                    <input type="hidden" name="container-unlink" value="1"/>
                    <table class="centertable">
                        <tbody>
                            <tr class="nav-row">
                                <th colspan=100%>Container:</th>
                            </tr>
                            <tr class="nav-row">
                                <td style="width: 200px"><label class="nav-v-c align-middle">Container ID:</label></td>
                                <td style="margin-left:10px"><label id="unlink-container-id" class="nav-v-c align-middle">PLACEHOLDER ID</label></td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width: 200px"><label class="nav-v-c align-middle">Container Name:</label></td>
                                <td style="margin-left:10px"><label id="unlink-container-name" class="nav-v-c align-middle">PLACEHOLDER NAME</label></td>
                            </tr>
                            <tr class="nav-row" style="padding-top:20px">
                                <th colspan=100%>Item to unlink:</th>
                            </tr>
                            <tr class="nav-row">
                                <td style="width: 200px"><label class="nav-v-c align-middle">Item ID:</label></td>
                                <td style="margin-left:10px"><label id="unlink-container-item-id" class="nav-v-c align-middle">PLACEHOLDER ID</label></td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width: 200px"><label class="nav-v-c align-middle">Item Name:</label></td>
                                <td style="margin-left:10px"><label id="unlink-container-item-name" class="nav-v-c align-middle">PLACEHOLDER NAME</label></td>
                            </tr>
                            <tr class="nav-row text-center align-middle" style="padding-top:10px">
                                <td class="text-center align-middle" colspan=100% style="width:100%">
                                    <span style="white-space:nowrap; width:100%">
                                        <button class="btn btn-danger" type="submit" name="submit" style="color:black !important; margin-right:10px">Unlink <i style="margin-left:5px" class="fa fa-unlink"></i></button>
                                        <button class="btn btn-warning" type="button" onclick="modalCloseUnlinkContainer()" style="margin-left:10px">Cancel</button>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal for uninking from container -->
    <!-- Container Add New Modal -->
    <div id="modalDivAddContainer" class="modal">
        <span class="close" onclick="modalCloseAddContainer()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;" id="property-container">
                <form action="includes/containers.inc.php" method="POST" enctype="multipart/form-data">
                    <!-- Include CSRF token in the form -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <table class="centertable">
                        <tbody>
                            <tr class="nav-row">
                                <td style="width: 200px"><label for="container_name" class="nav-v-c align-middle">Container Name:</label></td>
                                <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="container_name" name="container_name" placeholder="Name" /></td>
                                <td></td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width:200px"><label for="container_description" class="nav-v-c align-middle">Container Description:</label></td>
                                <td style="margin-left:10px"><input type="text" class="form-control nav-v-c align-middle" id="container_description" name="container_description" placeholder="Description" /></td>              
                                <!-- <td style="margin-left:5px"><input type="submit" name="submit" value="Add Container" class="btn btn-success"/></td> -->
                            </tr>
                            <tr class="nav-row">
                                <td style="width:200px"><label for="container_description" class="nav-v-c align-middle">Site:</label></td>
                                <td style="margin-left:10px">
                                    <?php
                                        echo('
                                        <select class="form-control stock-inputSize" id="site" name="site" style="width:228px !important" required>
                                            <option value="" selected disabled hidden>Select Site</option>');
                                                include 'includes/dbh.inc.php';
                                                $sql = "SELECT id, name
                                                        FROM site 
                                                        WHERE site.deleted=0
                                                        ORDER BY id";
                                                $stmt = mysqli_stmt_init($conn);
                                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                                    // fails to connect
                                                } else {
                                                    mysqli_stmt_execute($stmt);
                                                    $result = mysqli_stmt_get_result($stmt);
                                                    $rowCount = $result->num_rows;
                                                    if ($rowCount < 1) {
                                                        echo('<option value="0">No Sites Found...</option>');
                                                    } else {
                                                        // rows found
                                                        while ($row = $result->fetch_assoc()) {
                                                            $sites_id = $row['id'];
                                                            $sites_name = $row['name'];
                                                            echo('<option value="'.$sites_id.'">'.$sites_name.'</option>');
                                                        }
                                                    }
                                                }
                                        echo('
                                        </select>
                                        ');
                                    ?>
                                </td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width:200px"><label for="container_description" class="nav-v-c align-middle">Area:</label></td>
                                <td style="margin-left:10px">
                                    <select class="form-control stock-inputSize" id="area" name="area" style="width:228px !important" disabled required>
                                        <option value="" selected disabled hidden>Select Area</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width:200px"><label for="container_description" class="nav-v-c align-middle">Shelf:</label></td>
                                <td style="margin-left:10px">
                                <select class="form-control stock-inputSize" id="shelf" name="shelf" style="width:228px !important" disabled required>
                                        <option value="" selected disabled hidden>Select Shelf</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="nav-row">
                                <td style="width:200px"></td>
                                <td style="margin-top:10px;margin-left:10px"><button type="submit" name="container_add_submit" value="Add Container" class="btn btn-success">Add Container</button></td>
                                <td hidden><input id="container_type" type="hidden" name="type" value="container" /></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

    <!-- Link to Container Modal -->
    <div id="modalDivAddChildren" class="modal">
        <span class="close" onclick="modalCloseAddChildren()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; overflow-x: auto; height:600px; " id="property-container" >
                <h4 class="text-center align-middle" style="width:100%;margin-top:10px">Add item to selected container</h4>
                <table class="centertable"><tbody><tr><th style="padding-right:5px">Container ID:</th><td style="padding-right:20px" id="contID"></td><th style="padding-right:5px">Container Name:</th><td id="contName"></td></tr></tbody></table>
                <div class="row" id="TheRow" style="min-width: 100%; max-width:1920px; flex-wrap:nowrap !important; padding-left:10px;padding-right:10px; max-width:max-content">
                    <div class="col well-nopad theme-divBg" style="margin: 20px 10px 20px 10px; padding:20px;">
                        <p><strong>Stock</strong></p>
                        <input type="text" name="search" class="form-control" style="width:300px; margin-bottom:5px" placeholder="Search" oninput="addChildrenSearch(document.getElementById('contID').innerHTML, this.value)"/>
                        <div style=" overflow-y:auto; overflow-x: hidden; height:300px; ">
                            <table id="containerSelectTable" class="table table-dark theme-table centertable" style="margin-bottom:0px; white-space:nowrap;">
                                <thead>
                                    <tr>
                                        <th class='text-center align-middle'>Stock ID</th>
                                        <th class='text-center align-middle'>Name</th>
                                        <th class='text-center align-middle'>Serial Number</th>
                                        <th class='text-center align-middle'>Quantity</th>
                                        <th class='text-center align-middle'>Item ID</th>
                                    </tr>
                                </thead>
                                <tbody id="addChildrenTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <form enctype="multipart/form-data" action="./includes/stock-modify.inc.php" method="POST" style="padding: 0px; margin:0px">
                <!-- Include CSRF token in the form -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">                               
                <input type="hidden" name="container-link-fromcontainers" value="1" />
                <input type="hidden" id="addChildrenIsItem" name="is_item" value="" />
                <input type="hidden" id="addChildrenContID" name="container_id" value="" />
                <input type="hidden" id="addChildrenStockID" name="stock_id" value="" />
                <input type="hidden" id="addChildrenItemID" name="item_id" value="" />
                <span class="align-middle text-center" style="display:block; white-space:nowrap;width:100%">
                    <input id="submit-button-addChildren" type="submit" name="submit" value="Link" class="btn btn-success" style="margin:10px 10px 0px 10px" disabled></input>
                    <button class="btn btn-warning" type="button" style="margin:10px 10px 0px 10px" onclick="modalCloseAddChildren()">Cancel</button>
                </span>
            </form>
        </div>
    </div>
    <!-- End of Container Add item Modal -->

    <!-- Add the JS for the file -->
    <script src="assets/js/containers.js"></script>

    <?php include 'foot.php'; ?>
</body>
