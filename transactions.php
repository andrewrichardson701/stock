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

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Transactions</title>
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
                $stock_id = htmlspecialchars($_GET['stock_id']);
            } else {
                if (isset($_GET['modify'])) {
                    echo('<div class="container" style="padding-top:25px"><p class="red">Non-numeric Stock ID: <or class="blue">'.$_GET['stock_id'].'</or>.<br>Please check the URL or <a class="link" onclick="navPage(updateQueryParameter(\'\', \'stock_id\', 0))">add new stock item</a>.</p></div>');
                    exit();
                } else {
                    echo('<div class="container" style="padding-top:25px"><p class="red">Non-numeric Stock ID: <or class="blue">'.$_GET['stock_id'].'</or>.<br>Please check the URL or go back to the <a class="link" href="./">home page</a>.</p></div>');
                    exit();
                }
                
            }
        } else {
            header("Location: ./?error=noStockSelected");
            exit();
        }
        if (isset($_GET['modify'])) {
            $stock_modify = htmlspecialchars($_GET['modify']);
        }
        if (!isset($_SERVER['HTTP_REFERER'])) {
            $_SERVER['HTTP_REFERER'] = './index.php';
        }
    if (isset($_GET['stock_id'])) {
        if (is_numeric($_GET['stock_id'])) {
            if ($_GET['stock_id'] !== '') {
                $stock_id = htmlspecialchars($_GET['stock_id']);
                $currency_symbol = $config_currency;

                include 'includes/dbh.inc.php';

                $sql_stock = "SELECT * FROM stock WHERE id=$stock_id";
                $stmt_stock = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt_stock, $sql_stock)) {
                    echo("ERROR getting entries");
                } else {
                    mysqli_stmt_execute($stmt_stock);
                    $result_stock = mysqli_stmt_get_result($stmt_stock);
                    $rowCount_stock = $result_stock->num_rows;
                    if ($rowCount_stock < 1) {
                        echo ("No Stock found for id: $current_default_stock_id");

                    } else {
                        $row_stock = $result_stock->fetch_assoc();
                        $stock_name = $row_stock['name'];
                        $stock_is_cable = $row_stock['is_cable'];

                        echo('<div class="container" style="padding-bottom:25px">
                            <h2 class="header-small" style="padding-bottom:5px">Transactions - <a class="link" href="../stock.php?stock_id='.$_GET['stock_id'].'">'.$stock_name.'</a> - Stock ID: '.$_GET['stock_id']); if ($stock_is_cable == 1) { echo(' (cable)'); } echo('</h2>
                            </div>');

                        // Pagination variables
                        $results_per_page = 100;
                        $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
                        $start_index = ($current_page - 1) * $results_per_page;
                        
                        if ($stock_is_cable !== 1) { // normal stock

                            $sql_tran_init = "SELECT t.id AS t_id, t.stock_id AS t_stock_id, t.item_id AS t_item_id, t.type AS t_type, t.quantity AS t_quantity,
                                                t.price AS t_price, t.serial_number AS t_serial_number, t.reason AS t_reason, t.comments AS t_comments,
                                            t.date AS t_date, t.time AS t_time, t.username as t_username,
                                            s.name AS s_name, a.name AS a_name
                                        FROM transaction AS t
                                        LEFT JOIN item AS i ON t.item_id=i.id
                                        LEFT JOIN shelf AS s ON i.shelf_id=s.id
                                        LEFT JOIN area AS a ON s.area_id=a.id
                                        WHERE t.stock_id=?
                                        ORDER BY t_date DESC , t_time DESC, t_quantity DESC";
                            $sql_tran_add = " LIMIT ?, ?";
                            $sql_tran = $sql_tran_init.$sql_tran_add;
                            $stmt_tran = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_tran, $sql_tran)) {
                                echo("ERROR getting entries");
                            } else {
                                mysqli_stmt_bind_param($stmt_tran, "sii", $stock_id, $start_index, $results_per_page);
                                mysqli_stmt_execute($stmt_tran);
                                $result_tran = mysqli_stmt_get_result($stmt_tran);
                                $rowCount_tran = $result_tran->num_rows;
                                if ($rowCount_tran < 1) {
                                    echo ("No Transactions.");
                                    exit();
                                } else {
                                    echo('
                                    <table class="table table-dark theme-table centertable" id="transactions" style="max-width:max-content">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th hidden>ID</th>
                                                <th hidden>Stock ID</th>
                                                <th hidden>Item ID</th>
                                                <th>Type</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Location</th>
                                                <th>Shelf</th>
                                                <th>Username</th>
                                                <th>Quantity</th>
                                                <th'); if($current_cost_enable_normal == 0) {echo(' hidden');} echo('>Price</th>
                                                <th>Serial Number</th>
                                                <th hidden>Comments</th>
                                                <th>Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                        
                                    ');
                                    while( $row_tran = $result_tran->fetch_assoc() ) {
                                        $t_id = $row_tran['t_id'];
                                        $t_stock_id = $row_tran['t_stock_id'];
                                        $t_item_id = $row_tran['t_item_id'];
                                        $t_type = $row_tran['t_type'];
                                        $t_quantity = $row_tran['t_quantity'];
                                        $t_price = $row_tran['t_price'];
                                        if ($t_price == '' || $t_price == null) { $t_price = 0;}
                                        $t_serial_number = $row_tran['t_serial_number'];
                                        $t_reason = $row_tran['t_reason'];
                                        $t_comments = $row_tran['t_comments'];
                                        $t_date = $row_tran['t_date'];
                                        $t_time = $row_tran['t_time'];
                                        $t_username = $row_tran['t_username'];
                                        $s_name = $row_tran['s_name'];
                                        $a_name = $row_tran['a_name'];
                                        switch ($t_type) {
                                            case 'add':
                                                $t_type_color = 'transactionAdd';
                                                break;
                                            case 'remove':
                                                $t_type_color = 'transactionRemove';
                                                break;
                                            case 'delete':
                                                $t_type_color = 'transactionDelete';
                                                break;
                                            case 'restore':
                                                $t_type_color = 'transactionDelete';
                                                break;
                                            case 'move':
                                                $t_type_color = 'transactionMove';
                                                break;
                                            default:
                                                $t_type_color = '';
                                        }
                                            echo('
                                            <tr class="'.$t_type_color.'">
                                                <td id="t_id" hidden>'.$t_id.'</td>
                                                <td hidden>'.$t_stock_id.'</td>
                                                <td hidden>'.$t_item_id.'</td>
                                                <td id="t_type">'.ucwords($t_type).'</td>
                                                <td id="t_date">'.$t_date.'</td>
                                                <td id="t_time">'.$t_time.'</td>
                                                <td id="a_name">'.$a_name.'</td>
                                                <td id="s_name">'.$s_name.'</td>
                                                <td id="t_username">'.$t_username.'</td>
                                                <td id="t_quantity">'.$t_quantity.'</td>
                                                <td'); if($current_cost_enable_normal == 0) {echo(' hidden');} echo('>'.$currency_symbol.$t_price.'</td>
                                                <td>'.$t_serial_number.'</td>
                                                <td hidden>'.$t_comments.'</td>
                                                <td id="t_reason">'.$t_reason.'</td>
                                            </tr>
                                            ');

                                    }
                                    
                                    echo('</tbody>
                                    </table>');
                                } 
                            }
                        } elseif ($stock_is_cable == 1) { // cable stock

                            $sql_tran_init = "SELECT t.id AS t_id, t.stock_id AS t_stock_id, t.item_id AS t_item_id, t.type AS t_type, t.quantity AS t_quantity,
                                                t.reason AS t_reason,
                                            t.date AS t_date, t.time AS t_time, t.username as t_username,
                                            s.name AS s_name, a.name AS a_name
                                        FROM cable_transaction AS t
                                        LEFT JOIN cable_item AS i ON t.item_id=i.id
                                        LEFT JOIN shelf AS s ON i.shelf_id=s.id
                                        LEFT JOIN area AS a ON s.area_id=a.id
                                        WHERE t.stock_id=?
                                        ORDER BY t_date DESC , t_time DESC, t_quantity DESC";
                            $sql_tran_add = " LIMIT ?, ?";
                            $sql_tran = $sql_tran_init.$sql_tran_add;
                            $stmt_tran = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt_tran, $sql_tran)) {
                                echo("ERROR getting entries");
                            } else {
                                mysqli_stmt_bind_param($stmt_tran, "sii", $stock_id, $start_index, $results_per_page);
                                mysqli_stmt_execute($stmt_tran);
                                $result_tran = mysqli_stmt_get_result($stmt_tran);
                                $rowCount_tran = $result_tran->num_rows;
                                if ($rowCount_tran < 1) {
                                    echo ("No Transactions.");
                                    exit();
                                } else {
                                    echo('
                                    <table class="table table-dark theme-table centertable" id="transactions" style="max-width:max-content">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th hidden>ID</th>
                                                <th hidden>Stock ID</th>
                                                <th hidden>Item ID</th>
                                                <th>Type</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Location</th>
                                                <th>Shelf</th>
                                                <th>Username</th>
                                                <th>Quantity</th>
                                                <th>Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                        
                                    ');
                                    while( $row_tran = $result_tran->fetch_assoc() ) {
                                        $t_id = $row_tran['t_id'];
                                        $t_stock_id = $row_tran['t_stock_id'];
                                        $t_item_id = $row_tran['t_item_id'];
                                        $t_type = $row_tran['t_type'];
                                        $t_quantity = $row_tran['t_quantity'];
                                        $t_reason = $row_tran['t_reason'];
                                        $t_date = $row_tran['t_date'];
                                        $t_time = $row_tran['t_time'];
                                        $t_username = $row_tran['t_username'];
                                        $s_name = $row_tran['s_name'];
                                        $a_name = $row_tran['a_name'];
                                        switch ($t_type) {
                                            case 'add':
                                                $t_type_color = 'transactionAdd';
                                                break;
                                            case 'remove':
                                                $t_type_color = 'transactionRemove';
                                                break;
                                            case 'delete':
                                                $t_type_color = 'transactionDelete';
                                                break;
                                            case 'move':
                                                $t_type_color = 'transactionMove';
                                                break;
                                            default:
                                                $t_type_color = '';
                                        }
                                            echo('
                                            <tr class="'.$t_type_color.'">
                                                <td id="t_id" hidden>'.$t_id.'</td>
                                                <td hidden>'.$t_stock_id.'</td>
                                                <td hidden>'.$t_item_id.'</td>
                                                <td id="t_type">'.ucwords($t_type).'</td>
                                                <td id="t_date">'.$t_date.'</td>
                                                <td id="t_time">'.$t_time.'</td>
                                                <td id="a_name">'.$a_name.'</td>
                                                <td id="s_name">'.$s_name.'</td>
                                                <td id="t_username">'.$t_username.'</td>
                                                <td id="t_quantity">'.$t_quantity.'</td>
                                                <td id="t_reason">'.$t_reason.'</td>
                                            </tr>
                                            ');

                                    }
                                    echo('</tbody>
                                    </table>');
                                } 
                            }       
                        }

                        $stmt_count = mysqli_stmt_init($conn);
                        if (mysqli_stmt_prepare($stmt_count, $sql_tran_init)) {
                            mysqli_stmt_bind_param($stmt_count, "s", $stock_id);
                            mysqli_stmt_execute($stmt_count);
                            $result_count = mysqli_stmt_get_result($stmt_count);
                            $row_count = $result_count->num_rows;
                            $total_pages = ceil($row_count / $results_per_page);
                            
                            if ( $total_pages > 1 && $total_pages <= 15){
                                echo '<div class="container" style="text-align: center;">';
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
                                echo '</div>';
                            } else {
                                echo ('
                                <div class="container" style="text-align: center;">
                                    <form style="margin-bottom:0px">
                                        <table class="centertable">
                                            <tbody>
                                                <tr>
                                                    <td style="padding-right:10px">Page:</td>
                                                    <td style="padding-right:10px">
                                                        <select id="page-select" class="form-control row-dropdown" style="width:50px;height:25px; padding:0px" onchange="navPage(updateQueryParameter(\'\', \'page\', document.getElementById(\'page-select\').value + \'#transactions\'))" name="page">');
                                                        for ($i = 1; $i <= $total_pages; $i++) {
                                                            echo('<option value="'.$i.'"'); if ($i == $current_page) { echo(' selected'); } echo('>'.$i.'</option>');
                                                        }
                                                        echo('
                                                        </select>
                                                    </td>
                                            </tbody>
                                        </table>        
                                    </form>
                                </div>
                                ');
                            }
                        }
                    }
                }

                
            } else {
                echo ("error = id empty");
            }
        } else {
            echo ("error = non-numeric id");
        }
    }
    ?>
</div>
    
<?php include 'foot.php'; ?>

</body>