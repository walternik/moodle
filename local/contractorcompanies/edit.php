<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Synegen
 * @package local_contractorcompanies
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/contractorcompanies/lib.php');
require_once($CFG->dirroot . '/local/contractorcompanies/contractorcompanies_forms.php');
require_once($CFG->libdir.'/adminlib.php');

$action = optional_param('action', null, PARAM_ALPHANUMEXT);
$id = 0;
if ($action != 'new') {
    $id = required_param('id', PARAM_INT);
}

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/local/contractorcompanies/edit.php');
$PAGE->set_title('Contractor Company');

$returnurl = new moodle_url('/local/contractorcompanies/index.php');

$company = new company($id);
$company_fromform = $company->get_for_form();

// Create the form
$mform = new company_edit_form(null, array('company' => $company_fromform));

if ($mform->is_cancelled()) {
    redirect($returnurl);
}
if ($fromform = $mform->get_data()) {
    if (empty($fromform->submitbutton)) {
        \core\notification::error(get_string('error:unknownbuttonclicked', 'local_contractorcompanies'));
        redirect($returnurl);
    }

    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', 'error');
    }

    $company->set_from_form($fromform)->save();

    \core\notification::success(get_string('saved', 'local_contractorcompanies'));
    redirect($returnurl);
}

if ($id == 0) {
    $heading = get_string('save', 'local_contractorcompanies');
    $name = get_string('create', 'local_contractorcompanies');
} else {
    $heading = get_string('edit', 'local_contractorcompanies');
    $name = $company->name;
}

$title = $PAGE->title . ': ' . $heading;
$PAGE->set_title($title);
$PAGE->set_heading($heading);
$PAGE->navbar->add($name);

$output = $PAGE->get_renderer('local_contractorcompanies');

echo $output->header();
echo $output->heading(get_string('manage', 'local_contractorcompanies'));

$mform->display();

echo $output->footer();