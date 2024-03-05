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

/**
 * Output renderer for local_contractorcompanies
 */
class local_contractorcompanies_renderer extends plugin_renderer_base {
    /**
     * Return a button to  create a new contractor company
     *
     * @return string HTML to display the button
     */
    public function create_company_button() {
        $url = new moodle_url('/local/contractorcompanies/edit.php', array('action' => 'new', 'adminedit' => 1));
        return $this->output->single_button($url, get_string('create', 'local_contractorcompanies'), 'get');
    }

    public function generate_company_button() {
        $url = new moodle_url('/local/contractorcompanies/do_download.php');
        return $this->output->single_button($url, get_string('generate', 'local_contractorcompanies'), 'get');
    }

    public function sort_company_button() {
        $url = new moodle_url('/local/contractorcompanies/do_sort.php');
        return $this->output->single_button($url, get_string('list', 'local_contractorcompanies'), 'get');
    }

    public function report_company_button() {
        $url = new moodle_url('/local/contractorcompanies/do_report.php');
        return $this->output->single_button($url, get_string('report', 'local_contractorcompanies'), 'get');
    }

    public function back_company_button() {
        $url = new moodle_url('/local/contractorcompanies/index.php');
        return $this->output->single_button($url, get_string('back', 'local_contractorcompanies'), 'get');
    }

    /**
     * Renders a table containing contractorcompanies list
     *
     * @param local_contractorcompanies[] $cc array of local_contractorcompanies object
     * @return string HTML table
     */
    public function company_manage_table($cc) {
        global $CFG, $DB;
        if (empty($cc)) {
            return get_string('nocc', 'local_contractorcompanies');
        }

        $tableheader = array(get_string('name', 'local_contractorcompanies'), get_string('location', 'local_contractorcompanies'),
         get_string('sortorder', 'local_contractorcompanies'));

        $tableheader[] = get_string('options', 'local_contractorcompanies');

        $cctable = new html_table();
        $cctable->summary = '';
        $cctable->head = $tableheader;
        $cctable->data = array();
        $cctable->attributes = array('class' => 'generaltable fullwidth', 'id' => 'alldashboards');

        $strdelete = get_string('delete', 'local_contractorcompanies');
        $stredit = get_string('edit', 'local_contractorcompanies');
        $strclone = get_string('clone', 'local_contractorcompanies');

        $locations = array(
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

        $data = array();
        foreach ($cc as $company) {
            $id = $company->get_id();
            $urledit = new moodle_url('/local/contractorcompanies/edit.php', array('id' => $id));
            $urlclone = new moodle_url('/local/contractorcompanies/index.php', array('action' => 'clone', 'id' => $id, 'sesskey' => sesskey()));
            $deleteurl = new moodle_url('/local/contractorcompanies/index.php', array('action' => 'delete', 'id' => $id));

            $row = array();
            $row[] = format_string($company->name);
            $row[] = $locations[$company->location];
            $row[] = $company->sortorder;
            $options = '';
            $options .= $this->output->action_icon($urledit, new pix_icon('/t/edit', $stredit, 'moodle'), null, array('class' => 'action-icon edit'));
            $options .= $this->output->action_icon($urlclone, new pix_icon('/t/copy', $strclone, 'moodle'), null, array('class' => 'action-icon clone'));
            $options .= $this->output->action_icon($deleteurl, new pix_icon('/t/delete', $strdelete, 'moodle'), null, array('class' => 'action-icon delete'));
            $row[] = $options;

            $data[] = $row;
        }
        $cctable->data = $data;

        return html_writer::table($cctable);
    }
}