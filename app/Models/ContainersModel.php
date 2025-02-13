<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

// use App\Models\FunctionsModel;
use App\Models\GeneralModel;


class ContainersModel extends Model
{
    //
    protected $table = 'container'; // Specify your table name
    protected $fillable = ['name', 'description', 'shelf_id', 'deleted'];

    static public function getContainersInUse() 
    {
        $instance = new self();
        $instance->setTable('item_container as ic');
        
        return $instance->selectRaw('
                        c.id AS c_id, c.name AS c_name, c.description AS c_description,
                        ic.id AS ic_id, ic.item_id AS ic_item_id, ic.container_id AS ic_container_id, ic.container_is_item AS ic_container_is_item,
                        icontainer.id AS icontainer_id,
                        scontainer.id AS scontainer_id, scontainer.name AS scontainer_name, scontainer.description AS scontainer_description,
                        i.id AS i_id,
                        c_sh.id AS c_sh_id, i_sh.id AS i_sh_id,
                        CONCAT(c_si.name, " - ", c_a.name, " - ", c_sh.name) AS c_location,
                        CONCAT(i_si.name, " - ", i_a.name, " - ", i_sh.name) AS i_location,
                        s.id AS s_id, s.name AS s_name, s.description AS s_description,
                        (SELECT COUNT(item_id) 
                        FROM item_container 
                        WHERE item_container.container_id = ic.container_id 
                        AND item_container.container_is_item = ic.container_is_item
                        ) AS object_count,
                        (SELECT id FROM stock_img WHERE stock_id = scontainer.id LIMIT 1) AS simgcontainer_id,
                        (SELECT image FROM stock_img WHERE stock_id = scontainer.id LIMIT 1) AS simgcontainer_image,
                        (SELECT id FROM stock_img WHERE stock_id = s.id LIMIT 1) AS simg_id,
                        (SELECT image FROM stock_img WHERE stock_id = s.id LIMIT 1) AS simg_image
                    ')
                    ->leftJoin('container as c', function ($join) {
                        $join->on('ic.container_id', '=', 'c.id')
                            ->where('ic.container_is_item', '=', 0)
                            ->where('c.deleted', '=', 0);
                    })
                    ->leftJoin('item as icontainer', function ($join) {
                        $join->on('icontainer.id', '=', 'ic.container_id')
                            ->where('ic.container_is_item', '=', 1)
                            ->where('icontainer.deleted', '=', 0);
                    })
                    ->leftJoin('stock as scontainer', 'scontainer.id', '=', 'icontainer.stock_id')
                    ->leftJoin('stock_img as simgcontainer', 'simgcontainer.stock_id', '=', 'scontainer.id')
                    ->leftJoin('item as i', 'i.id', '=', 'ic.item_id')
                    ->leftJoin('stock as s', 's.id', '=', 'i.stock_id')
                    ->leftJoin('stock_img as simg', 'simg.stock_id', '=', 's.id')
                    ->leftJoin('shelf as c_sh', 'c.shelf_id', '=', 'c_sh.id')
                    ->leftJoin('area as c_a', 'c_sh.area_id', '=', 'c_a.id')
                    ->leftJoin('site as c_si', 'c_a.site_id', '=', 'c_si.id')
                    ->leftJoin('shelf as i_sh', 'i.shelf_id', '=', 'i_sh.id')
                    ->leftJoin('area as i_a', 'i_sh.area_id', '=', 'i_a.id')
                    ->leftJoin('site as i_si', 'i_a.site_id', '=', 'i_si.id')
                    ->groupBy([
                        'c.id', 'c.name', 'c.description',
                        'ic.id', 'ic.item_id', 'ic.container_id', 'ic.container_is_item',
                        'icontainer.id',
                        'scontainer.id', 'scontainer.name', 'scontainer.description',
                        'i.id',
                        's.id', 's.name', 's.description',
                        'c_sh.id', 'i_sh.id',
                        'i_location', 'c_location',
                        'simgcontainer_id', 'simgcontainer_image',
                        'simg_id', 'simg_image'
                    ])
                    ->orderBy('c.name')
                    ->orderBy('scontainer.name')
                    ->get()
                    ->toArray();
    }

    static public function getContainersEmpty() 
    {
        $instance = new self();
        $instance->setTable('container as c');

        return $instance->selectRaw('
                        c.id AS c_id, 
                        c.name AS c_name, 
                        c.description AS c_description, 
                        CONCAT(si.name, " - ", a.name, " - ", sh.name) AS location
                    ')
                    ->leftJoin('item_container as ic', function ($join) {
                        $join->on('c.id', '=', 'ic.container_id')
                            ->where('ic.container_is_item', '=', 0);
                    })
                    ->leftJoin('shelf as sh', 'sh.id', '=', 'c.shelf_id')
                    ->leftJoin('area as a', 'a.id', '=', 'sh.area_id')
                    ->leftJoin('site as si', 'si.id', '=', 'a.site_id')
                    ->whereNull('ic.id')
                    ->where('c.deleted', '=', 0)
                    ->orderBy('c.name')
                    ->get()
                    ->toArray();
    }

    static public function getContainersItemEmpty() 
    {
        $instance = new self();
        $instance->setTable('item as i');
        return $instance->selectRaw('
                        i.id AS i_id,
                        s.id AS s_id,
                        s.name AS s_name,
                        s.description AS s_description,
                        CONCAT(si.name, " - ", a.name, " - ", sh.name) AS location,
                        (SELECT id FROM stock_img WHERE stock_id = s.id LIMIT 1) AS img_id,
                        (SELECT image FROM stock_img WHERE stock_id = s.id LIMIT 1) AS img_image
                    ')
                    ->leftJoin('item_container as ic', function ($join) {
                        $join->on('i.id', '=', 'ic.container_id')
                            ->where('ic.container_is_item', '=', 1);
                    })
                    ->leftJoin('shelf as sh', 'sh.id', '=', 'i.shelf_id')
                    ->leftJoin('area as a', 'a.id', '=', 'sh.area_id')
                    ->leftJoin('site as si', 'si.id', '=', 'a.site_id')
                    ->leftJoin('stock as s', 's.id', '=', 'i.stock_id')
                    ->whereNull('ic.id')
                    ->where('i.is_container', '=', 1)
                    ->where('i.deleted', '=', 0)
                    ->orderBy('s.name')
                    ->get()
                    ->toArray();
    }

    static public function compileContainers()
    {
        $containers_in_use = ContainersModel::getContainersInUse();

        $containers_empty = ContainersModel::getContainersEmpty();
        $containers_item_empty = ContainersModel::getContainersItemEmpty();

        $container_array = [ 'container' => [],
                             'itemcontainer' => [] ];
        if (count($containers_in_use) > 0) {
            unset($row);
            foreach($containers_in_use as $row) {
                $containers_in_array = $container_array['container'];
                if (!is_null($row['c_id'])) {
                    if (!array_key_exists($row['c_id'], $containers_in_array)) {
                        $container_array['container'][$row['c_id']] = array('id' => $row['c_id'], 'name' => $row['c_name'], 'description' => $row['c_description'], 'count' => $row['object_count'],
                                                                            'img_id' => $row['simgcontainer_id'], 'img_image' => $row['simgcontainer_image'], 'location' => $row['c_location']);
                    }
                    $container_array['container'][$row['c_id']]['object'][] = array('ic_id' => $row['ic_id'], 'item_id' => $row['i_id'], 'id' => $row['s_id'], 'name' => $row['s_name'], 'description' => $row['s_description'],
                                                                                    'img_id' => $row['simg_id'], 'img_image' => $row['simg_image']);
                }
                $itemcontainers_in_array = $container_array['itemcontainer'];
                if (!is_null($row['icontainer_id'])) {
                    if (!array_key_exists($row['icontainer_id'], $itemcontainers_in_array)) {
                        $container_array['itemcontainer'][$row['icontainer_id']] = array('id' => $row['icontainer_id'], 'stock_id' => $row['scontainer_id'], 'name' => $row['scontainer_name'], 'description' => $row['scontainer_description'], 'count' => $row['object_count'],
                                                                                            'img_id' => $row['simgcontainer_id'], 'img_image' => $row['simgcontainer_image'], 'location' => $row['i_location']);
                    }
                    $container_array['itemcontainer'][$row['icontainer_id']]['object'][] = array('ic_id' => $row['icontainer_id'], 'item_id' => $row['i_id'], 'id' => $row['s_id'], 'name' => $row['s_name'], 'description' => $row['s_description'],
                                                                                                    'img_id' => $row['simg_id'], 'img_image' => $row['simg_image']);
                }
            }
        }

        if (count($containers_empty) > 0) {
            unset($row);
            foreach($containers_empty as $row) {
                $containers_in_array = $container_array['container'];
                if (!is_null($row['c_id'])) {
                    if (!array_key_exists($row['c_id'], $containers_in_array)) {
                        $container_array['container'][$row['c_id']] = array('id' => $row['c_id'], 'name' => $row['c_name'], 'description' => $row['c_description'], 'count' => 0,
                                                                            'img_id' => '', 'img_image' => '', 'location' => $row['location']);
                    }
                }
            }
        }

        if (count($containers_item_empty) > 0) {
            unset($row);
            foreach($containers_item_empty as $row) {
                $itemcontainers_in_array = $container_array['itemcontainer'];
                if (!is_null($row['i_id'])) {
                    if (!array_key_exists($row['i_id'], $itemcontainers_in_array)) {
                        $container_array['itemcontainer'][$row['i_id']] = array('id' => $row['i_id'], 'stock_id' => $row['s_id'], 'name' => $row['s_name'], 'description' => $row['s_description'], 
                                                                                            'count' => 0,
                                                                                            'img_id' => $row['img_id'], 'img_image' => $row['img_image'], 'location' => $row['location']);
                    }
                }
            }
        }

        return $container_array;
    }

    static public function addContainer($request) 
    {
        if (GeneralModel::checkShelfAreaMatch($request['shelf'], $request['area']) && GeneralModel::checkAreaSiteMatch($request['area'], $request['site'])) {
            $data = [
                    'name' => $request['container_name'], 
                    'description' => $request['container_description'], 
                    'shelf_id' => $request['shelf']
                    ];

            $insert = ContainersModel::create($data);

            $id = $insert->id;
                    
            $info = [
                'user' => GeneralModel::getUser(),
                'table' => 'container',
                'record_id' => $id,
                'field' => 'name',
                'new_value' => $request['container_name'],
                'action' => 'New record',
                'previous_value' => '',
            ];

            GeneralModel::updateChangelog($info);
            return redirect()->route('containers', ['success' => 'added']);
        }
    }

    static public function deleteContainer($request) 
    {   
        $id = $request['container_id'];
        $container = ContainersModel::find($id);

        if (!$container) {
            return redirect()->route('containers')->with('error', 'Container not found!');
        }

        $info = [
            'user' => GeneralModel::getUser(),
            'table' => 'container',
            'record_id' => $id,
            'field' => 'deleted',
            'new_value' => 1,
            'action' => 'Delete record',
            'previous_value' => null,
        ];

        GeneralModel::updateChangelog($info);

        $container->update([
            'deleted' => 1
        ]);
        
        return redirect()->route('containers', ['success' => 'deleted']);
    }

    static public function editContainer($request) 
    {   
        
        $id = $request['container_id'];
        $name = $request['container_name'];
        $description = $request['container_description'];

        $container = ContainersModel::find($id);

        if (!$container) {
            return redirect()->route('containers')->with('error', 'Container not found!');
        }

        if ($name !== $container->toArray()['name']) {
            $info = [
                'user' => GeneralModel::getUser(),
                'table' => 'container',
                'record_id' => $id,
                'field' => 'name',
                'new_value' => $name,
                'action' => 'Update record',
                'previous_value' => null,
            ];
    
            GeneralModel::updateChangelog($info);
            
            $container->update([
                'name' => $name
            ]);
        }

        if ($description !== $container->toArray()['description']) {
            $info = [
                'user' => GeneralModel::getUser(),
                'table' => 'container',
                'record_id' => $id,
                'field' => 'description',
                'new_value' => $description,
                'action' => 'Update record',
                'previous_value' => null,
            ];
    
            GeneralModel::updateChangelog($info);
            
            $container->update([
                'description' => $description
            ]);
        }       

        return redirect()->route('containers', ['success' => 'updated']);
    }

    static public function unlinkFromContainer($request) 
    {
        $deleted_id = DB::table('item_container')
                    ->where('item_id', $request['item_id'])
                    ->value('id'); // Get the 'id' of the matching row

        // Perform the delete operation only if the row exists
        if ($deleted_id) {
            $info = [
                'user' => GeneralModel::getUser(),
                'table' => 'item_container',
                'record_id' => $deleted_id,
                'field' => 'item_id',
                'new_value' => null,
                'action' => 'Delete record',
                'previous_value' => null,
            ];
            GeneralModel::updateChangelog($info);

            DB::table('item_container')->where('id', $deleted_id)->delete();
            return redirect()->route('containers', ['success' => 'unlinked']);
        } else {
            return redirect()->route('containers', ['error' => 'noRows']);
        }
    }

    static public function linkToContainer($request) 
    {
        $insert = DB::table('item_container')->insertGetId([
            'item_id' => $request['item_id'],
            'container_id' => $request['container_id'],
            'container_is_item' => $request['is_item'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $info = [
            'user' => GeneralModel::getUser(),
            'table' => 'item_container',
            'record_id' => $insert,
            'field' => 'item_id',
            'new_value' => $request['item_id'],
            'action' => 'New record',
            'previous_value' => '',
        ];

        GeneralModel::updateChangelog($info);

        return redirect()->route('containers', ['success' => 'linked']);
    }
}
