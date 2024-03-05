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

echo "<br/><br/><b>Copy this function and replace it on totara\\reportbuilder\lib.php file:</b><br/><br/>";


$report = "
private function set_options_by_tenant(\$filter) {<br/>
    &emsp;global \$USER;<br/>
    <br/>
    &emsp;\$choices = array();<br/>
    &emsp;if(\$filter == 'Location') {<br/>
    &emsp;&emsp;if(\$USER->tenantid == 1) { // Walker<br/>
        &emsp;&emsp;&emsp;\$choices = array(<br/>
        &emsp;&emsp;&emsp;&emsp;'All WEG locations' => 'All WEG locations',<br/>
        &emsp;&emsp;&emsp;&emsp;'All Walker Agg/Con Locations' => 'All Walker Agg/Con Locations',<br/>
        &emsp;&emsp;&emsp;&emsp;'Orangeburg' => 'Orangeburg',<br/>
        &emsp;&emsp;&emsp;&emsp;'Portland' => 'Portland',<br/>
        &emsp;&emsp;&emsp;&emsp;'Burlington' => 'Burlington'<br/>
        &emsp;&emsp;&emsp;);<br/>
        &emsp;&emsp;}<br/>
    &emsp;&emsp;if(\$USER->tenantid == 2) { // Modern<br/>
        &emsp;&emsp;&emsp;\$choices = array(<br/>
        &emsp;&emsp;&emsp;&emsp;'USA' => 'USA',<br/>
        &emsp;&emsp;&emsp;&emsp;'Canada' => 'Canada'<br/>
        &emsp;&emsp;&emsp;);<br/>
        &emsp;&emsp;}<br/>
    &emsp;&emsp;if(\$USER->tenantid == 3) { // Nestle Purina<br/>
        &emsp;&emsp;&emsp;\$choices = array(<br/>
        &emsp;&emsp;&emsp;&emsp;'Dunkirk' => 'Dunkirk',<br/>
        &emsp;&emsp;&emsp;&emsp;'Allentown' => 'Allentown',<br/>
        &emsp;&emsp;&emsp;&emsp;'Hartwell' => 'Hartwell',<br/>
        &emsp;&emsp;&emsp;&emsp;'Bloomfield' => 'Bloomfield',<br/>
        &emsp;&emsp;&emsp;&emsp;'Mechanicsburg' => 'Mechanicsburg',<br/>
        &emsp;&emsp;&emsp;&emsp;'Clinton' => 'Clinton',<br/>
        &emsp;&emsp;&emsp;&emsp;'Batavia' => 'Batavia'<br/>
        &emsp;&emsp;&emsp;);<br/>
        &emsp;&emsp;}<br/>
    &emsp;} elseif(\$filter == 'ContractorCompany') {<br/>
        &emsp;&emsp;if(\$USER->tenantid == 1) { // Walker<br/>
        &emsp;&emsp;&emsp;\$choices = array(<br/>";
// Walker
$sql = "SELECT * FROM {contractorcompanies} WHERE location IN ('0', '1', '2', '11', '13') ORDER BY sortorder";
$records = $DB->get_records_sql($sql);
foreach($records as $record) {
    $report .= "&emsp;&emsp;&emsp;&emsp;'".$record->name."' => '".$record->name."',<br/>";
}
$report .= "&emsp;&emsp;&emsp;&emsp;'Walker' => 'Walker',<br/>
            &emsp;&emsp;&emsp;&emsp;'*NOT LISTED*' => '*NOT LISTED*'<br/>
            &emsp;&emsp;&emsp;);<br/>
    &emsp;&emsp;}<br/>
    &emsp;&emsp;if(\$USER->tenantid == 2) { // Modern<br/>
        &emsp;&emsp;&emsp;\$choices = array(<br/>";

// Modern
$sql = "SELECT * FROM {contractorcompanies} WHERE location IN ('9', '10') ORDER BY sortorder";
$records = $DB->get_records_sql($sql);
foreach($records as $record) {
    $report .= "&emsp;&emsp;&emsp;&emsp;'".$record->name."' => '".$record->name."',<br/>";
}
$report .= "&emsp;&emsp;&emsp;&emsp;'Modern' => 'Modern',<br/>
            &emsp;&emsp;&emsp;&emsp;'*NOT LISTED*' => '*NOT LISTED*'<br/>
            &emsp;&emsp;&emsp;);<br/>
        &emsp;&emsp;}<br/>
        &emsp;&emsp;if(\$USER->tenantid == 3) { // Nestle Purina<br/>
            &emsp;&emsp;&emsp;\$choices = array(<br/>";

// Nestle Purina
$sql = "SELECT * FROM {contractorcompanies} WHERE location IN ('3','4','5','6','7','8','12') ORDER BY sortorder";
$records = $DB->get_records_sql($sql);
foreach($records as $record) {
    $report .= "&emsp;&emsp;&emsp;&emsp;'".$record->name."' => '".$record->name."',<br/>";
}
$report .= "&emsp;&emsp;&emsp;&emsp;'Nestle Purina' => 'Nestle Purina',<br/>
            &emsp;&emsp;&emsp;&emsp;'*NOT LISTED*' => '*NOT LISTED*',<br/>
            &emsp;&emsp;&emsp;&emsp;'*NO LISTADA*' => '*NO LISTADA*'<br/>
            &emsp;&emsp;&emsp;);<br/>
        &emsp;&emsp;}<br/>
    &emsp;}<br/>
    <br/>
    &emsp;return \$choices;<br/>
}<br/>";

echo $report;

echo $OUTPUT->container_end();
echo $OUTPUT->footer();
