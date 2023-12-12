<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Verify authorization callback.
 *
 * @package    core_badges
 * @copyright  2020 Tung Thai
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Tung Thai <Tung.ThaiDuc@nashtechglobal.com>
 */
require_once(__DIR__ . '/../../config.php');


// Google API configuration
define('GOOGLE_OAUTH_SCOPE', 'https://www.googleapis.com/auth/calendar');

// Face-to-face session ID
$s = required_param('s', PARAM_INT);
$action = optional_param('action', 'insert', PARAM_TEXT);
$f = optional_param('f', 0, PARAM_INT);

$_SESSION['sessionid'] = $s;
$_SESSION['action'] = $action;
$_SESSION['facetofaceid'] = $f;

$credentials = 'credentials.json';
$json = json_decode(file_get_contents($credentials));

// Google OAuth URL 
$googleOauthURL = 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode(GOOGLE_OAUTH_SCOPE) . '&redirect_uri=' . $json->web->redirect_uris[0] . 
'&response_type=code&client_id=' . $json->web->client_id . '&access_type=online';

header("Location: $googleOauthURL");