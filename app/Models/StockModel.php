<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GeneralModel;
use Illuminate\Support\Facades\DB;

class StockModel extends Model
{
    //
    static public function getStockAjax($request, $limit, $offset)
    {
        $oos = isset($request['oos']) ? (int)$request['oos'] : 0;
        $site = isset($request['site']) ? $request['site'] : "0";
        $area = isset($request['area']) && !empty($request['area']) ? $request['area'] : "0";
        $name = isset($request['name']) ? $request['name'] : "";
        $sku = isset($request['sku']) ? $request['sku'] : "";
        $location = isset($request['location']) ? $request['location'] : "";
        $shelf = isset($request['shelf']) ? $request['shelf'] : "";
        $tag = isset($request['tag']) ? $request['tag'] : "";
        $manufacturer = isset($request['manufacturer']) ? $request['manufacturer'] : "";

        // Confirm the site and area are a match
        if (($site !== "0" && $site !== '' && $site !== 0) && ($area !== "0" && $area !== '' && $area !== 0)) {
            if (GeneralModel::checkAreaSiteMatch($area, $site) == 0) {
                $area = 0;
            }
        }

        $instance = new self();
        $instance->setTable('stock');

        // Define the subquery for calculating item quantities (CTE equivalent)
        $quantityCTE = DB::table('item')
            ->select([
                'item.stock_id',
                'area.site_id',
                DB::raw('SUM(quantity) AS total_item_quantity'),
            ])
            ->join('shelf', 'item.shelf_id', '=', 'shelf.id')
            ->join('area', 'shelf.area_id', '=', 'area.id')
            ->where('item.deleted', 0)
            ->groupBy('item.stock_id', 'area.site_id');

        // Main query
        $query = $instance->select([
                'stock.id AS stock_id',
                'stock.name AS stock_name',
                'stock.description AS stock_description',
                'stock.sku AS stock_sku',
                'stock.min_stock AS stock_min_stock',
                'stock.is_cable AS stock_is_cable',
                DB::raw("GROUP_CONCAT(DISTINCT area.name SEPARATOR ', ') AS area_names"),
                'site.id AS site_id',
                'site.name AS site_name',
                'site.description AS site_description',
                DB::raw('COALESCE(quantity_cte.total_item_quantity, 0) AS item_quantity'),
                'tag_names.tag_names AS tag_names',
                'tag_ids.tag_ids AS tag_ids',
                'stock_img_image.stock_img_image',
            ])
            // ->distinct()
            ->leftJoin('item', 'stock.id', '=', 'item.stock_id')
            ->leftJoin('shelf', 'item.shelf_id', '=', 'shelf.id')
            ->leftJoin('area', 'shelf.area_id', '=', 'area.id')
            ->leftJoin('site', 'area.site_id', '=', 'site.id')
            ->leftJoin('manufacturer', 'item.manufacturer_id', '=', 'manufacturer.id')
            ->leftJoinSub(
                DB::table('stock_img')
                    ->select(['stock_id', DB::raw('MIN(image) AS stock_img_image')])
                    ->groupBy('stock_id'),
                'stock_img_image',
                'stock_img_image.stock_id',
                '=',
                'stock.id'
            )
            ->leftJoinSub(
                DB::table('stock_tag')
                    ->join('tag', 'stock_tag.tag_id', '=', 'tag.id')
                    ->select(['stock_tag.stock_id', DB::raw("GROUP_CONCAT(DISTINCT tag.name SEPARATOR ', ') AS tag_names")])
                    ->groupBy('stock_tag.stock_id'),
                'tag_names',
                'tag_names.stock_id',
                '=',
                'stock.id'
            )
            ->leftJoinSub(
                DB::table('stock_tag')
                    ->select(['stock_tag.stock_id', DB::raw("GROUP_CONCAT(DISTINCT tag_id SEPARATOR ', ') AS tag_ids")])
                    ->groupBy('stock_tag.stock_id'),
                'tag_ids',
                'tag_ids.stock_id',
                '=',
                'stock.id'
            )
            ->leftJoinSub($quantityCTE, 'quantity_cte', function ($join) {
                $join->on('stock.id', '=', 'quantity_cte.stock_id')
                    ->on('site.id', '=', 'quantity_cte.site_id');
            })
            ->where('stock.is_cable', 0)
            ->where('stock.deleted', 0)
            ->when($oos === 0, function ($query) {
                $query->where('item.deleted', 0);
            })
            ->when($site !== '0', function ($query) use ($site) {
                $query->where('site.id', $site);
            })
            ->when($area !== '0', function ($query) use ($area) {
                $query->where('area.id', $area);
            })
            ->when(!empty($name), function ($query) use ($name) {
                $query->where(function ($subQuery) use ($name) {
                    $subQuery->whereRaw("MATCH(stock.name) AGAINST (? IN NATURAL LANGUAGE MODE)", [$name])
                            ->orWhereRaw("MATCH(stock.description) AGAINST (? IN NATURAL LANGUAGE MODE)", [$name])
                            ->orWhere('stock.name', 'LIKE', "%{$name}%");
                });
            })
            ->when(!empty($sku), function ($query) use ($sku) {
                $query->where('stock.sku', 'LIKE', "%{$sku}%");
            })
            ->when(!empty($location), function ($query) use ($location) {
                $query->where('area.name', 'LIKE', "%{$location}%");
            })
            ->when(!empty($shelf), function ($query) use ($shelf) {
                $query->where('shelf.name', 'LIKE', "%{$shelf}%");
            })
            ->when(!empty($tag), function ($query) use ($tag) {
                $query->where('tag_names', 'LIKE', "%{$tag}%");
            })
            ->when(!empty($manufacturer), function ($query) use ($manufacturer) {
                $query->where('manufacturer.name', $manufacturer);
            })
            ->when($oos === 1, function ($query) {
                $query->havingRaw('item_quantity IS NULL OR item_quantity = 0');
            })
            ->when($limit !== 0, function ($query) use ($limit) {
                $query->limit($limit);
            })
            ->when($offset > 0, function ($query) use ($offset) {
                $query->offset($offset);
            })
            ->groupBy([
                'stock.id', 'stock.name', 'stock.description', 'stock.sku',
                'stock.min_stock', 'stock.is_cable', 'site.id', 'site.name',
                'site.description', 'stock_img_image.stock_img_image', 'quantity_cte.total_item_quantity',
            ])
            ->when($area != 0, function ($query) {
                $query->groupBy('area.id');
            })
            ->orderBy('stock.name');

        return [
            'query' => $query,
            'data' => [
                'site' => $site,
                'area' => $area,
                'shelf' => $shelf,
                'name' => $name,
                'sku' => $sku,
                'tag' => $tag,
                'location' => $location,
                'manufacturer' => $manufacturer,
                'oos' => $oos,
            ],
        ];
    }

