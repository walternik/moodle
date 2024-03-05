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
require_once($CFG->libdir.'/adminlib.php');

$action = optional_param('action', null, PARAM_ALPHANUMEXT);

require_login();

$contextsystem = context_system::instance();
$PAGE->set_context($contextsystem);
$PAGE->set_url('/local/contractorcompanies/index.php');
$PAGE->set_title('Contractor Companies');

$output = $PAGE->get_renderer('local_contractorcompanies');

$contractorcompanies = company::get_manage_list();

$cc = null;
if ($action != '') {
    $id = required_param('id', PARAM_INT);
    $cc = new company($id);
    $returnurl = new moodle_url('/local/contractorcompanies/index.php');
}

switch ($action) {
    case 'clone':
        // This operation clones the given company as well as the blocks it uses and any assigned audiences.
        // It does not clone any user customisations of the company.
        $confirm = optional_param('confirm', null, PARAM_INT);
        if ($confirm) {
            require_sesskey();
            $newid = $cc->clone_company();
            $clone = new company($newid);
            $args = array(
                'original' => $cc->name,
                'clone' => $clone->name
            );
            \core\notification::success(get_string('clonesuccess', 'local_contractorcompanies', $args));
            redirect($returnurl);
        }
        break;
    case 'delete':
        $confirm = optional_param('confirm', null, PARAM_INT);
        if ($confirm) {
            require_sesskey();
            $cc->delete($id);
            \core\notification::success(get_string('deletesuccess', 'local_contractorcompanies'));
            redirect($returnurl);
        }
        break;
}

$requiresconfirmation = array('delete', 'clone');
if (in_array($action, $requiresconfirmation)) {
    switch ($action) {
        case 'delete':
            $confirmtext = get_string('deleteconfirm', 'local_contractorcompanies', $cc->name);
            break;
        case 'clone':
            $confirmtext = get_string('cloneconfirm', 'local_contractorcompanies', $cc->name);
            break;
        default:
            throw new coding_exception('Invalid action passed to confirmation.');
            break;
    }

    $url = new moodle_url('/local/contractorcompanies/index.php', array('action'=> $action, 'id' => $id, 'confirm' => 1));
    $continue = new single_button($url, get_string('continue'), 'post');
    $cancel = new single_button($returnurl, get_string('cancel'), 'get');

    echo $output->header();
    echo $output->confirm(format_text($confirmtext), $continue, $cancel);
    echo $output->footer();
    exit;
}

echo $output->header();
echo $output->heading(get_string('manage', 'local_contractorcompanies'));

echo $output->create_company_button();
echo $output->sort_company_button();
echo $output->generate_company_button();
echo $output->report_company_button();
echo $output->company_manage_table($contractorcompanies);
echo $output->create_company_button();

echo $output->footer();
