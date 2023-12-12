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


defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/form/filemanager.php');

/**
 * Course resources element.
 *
 * @package   local_uploadfile
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_uploadfile_course_resource_form_element extends MoodleQuickForm_filemanager {

    /**
     * Constructor
     *
     * @param string $elementName Element name
     * @param mixed $elementLabel Label(s) for an element
     * @param array $options Options to control the element's display
     * @param mixed $attributes Either a typical HTML attribute string or an associative array.
     */
    public function __construct($elementName=null, $elementLabel=null, $options=array(), $attributes=null) {
        global $DB;
        if ($elementName == null) {
            // This is broken quickforms messing with the constructors.
            return;
        }

        if (!empty($options['cmid'])) {
            $cmid = $options['cmid'];

            if($current = $DB->get_record('local_uploadfile', ['cmid' => $cmid])) {
                // data_preprocessing
                $context = context_module::instance($cmid);
                $draftitemid = file_get_submitted_draft_itemid('resource');
                file_prepare_draft_area($draftitemid, $context->id, 'backup', 'activity', 0);
                $this->setValue($draftitemid);
            }
        }
        $validoptions = array();
        parent::__construct($elementName, $elementLabel, $validoptions, $attributes);
    }
}