    static public function returnStockAjax($request) 
    {
        $results = []; // to return
        if (isset($request['request-inventory']) && $request['request-inventory'] == 1) {
            $all_rows_data = StockModel::getStockAjax($request, 0, -1);
            if ($all_rows_data == null) {
                return null;
            }
            $all_rows_count = count($all_rows_data['query']->get()->toArray());

            if (isset($request['rows'])) {
                if ($request['rows'] == 50 || $request['rows'] == 100) {
                    $results_per_page = htmlspecialchars($request['rows']);
                } else {
                    $results_per_page = 10;
                }
            } else {
                $results_per_page = 10;
            }

            $total_pages = ceil($all_rows_count / $results_per_page);

            $current_page = isset($request['page']) ? intval($request['page']) : 1;
            if ($current_page < 1) {
                $current_page = 1;
            } elseif ($current_page > $total_pages) {
                $current_page = $total_pages;
            } 

            $offset = ($current_page - 1) * $results_per_page;
            if ($offset < 0) {
                $offset = $results_per_page;
            }

            $requested_rows_data = StockModel::getStockAjax($request, $results_per_page, $offset);
            $requested_rows_array = $requested_rows_data['query']->get()->toArray();

            $page_number_area = StockModel::getPageNumberArea($total_pages, $current_page);
                
            $results[-1]['site'] = $site = $requested_rows_data['data']['site'];
            $results[-1]['area'] = $area = $requested_rows_data['data']['area'];
            $results[-1]['shelf'] = $shelf = $requested_rows_data['data']['shelf'];
            $results[-1]['name'] = $name = $requested_rows_data['data']['name'];
            $results[-1]['sku'] = $sku = $requested_rows_data['data']['sku'];
            $results[-1]['location'] = $location = $requested_rows_data['data']['location'];
            $results[-1]['tag'] = $tag = $requested_rows_data['data']['tag'];
            $results[-1]['manufacturer'] = $manufacturer = $requested_rows_data['data']['manufacturer'];
            $results[-1]['total-pages'] = $total_pages;
            $results[-1]['page-number-area'] = $page_number_area;
            $results[-1]['page'] = $page = $current_page;
            $results[-1]['rows'] = $rows = $results_per_page;
            $results[-1]['oos'] = $oos = $requested_rows_data['data']['oos'];
            $results[-1]['url'] = "./?oos=$oos&site=$site&area=$area&name=$name&sku=$sku&shelf=$shelf&manufacturer=$manufacturer&tag=$tag&page=$page&rows=$rows";
            $results[-1]['sql'] = GeneralModel::interpolatedQuery($requested_rows_data['query']->toSql(),$requested_rows_data['query']->getBindings());
            $results[-1]['areas'] = GeneralModel::allDistinctAreas($site, 0);
            $results[-1]['query_data'] = $all_rows_data['query']->get()->toArray();

            $img_directory = 'img/stock/';

            if (count($requested_rows_array) < 1) {
                $result = "<tr><td colspan=100%>No Inventory Found</td></tr>";
                $results[] = $result;
            } else {
                foreach ($requested_rows_array as $row) {
                    $stock_id = $row['stock_id'];
                    $stock_img_file_name = $row['stock_img_image'];
                    $stock_name = $row['stock_name'];
                    $stock_sku = $row['stock_sku'];
                    $stock_quantity_total = $row['item_quantity'];
                    $stock_locations = $row['area_names'];
                    $stock_site_id = $row['site_id'];
                    $stock_site_name = $row['site_name'];
                    $stock_tag_names = ($row['tag_names'] !== null) ? explode(", ", $row['tag_names']) : '---';
                    

                    // Echo each row (inside of SQL results)

                    $result =
                    '<tr class="vertical-align align-middle highlight" id="'.$stock_id.'">
                        <td class="align-middle" id="'.$stock_id.'-id" hidden>'.$stock_id.'</td>
                        <td class="align-middle" id="'.$stock_id.'-img-td">
                        ';
                    if (!is_null($stock_img_file_name)) {
                        $result .= '<img id="'.$stock_id.'-img" class="inv-img-main thumb" src="'.$img_directory.$stock_img_file_name.'" alt="'.$stock_name.'" onclick="modalLoad(this)" />';
                    }
                    $result .= '</td>
                        <td class="align-middle gold" id="'.$stock_id.'-name" style="white-space:wrap"><a class="link" href="stock?stock_id='.$stock_id.'">'.$stock_name.'</a></td>
                        <td class="align-middle viewport-large-empty" id="'.$stock_id.'-sku">'.$stock_sku.'</td>
                        <td class="align-middle" id="'.$stock_id.'-quantity">'; 
                    if ($stock_quantity_total == 0) {
                        $result .= '<or class="red" title="Out of Stock">0 <i class="fa fa-warning" /></or>';
                    } else {
                        $result .= $stock_quantity_total;
                    }
                    $result .= '</td>';
                    if ($site == 0) { $result .= '<td class="align-middle link gold" style="white-space: nowrap !important;"id="'.$stock_id.'-site" onclick="navPage(updateQueryParameter(\'\', \'site\', \''.$stock_site_id.'\'))">'.$stock_site_name.'</td>'; }
                    $result .= '</td>
                    <td class="align-middle" id="'.$stock_id.'-location">'.$stock_locations.'</td>
                    ';
                    $result .= '<td class="align-middle viewport-large-empty" style="white-space: wrap" id="'.$stock_id.'-tag">';
                    if (is_array($stock_tag_names)) {
                        for ($o=0; $o < count($stock_tag_names); $o++) {
                            $divider = $o < count($stock_tag_names)-1 ? ', ' : '';
                            $result .= '<or class="gold link" onclick="navPage(updateQueryParameter(\'\', \'tag\', \''.$stock_tag_names[$o].'\'))">'.$stock_tag_names[$o].'</or>'.$divider;
                        }
                    } 
                    $result .= '</tr>';
                    
                    $results[] = $result;
                }
            }
        } else {
            $result = null;
        }

        return $results;
    }

