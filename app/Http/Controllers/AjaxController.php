<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

use Illuminate\View\View;

// use App\Models\IndexModel;
use App\Models\GeneralModel;
use App\Models\PropertiesModel;
use App\Models\StockModel;
use App\Models\CablestockModel;
// use App\Models\ResponseHandlingModel;

class AjaxController extends Controller
{
    //
    public function getStockAjax(Request $request)
    {
        // Replace this with the actual DB logic from your PHP script
        $stock = StockModel::returnStockAjax($request);

        // Return data as JSON
        return $stock;
    }

    public function getCablesAjax(Request $request)
    {
        // Replace this with the actual DB logic from your PHP script
        $stock = CablestockModel::returnCablesAjax($request);

        // Return data as JSON
        return $stock;
    }

    public function getNearbyStockAjax(Request $request)
    {
        // Replace this with the actual DB logic from your PHP script
        $stock = StockModel::getNearbyStockAjax($request);

        // Return data as JSON
        return $stock;
    }

    public function getSelectBoxes(Request $request)
    {
        if (isset($request['site'])) {
            $areas = AjaxController::getSelectBoxAreas(htmlspecialchars($request['site']));

            return $areas;
        }

        if (isset($request['area'])) {
            $shelves = AjaxController::getSelectBoxShelves(htmlspecialchars($request['area']));

            return $shelves;
        }
    }

    public function getSelectBoxAreas($site) 
    {
        if (is_numeric($site) && $site > 0) {
            $areas = [];

            $areas = GeneralModel::allDistinctAreas($site, 0);

            if ($areas !== null) {
                return $areas;
            } else {
                return null;
            }
        }
    }

    public function getSelectBoxShelves($area) 
    {
        if (is_numeric($area) && $area > 0) {
            $shelves = [];

            $shelves = GeneralModel::allDistinctShelves($area, 0);

            if ($shelves !== null) {
                return $shelves;
            } else {
                return null;
            }
        }
    }

    static public function addProperty(Request $request) 
    {
        $previous_url = GeneralModel::previousURL();
        // get all to check for a match
        if ($request['_token'] == csrf_token()) {
            $request->validate([
                'type' => 'required',
                'description' => 'string',
                'property_name' => 'required',
                'area_id' => 'integer',
                'site_id' => 'integer'
            ]);

            return PropertiesModel::addProperty($request->input());
        } else {
            return redirect()->GeneralModel::redirectURL($previous_url, ['error' => 'csrfMissmatch']);
        }
    }
}
