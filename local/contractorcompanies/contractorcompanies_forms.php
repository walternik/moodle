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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->dirroot . '/lib/formslib.php');
require_once('lib.php');

/**
 * Contractor Company settings form
 */
class company_edit_form extends moodleform {

    public function definition() {
        global $DB, $CFG;
        $mform = & $this->_form;
        $company = $this->_customdata['company'];

        // General settings.
        $mform->addElement('header', 'generalhdr', get_string('general'));
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('text', 'name', get_string('name', 'local_contractorcompanies'), ['size' => 100]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255);

        $choices = array(
            '11' => 'ALL Weg Locations (Walker)',
            '13' => 'All Walker Agg/Con Locations',
            '0' => 'Orangeburg (Walker)',
            '1' => 'Portland (Walker)',
            '2' => 'Burlington (Walker)',
            '3' => 'Dunkirk (Nestle Purina)',
            '4' => 'Allentown (Nestle Purina)',
            '5' => 'Hartwell (Nestle Purina)',
            '6' => 'Bloomfield (Nestle Purina)',
            '7' => 'Mechanicsburg (Nestle Purina)',
            '8' => 'Clinton (Nestle Purina)',
            '12' => 'Batavia (Nestle Purina)',
            '9' => 'USA (Modern)',
            '10' => 'Canada (Modern)'
        );
        $mform->addElement('select', 'location', get_string('location', 'local_contractorcompanies'), $choices);

        $submittitle = get_string('save', 'local_contractorcompanies');
        if ($company->id > 0) {
            $submittitle = get_string('savechanges', 'local_contractorcompanies');
        }
        $this->add_action_buttons(true, $submittitle);
        $this->set_data($this->_customdata['company']);
    }

    
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if(strpos($data['name'], "'") !== false){
            $errors['name'] = get_string('err_name', 'local_contractorcompanies');
        }

        return $errors;
    }
}