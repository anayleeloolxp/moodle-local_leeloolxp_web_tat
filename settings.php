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
 * Plugin administration pages are defined here.
 *
 * @package     local_leeloolxp_web_tat
 * @category    admin
 * @copyright   2020 Leeloo LXP <info@leeloolxp.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
if ($hassiteconfig) {
    $settings = new admin_settingpage('local_leeloolxp_web_tat', get_string('setting_title',
        'local_leeloolxp_web_tat'));
    $ADMIN->add('localplugins', $settings);
    $settings->add(new admin_setting_configtext('local_leeloolxp_web_tat/leeloolxp_web_tatlicensekey',
        get_string('license_id', 'local_leeloolxp_web_tat'), get_string('license_id_desc',
            'local_leeloolxp_web_tat'), '0', PARAM_TEXT));
    $settings->add(new admin_setting_configcheckbox('local_leeloolxp_web_tat/leeloolxp_web_tatenabled',
        get_string('enabled_plugin', 'local_leeloolxp_web_tat'), get_string('enabled_plugin_desc',
            'local_leeloolxp_web_tat'), '0', 1));
}
