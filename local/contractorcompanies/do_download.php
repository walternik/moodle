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

// Print rows
// 0, 1, 2, 13
$sql = "SELECT * FROM {contractorcompanies} WHERE location IN ('0','1','2','13') ORDER BY location, sortorder";
$records = $DB->get_records_sql($sql);
$location = '';
foreach($records as $record) {
    if($location != $record->location) {
        echo "<br/><br/><b>Location: ".$record->location."</b><br/>";
    }
    $line = "\$secondChoice.append('<option value=\"$record->sortorder\">".$record->name."</option>');";
    $new = htmlspecialchars($line, ENT_QUOTES);
    echo $new . "<br/>";
    $location = $record->location;
}

// 0, 1 and 2 now are also grouped in 13
/*$sql = "SELECT * FROM {contractorcompanies} WHERE location IN ('0','1','2','13') ORDER BY sortorder";
$records = $DB->get_records_sql($sql);
echo "<br/><br/><b>Location: 13</b><br/>";
foreach($records as $record) {
    $line = "\$secondChoice.append('<option value=\"$record->sortorder\">".$record->name."</option>');";
    $new = htmlspecialchars($line, ENT_QUOTES);
    echo $new . "<br/>";
}*/

$sql = "SELECT * FROM {contractorcompanies} WHERE location NOT IN ('0','1','2','13','99') ORDER BY location, sortorder";
$records = $DB->get_records_sql($sql);
$location = '';
foreach($records as $record) {
    if($location != $record->location) {
        echo "<br/><br/><b>Location: ".$record->location."</b><br/>";
    }
    $line = "\$secondChoice.append('<option value=\"$record->sortorder\">".$record->name."</option>');";
    $new = htmlspecialchars($line, ENT_QUOTES);
    echo $new . "<br/>";
    $location = $record->location;
}

$sql = "SELECT * FROM {contractorcompanies} WHERE location='99' ORDER BY sortorder";
$records = $DB->get_records_sql($sql);
echo "<br/><b>NOT LISTED and Tenants:</b><br/>";
foreach($records as $record) {
    $line = "\$secondChoice.append('<option value=\"$record->sortorder\">".$record->name."</option>');";
    $new = htmlspecialchars($line, ENT_QUOTES);
    echo $new . "<br/>";
}


echo $OUTPUT->container_end();
echo $OUTPUT->footer();
