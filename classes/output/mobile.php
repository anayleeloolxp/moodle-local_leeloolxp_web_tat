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
 * Output in mobile app for local_leeloolxp_web_tat.
 *
 * @package    local_leeloolxp_web_tat
 * @author Leeloo LXP <info@leeloolxp.com>
 * @copyright  2020 Leeloo LXP (https://leeloolxp.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_leeloolxp_web_tat\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Provider implementation for local_leeloolxp_web_tat.
 *
 */
class mobile {

    public static function view_hello() {
        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => '<h1 class="text-center">checkingblock</h1>',
                ],
                'javascript' => 'console.log("local_leeloolxp_web_tat view")',
            ],
        ];
    }
    
    public static function mobile_init($args) {

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => '<h1 class="text-center">checkingblock</h1>',
                ],
                
            ],
            'javascript' => 'console.log("local_leeloolxp_web_tat 1");
            console.log(this);
            this.ionRouteDataChanged = function() {
                console.log("local_leeloolxp_web_tat asdasd");
            };
            this.ionViewWillEnter = function() {
                console.log("local_leeloolxp_web_tat ionViewWillEnter");
            };
            this.ionViewDidEnter = function() {
                console.log("local_leeloolxp_web_tat ionViewDidEnter");
            };
            this.ionViewDidLeave = function() {
                console.log("local_leeloolxp_web_tat ionViewDidLeave");
            };

            this.canLeave = function() {
                console.log("local_leeloolxp_web_tat canLeave");
            };
            this.nav.DidLeave = function() {
                console.log("local_leeloolxp_web_tat DidLeave");
            };
            ',
        ];

        global $CFG, $USER, $PAGE;

        require_once($CFG->dirroot . '/local/leeloolxp_web_tat/lib.php');
        require_once($CFG->dirroot . '/lib/filelib.php');

        $configtat = get_config('local_leeloolxp_web_tat');

        if (!isset($configtat->leeloolxp_web_tatlicensekey) && isset($configtat->leeloolxp_web_tatlicensekey) == '') {
            return [
                'templates' => [
                    [
                        'id' => 'main',
                        'html' => '<h1 class="text-center">checkingblock</h1>',
                    ],
                    
                ],
                'javascript' => 'console.log("local_leeloolxp_web_tat 1")',
            ];
        }

        $licensekey = $configtat->leeloolxp_web_tatlicensekey;

        $tatenabled = $configtat->leeloolxp_web_tatenabled;

        if ($tatenabled == 0) {
            return [
                'templates' => [
                    [
                        'id' => 'main',
                        'html' => '<h1 class="text-center">checkingblock</h1>',
                    ],
                    'javascript' => 'console.log("local_leeloolxp_web_tat 2")',
                ],
            ];
        }

        if (!isset($USER->email) && isset($USER->email) == '') {
            return [
                'templates' => [
                    [
                        'id' => 'main',
                        'html' => '<h1 class="text-center">checkingblock</h1>',
                    ],
                    'javascript' => 'console.log("local_leeloolxp_web_tat 3")',
                ],
            ];
        }

        $useremail = $USER->email;

        $teamniourl = local_leeloolxp_web_tat_get_leelooinstall();

        if ($teamniourl == 'no') {
            return [
                'templates' => [
                    [
                        'id' => 'main',
                        'html' => '<h1 class="text-center">checkingblock</h1>',
                    ],
                    'javascript' => 'console.log("local_leeloolxp_web_tat 4")',
                ],
            ];
        }

        $useridteamnio = local_leeloolxp_web_tat_checkuser($teamniourl, $useremail);

        $checkahead = true;

        if ($useridteamnio == '0') {
            $checkahead = false;
        }

        if ($checkahead) {
            
            $returnjs = 'class AddonLocalLeeloolxpwebtatOfflineProvider {
                constructor() {

                    const myInterval = setInterval(function() {
                        var thisurl = window.location.href;
                        var checkifmodpage = thisurl.includes("mod_");
                        console.log(thisurl);
                        if( checkifmodpage ){
                            console.log("yes mod "+thisurl);
                        }
                    }, 1000);

                    '; 

            /*$returnjs .= '
                var is_popup_for_lat = ' . $popupison . ';
                var user_id = ' . $userid . ';
                var task_id = ' . $taskid . ';
                var teamniourl = "' . $teamniourl . '";

                // set local data for task id
                var already_set =  sessionStorage.getItem("tracking_activity_id");
                if(already_set == task_id) {
                var new_entry = "0";
                } else {
                    var  new_entry = "1";
                }

                document.getElementById("new_entry_val").value = new_entry;
                sessionStorage.setItem("tracking_activity_id", "null");
                sessionStorage.setItem("tracking_activity_id", task_id);

                if(is_popup_for_lat=="1") {
                    var tracking_on_for_LLT = sessionStorage.getItem("tracked");
                } else {
                    var tracking_on_for_LLT = 1;
                }

                if(tracking_on_for_LLT=="1") {
                    update_task_time(user_id,task_id,new_entry);

                    function update_task_time(user_id,tast_id,new_entry) {
                        var xhttp = new XMLHttpRequest();

                        xhttp.onreadystatechange = function(responseText) {
                            if (this.readyState == 4 && this.status == 200) {
                                var new_entry = "0";
                                document.getElementById("new_entry_val").value = new_entry;
                            }
                        };

                        xhttp.open(
                            "GET",
                            teamniourl+"/admin/sync_moodle_course/task_time_update/?user_id="+user_id+"&task_id="+task_id+"&is_new_entry="+new_entry+"&clockin="+1,
                            true
                        );
                        xhttp.send();

                    }

                    var myVar = setInterval(function() {
                        var new_new_entry = document.getElementById("new_entry_val").value;
                        update_task_time(user_id,task_id,new_new_entry);
                    },  60*1000);

                    window.onbeforeunload = function (e) {
                        var new_new_entry = document.getElementById("new_entry_val").value;
                        update_task_time(user_id,task_id,new_new_entry);

                        if(is_popup_for_lat=="1") {
                            var tracking_on = sessionStorage.getItem("tracked");
                        } else {
                            var tracking_on = 1;
                        }

                        if(tracking_on=="1") {
                            var xhttp = new XMLHttpRequest();

                            xhttp.onreadystatechange = function() {
                                if (this.readyState == 4 && this.status == 200) {
                                    //document.getElementById("new_entry_val").value = 0;
                                }
                            };

                            xhttp.open(
                                "GET",
                                teamniourl+"/admin/sync_moodle_course/update_clockin_on_task_update/"+user_id,
                                true
                            );
                            xhttp.send();

                        }

                    };
                }
            ';*/

            $returnjs .= '} }

            const webtattrackingOffline = new AddonLocalLeeloolxpwebtatOfflineProvider();
            const result = {
                webtattrackingOffline: webtattrackingOffline,
            };
            
            result;
            ';

        }

        file_put_contents(dirname(__FILE__).'/returnjs.js', print_r($returnjs, true));

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => '<h1 class="text-center">checkingblock</h1>',
                ],
            ],
            'javascript' => 'console.log("local_leeloolxp_tat end"); '.$returnjs.' ',
        ];
    }

}
