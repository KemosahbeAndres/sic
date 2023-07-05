<?php
// This file is part of Moodle - https://moodle.org/
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
 * Adds admin settings for the plugin.
 *
 * @package     local_helloworld
 * @category    admin
 * @copyright   2020 Your Name <email@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (isset($hassiteconfig) && $hassiteconfig) {
    global $ADMIN;
    $ADMIN->add('blockplugins', new admin_category('block_sic_settings', new lang_string('pluginname', 'block_sic')));
    $settingspage = new admin_settingpage('manageblocksic', new lang_string('manage', 'block_sic'));

    if ($ADMIN->fulltree) {
        $rut_otec = new admin_setting_configtext("block_sic/rutotec", get_string('rutotec','block_sic'), get_string('rutotecdesc','block_sic'), "", PARAM_TEXT);
        $settingspage->add($rut_otec);
    }

    $ADMIN->add('blockplugins', $settingspage);
}