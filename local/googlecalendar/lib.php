<?php
require_once($CFG->srcroot . '/vendor/autoload.php');

function getGoogleClient($googleAuthCode) {

    $credentials = 'credentials.json';
    $client = new Google_Client();

    $client->setScopes(array(Google_Service_Calendar::CALENDAR));
    $client->setAuthConfig($credentials);
    $client->setAccessType('offline');

    $accessToken = $client->fetchAccessTokenWithAuthCode($googleAuthCode);
    $client->setAccessToken($accessToken);

    return $client;
}


function get_session_info_field($facetofacesessionid, $shortname, $default = false) {
    global $DB;

    $record = $DB->get_record('facetoface_session_info_field', array('shortname' => $shortname));
    if($record) {
        if($default && $record->defaultdata != '') {
            return format_string($record->defaultdata);
        } else {
            $existing = $DB->get_record('facetoface_session_info_data', array('facetofacesessionid' => $facetofacesessionid, 'fieldid' => $record->id));
            if($existing) {
                return $existing->data;
            }
        }
    }
    return '';
}

function get_session_dates($sessionid) {
    global $DB;

    return $DB->get_record('facetoface_sessions_dates', array('sessionid' => $sessionid));
}

function get_session_attendees($sessionid) {
    global $DB;

    $sql = "SELECT email FROM {user} u 
            JOIN {facetoface_signups} s ON u.id=s.userid 
            WHERE s.sessionid = ". $sessionid ." AND email <> ''";
    return $DB->get_records_sql($sql);
}