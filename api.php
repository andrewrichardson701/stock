<?php

$sql = $sql_main = $sql_where = $sql_groupby = $sql_orderby = $sql_orderdir = '';

$api_keys = ["ABCDEFG" , "123456"];

header("Content-Type: application/json");
include 'includes/dbh.inc.php';

// do the SQL tings.
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dBName", $dBUsername, $dBPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// GET / POST / PUT / DELETE
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

checkAPIkey(); // check if API key is correct, if not exit.

$tables = getTables();

checkQuery(); // check for valid queries
checkParams(); // check extra params, e.g filters and order

buildSQL(); // put the SQL pieces together

//print_r($sql);
//exit();

submitMethod($method);


function buildSQL() {
    global $sql, $sql_main, $sql_where, $sql_groupby, $sql_orderby, $sql_orderdir;

    $sql = "$sql_main$sql_where$sql_groupby$sql_orderby$sql_orderdir";
}

function getTables() {
    $sql = "SHOW TABLES";

    $result = basicSQL($sql);

    $final = array();
    foreach ($result as $table) {
        foreach ($table as $t) {
            $final[] = $t;
        }
    }   
    return $final;
}

function basicSQL($sql) {
    global $pdo, $dBName;

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getFields() {
    global $tables;
    if (isset($_REQUEST['q']) && in_array($_REQUEST['q'], $tables)) { // if the query is set and the query is a valid table
        // get fields from mysql
        $table = $_REQUEST['q'];

        $sql = "SELECT distinct COLUMN_NAME 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_NAME = '$table'";
        
        $result = basicSQL($sql);
        $final = array();
        foreach ($result as $table) {
            foreach ($table as $t) {
                $final[] = $t;
            }
        }   
        return $final;
    } else {
        return false;
    }
}

function checkParams() {
    global $sql, $sql_main, $sql_where, $sql_groupby, $sql_orderby, $sql_orderdir;

    if (isset($_REQUEST['q'])) {
        $where_t = '=';
        $where_t_l = '';
        $where_t_r = '';
        if (isset($_REQUEST['t'])) { // search type e.g. LIKE or  = 
            $t = $_REQUEST['t'];

            switch ($t) {
                case 'like':
                case 'LIKE':
                    $where_t = 'LIKE';
                    $where_t_l = $where_t_r = '%';
                    break;
                case 'equal':
                case 'EQUAL':
                    $where_t = '=';
                    $where_t_l = '';
                    $where_t_r = '';
                    break;
                default:
                    $where_t = '=';
                    $where_t_l = '';
                    $where_t_r = '';
                    break;
            }
        }
        $fields = getFields();
        if ($fields) { // if there are valid fields for the q (it is a table)
            foreach (array_keys($_REQUEST) as $R) {
                $V = $_REQUEST[$R];
                if (in_array($R, $fields)) {
                    if (str_contains($sql_where, 'WHERE')) {
                        $sql_where .= " AND ".$R." $where_t '$where_t_l$V$where_t_r'";
                    } else {
                        $sql_where .= " WHERE ".$R." $where_t '$where_t_l$V$where_t_r'";
                    }
                } elseif ($R == 'orderby') {
                    if (in_array($V, $fields)) {
                        if (str_contains($sql_orderby, 'ORDER BY')) {
                            $sql_orderby .= ", $V'";
                        } else {
                            $sql_orderby .= " ORDER BY $V";
                        }
                    }
                } elseif ($R == 'orderdir') {
                    if (isset($sql_orderby) && $sql_orderby !== '') {
                        switch ($V) {
                            case 'ASC':
                            case 'asc':
                            case 'A':
                            case 'a':
                                $sql_orderdir = ' ASC';
                                break;
                            case 'DESC':
                            case 'desc':
                            case 'D':
                            case 'd':
                                $sql_orderdir = ' DESC';
                                break;
                            default:
                                $sql_orderdir = ' ASC';
                                break;
                        }
                    }
                }             
            }
        } 
    }

}


function checkQuery() {
    global $sql, $sql_main, $sql_where, $sql_groupby, $sql_orderby, $sql_orderdir;

    $tables = getTables(); // get list of tables from SQL

    if (empty($_REQUEST['q'])) {
        echo("Usage: \n");
        foreach ($tables as $table) {
            $url = basename(__FILE__)."?".$_SERVER['QUERY_STRING'];
            echo (" $url&q=$table\n");
        }
        echo ("\n");
        echo ("Other usage:\n");
        echo (" $url&q=inventory&site=1&area=1&shelf=1\n");
        echo ("\n");
        echo ("Params:\n");
        echo(" ?api_key = API Key for authentication (?api_key=abc123)\n");
        echo(" &q = query (&q=stock &t=users &t=inventory)\n");
        echo(" &t = search type (&t=like &t=equal)\n");
        echo(" &orderby = order results (&orderby=name &orderby=id &orderby=sku)\n");
        echo(" &orderdir = order direction (&orderdir=ASC &orderdir=A &orderdir=asc &orderdir=a &orderdir=DESC &orderdir=D &orderdir=desc &orderdir=d)\n");
        exit();
    } else {
        $q = $_REQUEST['q'];
        switch ($q) {
            case in_array($q, $tables):
                if ($q == 'users') {
                    $sql_main = "SELECT id, username, first_name, last_name, email, auth, role_id, enabled, password_expired, theme_id, card_primary, card_secondary, 2fa_secret, 2fa_enabled FROM $q";
                } else {
                    $sql_main = "SELECT * FROM $q";
                }
                break;
            case 'inventory':
                $sql_main = "WITH QuantityCTE AS (
                            SELECT
                                stock_id,
                                site_id,
                                SUM(item_quantity) AS total_item_quantity
                            FROM (
                                SELECT
                                    item.stock_id,
                                    area.site_id,
                                    area.id AS area_id_global,
                                    SUM(quantity) AS item_quantity
                                FROM
                                    item
                                    INNER JOIN shelf ON item.shelf_id = shelf.id
                                    INNER JOIN area ON shelf.area_id = area.id
                                WHERE
                                    item.deleted = 0
                                GROUP BY
                                    item.stock_id, area.site_id, area.id
                            ) AS Subquery
                            GROUP BY
                                stock_id, site_id
                        )
                        SELECT
                            stock.id AS stock_id,
                            stock.name AS stock_name,
                            stock.description AS stock_description,
                            stock.sku AS stock_sku,
                            stock.min_stock AS stock_min_stock,
                            stock.is_cable AS stock_is_cable,
                            GROUP_CONCAT(DISTINCT area.name SEPARATOR ', ') AS area_names,
                            site.id AS site_id,
                            site.name AS site_name,
                            site.description AS site_description,
                            COALESCE(cte.total_item_quantity, 0) AS item_quantity,
                            tag_names.tag_names AS tag_names,
                            tag_ids.tag_ids AS tag_ids,
                            stock_img_image.stock_img_image
                        FROM
                            stock
                            LEFT JOIN item ON stock.id = item.stock_id
                            LEFT JOIN shelf ON item.shelf_id = shelf.id
                            LEFT JOIN area ON shelf.area_id = area.id
                            LEFT JOIN site ON area.site_id = site.id
                            LEFT JOIN manufacturer ON item.manufacturer_id = manufacturer.id
                            LEFT JOIN (
                                SELECT
                                    stock_img.stock_id,
                                    MIN(stock_img.image) AS stock_img_image
                                FROM
                                    stock_img
                                GROUP BY
                                    stock_img.stock_id
                            ) AS stock_img_image ON stock_img_image.stock_id = stock.id
                            LEFT JOIN (
                                SELECT
                                    stock_tag.stock_id,
                                    GROUP_CONCAT(DISTINCT tag.name SEPARATOR ', ') AS tag_names
                                FROM
                                    stock_tag
                                    INNER JOIN tag ON stock_tag.tag_id = tag.id
                                GROUP BY
                                    stock_tag.stock_id
                            ) AS tag_names ON tag_names.stock_id = stock.id
                            LEFT JOIN (
                                SELECT
                                    stock_tag.stock_id,
                                    GROUP_CONCAT(DISTINCT tag_id SEPARATOR ', ') AS tag_ids
                                FROM
                                    stock_tag
                                GROUP BY
                                    stock_tag.stock_id
                            ) AS tag_ids ON tag_ids.stock_id = stock.id
                            LEFT JOIN QuantityCTE cte ON stock.id = cte.stock_id AND site.id = cte.site_id";
                $sql_where = " WHERE
                            stock.is_cable = 0
                            AND stock.deleted = 0 AND item.deleted = 0 AND item.deleted = 0";
                if (isset($_REQUEST['site']) && is_numeric($_REQUEST['site'])) {
                    $site = $_REQUEST['site'];
                    $sql_where .= " AND site.id = $site";
                }
                if (isset($_REQUEST['area']) && is_numeric($_REQUEST['area'])) {
                    $area = $_REQUEST['area'];
                    $sql_where .= " AND area.id = $area";
                }
                if (isset($_REQUEST['shelf']) && is_numeric($_REQUEST['shelf'])) {
                    $shelf = $_REQUEST['shelf'];
                    $sql_where .= " AND shelf.id = $shelf";
                }

                $sql_groupby .= " GROUP BY
                            stock.id, stock_name, stock_description, stock_sku, stock_min_stock, stock_is_cable,
                            site_id, site_name, site_description, stock_img_image.stock_img_image ";
                $sql_orderby = " ORDER BY stock.id ";
                $sql_orderdir = " ASC";
                break;
            default:
                $sql = $sql_where = $sql_groupby = $sql_orderby = $sql_orderdir = '';
                break;
        }
    }
}

