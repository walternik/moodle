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

use totara_core\advanced_feature;

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->dirroot.'/totara/core/lib.php');

/**
 * Company instance management
 */
class company {

    /**
     * Company id
     *
     * @var int
     */
    protected $id = 0;

    /**
     * Company name
     *
     * @var string
     */
    public $name = '';

    /**
     * Company location
     *
     * @var int
     */
    public $location = null;

    /**
     * Order of company display in navigation
     *
     * @var int
     */
    public $sortorder = 0;


    /**
     * Get List of all contractor companies
     * It is not expected to have more than 100 contractorcompanies, so no paging here.
     * Much bigger number of contractorcompanies might reduce performance.
     *
     * @return array of companies
     */
    public static function get_manage_list() {
        global $DB;
        $sql = "SELECT * FROM {contractorcompanies} WHERE location<>'99' ORDER BY sortorder";
        $records = $DB->get_records_sql($sql);
        $cc = array();
        foreach ($records as $record) {
            $cc[] = new company($record->id);
        }
        return $cc;
    }

    
    /**
     * Create instance of company
     *
     * @param int $id
     */
    public function __construct($id = 0) {
        global $DB;

        if ($id == 0) {
            $this->name = '';
        }
        else {
            $record = $DB->get_record('contractorcompanies', array('id' => $id));
            $this->id = $record->id;
            $this->name = $record->name;
            $this->location = $record->location;
            $this->sortorder = $record->sortorder;
        }
    }

    /**
     * Is current company first in order
     *
     * @return boolean
     */
    public function is_first() {
        global $DB;
        $record = $DB->get_record_sql('SELECT MIN(sortorder) minsort FROM {contractorcompanies}');
        if ($record->minsort == $this->sortorder) {
            return true;
        }
        return false;
    }

    /**
     * Is current company last in order
     *
     * @return boolean
     */
    public function is_last() {
        global $DB;
        $record = $DB->get_record_sql('SELECT MAX(sortorder) maxsort FROM {contractorcompanies}');
        if ($record->maxsort == $this->sortorder) {
            return true;
        }
        return false;
    }


    /**
     * Save instance to database
     */
    public function save() {
        global $DB;
        $record = $this->get_for_form();

        if ($this->id > 0) {
            $DB->update_record('contractorcompanies', $record);
        } else {
            $id = $DB->insert_record('contractorcompanies', $record);
            $this->id = $id;
            db_reorder($this->id, -1, 'contractorcompanies');
        }
    }

    /**
     * Return instance data
     *
     * @return stdClass
     */
    public function get_for_form() {
        $instance = new stdClass();
        $instance->id = $this->id;
        $instance->name = $this->name;
        $instance->location = $this->location;
        $instance->sortorder = (int)$this->sortorder;

        return $instance;
    }

    /**
     * Set instance fields from stdClass
     *
     * @param stdClass $data
     * @return company $this
     */
    public function set_from_form(stdClass $data) {
        $this->name = '';
        $this->location = null;

        if (isset($data->name)) {
            $this->name = $data->name;
        }
        if (isset($data->location)) {
            $this->location = (int)$data->location;
        }

        return $this;
    }

    /**
     * Get company id
     *
     * @return int
     */
    public function get_id() {
        return $this->id;
    }


    /**
     * Remove contractor company from DB
     */
    public function delete() {
        global $DB;
        if ($this->id) {
            // Reorder it to last.
            db_reorder($this->id, -1, 'contractorcompanies');

            // Delete company.
            $DB->delete_records('contractorcompanies', array('id' => $this->id));
        }
    }


    /**
     * Clones the current company.
     *
     * This method clones the company
     *
     * @param string|null $name
     * @return int The id of the newly created company
     */
    public function clone_company(string $name = null) {
        global $DB;

        $trans = $DB->start_delegated_transaction();

        $old = $DB->get_record('contractorcompanies', array('id' => $this->id), '*', MUST_EXIST);
                
        // First clone the company record.
        $cc = clone($old);
        unset($cc->id);
        $cc->name = empty($name) ? $this->generate_clone_name() : $name;
        
        // Add to the end.
        $cc->sortorder = $DB->get_field('contractorcompanies', "MAX(sortorder) + 1", []);
        $cc->id = $DB->insert_record('contractorcompanies', $cc);

        $trans->allow_commit();

        return $cc->id;
    }

    /**
     * Generates a new name to use for the company when it is being cloned.
     *
     * @return string
     * @throws coding_exception
     */
    protected function generate_clone_name() {
        global $DB;
        $count = 1;
        $name = get_string('clonename', 'local_contractorcompanies', array('name' => $this->name, 'count' => $count));
        $stop = false;
        while ($DB->record_exists('contractorcompanies', array('name' => $name)) && !$stop) {
            $count++;
            if ($count > 25) {
                // This is getting mad. 25 is plenty, we'll stop here.
                $stop = true;
                // Append a + to show that there are more. This probably won't translate perfectly but it should be a very rare
                // edge case to end up with 25 clones.
                $count = '25+';
            }
            $name = get_string('clonename', 'local_contractorcompanies', array('name' => $this->name, 'count' => $count));
        }
        return $name;
    }
}
