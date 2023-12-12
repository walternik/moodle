<?php

/**
 * Inject the upload element into all moodle module settings forms.
 *
 * @param moodleform $formwrapper The moodle quickforms wrapper object.
 * @param MoodleQuickForm $mform The actual form object (required to modify the form).
 */
function local_uploadfile_coursemodule_standard_elements($formwrapper, $mform) {
    global $CFG, $COURSE;

    if(!get_config('local_uploadfile', 'enabled')) {
        return;
    }

    $mform->addElement('header', 'resourcessection', get_string('resources', 'local_uploadfile'));

    MoodleQuickForm::registerElementType('course_resource', 
                                        "$CFG->dirroot/local/uploadfile/classes/course_resource_form_element.php",
                                        'local_uploadfile_course_resource_form_element');
    $cmid = null;
    if ($cm = $formwrapper->get_coursemodule()) {
        $cmid = $cm->id;
    }
    $options = array(
        'courseid' => $COURSE->id,
        'cmid' => $cmid
    );
    $attributes = array(
        'maxfiles' => 1
    );
    $mform->addElement('course_resource', 'resource', get_string('modresource', 'local_uploadfile'), $options, $attributes);
    $mform->addHelpButton('resource', 'modresource', 'local_uploadfile');
}


/**
 * Hook the add/edit of the course module.
 *
 * @param stdClass $data Data from the form submission.
 * @param stdClass $course The course.
 */
function local_uploadfile_coursemodule_edit_post_actions($data, $course) {
    global $DB, $USER;

    // It seems like the form did not contain the form field, we can return.
    if (!isset($data->resource)) {
        return $data;
    }
    
    $context = context_module::instance($data->coursemodule);
    // Set the filestorage object.
    $fs = get_file_storage();
    // Save the file if it exists that is currently in the draft area.
    file_save_draft_area_files($data->resource, $context->id, 'backup', 'activity', 0);
    // Get the file if it exists.
    $files = $fs->get_area_files($context->id, 'backup', 'activity', 0, 'itemid, filepath, filename', false);
    // Check that there is a file to process.
    if (count($files) == 1) {
        // Get the first (and only) file.
        $file = reset($files);

        $record = new stdClass();
        $record->cmid = $data->coursemodule;
        $record->file = $file->get_filename();
        $record->usermodified = $USER->id;
        $record->timemodified = time();
        if($existing = $DB->get_record('local_uploadfile', ['cmid' => $data->coursemodule])) {
            $record->id = $existing->id;
            $DB->update_record('local_uploadfile', $record);
        } else {
            $record->timecreated = time();
            $DB->insert_record('local_uploadfile', $record);
        }
    }

    return $data;
}


/**
 * This function extends the course navigation in the module context
 * adding the 'download summary' button
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $user
 * @param stdClass $course The course to object for the report
 */
function local_uploadfile_extend_navigation_user($navigation, $user, $course) {
    global $PAGE, $CFG, $USER;

    if(!get_config('local_uploadfile', 'enabled')) {
        return;
    }

    $pagecontext = $PAGE->context;
    if($pagecontext->contextlevel == CONTEXT_MODULE) {
        $cmid = optional_param('id', 0, PARAM_INT);
        if($cmid) {
            $context = context_module::instance($cmid);
            $fs = get_file_storage();
            $files = $fs->get_area_files($context->id, 'backup', 'activity', 0, 'itemid, filepath, filename', false);
            if (count($files) == 1) {
                // Get the first (and only) file.
                $file = reset($files);
                $modulefile = file_encode_url("$CFG->wwwroot/pluginfile.php",
                '/'. $file->get_contextid(). '/'. $file->get_component(). '/'. $file->get_filearea(). $file->get_filepath(). $file->get_filename(),
                true);
                $output = "<a href='".$modulefile."' target='_blank'>
                <button name='btn_download'><i class='icon fa fa-download fa-fw'></i>".get_string('buttonname', 'local_uploadfile')."</button>
                </a>";
                $PAGE->add_header_action($output);
            }
        }
    }
}