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

/**
 * Function to get Leeloo Install
 *
 * @return string leeloo url
 */
function local_leeloolxp_web_tat_get_leelooinstall() {

    global $SESSION;

    if (isset($SESSION->tatleelooinstall)) {
        return $SESSION->tatleelooinstall;
    }

    global $CFG;
    require_once($CFG->dirroot . '/lib/filelib.php');

    $configtat = get_config('local_leeloolxp_web_tat');
    $licensekey = $configtat->leeloolxp_web_tatlicensekey;
    $postdata = array('license_key' => $licensekey);
    $url = 'https://leeloolxp.com/api_moodle.php/?action=page_info';
    $curl = new curl;
    $options = array(
        'CURLOPT_RETURNTRANSFER' => true,
        'CURLOPT_HEADER' => false,
        'CURLOPT_POST' => count($postdata),
    );

    if (!$output = $curl->post($url, $postdata, $options)) {
        $tatleelooinstallurl = 'no';
        $SESSION->tatleelooinstall = $tatleelooinstallurl;
    }

    $infoteamnio = json_decode($output);
    if ($infoteamnio->status != 'false') {
        $tatleelooinstallurl = $infoteamnio->data->install_url;
        $SESSION->tatleelooinstall = $tatleelooinstallurl;
    } else {
        $tatleelooinstallurl = 'no';
        $SESSION->tatleelooinstall = $tatleelooinstallurl;
    }

    return $tatleelooinstallurl;
}

/**
 * Function to check user
 *
 * @param string $teamniourl the teamniourl
 * @param string $useremail the useremail
 *
 * @return mixed user id or no
 */
function local_leeloolxp_web_tat_checkuser($teamniourl, $useremail) {

    global $SESSION;

    if (isset($SESSION->latcheckemail)) {
        return $SESSION->latcheckemail;
    }

    global $CFG;
    require_once($CFG->dirroot . '/lib/filelib.php');

    $postdata = array('email' => base64_encode($useremail));

    $url = $teamniourl . '/admin/sync_moodle_course/check_user_by_email/' . base64_encode($useremail);
    $curl = new curl;
    $options = array(
        'CURLOPT_RETURNTRANSFER' => true,
        'CURLOPT_HEADER' => false,
        'CURLOPT_POST' => count($postdata),
    );
    if (!$output = $curl->post($url, $postdata, $options)) {
        $latcheckemail = 'no';
        $SESSION->latcheckemail = $latcheckemail;
    }
    $latcheckemail = $output;
    $SESSION->latcheckemail = $latcheckemail;
    return $latcheckemail;
}

/**
 * Function to get user tat/tct settings
 *
 * @param string $teamniourl the teamniourl
 * @param string $userid the userid
 *
 * @return mixed lattatsetting or no
 */
function local_leeloolxp_web_tat_tattctsetting($teamniourl, $userid) {

    global $SESSION;

    if (isset($SESSION->lattatsetting)) {
        return $SESSION->lattatsetting;
    }

    global $CFG;
    require_once($CFG->dirroot . '/lib/filelib.php');

    $postdata = array();

    $url = $teamniourl . '/admin/sync_moodle_course/get_user_settings_tct_tat/' . $userid;
    $curl = new curl;
    $options = array(
        'CURLOPT_RETURNTRANSFER' => true,
        'CURLOPT_HEADER' => false,
        'CURLOPT_POST' => count($postdata),
    );
    if (!$output = $curl->post($url, $postdata, $options)) {
        $lattatsetting = 'no';
        $SESSION->lattatsetting = $lattatsetting;
    }
    $lattatsetting = $output;
    $SESSION->lattatsetting = $lattatsetting;
    return $lattatsetting;
}

/**
 * Plugin to sync user's  tracking on activity to LeelooLXP account of the Moodle Admin
 */
