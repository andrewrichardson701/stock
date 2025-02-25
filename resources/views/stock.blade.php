<!DOCTYPE html>
<html lang="en">
<head>
    @include('head')
    <title>{{$head_data['config_compare']['system_name']}} - Stock</title>
</head>
<body>
    <!-- Header and Nav -->
    @include('nav')
    <!-- End of Header and Nav -->

    <div class="content">
        @include('includes.stock.new-properties')

        @if(!is_numeric($params['stock_id']))
            @if(!empty($params['modify_type']))
                <div class="container" style="padding-top:25px"><p class="red">Non-numeric Stock ID: <or class="blue">{{ $params['stock_id'] }}</or>.<br>Please check the URL or <a class="link" onclick="navPage(updateQueryParameter('', 'stock_id', 0))">add new stock item</a>.</p></div>
            @else
                <div class="container" style="padding-top:25px"><p class="red">Non-numeric Stock ID: <or class="blue">{{ $params['stock_id'] }}</or>.<br>Please check the URL or go back to the <a class="link" href="./">home page</a>.</p></div>
            @endif
        @endif

        <!-- Get Inventory -->
        @if(!empty($params['modify_type']))
            <div class="container" style="padding-bottom:25px">
                <h2 class="header-small" style="padding-bottom:5px">Stock - {{ ucwords($params['modify_type']) }}</h2>
                {!! $response_handling !!}
            </div>
            @include('includes.stock.stock-' . $stock_modify)
        @else
            @if (($stock_data['count'] ?? 0) < 1)
                <div class="container" id="no-stock-found">No Stock Found</div>
            @else
                <script src="assets/js/favourites.js"></script>
                <div id="favouriteButton" class="" style="width: max-content">
                    <button onclick="favouriteStock({{ $params['stock_id'] }})" class="favouriteBtn" id="favouriteBtn" title="Favourite Stock">
                        <i id="favouriteIcon" class=" @if (($favourited ?? 0) == 1) fa-solid @else fa-regular @endif fa-star"></i>
                    </button>
                </div>
                <div class="container stock-heading">
                    <div class="row">
                        <div class="col">
                            <h2 class="header-small" style="padding-bottom:0px">Stock</h2>
                        </div>
                        <div class="col nav-div nav-right" style="margin-bottom: 5px;max-width:max-content; width:max-content;margin-right:0px !important">
                            <div class="nav-row">
                                <div id="edit-div" class="nav-div nav-right" style="margin-right:5px">
                                    <button id="edit-stock" class="btn btn-info theme-textColor nav-v-b stock-modifyBtn" onclick="navPage('stock/{{ $stock_id }}/edit')">
                                        <i class="fa fa-pencil"></i><or class="viewport-large-empty"> Edit</or>
                                    </button>
                                </div> 
                                <div id="add-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                                    <button id="add-stock" class="btn btn-success theme-textColor nav-v-b stock-modifyBtn" onclick="navPage('stock/{{ $stock_id }}/add')" @if ($stock_data['deleted'] == 1) disabled @endif>
                                        <i class="fa fa-plus"></i><or class="viewport-large-empty"> Add</or>
                                    </button>
                                </div> 
                                <div id="remove-div" class="nav-div" style="margin-left:5px;margin-right:5px">
                                    <button id="remove-stock" class="btn btn-danger theme-textColor nav-v-b stock-modifyBtn" onclick="navPage('stock/{{ $stock_id }}/remove')" @if ($stock_data['deleted'] == 1) disabled @endif>
                                        <i class="fa fa-minus"></i><or class="viewport-large-empty"> Remove</or>
                                    </button>
                                </div> 
                                <div id="transfer-div" class="nav-div" style="margin-left:5px;margin-right:0px">
                                    <button id="transfer-stock" class="btn btn-warning nav-v-b stock-modifyBtn" style="color:black" onclick="navPage('stock/{{ $stock_id }}/move')" @if ($stock_data['deleted'] == 1) disabled @endif>
                                        <i class="fa fa-arrows-h"></i><or class="viewport-large-empty"> Move</or>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! $response_handling !!}
                    <div class='row ' style='margin-top:5px;margin-top:10px;'>
                        <div class='col' style='margin-top:auto;margin-bottom:auto;'>
                            <h3 style='font-size:22px;margin-bottom:0px;' id='stock-name'>{{ $stock_data['name'] }}({{ $stock_data['sku'] }})</h3>
                            <input type='hidden' id='hiddenStockName' value='".$stock_name."'>
                        </div>
                        
                    </div>
                    <p id='stock-description' style='color:#898989;margin-bottom:0px;margin-top:10px'>{{ str_replace(array("\r\n","\\r\\n"), "<br/>", $stock_data['description']) }}</p>
                    @if ($stock_data['deleted'] == 1)
                        <p class="red" style="margin-top:20px;font-size:20">Stock Deleted. <a class="link" style="font-size:20" href="admin.php#stockmanagement-settings">Restore?</a></p>
                    @endif
                </div>

                <!-- Modal Image Div -->
                <div id="modalDiv" class="modal" onclick="modalClose()">
                    <span class="close" onclick="modalClose()">&times;</span>
                    <img class="modal-content bg-trans modal-imgWidth" id="modalImg">
                    <div id="caption" class="modal-caption"></div>
                </div>
                <!-- End of Modal Image Div -->

                <div class="container well-nopad theme-divBg">
                    <div class="row">
                        <div class="col-sm-7 text-left" id="stock-info-left">
                            <table class="" id="stock-info-table" style="max-width:max-content">
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
                            @foreach ($stock_inv_data['rows'] as $key => $row)
                                @if (($row['quantity'] ?? 0) !== 0)
                                    <tr id="stock-row-{{ $key }}">
                                        <td hidden>{{ $row['id'] }}</td>
                                        <td id="stock-row-{{ $key }}-site-{{ $row['site_id'] }}"><or class="clickable" onclick="window.location.href='/?site={{ $row['site_id'] }}">{{ $row['site_name'] }}</or></td>
                                        <td id="stock-row-{{ $key }}-area-{{ $row['area_id'] }}" style="padding-left: 10px"><or class="clickable" onclick="window.location.href='/?site={{ $row['site_id'] }}&area={{ $row['area_id'] }}'">{{ $row['area_name'] }}</or>:</td>
                                        <td id="stock-row-{{ $key }}-shelf-{{ $row['shelf_id'] }}" style="padding-left: 5px"><button class="btn theme-btn btn-stock-click gold clickable" onclick="window.location.href='/?shelf={{ str_replace(' ', '+', $row['shelf_name']) }}'">{{ $row['shelf_name'] }}</button></td>
                                        <td style="padding-left: 5px" class="text-center theme-textColor">{{ $row['quantity'] }}</td>
                                    </tr>
                                @endif
                            @endforeach

                            @if (($stock_inv_data['total_quantity'] ?? 0) == 0)
                                <tr id="stock-row-na-0">
                                    <td colspan=100% style="padding-left: 5px" class="text-center">N/A</td>
                                </tr>
                            @endif

                            
                                </tbody>
                            </table>
                            <p id="min-stock"><strong>Minimum Stock Count:</strong> <or class="specialColor">$stock_data['min_stock']</or></p>

                            @if ($stock_data['is_cable'] == 0)
                                <p class="clickable gold" id="extra-info-dropdown" onclick="toggleSection(this, 'extra-info')">More Info <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></p> 
                                <div id="extra-info" hidden>
                                    <p id="tags-head"><strong>Tags</strong></p>
                                    <p id="tags">
                                    @if (is_array($stock_inv_data['tags'])) 
                                        @foreach($stock_inv_data['tags'] as $key => $tag)
                                            <button class="btn theme-btn btn-stock-click gold clickable" id="tag-{{ $tag['id'] }}" onclick="window.location.href='/?tag={ $tag['name'] }}'">{{ $tag['name'] }}</button> 
                                        @endforeach
                                    @else
                                        None
                                    @endif
                                    </p>
                                    <p id="manufacturer-head"><strong>Manufacturers</strong></p><p id="manufacturers">
                                    @if (is_array($stock_inv_data['manufacturers'])) 
                                        @foreach($stock_inv_data['manufacturers'] as $key => $manufacturer)
                                            <button class="btn theme-btn btn-stock-click gold clickable" id="manufacturer-{{ $manufacturer['id'] }}" onclick="window.location.href='/?manufacturer={ $manufacturer['name'] }}'">{{ $manufacturer['name'] }}</button> 
                                        @endforeach
                                    @else
                                        None
                                    @endif
                                    </p>
                                    <p id="serial-numbers-head"><strong>Serial Numbers</strong></p>
                                    <p>
                                    @foreach ($serial_numbers as $key => $row)
                                        <a class="serial-bg" id="serialNumber-{{ $key }}">$row['serial_number']</a>
                                    @endforeach
                                    </p>
                        
                                </div>
                            @endif
                        </div>

                        <div class="col text-right" id="stock-info-right">
                        @if (!empty($stock_data['img_data']))  
                            <div class="well-nopad theme-divBg nav-right stock-imageBox">
                                <div class="nav-row stock-imageMainSolo">
                                @foreach ($stock_data['img_data']['rows'] as $key => $row)
                                    @if ($loop->iteration == 1)
                                        
                                        @if ($stock_data['img_data']['count'] <= 1)
                                            <div class="thumb theme-divBg-m text-center stock-imageMainSolo" onclick="modalLoadCarousel()">
                                                <img class="nav-v-c stock-imageMainSolo" id="stock-{{ $row['stock_id'] }}-img-{{ $row['id'] }}" alt="{{ $stock_data['name'] - image {{ $loop->iteration }}" src="assets/img/stock/{{ $row['image'] }}" />
                                            </div>
                                            <span id="side-images" style="margin-left:5px">
                                        @else 
                                            <div class="thumb theme-divBg-m text-center stock-imageMain" onclick="modalLoadCarousel()">
                                                <img class="nav-v-c stock-imageMain" id="stock-{{ $row['stock_id'] }}-img-{{ $row['id'] }}" alt="{{ $stock_data['name'] - image {{ $loop->iteration }}" src="assets/img/stock/{{ $row['image'] }}" />
                                            </div>
                                            <span id="side-images" style="margin-left:5px">
                                        @endif
                                    @endif
                                    
                                    @if ($loop->iteration == 2 || $loop->iteration == 3)
                                        <div class="thumb theme-divBg-m stock-imageOther" style="margin-bottom:5px" onclick="modalLoadCarousel()">
                                            <img class="nav-v-c stock-imageOther" id="stock-{{ $row['stock_id'] }}-img-{{ $row['id'] }}" alt="{{ $stock_data['name'] - image {{ $loop->iteration }}" src="assets/img/stock/{{ $row['image'] }}" />
                                        </div>
                                    @endif
                                    @if ($loop->iteration == 4)
                                        @if ($loop->iteration < $stock_data['img_data']['count'])
                                        <div class="thumb theme-divBg-m stock-imageOther" onclick="modalLoadCarousel()">
                                            <p class="nav-v-c text-center stock-imageOther" id="stock-{{ $stock_data['id'] }-img-more">+{{ $stock_data['img_data']['count']-3 }}</p>
                                        @else
                                        <div class="thumb theme-divBg-m stock-imageOther" onclick="modalLoadCarousel()">
                                            <img class="nav-v-c stock-imageOther" id="stock-{{ $stock_data['id'] }-img-{{  $row['id'] }}" src="img/stock/{{ $row['image'] }}" onclick="modalLoad(this)"/>
                                        @endif
                                        </div>
                                    @endif
                                    @if ($loop->last || $loop->iteration == 4)
                                        </span>
                                        @break
                                    @endif
                                @endforeach
                                </div>
                            </div>

                            @if ($stock_data['img_data']['count'] == 1)
                            <!-- Modal Image Div -->
                            <div id="modalDivCarousel" class="modal" onclick="modalCloseCarousel()">
                                <span class="close" onclick="modalCloseCarousel()">&times;</span>
                                @foreach($stock_data['img_data']['rows'] as $key => $row)
                                    <img class="modal-content bg-trans modal-imgWidth" id="stock-{{ $row['stock_id'] }}-img-{{ $row['id'] }}" src="img/stock/{{  $row['image'] }}"/>
                                @endforeach
                                <img class="modal-content bg-trans" id="modalImg">
                                <div id="caption" class="modal-caption"></div>
                            </div>
                            <!-- End of Modal Image Div -->
                            @else 
                            <link rel="stylesheet" href="css/carousel.css">
                            <script src="js/carousel.js"></script>
                            <!-- Modal Image Div -->
                            <div id="modalDivCarousel" class="modal">
                                <span class="close" onclick="modalCloseCarousel()">&times;</span>
                                <img class="modal-content bg-trans" id="modalImg">
                                    <div id="myCarousel" class="carousel slide" data-ride="carousel" align="center" style="margin-left:10vw; margin-right:10vw">
                                        <!-- Indicators -->
                                        <ol class="carousel-indicators">
                                        @for ($a=0; $a < $stock_data['img_data']['count']; $a++)
                                            @if ($a == 0)
                                            <li data-target="#myCarousel" data-slide-to="{{ $a }}"></li>
                                            @else
                                            <li data-target="#myCarousel" data-slide-to="{{ $a }}" class="active"></li>
                                            @endif
                                            
                                        @endfor
                                        </ol>

                                        <!-- Wrapper for slides -->
                                        <div class="carousel-inner" align="centre">');
                                        @foreach ($stock_data['img_data']['rows'] as $key => $row)
                                            <div class="item @if ($loop->iteration == 1) active @endif " align="centre">
                                            <img class="modal-content bg-trans modal-imgWidth" id="stock-{{ $row['stock_id']}}-img-{{ $row['id'] }}" src="img/stock/{{ $row['image'] }}"/>
                                                <div class="carousel-caption">
                                                    <h3></h3>
                                                    <p></p>
                                                </div>
                                            </div>
                                        @endforeach
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
                            @endif
                        @else
                            <div id="edit-images-div" class="nav-div-mid nav-v-c">
                                <button id="edit-images" class="btn btn-success theme-textColor nav-v-b" style="padding: 3px 6px 3px 6px" onclick="navPage(updateQueryParameter('stock/{{ $stock_data['id'] }}/edit', \'images\', \'edit\'))">
                                    <i class="fa fa-plus"></i> Add images
                                </button>
                            </div> 
                        @endif
                        </div>
                    </div>
                </div>
                <div class="container well-nopad theme-divBg" style="margin-top:5px">
                    <h2 style="font-size:22px">Stock</h2>
                    <div class='row ' style='margin-top:5px;margin-top:10px;'>
                    <div class='col' style='margin-top:auto;margin-bottom:auto;'>
                        <h3 style='font-size:22px;margin-bottom:0px;' id='stock-name'>{{ $stock_data['name'] }} ({{ $stock_data['sku'] }})</h3>
                        <input type='hidden' id='hiddenStockName' value='".$stock_name."'>
                    </div>
                    
                </div>
                <p id='stock-description' style='color:#898989;margin-bottom:0px;margin-top:10px'>{{ str_replace(array("\r\n","\\r\\n"), "<br/>", $stock_data['description']) }}</p>

                <table class="table table-dark theme-table centertable">
                    <thead>
                        <tr class="theme-tableOuter">
                            <th class="align-middle text-center" hidden>ID</th>
                            <th class="align-middle text-center">Site</th>
                            <th class="align-middle text-center">Location</th>
                            <th class="align-middle text-center">Shelf</th>
                            @if ($stock_data['is_cable'] == 0) 
                                
                                <th class="align-middle text-center viewport-large-empty">Manufacturer</th>
                                <th class="align-middle text-center viewport-small-empty">Manu.</th>
                                <th class="align-middle text-center viewport-large-empty">UPC</th>
                                <th title="Serial Numbers" class="align-middle text-center">Serial</th>
                                <th class="align-middle text-center" hidden>Tags</th>
                                <th class="viewport-large-empty align-middle text-center" @if ($config_compare['cost_enable_normal'] == 0) hidden @endif >Cost</th>
                                <th class="viewport-large-empty align-middle text-center">Comments</th>
                            @else 
                                <th class="viewport-large-empty align-middle text-center" @if ($config_compare['cost_enable_cable'] == 0) hidden @endif >Cost</th>
                            @endif
                            
                            <th class="align-middle text-center">Stock</th>
                        </tr>
                    </thead>
                    <tbody>                           
                    @foreach($stock_inv_data['rows'] as $row)
                        <tr id="item-{{ $loop->iteration }}" @if ($stock_data['is_cable'] == 0) 'class="clickable row-show" onclick="toggleHiddenStock({{ $loop->iteration }})">
                            <td hidden>{{ $loop->iteration }}</td>
                            <td id="item-{{ $loop->iteration }}-{{ $row['site_id'] }}" class="align-middle text-center">{{ $row['site_name'] }}</td>
                            <td id="item-{{ $loop->iteration }}-{{ $row['site_id'] }}-{{ $row['area_id'] }}" class="align-middle text-center">{{ $row['area_name'] }}</td>
                            <td id="item-{{ $loop->iteration }}-{{ $row['site_id'] }}-{{ $row['area_id'] }}-{{ $row['shelf_id'] }}" class="align-middle text-center">{{ $row['shelf_name'] }}</td>
                            @if ($stock_data['is_cable'] == 0)
                            <td id="item-{{ $loop->iteration }}-manu-{{ $row['manufacturer_id'] }}" class="align-middle text-center">{{  $row['manufacturer_name'] }}</td>
                            <td id="item-{{ $loop->iteration }}-upc" class="viewport-large-empty align-middle text-center">{{ $row['upc'] }}</td>
                            <td id="item-{{ $loop->iteration }}-sn" class="align-middle text-center">{{ $row['serial_number'] }}</td>
                            <td id="item-{{ $loop->iteration }}-tags" class="align-middle text-center" hidden>{{ $row['tag_names'] }}</td>
                            <td id="item-{{ $loop->iteration }}-cost" class="viewport-large-empty align-middle text-center" @if ($config_compare['cost_enable_normal'] == 0) hidden @endif >{{ $row['cost'] }}</td>
                            <td id="item-{{ $loop->iteration }}-comments" class="viewport-large-empty align-middle text-center">{{ $row['comments'] }}</td>
                            <td id="item-{{ $loop->iteration }}-stock" class="align-middle text-center">{{ $row['quantity'] }}</td>
                            @else
                            <td id="item-{{ $loop->iteration }}-cost" class="viewport-large-empty align-middle text-center" @if ($config_compare['cost_enable_cable'] == 0) hidden @endif >{{ $row['cost'] }}</td>
                            <td id="item-{{ $loop->iteration }}-stock" @if($row['quantity'] < $stock_data['min_stock']) class="red align-middle text-center" title="Below minimum stock count. Please re-order." @else class="align-middle text-center" @endif >{{ $row['quantity'] }}</td>
                            @endif
                        </tr>
                        @if ($stock_data['is_cable'] == 0)
                        <tr id="item-{{ $loop->iteration }}-hidden" class="row-hide" hidden>
                            <td colspan=100%>
                                <div style="max-height:75vh;overflow-x: hidden;overflow-y: auto;">
                                    <table class="table table-dark theme-table centertable" style="border-left: 1px solid #454d55;border-right: 1px solid #454d55;border-bottom: 1px solid #454d55">
                                        <thead>
                                            <tr class="theme-tableOuter">
                                                <th class="align-middle text-center">ID</th>
                                                <th class="align-middle text-center" hidden>Site</th>
                                                <th class="align-middle text-center" hidden>Location</th>
                                                <th class="align-middle text-center" hidden>Shelf</th>
                                                <th class="align-middle text-center">Manufacturer</th>
                                                <th class="align-middle text-center">UPC</th>
                                                <th class="align-middle text-center">Serial</th>
                                                <th class="align-middle text-center" @if ($config_compare['cost_enable_normal'] == 0) hidden >Cost ({{ $config_compare['currency'] }})</th>
                                                <th class="align-middle text-center">Comments</th>
                                                <th class="align-middle text-center" colspan=2>Container</th>
                                                <th class="align-middle text-center">Stock</th>
                                                <th class="align-middle text-center"></th>
                                                <th class="align-middle text-center"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @set('matchCount', 0)
                                        @foreach ($stock_item_data['rows'] as $item)
                                            @if ($item['manufacturer_id'] == $row['manufacturer_id'] &&
                                                $item['serial_number'] == $row['serial_number'] &&
                                                $item['comments'] == $row['comments'] &&
                                                $item['cost'] == $row['cost']) // serial and others from below needed here -- might work?
                                                    @set('matchCount', $matchCount + 1)
                                                    <tr class="align-middle">
                                                        <form action="includes/stock-modify.inc.php" method="POST" id="form-item-{{ $item['item_id'] }}" enctype="multipart/form-data"></form>
                                                        <!-- Include CSRF token in the form -->
                                                        @csrf
                                                        <input type="hidden" form="form-item-{{ $item['item_id'] }}" name="submit" value="row"/>
                                                        <td class="align-middle text-center"><input type="hidden" form="form-item-{{ $item['item_id'] }}" name="item-id" value="{{ $item['item_id'] }}" />{{ $item['item_id'] }}</td>
                                                        <td hidden>{{ $item['site_name'] }}</td>
                                                        <td hidden>{{ $item['area_name'] }}</td>
                                                        <td hidden>{{ $item['shelf_name'] }}</td>
                                                        <td class="align-middle text-center">
                                                            <select class="form-control manufacturer-select" form="form-item-{{ $item['item_id'] }}" name="manufacturer_id" style="max-width:max-content">
                                                            @if (!empty($manufacturers['rows']))
                                                                @foreach ($manufacturers['rows'] as $manufacturer) 
                                                                <option value="{{ $manufacturer['name'] }}" @if ($item['manufacturer_id'] == $manufacturer['name']) selected @endif >{{ $manufacturer['name'] }}</option>
                                                                @endforeach
                                                            @else
                                                                <option>No Manufacturers Found</option>
                                                            @endif
                                                            </select>
                                                        </td>
                                                        <td class="align-middle text-center"><input type="text" form="form-item-{{ $item['item_id'] }}" class="form-control" style="" value="{{ $item['item_upc'] }}" name="upc" /></td>
                                                        <td class="align-middle text-center"><input type="text" form="form-item-{{ $item['item_id'] }}" class="form-control" style="" value="{{ $item['item_serial_number'] }}" name="serial_number" /></td>
                                                        <td class="align-middle text-center" @if ($config_compare['cost_enable_normal'] == 0) hidden @endif ><input type="number" step=".01" form="form-item-{{ $item['item_id'] }}" class="form-control" style="width:75px" value="{{ $item['cost'] }}" name="cost" min=0 /></td>
                                                        <td class="align-middle text-center"><input type="text" form="form-item-{{ $item['item_id'] }}" class="form-control" style="" value="{{ htmlspecialchars($item['item_comments'], ENT_QUOTES, 'UTF-8') }}" name="comments" /></td>
                                                        @if (!empty($container_data[$item['item_id']]))
                                                            <td class="align-middle text-center" style="padding-right:2px" @if ($item['is_container'] == 1) colspan=2 @endif>
                                                            @if ($item['is_container'] == 1 && isset($matchCount) && $matchCount > 0) 
                                                                <input type="checkbox" form="form-item-{{ $item['item_id'] }}" name="container-toggle" checked hidden>
                                                            @endif
                                                                <label class="switch align-middle" style="margin-bottom:0px;margin-top:0px" >
                                                                    <input type="checkbox" form="form-item-{{ $item['item_id'] }}"
                                                                        @if ({{ $item['is_container'] }} == 1) 
                                                                            @if (isset($matchCount) && $matchCount > 0)
                                                                                checked name="container-toggle-disabled" disabled
                                                                            @else
                                                                                checked name="container-toggle"
                                                                            @endif
                                                                        @else 
                                                                            name="container-toggle"
                                                                        @endif
                                                                    >
                                                                    <span class="slider round align-middle" style="transform: scale(0.8, 0.8); @if ($item['is_container'] == 1 && isset($matchCount) && $matchCount > 0) opacity: 0.5; cursor: no-drop;" title="Please un-assign the children first @endif "></span>
                                                                </label>
                                                            </td>
                                                            @if ($item['is_container'] == 0)
                                                                <td class="align-middle text-center" style="padding-left:2px">
                                                                        <button class="btn btn-warning" type="button" style="opacity: 0.85; margin-left:5px; padding: 0px 3px 0px 3px" title="Link to container" onclick="modalLoadLinkToContainer({{ $item['item_id'] }})">
                                                                            <i class="fa fa-link"></i>
                                                                        </button>
                                                                </td>
                                                            @endif
                                                        @else
                                                            <td class="align-middle text-center" style="padding-right:2px">
                                                                <a class="link" id="modalUnlinkContainerItemName-{{ $item['item_id'] }}" href="containers.php?container_id=$col_id&con_is_item=$col_item">$col_name</a>
                                                            </td>
                                                            <td class="align-middle text-center" style="padding-left:2px">
                                                                <form action="includes/stock-modify.inc.php" method="POST" id="form-item-{{ $item['item_id'] }}-container-unlink" enctype="multipart/form-data">
                                                                    <!-- Include CSRF token in the form -->
                                                                    @csrf
                                                                    <input type="hidden" name="item_id" value="{{ $item['item_id'] }}" form="form-item-{{ $item['item_id'] }}-container-unlink" />
                                                                    <input type="hidden" name="container-unlink" value="1" form="form-item-{{ $item['item_id'] }}-container-unlink" />
                                                                    <button class="btn btn-danger" type="button" name="submit" onclick='modalLoadUnlinkContainer("{{ $item['item_id'] }}", "$col_id", 1)' form="form-item-{{ $item['item_id'] }}-container-unlink" style="color:black !important; opacity: 0.85; margin-left:5px; padding: 0px 3px 0px 3px" title="Unlink from container">
                                                                        <i class="fa fa-unlink"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        @endif
                                                        <td class="align-middle text-center">{{ $item['quantity'] }}</td>
                                                        <td style="padding-right:3px"><input type="submit" form="form-item-{{ $item['item_id'] }}" class="btn btn-success" name="stock-row-submit" value="Update" /></td>
                                                        <td style="padding-left:3px"><button type="button" class="btn btn-danger" onclick="navPage(updateQueryParameter('stock/{{ $stock_data['id'] }}&manufacturer={{ $item['manufacturer_id'] }}&shelf={{ $item['shelf_id'] }}&serial={{ $item['serial_number'] }}', 'modify', 'remove'))" @if ($item['is_container'] == 1 && isset($matchCount) && $matchCount > 0) disabled @endif><i class="fa fa-trash"></i></button></td>
                                                    </tr>
                                                @if ($item['is_container'] == 1 && $container_data[$item['item_id']]['count'] > 0)
                                                    <tr class="theme-th-selected">
                                                        <td colspan="100%">
                                                            <div style="max-height:50vh;overflow-x: hidden;overflow-y: auto;">
                                                                <p class="centertable" style="width:85%; margin-bottom:5px">Contents</p>
                                                                <table class="table table-dark theme-table centertable" style="border-left: 1px solid #454d55;border-right: 1px solid #454d55;border-bottom: 1px solid #454d55; width:85%">
                                                                    <thead>
                                                                        <th class="align-middle text-center">Item ID</th>
                                                                        <th class="align-middle text-center">Name</th>
                                                                        <th class="align-middle text-center">UPC</th>
                                                                        <th class="align-middle text-center">Serial</th>
                                                                        <th class="align-middle text-center">Comments</th>
                                                                        <th class="align-middle text-center">
                                                                            <button class="btn btn-success" type="submit" name="button" onclick="modalLoadAddChildren({{ $item['item_id'] }})" style="color:black !important; opacity: 0.85; margin-left:5px; padding: 0px 3px 0px 3px" title="Add more children">
                                                                                + <i class="fa fa-link"></i>
                                                                            </button>
                                                                        </th>
                                                                    </thead>
                                                                    <tbody>
                                                                    @if ($container_data[$item['item_id']]['count'] == 0)
                                                                        <tr><td class="align-middle text-center" colspan=100%>No contents found.</td><tr>
                                                                    @else 
                                                                        @foreach($container_data[$item['item_id']]['rows'] as $child)
                                                                        <tr class="align-middle">
                                                                            <td class="align-middle text-center">{{ $child['item_id'] }}</td>
                                                                            <td class="align-middle text-center" style="white-space:wrap;"><a class="link" href="stock/{{ $child['stock_id'] }}" id="modalUnlinkContainerItemName-{{ $child['item_id'] }}">{{ $child['stock_name'] }}</a></td>
                                                                            <td class="align-middle text-center">{{ $child['item_upc'] }}</td>
                                                                            <td class="align-middle text-center">{{ $child['item_serial_number'] }}</td>
                                                                            <td class="align-middle text-center">{{ $child['item_comments'] }}</td>
                                                                            <td class="align-middle text-center">
                                                                                <input type="hidden" id="modalUnlinkContainerName" value="{{ $child['stock_name'] }}" />
                                                                                <button class="btn btn-danger" type="submit" name="button" onclick="modalLoadUnlinkContainer('$item['item_id']', '{{ $child['item_id'] }}', 0)" style="color:black !important; opacity: 0.85; margin-left:5px; padding: 0px 3px 0px 3px" title="Unlink from container">
                                                                                    <i class="fa fa-unlink"></i>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                        @endforeach
                                                                    @endif
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endif
                                 well       @endforeach
                                        @if ($matchCount == 0)
                                            <tr><td colpan=100%>No Stock Found</td></tr>
                                        @endif  
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>                 
            @endif

            <div class="container well-nopad theme-divBg viewport-large-empty" style="margin-top:5px">
                <h2 style="font-size:22px">Transactions</h2>');
                @include('includes.stock.transactions');
            </div>
            <div class="container well-nopad theme-divBg viewport-small-empty text-center" style="margin-top:5px">
                <or class="specialColor clickable" style="font-size:12px" onclick="navPage('transactions?stock_id={{ $stock_data['id'] }}')">View Transactions</or>
            </div>
        @endif

    </div>
    <!-- Start Modal for uninking from container -->
    <div id="modalDivUnlinkContainer" class="modal">
        <span class="close" onclick="modalCloseUnlinkContainer()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; height:450px; display:flex;justify-content:center;align-items:center;" id="property-container">
                <form action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data">
                    <!-- Include CSRF token in the form -->
                    @csrf
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
                @csrf
                <input type="hidden" name="container-link-fromstock" value="1" />
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

    <!-- Container Add item Modal -->
    <div id="modalDivLinkToContainer" class="modal">
        <span class="close" onclick="modalCloseLinkToContainer()">&times;</span>
        <div class="container well-nopad theme-divBg" style="padding:25px">
            <div class="well-nopad theme-divBg" style="overflow-y:auto; overflow-x: auto; height:600px; " id="property-container" >
                <h4 class="text-center align-middle" style="width:100%;margin-top:10px">Add to container</h4>
                <table class="centertable"><tbody><tr><th style="padding-right:5px">Item ID:</th><td style="padding-right:20px" id="linkToContainerItemID"></td><th style="padding-right:5px">Item Name:</th><td id="linkToContainerItemName"></td></tr></tbody></table>
                <div class="well-nopad theme-divBg" style="margin: 20px 10px 20px 10px; padding:20px">
                    <p><strong>Containers</strong></p>
                    <table id="containerSelectTable" class="table table-dark theme-table centertable" style="margin-bottom:0px; white-space:nowrap;">
                        <thead>
                            <tr>
                                <th class="text-center align-middle">ID</th>
                                <th class="text-center align-middle">Name</th>
                                <th class="text-center align-middle">Description</th>
                            </tr>
                        </thead>
                        <tbody id="containerSelectTableBody">
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <form class="padding:0px;margin:0px" action="includes/stock-modify.inc.php" method="POST" enctype="multipart/form-data">
                <!-- Include CSRF token in the form -->
                @csrf
                <span class="align-middle text-center" style="display:block; white-space:nowrap;width:100%">
                    <input type="hidden" name="container-link" value="1" />
                    <input type="hidden" id="linkToContainerTableItemID" name="item_id" />
                    <input type="hidden" id="linkToContainerTableID" name="container_id" />
                    <input type="hidden" id="linkToContainerTableItem" name="item" />
                    <input type="submit" id="containerLink-submit-button" name="submit" class="btn btn-success" style="margin:10px 10px 0px 10px" value="Link" disabled>
                    <button class="btn btn-warning" type="button" style="margin:10px 10px 0px 10px" onclick="modalCloseLinkToContainer()">Cancel</button>
                </span>
            </form>
        </div>
    </div>
    <!-- End of Link to Container-->
     
    <!-- Add the JS for the file -->
    <script src="js/stock.js"></script>

    @include('foot')
</body>
