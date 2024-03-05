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
 * This page lists all the upcoming classes and Learning history
 *
 * @package local_contractorcompanies
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/


require_once("../../config.php");

require_login();

$context = context_system::instance();
$baseurl = new moodle_url('/local/contractorcompanies/index.php');
$title = 'Contractor Companies';

/// Set the page
$PAGE->set_url($baseurl);
$PAGE->set_pagelayout('base');
$PAGE->set_context($context);
$PAGE->set_title($title);

$OUTPUT = $PAGE->get_renderer('local_contractorcompanies');

echo $OUTPUT->header();
echo $OUTPUT->container_start('', 'contractorcompanies_stories');

echo $OUTPUT->back_company_button();

// Process that builds section for login/locations
// Update sortorder
$sql = "SELECT * FROM {contractorcompanies} WHERE location<>'99' ORDER BY name";
$records = $DB->get_records_sql($sql);
$total = count($records);
$i = 0;
foreach($records as $record) {
    $obj = new stdClass();
    $obj->id = $record->id;
    $obj->sortorder = $i;
   	$DB->update_record('contractorcompanies', $obj);
    $i++;
}
// Update not listed and tenants
$sql = "SELECT * FROM {contractorcompanies} WHERE location='99'";
$records = $DB->get_records_sql($sql);
foreach($records as $record) {
    $obj = new stdClass();
    $obj->id = $record->id;
    if($record->name == 'Modern') {
        $obj->sortorder = $total;
    }
    if($record->name == 'Nestle Purina') {
        $obj->sortorder = $total+1;
    }
    if($record->name == 'Walker') {
        $obj->sortorder = $total+2;
    }
    if($record->name == '*NOT LISTED*') {
        $obj->sortorder = $total+3;
    }
    $DB->update_record('contractorcompanies', $obj);
}

// List of all Contractor Companies
$sql = "SELECT * FROM {contractorcompanies} WHERE location<>'99' ORDER BY name";
$records = $DB->get_records_sql($sql);
echo "<br/><br/><b>Copy and paste the ordered list of companies in Users -> User profile fields -> Contractor Company<br/>
    Then generate the login/locations.js file and replace it in Totara</b><br/><br/>";
foreach($records as $record) {
    echo $record->name."<br/>";
}
$sql = "SELECT * FROM {contractorcompanies} WHERE location='99' ORDER BY sortorder";
$records = $DB->get_records_sql($sql);
foreach($records as $record) {
    echo $record->name."<br/>";
}

echo $OUTPUT->container_end();
echo $OUTPUT->footer();
