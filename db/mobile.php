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
 * Code for mobile app to define addons.
 *
 * @package     local_leeloolxp_web_tat
 * @category    upgrade
 * @copyright   2020 Leeloo LXP <info@leeloolxp.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$addons = [
    'local_leeloolxp_web_tat' => [
        'handlers' => [
            'hello' => [
                'delegate' => 'CoreCourseModuleDelegate',
                'method' => 'view_hello',
                'init' => 'mobile_init',
                'displaydata' => [
                    'title' => 'hello',
                    'type' => 'title',
                ],
            ],
        ],
        'lang' => [
            ['hello', 'local_leeloolxp_web_tat'],
        ],
    ],
];
