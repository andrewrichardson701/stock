<?php
// This file is part of StockBase.
// StockBase is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// StockBase is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
// You should have received a copy of the GNU General Public License along with StockBase. If not, see <https://www.gnu.org/licenses/>.

if (isset($_SESSION['user_id']) && ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Root')) {
    $update_check = checkUpdates($versionNumber);
    $update_text = '';

    if (!array_key_exists('error', $update_check)) {
        if ($update_check['update'] == 1) {
            $update_available = 1;
        } else {
            $update_available = 0;
        }
        $messages = $update_check['message'];
        $m = 0;
        if (isset($messages)) {
            foreach($messages as $message) {
                $m ++;
                if ($m < count($messages)) {
                    $message .= '<br>';
                }
                $update_text .= $message;
            }
        }
    } else {
        // error exists
        $errors = $update_check['error'];
        $error_string = '<or class="red">';
        foreach ($errors as $error) {
            $error_string .= '&#8226; '.$error;
        }
        $error_string .= '</or>';
        $update_text = $error_string;
    }
}

function checkUpdates($version) {
    $return = [];
    $version_file_path = 'version.json';
    // master gitlab branch head file
    $remoteHeadFileUrl =  'https://gitlab.com/andrewrichardson701/stockbase/-/raw/master/head.php';

    // check if version file exists, if not create a blank one
    if (!file_exists($version_file_path)) {
        file_put_contents($version_file_path, '');
    }

    $currentVersion = ltrim($version, 'v'); // Remove the leading 'v'

    $version_file_info = json_decode(file_get_contents($version_file_path), true);
    
    // set the current version in the array
    $version_file_info['current_version'] = $currentVersion;

    if (!isset($version_file_info['check_time']) || !isset($version_file_info['latest_version']) || $version_file_info['check_time'] < time() - (60*15)) { // 15 minutes interval
        // check for updates
        // Fetch the latest head.php content
        $remoteHeadContent = file_get_contents($remoteHeadFileUrl);
        if ($remoteHeadContent === false) {
            $return['error'][] = "Could not retrieve the latest version information.";
        }

        // Extract and strip 'v' from the $version value in the fetched head.php content
        preg_match('/\$versionNumber\s*=\s*[\'"]v?([^\'"]+)[\'"]/', $remoteHeadContent, $matches);
        $latestVersion = isset($matches[1]) ? ltrim($matches[1], 'v') : null;

        $version_file_info['check_time'] = time();
        $version_file_info['latest_version'] = $latestVersion;
        
        file_put_contents($version_file_path, json_encode($version_file_info));
    } else {
        $latestVersion = $version_file_info['latest_version'];
    }

    

    if ($latestVersion === null) {
        $return['error'][] = "Failed to determine the latest version.";
    }

    if (!isset($return['error'])) {
        // Parse the current and latest versions
        $current = parseVersion($currentVersion);
        $latest = parseVersion($latestVersion);

        // Calculate the difference in each component, ignoring negative values
        $majorDifference = max($latest['major'] - $current['major'], 0);
        $minorDifference = max($latest['minor'] - $current['minor'], 0);
        $patchDifference = max($latest['patch'] - $current['patch'], 0);

        $return['message'][] = "You are using <or class='green'>v$currentVersion</or>.";
        // Generate the update message
        if ($majorDifference > 0 || $minorDifference > 0 || $patchDifference > 0) {
            $return['update'] = 1;

            $return['message'][] = "The latest version is <or class='green'>v$latestVersion</or>.";
            
            // Provide detailed message based on the differences
            $return['message'][] = "<br>You are behind by:";
            if ($majorDifference > 0) {
                $return['message'][] = "&#8226; <or class='red'>$majorDifference</or> major release(s)";
                $return['major'] = $majorDifference;
            }
            if ($minorDifference > 0) {
                $return['message'][] = "&#8226; <or class='red'>$minorDifference</or> minor release(s)";
                $return['minor'] = $minorDifference;
            }
            if ($patchDifference > 0) {
                $return['message'][] = "&#8226; <or class='red'>$patchDifference</or> patch(es)";
                $return['patch'] = $patchDifference;
            }
            
            $return['message'][] = "<br>Please update to the latest version.";
        } else {
            $return['update'] = 0;
            $return['message'][] = "You are up to date!";
        }
    }

    return $return;
}

// Function to split the version string into major, minor, and patch parts
function parseVersion($version) {
    $parts = explode('.', $version);
    return [
        'major' => isset($parts[0]) ? (int)$parts[0] : 0,
        'minor' => isset($parts[1]) ? (int)$parts[1] : 0,
        'patch' => isset($parts[2]) ? (int)$parts[2] : 0,
    ];
}