function submitMethod($method) {
    global $pdo;

    switch ($method) {
        case 'GET':
            handleGet($pdo);
            break;
        case 'POST':
            // handlePost($pdo, $input);
            break;
        case 'PUT':
            // handlePut($pdo, $input);
            break;
        case 'DELETE':
            // handleDelete($pdo, $input);
            break;
        default:
            echo json_encode(['message' => 'Invalid request method']);
            break;
    }
}
 
function checkAPIkey() {
    global $api_keys;
    if (isset($_REQUEST['api_key'])) {
        $key = $_REQUEST['api_key'];
        if (!in_array($key, $api_keys)) {
            echo ("Unauthorized.");
            exit();
        }
    } else {
        echo("API key missing. \nUsage: ".basename(__FILE__)."?api_key=[API_KEY]");
        exit();
    }
}



function handleGet($pdo) {
    global $sql;
    if ($sql == '') { 
        echo 'Invalid SQL';
        exit();
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
}

function handlePost($pdo, $input) {
    $sql = "INSERT INTO users (name, email) VALUES (:name, :email)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['name' => $input['name'], 'email' => $input['email']]);
    echo json_encode(['message' => 'User created successfully']);
}

function handlePut($pdo, $input) {
    $sql = "UPDATE users SET name = :name, email = :email WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['name' => $input['name'], 'email' => $input['email'], 'id' => $input['id']]);
    echo json_encode(['message' => 'User updated successfully']);
}

function handleDelete($pdo, $input) {
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $input['id']]);
    echo json_encode(['message' => 'User deleted successfully']);
}

?>