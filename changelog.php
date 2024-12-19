<?php 
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

include 'session.php'; // Session setup and redirect if the session is not active 
// include 'http-headers.php'; // $_SERVER['HTTP_X_*'] 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head.php'; // Sets up bootstrap and other dependencies ?>
    <title><?php echo ucwords($current_system_name);?> - Changelog</title>
</head>
<body>
    <?php // dependency PHP    
    // Redirect if the user is not in the admin list in the get-config.inc.php page. - this needs to be after the "include head.php" 
    if (!in_array($_SESSION['role'], $config_admin_roles_array)) {
        header("Location: ./login.php");
        exit();
    }
    ?>

    <!-- Header and Nav -->
    <?php include 'nav.php'; ?>
    <!-- End of Header and Nav -->

    <div class="container" style="padding-top:60px">
        <h2 class="header-small">Changelog</h2>
    </div> 

    <?php 
    $changelog_start_date = isset($_GET['start-date']) ? $_GET['start-date'] : date("Y-m-d", strtotime('- 14 days'));
    $changelog_start_date_time = date('Y-m-d H:i:s', strtotime($changelog_start_date.' 00:00:00'));
    $changelog_end_date = isset($_GET['end-date']) ? date($_GET['end-date']) : date('Y-m-d');
    $changelog_end_date_time = date('Y-m-d H:i:s', strtotime($changelog_end_date.' 23:59:59'));
    $get_table = isset($_GET['table']) ? $_GET['table'] : '';
    if ($get_table == 0 || $get_table == '0' || $get_table == 'all') {
        $get_table = '';
    }
    $changelog_table = ($get_table !== '') ? " AND table_name='".$get_table."' " : '';
    $get_userid = isset($_GET['userid']) ? $_GET['userid'] : '';
    if ($get_userid == 'all') {
        $get_userid = '';
    }
    $changelog_user = $get_userid !== '' ? " AND user_id='".$get_userid."' " : '';
    ?>

    <div style="margin-top:10px;margin-bottom:10px;padding-bottom:0px">
        <form action="" method="GET" class="text-center centertable" style="max-width:max-content">
            <div class="row" style="max-width:max-content">
                <div class="col" style="max-width:max-content">
                    <div class="row align-middle">
                        <div class="col" style="max-width:max-content;margin-top:3px">
                            <label class="nav-v-c">Start Date:</label>
                        </div>
                        <div class="col" style="max-width:max-content">
                            <input class="form-control nav-v-c row-dropdown" type="date" name="start-date" value="<?php echo($changelog_start_date); ?>" style="width:max-content"/>
                        </div>
                    </div>
                </div>
                <div class="col" style="max-width:max-content">
                    <div class="row align-middle">
                        <div class="col" style="max-width:max-content;margin-top:3px">
                            <label class="nav-v-c">End Date:</label>
                        </div>
                        <div class="col" style="max-width:max-content">
                            <input class="form-control nav-v-c row-dropdown" type="date" name="end-date" value="<?php echo($changelog_end_date); ?>" style="width:max-content"/>
                        </div>
                    </div>
                </div>
                <div class="col" style="max-width:max-content">
                    <div class="row align-middle">
                        <div class="col" style="max-width:max-content;margin-top:3px">
                            <label class="nav-v-c">Table:</label>
                        </div>
                        <div class="col" style="max-width:max-content">
                            <select class="form-control nav-v-c row-dropdown" styl="max-width:max-content" name="table">
                                <option <?php if($get_table !== '' || $get_table == 0 || $get_table == 'all') { echo('selected '); }?> value="all">All</option>
                                <?php
                                $sql = "SELECT TABLE_NAME
                                        FROM information_schema.tables
                                        WHERE table_schema = '$dBName' AND TABLE_NAME != 'changelog' ORDER BY TABLE_NAME;";
                                $stmt = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                    echo("<option selected disabled>Error reaching users database</option>");
                                } else {
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    $rowCount = $result->num_rows;
                                    if ($rowCount < 1) {
                                        echo("<option selected disabled>No Tables Found...</option>");
                                    } else {
                                        while ($row = $result->fetch_assoc()) {
                                            echo('<option'); 
                                            if ($get_table == $row['TABLE_NAME']) { echo(' selected'); }
                                            echo(' value="'.$row['TABLE_NAME'].'">'.$row['TABLE_NAME'].'</option>');
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col" style="max-width:max-content">
                    <div class="row align-middle">
                        <div class="col" style="max-width:max-content;margin-top:3px">
                            <label class="nav-v-c">User:</label>
                        </div>
                        <div class="col" style="max-width:max-content">
                            <select class="form-control nav-v-c row-dropdown" styl="max-width:max-content" name="userid">
                                <option <?php if($get_userid !== '' || $get_userid == 'all') { echo('selected '); }?> value="all">All</option>
                                <?php
                                $sql = "SELECT id, username
                                        FROM users
                                        ORDER BY username;";
                                $stmt = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($stmt, $sql)) {
                                    echo("<option selected disabled>Error reaching users table</option>");
                                } else {
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);
                                    $rowCount = $result->num_rows;
                                    if ($rowCount < 1) {
                                        echo("<option selected disabled>No Users Found...</option>");
                                    } else {
                                        while ($row = $result->fetch_assoc()) {
                                            $users_id = $row['id'];
                                            $users_username = $row['username'];

                                            echo('<option'); 
                                            if ($get_userid == $users_id) { echo(' selected'); }
                                            echo(' value="'.$users_id.'">'.$users_username.'</option>');
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col" style="max-width:max-content">
                    <div class="col" style="max-width:max-content">
                        <input class="form-control btn btn-info" type="submit" value="Filter" style="width:max-content"/>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div style="margin-bottom:75px;margin-left:20px;margin-right:20px">
        <?php 
        // Pagination variables
        $results_per_page = 50;
        $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
        $start_index = ($current_page - 1) * $results_per_page;

        include 'includes/dbh.inc.php';
        $sql_pre = "SELECT * 
                FROM changelog 
                WHERE changelog.timestamp >= ? 
                    AND changelog.timestamp <= ?
                    $changelog_table
                    $changelog_user
                ORDER BY id DESC, timestamp DESC";
        $sql = $sql_pre." LIMIT ?, ?";

        $stmt_count = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt_count, $sql_pre)) {
            mysqli_stmt_bind_param($stmt_count, "ss", $changelog_start_date_time, $changelog_end_date_time);
            mysqli_stmt_execute($stmt_count);
            $result_count = mysqli_stmt_get_result($stmt_count);
            $row_count = $result_count->num_rows;
            $total_pages = ceil($row_count / $results_per_page);
        }

        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo("<p class='red'>Error reaching changelog table</p>");
        } else {
            mysqli_stmt_bind_param($stmt, "ssii", $changelog_start_date_time, $changelog_end_date_time, $start_index, $results_per_page);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $rowCount = $result->num_rows;
            if ($rowCount < 1) {
                echo("<p>No entries found.</p>");
            } else {
                ?>
                <pre hidden>
                    <?php echo($sql); ?>
                </pre>
                <p class="container" style="margin-top:40px">Entry count: <or class="green"><?php echo($row_count); ?></or></p>
                <table id="changelogTable" class="table table-dark theme-table centertable" style="max-width:max-content">
                    <thead>
                        <tr class="theme-tableOuter align-middle text-center">
                            <th>id</th>
                            <th>timestamp</th>
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
                            <tr class="align-middle text-center clickable row-show" id="log-'.$row['id'].'" onclick="toggleHidden(\''.$row['id'].'\')">
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

                            <tr class="align-middle text-center row-hide" id="log-'.$row['id'].'-view" hidden>
                                <td class="align-middle text-center" colspan=100%>
                                    <table class="centertable" style="width:100%">
                                    ');
                                    $table_name = $row['table_name'];
                                    $record_id = $row['record_id'];
                                    $user_id = $row['user_id'];

                                    $sql_user = "SELECT * FROM users WHERE id=$user_id";
                                    $stmt_user = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($stmt_user, $sql_user)) {
                                        echo("<p class='red'>Error reaching changelog table</p>");
                                    } else {
                                        mysqli_stmt_execute($stmt_user);
                                        $result_user = mysqli_stmt_get_result($stmt_user);
                                        $rowCount_user = $result_user->num_rows;
                                        if ($rowCount_user < 1) {
                                            echo("<tr><td colspan=100%>User not found</td></tr>");
                                        } else {
                                            $row_user = $result_user->fetch_assoc();
                                            $username = $row_user['username'];
                                            

                                            $sql_table = "SELECT COLUMN_NAME
                                                            FROM INFORMATION_SCHEMA.COLUMNS
                                                            WHERE TABLE_SCHEMA = '$dBName' 
                                                                AND TABLE_NAME='$table_name';";
                                            $stmt_table = mysqli_stmt_init($conn);
                                            if (!mysqli_stmt_prepare($stmt_table, $sql_table)) {
                                                echo("<p class='red'>Error reaching changelog table</p>");
                                            } else {
                                                mysqli_stmt_execute($stmt_table);
                                                $result_table = mysqli_stmt_get_result($stmt_table);
                                                $rowCount_table = $result_table->num_rows;
                                                if ($rowCount_table < 1) {
                                                    echo("<tr><td colspan=100%>Table: $table_name not found.</td></tr>");
                                                } else {
                                                    $column_names = [];
                                                    while ($row_table = $result_table->fetch_assoc()) {
                                                        $column_names[] = $row_table['COLUMN_NAME'];
                                                        
                                                    }

                                                    if (!empty($column_names) && count($column_names)>0) {
                                                        if (($record_id !=='' && (int)$record_id !== 0) || $table_name == "users") {
                                                            $sql_record = "SELECT * FROM $table_name WHERE id=$record_id";
                                                            $stmt_record = mysqli_stmt_init($conn);
                                                            if (!mysqli_stmt_prepare($stmt_record, $sql_record)) {
                                                                echo("<p class='red'>Error reaching changelog table</p>");
                                                            } else {
                                                                mysqli_stmt_execute($stmt_record);
                                                                $result_record = mysqli_stmt_get_result($stmt_record);
                                                                $rowCount_record = $result_record->num_rows;
                                                                if ($rowCount_record < 1) {
                                                                    echo("<tr><td colspan=100%>Record not found.</td></tr>");
                                                                } else {
                                                                    $row_record = $result_record->fetch_assoc();

                                                                    echo('
                                                                        <thead>
                                                                            <tr class="align-middle text-center">
                                                                            ');
                                                                            foreach($column_names as $column) {
                                                                                echo('<th>'.$column.'</th>');
                                                                            }
                                                                            echo('
                                                                            </tr>
                                                                        </thead>
                                                                    ');
                                                                    echo('
                                                                        <tbody>
                                                                            <tr class="align-middle text-center">
                                                                            ');
                                                                            foreach($column_names as $column2) {
                                                                                if ($column2 == 'stock_id') {
                                                                                    echo('<td><a class="link" href="stock.php?stock_id='.$row_record[$column2].'">'.$row_record[$column2].'</a></td>');
                                                                                } else {
                                                                                    echo('<td>'.$row_record[$column2].'</td>');
                                                                                }
                                                                            }
                                                                            echo('
                                                                            </tr>
                                                                        </tbody>
                                                                    ');
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }                                          
                                        }
                                    }
                                    echo('  
                                    </table>
                                </td>
                            </tr>
                            ');
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            }
        }
        if ($total_pages !== NULL && $total_pages > 1) {
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
                echo ('</div>');
            } else {
                echo ('
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
                            <tr>
                        </tbody>
                    </table>        
                </form>
                ');
            }
        }

        ?>
    </div>

    <!-- Add the JS for the file -->
    <script src="assets/js/changelog.js"></script>

    <?php include 'foot.php'; ?>

</body>
