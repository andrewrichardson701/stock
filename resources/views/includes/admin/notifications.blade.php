<div class="container" style="padding-bottom:0px">
    <h3 class="clickable" style="margin-top:50px;font-size:22px" id="notification-settings" onclick="toggleSection(this, 'notification')">Email Notification Settings <i class="fa-solid fa-chevron-down fa-2xs" style="margin-left:10px"></i></h3> 

    <!-- Notification Settings -->
    <div style="padding-top: 20px" id="notification" hidden>
        <?php
        // if ((isset($_GET['section']) && $_GET['section'] == 'notification-settings')) {
        //     showResponse();
        // }
        ?>
        {!! $response_handling !!}

    
        @if ($head_data['config']['smtp_enabled'] == 1)
            @if ($notifications['count'] > 0)
            <p id="notification-output" class="last-edit-T" hidden></p>
            <table>
                <tbody>
                @foreach ($notifications['rows'] as $notification)
                    @if ($loop->first)
                    <tr>
                    @endif
                    @if (($loop->iteration -1) %4 == 0)
                    </tr><tr>
                    @endif
                        <td class="align-middle" style="margin-left:25px;margin-right:10px" id="notif-{{ $notification['id'] }}">
                            <p style="min-height:max-content;margin:0px" class="align-middle title" title="{{ $notification['description'] }}">{{ $notification['title'] }}:</p>
                        </td>
                        <td class="align-middle" style="padding-left:5px;padding-right:20px" id="notif-{{ $notification['id'] }}-toggle">
                            <label class="switch align-middle" style="margin-bottom:0px;margin-top:3px" >
                                <input type="checkbox" name="{{ $notification['name'] }}" onchange="mailNotification(this, {{ $notification['id'] }})" @if ($notification['enabled'] == 1) checked @endif>
                                <span class="sliderBlue round align-middle" style="transform: scale(0.8, 0.8)"></span>
                            </label>
                        </td>
                    @if ($loop->last)
                    </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
            @else
            <p id="notification-output"><or class="red">No notifications settings found in table...</or></p>
            @endif
        @else
            <p class="blue">SMTP is disabled. All email notifications have been disabled.</p>
        @endif
        <div class="well-nopad theme-divBg" style="margin-top:20px">
            <h4>Email example</h4>
            <input type="hidden" value="{{ urlencode('<p style=\'color:black !important\'>Cable stock added, for <strong><a class=\'link\' style=\'color: #0000EE !important;\' href=\'stock.php?stock_id=1\'>Stock Name</a></strong> in <strong>Site 1</strong>, <strong>Store 1</strong>, <strong>Shelf 1</strong>!<br>New stock count: <strong>12</strong>.</p>') }}" id="email-template-body" />
            <div id="email-template" style="margin-top:20px;margin-bottom:10px">
            </div>
            <a style="margin-left:5px" href="includes/smtp.inc.php?template=echo&body={{ urlencode('<p style=\'color:black !important\'>Cable stock added, for <strong><a class=\'link\' style=\'color: #0000EE !important;\' href=\'stock.php?stock_id=1\'>Stock Name</a></strong> in <strong>Site 1</strong>, <strong>Store 1</strong>, <strong>Shelf 1</strong>!<br>New stock count: <strong>12</strong>.</p>') }}" target="_blank">View in new tab</a>
        </div>
    </div>
</div>