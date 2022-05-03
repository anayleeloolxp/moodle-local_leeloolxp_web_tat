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
        $baseemail = base64_encode($useremail);

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

        if ($useridteamnio == '0') {
            return [
                'templates' => [
                    [
                        'id' => 'main',
                        'html' => '<h1 class="text-center">checkingblock</h1>',
                    ],
                    'javascript' => 'console.log("local_leeloolxp_web_tat 5")',
                ],
            ];

        }

        $logintrackingconfig = get_config('local_leeloolxp_web_login_tracking');
        $popupison = $logintrackingconfig->web_loginlogout_popup;
            
        $returnjs = '
        class AddonLocalLeeloolxpwebtatOfflineProvider {
            constructor() {

                (function(history){
                    var pushState = history.pushState;
                    history.pushState = function(state) {

                        var thisurl = arguments[2];
                        var checkifmodpage = thisurl.includes("mod_");
                        if( checkifmodpage ){
                            const urlarr = thisurl.split("?");
                            const idarr = urlarr[0].split("/");
                            var arid = idarr.at(-1);
                            if(arid){

                                console.log("run code for came on activity");

                                var is_popup_for_lat = ' . $popupison . ';

                                if(is_popup_for_lat=="1") {
                                    var tracking_on_for_LLT = sessionStorage.getItem("tracked");
                                } else {
                                    var tracking_on_for_LLT = 1;
                                }

                                if(tracking_on_for_LLT=="1") {
                                    var xhttp = new XMLHttpRequest();
                                    xhttp.onreadystatechange = function() {
                                        if (this.readyState == 4 && this.status == 200) {
                                            var taskid = xhttp.responseText;

                                            if( taskid ){

                                                
                                                var user_id = ' . $useridteamnio . ';
                                                var task_id = taskid;
                                                var osplatform = "Unknown";
                                                var ipaddress = "localhost";
                                                var browser = "mobileapp";
                                                var teamniourl = "' . $teamniourl . '";

                                                // set local data for task id
                                                var already_set =  sessionStorage.getItem("tracking_activity_id");
                                                if(already_set == task_id) {
                                                var new_entry = "0";
                                                } else {
                                                    var  new_entry = "1";
                                                }

                                                sessionStorage.setItem("new_entry_val", new_entry);
                                                sessionStorage.setItem("tracking_activity_id", "null");
                                                sessionStorage.setItem("tracking_activity_id", task_id);

                                                
                                                update_task_time(user_id,task_id,new_entry);

                                                function update_task_time(user_id,tast_id,new_entry) {
                                                    var xhttp1 = new XMLHttpRequest();

                                                    xhttp1.onreadystatechange = function(responseText) {
                                                        if (this.readyState == 4 && this.status == 200) {
                                                            var new_entry = "0";
                                                            sessionStorage.setItem("new_entry_val", new_entry);
                                                        }
                                                    };

                                                    xhttp1.open(
                                                        "GET",
                                                        teamniourl+"/admin/sync_moodle_course/task_time_update/?user_id="+user_id+"&task_id="+task_id+"&is_new_entry="+new_entry+"&clockin="+1+"&osplatform="+osplatform+"&browser="+browser+"&ipaddress="+ipaddress+"&installlogintoken='.$_COOKIE['installlogintoken'].'",
                                                        true
                                                    );
                                                    xhttp1.send();  

                                                }

                                                var myVar = setInterval(function() {
                                                    var new_new_entry = sessionStorage.getItem("new_entry_val");
                                                    var already_set =  sessionStorage.getItem("tracking_activity_id");
                                                    if(already_set!="null"){
                                                        update_task_time(user_id,task_id,new_new_entry);
                                                    }else{
                                                        clearInterval(myVar);
                                                    }
                                                    
                                                },  60*1000);

                                                

                                            }
                                        }
                                    };
                                    xhttp.open("GET", "'.$teamniourl.'/admin/sync_moodle_course/get_activity_task/?activityid="+arid+"&email='.$baseemail.'&installlogintoken='.$_COOKIE['installlogintoken'].'", true);
                                    xhttp.send();
                                }
                                
                            }
                        }

                        var lasturl = window.location.href;
                        var checkifprevmodpage = lasturl.includes("mod_");
                        if( checkifprevmodpage ){
                            const urlarr = lasturl.split("?");
                            const idarr = urlarr[0].split("/");
                            var arid = idarr.at(-1);
                            if(arid){
                                console.log("run code for left from activity");
                                var is_popup_for_lat = ' . $popupison . ';

                                if(is_popup_for_lat=="1") {
                                    var tracking_on = sessionStorage.getItem("tracked");
                                } else {
                                    var tracking_on = 1;
                                }

                                if(tracking_on=="1") {
                                    var xhttp2 = new XMLHttpRequest();
                                    xhttp2.onreadystatechange = function() {
                                        if (this.readyState == 4 && this.status == 200) {
                                            var taskid = xhttp2.responseText;

                                            if( taskid ){
                                                
                                                var user_id = ' . $useridteamnio . ';
                                                var task_id = taskid;
                                                var osplatform = "Unknown";
                                                var ipaddress = "localhost";
                                                var browser = "mobileapp";
                                                var teamniourl = "' . $teamniourl . '";

                                                var new_new_entry = sessionStorage.getItem("new_entry_val");

                                                function update_task_time(user_id,tast_id,new_entry) {
                                                    var xhttp1 = new XMLHttpRequest();

                                                    xhttp1.onreadystatechange = function(responseText) {
                                                        if (this.readyState == 4 && this.status == 200) {
                                                            var new_entry = "0";
                                                            sessionStorage.setItem("new_entry_val", new_entry);
                                                        }
                                                    };

                                                    xhttp1.open(
                                                        "GET",
                                                        teamniourl+"/admin/sync_moodle_course/task_time_update/?user_id="+user_id+"&task_id="+task_id+"&is_new_entry="+new_entry+"&clockin="+1+"&osplatform="+osplatform+"&browser="+browser+"&ipaddress="+ipaddress+"&installlogintoken='.$_COOKIE['installlogintoken'].'",
                                                        true
                                                    );
                                                    xhttp1.send();  

                                                }

                                                

                                                update_task_time(user_id,task_id,new_new_entry);

                                                var xhttp3 = new XMLHttpRequest();

                                                xhttp3.onreadystatechange = function() {
                                                    if (this.readyState == 4 && this.status == 200) {
                                                        //sessionStorage.setItem("new_entry_val",0);
                                                        sessionStorage.setItem("tracking_activity_id", "null");
                                                    }
                                                };

                                                xhttp3.open(
                                                    "GET",
                                                    teamniourl+"/admin/sync_moodle_course/update_clockin_on_task_update/"+user_id+"&installlogintoken='.$_COOKIE['installlogintoken'].'",
                                                    true
                                                );
                                                xhttp3.send();

                                                

                                            }
                                        }
                                    };
                                    xhttp2.open("GET", "'.$teamniourl.'/admin/sync_moodle_course/get_activity_task/?activityid="+arid+"&email='.$baseemail.'&installlogintoken='.$_COOKIE['installlogintoken'].'", true);
                                    xhttp2.send();

                                }

                            }
                        }
                        
                        return pushState.apply(history, arguments);
                    };
                })(window.history);
                

                '; 

        $returnjs .= '} }

        const webtattrackingOffline = new AddonLocalLeeloolxpwebtatOfflineProvider();
        const result = {
            webtattrackingOffline: webtattrackingOffline,
        };
        
        result;
        ';


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