function local_leeloolxp_web_tat_before_footer() {
    $configtat = get_config('local_leeloolxp_web_tat');

    if (!isset($configtat->leeloolxp_web_tatlicensekey) && isset($configtat->leeloolxp_web_tatlicensekey) == '') {
        return true;
    }

    $licensekey = $configtat->leeloolxp_web_tatlicensekey;

    $tatenabled = $configtat->leeloolxp_web_tatenabled;

    if ($tatenabled == 0) {
        return true;
    }

    global $USER;
    global $PAGE;
    global $CFG;
    $baseurl = $CFG->wwwroot;

    global $CFG;
    require_once($CFG->dirroot . '/lib/filelib.php');

    if (!isset($USER->email) && isset($USER->email) == '') {
        return true;
    }

    $useremail = $USER->email;

    $teamniourl = local_leeloolxp_web_tat_get_leelooinstall();

    if ($teamniourl == 'no') {
        return true;
    }

    $useridteamnio = local_leeloolxp_web_tat_checkuser($teamniourl, $useremail);

    $checkahead = true;

    if ($useridteamnio == '0') {
        $checkahead = false;
    }

    if ($checkahead) {
        $activityresourceid = '';
        $id = false;
        if (
            $PAGE->pagetype == 'mod-leeloolxpvc-conference' ||
            $PAGE->pagetype == 'mod-leeloolxpvc-view' ||
            $PAGE->pagetype == 'mod-resource-view' ||
            $PAGE->pagetype == 'mod-leeloolxpvimeo-view' ||
            $PAGE->pagetype == 'mod-forum-view' ||
            $PAGE->pagetype == 'mod-book-view' ||
            $PAGE->pagetype == 'mod-assign-view' ||
            $PAGE->pagetype == 'mod-survey-view' ||
            $PAGE->pagetype == 'mod-page-view' ||
            $PAGE->pagetype == 'mod-quiz-view' ||
            $PAGE->pagetype == 'mod-quiz-attempt' ||
            $PAGE->pagetype == 'mod-quiz-summary' ||
            $PAGE->pagetype == 'mod-quiz-summary' ||
            $PAGE->pagetype == 'mod-chat-view' ||
            $PAGE->pagetype == 'mod-choice-view' ||
            $PAGE->pagetype == 'mod-lti-view' ||
            $PAGE->pagetype == 'mod-feedback-view' ||
            $PAGE->pagetype == 'mod-data-view' ||
            $PAGE->pagetype == 'mod-forum-view' ||
            $PAGE->pagetype == 'mod-glossary-view' ||
            $PAGE->pagetype == 'mod-scorm-view' ||
            $PAGE->pagetype == 'mod-wiki-view' ||
            $PAGE->pagetype == 'mod-workshop-view' ||
            $PAGE->pagetype == 'mod-folder-view' ||
            $PAGE->pagetype == 'mod-imscp-view' ||
            $PAGE->pagetype == 'mod-label-view' ||
            $PAGE->pagetype == 'mod-url-view' ||
            $PAGE->pagetype == 'mod-lesson-view'
        ) {
            if ($PAGE->pagetype == 'mod-quiz-attempt' || $PAGE->pagetype == 'mod-quiz-summary') {
                $reqcmid = optional_param('cmid', null, PARAM_RAW);
                $id = $reqcmid;
            } else {
                $reqid = optional_param('id', null, PARAM_RAW);
                if (isset($reqid)) {
                    $id = $reqid;
                }
            }
            if ($id) {
                $activityresourceid = $id;
                $postdata = array('activityid' => $activityresourceid, 'email' => base64_encode($useremail));
                $url = $teamniourl . '/admin/sync_moodle_course/get_activity_task/';
                $curl = new curl;
                $options = array(
                    'CURLOPT_RETURNTRANSFER' => true,
                    'CURLOPT_HEADER' => false,
                    'CURLOPT_POST' => count($postdata),
                );
                $outputtaskdetails = $curl->post($url, $postdata, $options);
                $userid = $useridteamnio;
                if (!empty($outputtaskdetails)) {
                    $output = local_leeloolxp_web_tat_tattctsetting($teamniourl, $userid);
                    $usersettings = json_decode($output);
                    echo "<input type = 'hidden' value = '' id='new_entry_val'/>";
                    $taskid = $outputtaskdetails;
                    $logintrackingconfig = get_config('local_leeloolxp_web_login_tracking');
                    $popupison = $logintrackingconfig->web_loginlogout_popup;

                    // get users OS and browser

                    $useragent = $_SERVER['HTTP_USER_AGENT'];
                    $osplatform =   "Unknown";
                    $osarray =   array(
                        '/windows nt 10/i'      =>  'Windows 10',
                        '/windows nt 6.3/i'     =>  'Windows 8.1',
                        '/windows nt 6.2/i'     =>  'Windows 8',
                        '/windows nt 6.1/i'     =>  'Windows 7',
                        '/windows nt 6.0/i'     =>  'Windows Vista',
                        '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                        '/windows nt 5.1/i'     =>  'Windows XP',
                        '/windows xp/i'         =>  'Windows XP',
                        '/windows nt 5.0/i'     =>  'Windows 2000',
                        '/windows me/i'         =>  'Windows ME',
                        '/win98/i'              =>  'Windows 98',
                        '/win95/i'              =>  'Windows 95',
                        '/win16/i'              =>  'Windows 3.11',
                        '/macintosh|mac os x/i' =>  'Mac OS X',
                        '/mac_powerpc/i'        =>  'Mac OS 9',
                        '/linux/i'              =>  'Linux',
                        '/ubuntu/i'             =>  'Ubuntu',
                        '/iphone/i'             =>  'iPhone',
                        '/ipod/i'               =>  'iPod',
                        '/ipad/i'               =>  'iPad',
                        '/android/i'            =>  'Android',
                        '/blackberry/i'         =>  'BlackBerry',
                        '/webos/i'              =>  'Mobile'
                    );

                    foreach ( $osarray as $regex => $value ) { 
                        if ( preg_match($regex, $useragent ) ) {
                            $osplatform = $value;
                        }
                    } 

                    $browser        = "Unknown";
                    $browserarray  = array(
                        '/msie/i'       =>  'Internet Explorer',
                        '/firefox/i'    =>  'Firefox',
                        '/safari/i'     =>  'Safari',
                        '/chrome/i'     =>  'Chrome',
                        '/edge/i'       =>  'Edge',
                        '/opera/i'      =>  'Opera',
                        '/netscape/i'   =>  'Netscape',
                        '/maxthon/i'    =>  'Maxthon',
                        '/konqueror/i'  =>  'Konqueror',
                        '/mobile/i'     =>  'Mobile Browser'
                    );

                    foreach ( $browserarray as $regex => $value ) { 
                        if ( preg_match( $regex, $useragent ) ) {
                            $browser = $value;
                        }
                    }

                    echo '<script type="text/javascript">
                        var is_popup_for_lat = ' . $popupison . ';
                        var user_id = ' . $userid . ';
                        var task_id = ' . $taskid . ';
                        var osplatform = "'.$osplatform.'";
                        var browser = "'.$browser.'";
                        var teamniourl = "' . $teamniourl . '";

                        // set local data for task id
                        var already_set =  localStorage.getItem("tracking_activity_id");
                        if(already_set == task_id) {
                          var new_entry = "0";
                        } else {
                            var  new_entry = "1";
                        }

                        document.getElementById("new_entry_val").value = new_entry;
                        localStorage.setItem("tracking_activity_id", "null");
                        localStorage.setItem("tracking_activity_id", task_id);

                        if(is_popup_for_lat=="1") {
                            var tracking_on_for_LLT = localStorage.getItem("tracked");
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
                                    teamniourl+"/admin/sync_moodle_course/task_time_update/?user_id="+user_id+"&task_id="+task_id+"&is_new_entry="+new_entry+"&clockin="+1+"&osplatform="+osplatform+"&browser="+browser,
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
                                    var tracking_on = localStorage.getItem("tracked");
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
                    </script>';
                }
            }
        }
    }
}
