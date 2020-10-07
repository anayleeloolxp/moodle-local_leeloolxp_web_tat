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

require_once(dirname(dirname(__DIR__)) . '/config.php');

function local_leeloolxp_web_tat_before_footer() {

    $configtat = get_config('local_leeloolxp_web_tat');

    $licensekey = $configtat->leeloolxp_web_tatlicensekey;

    $tatenabled = $configtat->leeloolxp_web_tatenabled;

    if ($tatenabled == 0) {
        return true;
    }

    global $USER;

    global $PAGE;

    global $DB;

    global $CFG;
    $baseurl = $CFG->wwwroot;
    $useremail = $USER->email;
    $sesskey = $USER->sesskey;
    $logouturl = $baseurl . "/login/logout.php?sesskey=" . $sesskey;
    $postdata = '&license_key=' . $licensekey;
    $url = 'https://leeloolxp.com/api_moodle.php/?action=page_info';
    $curl = new curl;
    $options = array(
        'CURLOPT_RETURNTRANSFER' => true,
        'CURLOPT_HEADER' => false,
        'CURLOPT_POST' => count($postdata),
    );
    if (!$output = $curl->post($url, $postdata, $options)) {
            return true;
    }
    $infoteamnio = json_decode($output);
    if ($infoteamnio->status != 'false') {
        $teamniourl = $infoteamnio->data->install_url;
    } else {
        return true;
    }

    $useridteamnio = local_leeloolxp_web_tat_check_user_teamnio($useremail, $teamniourl);

    $checkahead = true;

    if ($useridteamnio == '0') {
        $checkahead = false;
    }

    if ($checkahead) {
        $activityresourceid = '';
        $id = false;
        if ($PAGE->pagetype == 'mod-wespher-conference'
        || $PAGE->pagetype == 'mod-wespher-view' || $PAGE->pagetype == 'mod-resource-view' ||
        $PAGE->pagetype == 'mod-regularvideo-view' || $PAGE->pagetype == 'mod-forum-view' ||
        $PAGE->pagetype == 'mod-book-view' || $PAGE->pagetype == 'mod-assign-view' || $PAGE->pagetype
        == 'mod-survey-view' || $PAGE->pagetype == 'mod-page-view' || $PAGE->pagetype ==
        'mod-quiz-view' || $PAGE->pagetype == 'mod-quiz-attempt' || $PAGE->pagetype ==
        'mod-quiz-summary' || $PAGE->pagetype == 'mod-quiz-summary' || $PAGE->pagetype ==
        'mod-chat-view' || $PAGE->pagetype == 'mod-choice-view' || $PAGE->pagetype == 'mod-lti-view' ||
        $PAGE->pagetype == 'mod-feedback-view' || $PAGE->pagetype == 'mod-data-view' || $PAGE->pagetype
        == 'mod-forum-view' || $PAGE->pagetype == 'mod-glossary-view' || $PAGE->pagetype ==
        'mod-scorm-view' || $PAGE->pagetype == 'mod-wiki-view' || $PAGE->pagetype ==
        'mod-workshop-view' || $PAGE->pagetype == 'mod-folder-view' || $PAGE->pagetype ==
        'mod-imscp-view' || $PAGE->pagetype == 'mod-label-view' || $PAGE->pagetype == 'mod-url-view') {
            if ($PAGE->pagetype == 'mod-quiz-attempt' || $PAGE->pagetype == 'mod-quiz-summary') {
                $id = $_REQUEST['cmid'];
            } else {
                if (isset($_REQUEST['id'])) {
                    $id = $_REQUEST['id'];
                }
            }
            if ($id) {
                $activityresourceid = $id;
                $postdata = '&activityid=' . $activityresourceid . "&email=" . $useremail;
                $url = $teamniourl . '/admin/sync_moodle_course/get_activity_task/';
                $curl = new curl;
                $options = array(
                    'CURLOPT_RETURNTRANSFER' => true,
                    'CURLOPT_HEADER' => false,
                    'CURLOPT_POST' => count($postdata),
                );
                $outputtaskdetails = $curl->post($url, $postdata, $options);
                $userid = $useridteamnio;
                $PAGE->requires->js('/local/leeloolxp_web_tat/javascript/jquery.js');
                if (!empty($outputtaskdetails)) {
                    $url = $teamniourl . '/admin/sync_moodle_course/get_user_settings_tct_tat/' . $userid;
                    $curl = new curl;
                    $options = array(
                        'CURLOPT_RETURNTRANSFER' => true,
                        'CURLOPT_HEADER' => false,
                        'CURLOPT_POST' => count($postdata),
                    );
                    $output = $curl->post($url, $postdata, $options);
                    $usersettings = json_decode($output);
                    $workingdate = date('Y-m-d');
                    echo "<input type = 'hidden' value = '' id='new_entry_val'/>";
                    $taskid = $outputtaskdetails;
                    if ($usersettings->user_data->webcam_shots == 1 && 1 == 0) {
                        $webinterval = $usersettings->user_data->webcam_interval * 60;
                        echo '<div style="display:none" class="for_webcam" id="my_camera"></div>
                        <script type="text/javascript" src="'.$baseurl.'/local/
                        leeloolxp_web_tat/javascript/webcam.min.js"></script>
                        <script>
                        document.getElementsByTagName("body")[0].style.display = "none";
                        var webcam_interval = '.$webinterval.';
                        if (navigator.mediaDevices.getUserMedia) {
                            navigator.mediaDevices.getUserMedia({ video: true }) .then(function (stream) {
                                document.getElementsByTagName("body")[0].style.display = "block";
                            }).catch(function (err0r) {
                                document.getElementsByTagName("body")[0].style.display = "none";
                                alert("'.get_string("need_webcam", "local_leeloolxp_web_tat").'");
                                window.location.href = '.$baseurl.';
                            });

                            }

                            Webcam.set({

                                width: 320,

                                height: 240,

                                image_format: "jpeg",

                                jpeg_quality: 90,



                            });



                            function take_webshot() {



                                Webcam.attach( "#my_camera" );

                                setTimeout(function(){

                                    // take snapshot and get image data

                                    Webcam.snap(function(data_uri) {

                                        var base64image = data_uri;

                                        $.ajax({

                                            url: "'.$teamniourl.'/admin/sync_moodle_course/save_webcamshot/",

                                            type: "post",

                                            data: {image: data_uri,task_id: '.$taskid.',user_id: '.$userid.'},

                                            success: function(data){



                                            }

                                        });



                                        Webcam.reset();

                                    });

                                }, 3000);



                            }





                            setInterval(function() {

                                take_webshot();

                            },  webcam_interval*1000);

                        </script>';



                    }

                    if ($usersettings->user_data->screenshot_active == 1 && 1 == 0) {
                        $screnshotint = $usersettings->user_data->screenshots_interval * 60;
                        $PAGE->requires->js('/local/leeloolxp_web_tat/javascript/screen_recording.js');
                        $PAGE->requires->js('/local/leeloolxp_web_tat/javascript/screen_rec_start_stop.
                        js');
                        echo '<div class="for_screen" style="display:none;">
                        <button id="btn-start-recording">'.get_string("start_recording",
                        "local_leeloolxp_web_tat").'</button>
                        <button id="btn-stop-recording" disabled>'.get_string("stop_recording",
                        "local_leeloolxp_web_tat").'</button>
                        <video id="screenvideo" controls autoplay playsinline></video>
                        <div id="header" disabled></div>
                        <canvas id = "canvas" width = "600" height = "300"></canvas>
                        <button id = "snap" onclick = "snap()">'.get_string("take_snapshot",
                        "local_leeloolxp_web_tat").'</button></div><script>
                        var screenshot_interval = '.$screnshotint.'

                            var video = document.getElementById("screenvideo");

                            var canvas = document.getElementById("canvas");

                            var context = canvas.getContext("2d");

                            var myWidth, myHeight, ratio;



                            video.addEventListener("loadedmetadata", function() {

                                ratio = video.videoWidth/video.videoHeight;

                                myWidth = video.videoWidth-100;

                                myHeight = parseInt(myWidth/ratio,10);

                                canvas.width = myWidth;

                                canvas.height = myHeight;

                            },false);



                            function captureScreen(callback) {

                                document.getElementsByTagName("body")[0].style.display = "none";

                                invokeGetDisplayMedia(function(screen) {

                                    addStreamStopListener(screen, function() {

                                        document.getElementById("btn-stop-recording").click();

                                    });

                                    document.getElementsByTagName("body")[0].style.display = "block";

                                    videoTrack = screen.getVideoTracks()[0];



                                    if( videoTrack.getSettings().displaySurface != "monitor" ){

                                        document.getElementsByTagName("body")[0].style.display = "none";

                                        alert("'.get_string("entire_screen", "local_leeloolxp_web_tat").'");

                                        window.location.href = '.$baseurl.';

                                    }

                                    callback(screen);

                                }, function(error) {

                                    document.getElementsByTagName("body")[0].style.display = "none";

                                    alert("'.get_string("need_screencast", "local_leeloolxp_web_tat").'");

                                    window.location.href = '.$baseurl.';

                                });

                            }



                            function snap() {

                                context.fillRect(0,0,myWidth,myHeight);

                                context.drawImage(video,0,0,myWidth,myHeight);

                                var dataURI = document.getElementById("canvas").toDataURL("image/jpeg");
                                $.ajax({
                                    url: "'.$teamniourl.'/admin/sync_moodle_course/save_screshots/",
                                    type: "post",
                                    data: {image: dataURI,task_id: '.$taskid.',user_id: '.$userid.'},
                                    success: function(data){}
                                });



                            }

                            setInterval(function() {

                                snap();

                            },  screenshot_interval*1000);
                            </script>';
                    }
                    $logintrackingconfig = get_config('local_leeloolxp_web_login_tracking');
                    $popupison = $logintrackingconfig->web_loginlogout_popup;
                    echo '<script type="text/javascript">
                    var is_popup_for_lat = '.$popupison.';
                    var user_id = '.$userid.';
                        var task_id = '.$taskid.';
                        var teamniourl = '.$teamniourl.';
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
                            xhttp.open("GET", teamniourl+"/admin/sync_moodle_course/
                            task_time_update/?user_id="+user_id+"&task_id="+task_id+"&is_new_entry="
                            +new_entry, true);
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





                                xhttp.open("GET", teamniourl+"/admin/sync_moodle_course/update_clockin_on_task_update/"+user_id, true);



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

function local_leeloolxp_web_tat_check_user_teamnio($email, $teamniourl) {

    $url = $teamniourl . '/admin/sync_moodle_course/check_user_by_email/' . $email;
    $postdata = array('email'=>$email);
    $curl = new curl;
    $options = array(
        'CURLOPT_RETURNTRANSFER' => true,
        'CURLOPT_HEADER' => false,
        'CURLOPT_POST' => count($postdata),
    );
    $output = $curl->post($url, $postdata, $options);
    return $output;
}
