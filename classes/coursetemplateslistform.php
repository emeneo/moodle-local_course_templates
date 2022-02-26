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
 * The definition of the plugin's 'list templates' form.
 *
 * @package     local_course_templates
 * @copyright   2022 Andrew Caya <andrewscaya@yahoo.ca>
 * @author      Andrew Caya
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_templates;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir.'/formslib.php');

/**
 * The coursetemplateslistform class.
 *
 * This class is this plugin's 'list templates' form.
 */
class coursetemplateslistform extends \moodleform {

    /**
     * The button's id is the category id.
     *
     * @var int
     */
    protected $buttonid = 1;

    /**
     * The form's main definition method.
     *
     * @throws \coding_exception
     */
    protected function definition() {
        $mform = $this->_form;

        if (isset($this->_customdata['buttonid'])) {
            $this->set_button_id($this->_customdata['buttonid']);
        } else {
            return;
        }

        $mform->addElement(
            'submit',
            'coursetemplateslist_' . $this->get_button_id(),
            get_string('useastemplate', 'local_course_templates')
        );
    }

    /**
     * Gets the button id (form's custom data).
     *
     * @return int
     */
    public function get_button_id() {
        return $this->buttonid;
    }

    /**
     * Sets the button id (form's custom data).
     *
     * @param int $id
     *
     * @return int
     */
    public function set_button_id($id) {
        $this->buttonid = (int) $id;

        return $this->buttonid;
    }
}