    static public function getPageNumberArea($total_pages, $current_page) 
    {
        $pageNumberArea = '';

        if ($total_pages > 1) {
            if ($current_page > 1) {
                $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page - 1).'\') + \'\')"><</or>';
            }
            if ($total_pages > 5) {
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $current_page) {
                        $pageNumberArea .= '<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>';
                        // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                    } elseif ($i == 1 && $current_page > 5) {
                        $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or><or style="padding-left:5px;padding-right:5px">...</or>';  
                    } elseif ($i < $current_page && $i >= $current_page-2) {
                        $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                    } elseif ($i > $current_page && $i <= $current_page+2) {
                        $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                    } elseif ($i == $total_pages) {
                        $pageNumberArea .= '<or style="padding-left:5px;padding-right:5px">...</or><or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';  
                    }
                }
            } else {
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $current_page) {
                        $pageNumberArea .= '<span class="current-page pageSelected" style="padding-right:2px;padding-left:2px">' . $i . '</span>';
                        // onclick="navPage(updateQueryParameter(\'\', \'page\', \'$i\'))"
                    } else {
                        $pageNumberArea .= '<or class="gold clickable" style="padding-right:2px;padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.$i.'\') + \'\')">'.$i.'</or>';
                    }
                }
            }

            if ($current_page < $total_pages) {
                $pageNumberArea .= '<or class="gold clickable" style="padding-left:2px" onclick="navPage(updateQueryParameter(\'\', \'page\', \''.($current_page + 1).'\') + \'\')">></or>';
            }  
        }
        
        return $pageNumberArea;
    }

    static public function getNearbyStockAjax($request) 
    {
        $results = [];
        if (isset($request['item_id']) && is_numeric($request['item_id'])) {
            $item_id = htmlspecialchars($request['item_id']);

            if (isset($rquest['name']) && $request['name'] !== null) {
                $name = htmlspecialchars($request['name']);
            } else {
                $name = '';
            }

            $instance = new self();
            $instance->setTable('item as i');

            if (isset($request['is_item']) && $request['is_item'] == 1) {
                $rows = $instance->select(
                                'st.id as st_id',
                                'st.name as st_name',
                                'st.sku as st_sku',
                                'i.serial_number as i_serial_number',
                                DB::raw('COUNT(i.quantity) as quantity'),
                                DB::raw('MIN(i.id) as item_id') // Ensure we get the lowest item.id, without any NULL values
                            )
                            ->from('item as i') // Explicitly selecting from the 'item' table
                            ->join('stock as st', 'i.stock_id', '=', 'st.id') // Joining stock table
                            ->join('shelf as sh', 'i.shelf_id', '=', 'sh.id') // Joining shelf table
                            ->leftJoin('item_container as ic', 'i.id', '=', 'ic.item_id') // Left join with item_container (ic)
                            ->leftJoin('item_container as ic2', function($join) {
                                $join->on('i.id', '=', 'ic2.container_id')
                                    ->where('ic2.container_is_item', '=', 1); // Left join condition for ic2
                            })
                            ->where('i.shelf_id', function($query) use ($item_id) {
                                $query->select('shelf_id')
                                    ->from('item')
                                    ->where('id', $item_id);
                            })
                            ->where('i.is_container', 0) // Ensuring the item is not a container
                            ->where('i.deleted', 0) // Ensuring the item is not deleted
                            ->where('i.id', '!=', $item_id) // Excluding the current item from the query
                            ->whereNull('ic.item_id') // Ensuring that the item is not in a container
                            ->whereNull('ic2.container_id') // Ensuring the item is not a container in ic2
                            ->when($name !== '', function ($query) use ($name) {
                                $query->where('st.name', 'LIKE', "%$name%");
                            })
                            ->groupBy('st.id', 'st.name', 'st.sku', 'i.serial_number')
                            ->orderBy('st_name')
                            ->orderBy('i_serial_number')
                            ->get()
                            ->toArray();
            } else {
                $rows = $instance->select(
                                'st.id as st_id',
                                'st.name as st_name',
                                'st.sku as st_sku',
                                'i.serial_number as i_serial_number',
                                DB::raw('COUNT(i.quantity) as quantity'),
                                DB::raw('MIN(i.id) as item_id') // Ensure we get the smallest item.id
                            )
                            ->join('stock as st', 'i.stock_id', '=', 'st.id') // Join with stock
                            ->join('shelf as sh', 'i.shelf_id', '=', 'sh.id') // Join with shelf
                            ->leftJoin('item_container as ic', 'i.id', '=', 'ic.item_id') // Left join with item_container (ic)
                            ->leftJoin('item_container as ic2', function($join) {
                                $join->on('i.id', '=', 'ic2.container_id')
                                    ->where('ic2.container_is_item', '=', 0); // Left join condition for ic2
                            })
                            ->where('i.shelf_id', function($query) use ($item_id) {
                                $query->select('shelf_id')
                                    ->from('container')
                                    ->where('id', $item_id);
                            }) // Filtering by shelf_id based on container
                            ->where('i.is_container', 0) // Ensuring the item is not a container
                            ->where('i.deleted', 0) // Ensuring the item is not deleted
                            ->where('i.id', '!=', $item_id) // Excluding the current item from the query
                            ->whereNull('ic.item_id') // Ensuring that the item is not in a container
                            ->whereNull('ic2.container_id') // Ensuring the item is not a container in ic2
                            ->when($name !== '', function ($query) use ($name) {
                                $query->where('st.name', 'LIKE', "%$name%"); // Optional filtering by stock name
                            })
                            ->groupBy('st.id', 'st.name', 'st.sku', 'i.serial_number') // Grouping by stock details and item serial number
                            ->orderBy('st_name') // Ordering by stock name
                            ->orderBy('i_serial_number') // Ordering by item serial number
                            ->get()
                            ->toArray(); // Fetch results as an array
            }

            if (count($rows) > 0) {
                $results['count'] = count($rows);
                foreach ($rows as $row) {
                    $results['data'][] = array('stock_id' => $row['st_id'], 'stock_name' => $row['st_name'], 'stock_sku' => $row['st_sku'], 
                                            'item_serial_number' => $row['i_serial_number'], 
                                            'quantity' => $row['quantity'], 
                                            'item_id' => $row['item_id']);
                    
                }
            } else {
                $results['count'] = 0;
            }
        }
        return $results;
    }

    static public function getStockData($stock_id)
    {
        $return = [];

        $instance = new self();
        $instance->setTable('stock');

        $data = $instance->selectRaw('stock.id AS stock_id, 
                                        stock.name AS stock_name, 
                                        stock.description AS stock_description, 
                                        stock.sku AS stock_sku, 
                                        stock.min_stock AS stock_min_stock, 
                                        stock.is_cable AS stock_is_cable,
                                        stock_img.id AS stock_img_id, 
                                        stock_img.stock_id AS stock_img_stock_id, 
                                        stock_img.image AS stock_img_image, 
                                        stock.deleted AS stock_deleted')
                        ->leftJoin('stock_img', 'stock.id', '=', 'stock_img.stock_id')
                        ->where('stock.id', '=', $stock_id)
                        ->get()
                        ->toarray();

        $r = 0;
        foreach ($data as $row) {
            if ($r == 0) {
                $return = ['id' => $row['stock_id'],
                    'name' => $row['stock_name'],
                    'description' => $row['stock_description'],
                    'sku' => $row['stock_sku'],
                    'min_stock' => $row['stock_min_stock'],
                    'is_cable' => $row['stock_is_cable'],
                    'deleted' => $row['stock_deleted']
                    ];
            }
            $return['img_data']['rows'][$row['stock_img_id']]['id'] = $row['stock_img_id'];
            $return['img_data']['rows'][$row['stock_img_id']]['stock_id'] = $row['stock_img_stock_id'];
            $return['img_data']['rows'][$row['stock_img_id']]['image'] = $row['stock_img_image'];
            $r++;
        }
        $return['img_data']['count'] = count($return['img_data']['rows']);
        $return['count'] = $r;

        return $return;
    }

    static public function checkFavourited($stock_id) 
    {
        $favourites_list = GeneralModel::getUserFavourites(GeneralModel::getUser()['id']);

        $favourites_rows = $favourites_list['rows'];

        if (array_key_exists($stock_id, $favourites_rows)){
            return 1;
        }

        return 0;
    }

    static public function getStockInvData($stock_id, $is_cable)
    {
        $stock_inv_data = [];

        $instance = new self();
        $instance->setTable('stock AS s');

        if ($is_cable == 0) {
            $rows = $instance->selectRaw("
                    s.id AS stock_id, s.name AS stock_name, s.description AS stock_description, 
                    s.sku AS stock_sku, s.min_stock AS stock_min_stock, 
                    a.id AS area_id, a.name AS area_name, 
                    sh.id AS shelf_id, sh.name AS shelf_name, 
                    si.id AS site_id, si.name AS site_name, si.description AS site_description,

                    (SELECT SUM(i.quantity) 
                    FROM item AS i 
                    WHERE i.stock_id = s.id AND i.shelf_id = sh.id
                    ) AS item_quantity,

                    (SELECT GROUP_CONCAT(DISTINCT m.name ORDER BY m.name SEPARATOR ', ') 
                    FROM item AS i 
                    INNER JOIN manufacturer AS m ON m.id = i.manufacturer_id 
                    WHERE i.stock_id = s.id
                    ) AS manufacturer_names,

                    (SELECT GROUP_CONCAT(DISTINCT m.id ORDER BY m.name SEPARATOR ', ') 
                    FROM item AS i 
                    INNER JOIN manufacturer AS m ON m.id = i.manufacturer_id 
                    WHERE i.stock_id = s.id
                    ) AS manufacturer_ids,

                    (SELECT GROUP_CONCAT(DISTINCT t.name ORDER BY t.name SEPARATOR ', ') 
                    FROM stock_tag AS st
                    INNER JOIN tag AS t ON st.tag_id = t.id 
                    WHERE st.stock_id = s.id
                    ORDER BY t.name
                    ) AS tag_names,

                    (SELECT GROUP_CONCAT(DISTINCT t.id ORDER BY t.name SEPARATOR ', ') 
                    FROM stock_tag AS st
                    INNER JOIN tag AS t ON st.tag_id = t.id
                    WHERE st.stock_id = s.id
                    ORDER BY t.name
                    ) AS tag_ids
                ")
                ->leftJoin('item AS i', 's.id', '=', 'i.stock_id')
                ->leftJoin('shelf AS sh', 'i.shelf_id', '=', 'sh.id')
                ->leftJoin('area AS a', 'sh.area_id', '=', 'a.id')
                ->leftJoin('site AS si', 'a.site_id', '=', 'si.id')
                ->where('s.id', '=', $stock_id)
                ->groupBy(
                    's.id', 's.name', 's.description', 's.sku', 's.min_stock', 
                    'si.id', 'si.name', 'si.description', 
                    'a.id', 'a.name', 
                    'sh.id', 'sh.name'
                )
                ->orderBy('si.id')
                ->orderBy('a.name')
                ->orderBy('sh.name')
                ->get()
                ->toArray();
        } elseif ($is_cable == 1) {
            $rows = $instance->selectRaw("
                    s.id AS stock_id, s.name AS stock_name, s.description AS stock_description, 
                    s.sku AS stock_sku, s.min_stock AS stock_min_stock, 
                    a.id AS area_id, a.name AS area_name, 
                    sh.id AS shelf_id, sh.name AS shelf_name, 
                    si.id AS site_id, si.name AS site_name, si.description AS site_description,

                    (SELECT SUM(ci.quantity) 
                    FROM cable_item AS ci
                    WHERE ci.stock_id = s.id AND ci.shelf_id = sh.id
                    ) AS item_quantity
                ")
                ->leftJoin('cable_item AS ci', 's.id', '=', 'ci.stock_id')
                ->leftJoin('shelf AS sh', 'ci.shelf_id', '=', 'sh.id')
                ->leftJoin('area AS a', 'sh.area_id', '=', 'a.id')
                ->leftJoin('site AS si', 'a.site_id', '=', 'si.id')
                ->where('s.id', '=', $stock_id)
                ->groupBy(
                    's.id', 's.name', 's.description', 's.sku', 's.min_stock', 
                    'si.id', 'si.name', 'si.description', 
                    'a.id', 'a.name', 
                    'sh.id', 'sh.name'
                )
                ->orderBy('si.id')
                ->orderBy('a.name')
                ->orderBy('sh.name')
                ->get()
                ->toArray();
        }

        foreach ($rows as $row) {
            if ($is_cable == 0) {
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
                    $stock_tag_data = null;
                }

                $stock_manufacturer_data = [];
                if ($stock_manufacturer_ids !== null) {
                    for ($n=0; $n < count(explode(", ", $stock_manufacturer_ids)); $n++) {
                        $stock_manufacturer_data[$n] = array('id' => explode(", ", $stock_manufacturer_ids)[$n],
                                                            'name' => explode(", ", $stock_manufacturer_names)[$n]);
                    }
                } else {
                    $stock_manufacturer_data = null;
                }
            } else {
                $stock_manufacturer_data = null;
                $stock_tag_data = null;
            }

            $stock_inv_data['rows'][] = array('id' => $row['stock_id'],
                                        'name' => $row['stock_name'],
                                        'sku' => $row['stock_sku'],
                                        'min_stock' => $row['stock_min_stock'],
                                        'quantity' => $row['item_quantity'],
                                        'shelf_id' => $row['shelf_id'],
                                        'shelf_name' => $row['shelf_name'],
                                        'area_id' => $row['area_id'],
                                        'area_name' => $row['area_name'],
                                        'site_id' => $row['site_id'],
                                        'site_name' => $row['site_name']
                                        ); 
        }

        $stock_inv_data['count'] = count($stock_inv_data['rows']);
        $stock_inv_data['tags'] = $stock_tag_data;
        $stock_inv_data['manufacturers'] = $stock_manufacturer_data;

        $total_quantity = 0;
        foreach ($stock_inv_data['rows'] as $row) {
            $total_quantity = $total_quantity + (int)$row['quantity'];
        }
        $stock_inv_data['total_quantity'] = $total_quantity;
        
        return $stock_inv_data;
    }


    static public function getStockItemData($stock_id, $is_cable)
    {
        $stock_inv_data = [];

        $instance = new self();
        $instance->setTable('stock');

        if ($is_cable == 0) {
            $rows = $instance->selectRaw('
                        stock.id AS stock_id, 
                        stock.name AS stock_name, 
                        stock.description AS stock_description, 
                        stock.sku AS stock_sku, 
                        stock.min_stock AS stock_min_stock, 
                        area.id AS area_id, 
                        area.name AS area_name, 
                        shelf.id AS shelf_id, 
                        shelf.name AS shelf_name, 
                        site.id AS site_id, 
                        site.name AS site_name, 
                        site.description AS site_description, 
                        item.serial_number AS item_serial_number, 
                        item.upc AS item_upc, 
                        item.cost AS item_cost, 
                        item.comments AS item_comments, 
                        (SELECT SUM(quantity) FROM item 
                            WHERE item.stock_id = stock.id 
                            AND item.shelf_id = shelf.id 
                            AND item.manufacturer_id = manufacturer.id 
                            AND item.serial_number = item_serial_number 
                            AND item.upc = item_upc 
                            AND item.comments = item_comments 
                            AND item.cost = item_cost) AS item_quantity, 
                        manufacturer.id AS manufacturer_id, 
                        manufacturer.name AS manufacturer_name, 
                        (SELECT GROUP_CONCAT(DISTINCT tag.name ORDER BY tag.name SEPARATOR ', ') 
                            FROM stock_tag 
                            INNER JOIN tag ON stock_tag.tag_id = tag.id 
                            WHERE stock_tag.stock_id = stock.id 
                            ORDER BY tag.name) AS tag_names, 
                        (SELECT GROUP_CONCAT(DISTINCT tag.id ORDER BY tag.name SEPARATOR ', ') 
                            FROM stock_tag 
                            INNER JOIN tag ON stock_tag.tag_id = tag.id 
                            WHERE stock_tag.stock_id = stock.id 
                            ORDER BY tag.name) AS tag_ids')
                    ->leftJoin('item', 'stock.id', '=', 'item.stock_id')
                    ->leftJoin('shelf', 'item.shelf_id', '=', 'shelf.id')
                    ->leftJoin('area', 'shelf.area_id', '=', 'area.id')
                    ->leftJoin('site', 'area.site_id', '=', 'site.id')
                    ->leftJoin('manufacturer', 'item.manufacturer_id', '=', 'manufacturer.id')
                    ->where('stock.id', '=', $stock_id)
                    ->where('quantity', '!=', 0)
                    ->groupBy([
                        'stock.id', 'stock_name', 'stock_description', 'stock_sku', 'stock_min_stock', 
                        'site_id', 'site_name', 'site_description', 
                        'area_id', 'area_name', 
                        'shelf_id', 'shelf_name', 
                        'manufacturer_name', 'manufacturer_id', 
                        'item_serial_number', 'item_upc', 'item_comments', 'item_cost'
                    ])
                    ->orderBy('site.id')
                    ->orderBy('area.name')
                    ->orderBy('shelf.name')
                    ->get()
                    ->toArray();
        } elseif ($is_cable == 1) {
            $rows = $instance->selectRaw('
                        stock.id AS stock_id, 
                        stock.name AS stock_name, 
                        stock.description AS stock_description, 
                        stock.sku AS stock_sku, 
                        stock.min_stock AS stock_min_stock, 
                        area.id AS area_id, 
                        area.name AS area_name, 
                        shelf.id AS shelf_id, 
                        shelf.name AS shelf_name, 
                        site.id AS site_id, 
                        site.name AS site_name, 
                        site.description AS site_description, 
                        cable_item.cost AS item_cost, 
                        (SELECT SUM(quantity) FROM cable_item 
                            WHERE cable_item.stock_id = stock.id 
                            AND cable_item.shelf_id = shelf.id) AS item_quantity, 
                        (SELECT GROUP_CONCAT(DISTINCT tag.name ORDER BY tag.name SEPARATOR ', ') 
                            FROM stock_tag 
                            INNER JOIN tag ON stock_tag.tag_id = tag.id 
                            WHERE stock_tag.stock_id = stock.id 
                            ORDER BY tag.name) AS tag_names, 
                        (SELECT GROUP_CONCAT(DISTINCT tag.id ORDER BY tag.name SEPARATOR ', ') 
                            FROM stock_tag 
                            INNER JOIN tag ON stock_tag.tag_id = tag.id 
                            WHERE stock_tag.stock_id = stock.id 
                            ORDER BY tag.name) AS tag_ids')
                    ->leftJoin('cable_item', 'stock.id', '=', 'cable_item.stock_id')
                    ->leftJoin('shelf', 'cable_item.shelf_id', '=', 'shelf.id')
                    ->leftJoin('area', 'shelf.area_id', '=', 'area.id')
                    ->leftJoin('site', 'area.site_id', '=', 'site.id')
                    ->where('stock.id', '=', $stock_id)
                    ->where('quantity', '!=', 0)
                    ->groupBy([
                        'stock.id', 'stock_name', 'stock_description', 'stock_sku', 'stock_min_stock', 
                        'site_id', 'site_name', 'site_description', 
                        'area_id', 'area_name', 
                        'shelf_id', 'shelf_name',
                        'item_cost'
                    ])
                    ->orderBy('site.id')
                    ->orderBy('area.name')
                    ->orderBy('shelf.name')
                    ->get()
                    ->toArray();
        }

        foreach ($rows as $row) {
            if ($is_cable == 0) {
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
                    $stock_tag_data = null;
                }

            } else {
                $stock_tag_data = null;
            }

            $stock_item_data['rows'][] = array('id' => $row['stock_id'],
                                        'name' => $row['stock_name'],
                                        'sku' => $row['stock_sku'],
                                        'quantity' => $row['item_quantity'] ?? 0,
                                        'min_stock' => $row['stock_min_stock'],
                                        'quantity' => $row['item_quantity'],
                                        'shelf_id' => $row['shelf_id'],
                                        'shelf_name' => $row['shelf_name'],
                                        'area_id' => $row['area_id'],
                                        'area_name' => $row['area_name'],
                                        'site_id' => $row['site_id'],
                                        'site_name' => $row['site_name'],
                                        'manufacturer_id' => $row['manufacturer_id'] ?? null,
                                        'manufacturer_name' => $row['manufacturer_name'] ?? null,
                                        'tag_names' => $row['tag_names'] ?? null,
                                        'upc' => $row['item_upc'] ?? null,
                                        'cost' => $row['item_cost'] ?? 0,
                                        'comments' => $row['item_comments'] ?? null,
                                        'serial_number' => $row['item_serial_number'] ?? null,
                                        ); 
        }

        $stock_item_data['count'] = count($stock_item_data['rows']);
        $stock_item_data['tags'] = $stock_tag_data;

        $total_quantity = 0;
        foreach ($stock_item_data['rows'] as $row) {
            $total_quantity = $total_quantity + (int)$row['quantity'];
        }
        $stock_item_data['total_quantity'] = $total_quantity;
        
        return $stock_item_data;
    }

    static public function getDistinctSerials($stock_id)
    {
        $instance = new self();
        $instance->setTable('item');

        return $instance->selectRaw('serial_number, id')
                        ->where('stock_id', '=', $stock_id)
                        ->where('serial_number', '!=', '')
                        ->where('quantity', '!=', 0)
                        ->where('deleted', '=', 0)
                        ->orderby('id')
                        ->distinct()
                        ->get()
                        ->toarray();
    }
}
