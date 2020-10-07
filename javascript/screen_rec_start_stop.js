var video = document.getElementById('screenvideo');

if(!navigator.getDisplayMedia && !navigator.mediaDevices.getDisplayMedia) {
    var error = 'Your browser does NOT support the getDisplayMedia API.';
    document.querySelector('h1').innerHTML = error;

    document.querySelector('video').style.display = 'none';
    document.getElementById('btn-start-recording').style.display = 'none';
    document.getElementById('btn-stop-recording').style.display = 'none';
    throw new Error(error);
}

function invokeGetDisplayMedia(success, error) {
    var displaymediastreamconstraints = {
        video: {
            displaySurface: 'tab', // monitor, window, application, browser
            logicalSurface: true,
            cursor: 'always' // never, always, motion
        }
    };

    // above constraints are NOT supported YET
    // that's why overridnig them
    displaymediastreamconstraints = {
        video: true
    };

    if(navigator.mediaDevices.getDisplayMedia) {
        navigator.mediaDevices.getDisplayMedia(displaymediastreamconstraints).then(success).catch(error);
    }
    else {
        navigator.getDisplayMedia(displaymediastreamconstraints).then(success).catch(error);
    }
}

function stopRecordingCallback() {
    document.getElementsByTagName("body")[0].style.display = "none";
    window.location.href = "/";

    // get recorded blob
    var blob = recorder.getBlob();

    // generating a random file name
    var fileName = getFileName('webm');

    // we need to upload "File" --- not "Blob"
    var fileObject = new File([blob], fileName, {
        type: 'video/webm'
    });

    /*uploadToPHPServer(fileObject, function(response, fileDownloadURL) {
        if(response !== 'ended') {
            document.getElementById('header').innerHTML = response; // upload progress
            return;
        }

        document.getElementById('header').innerHTML = '<a href="' + fileDownloadURL + '" target="_blank">' + fileDownloadURL + '</a>';

        alert('Successfully uploaded recorded blob.');

        // preview uploaded file
        document.getElementById('your-video-id').srcObject = null;
        document.getElementById('your-video-id').src = fileDownloadURL;

        // open uploaded file in a new tab
        window.open(fileDownloadURL);
    });*/

    video.src = video.srcObject = null;
    video.src = URL.createObjectURL(recorder.getBlob());
    
    recorder.screen.stop();
    recorder.destroy();
    recorder = null;

    document.getElementById('btn-start-recording').disabled = false;
}

var recorder; // globally accessible

document.getElementById('btn-start-recording').onclick = function() {
    this.disabled = true;
    captureScreen(function(screen) {
        video.srcObject = screen;

        recorder = RecordRTC(screen, {
            type: 'video'
        });

        recorder.startRecording();

        // release screen on stopRecording
        recorder.screen = screen;

        document.getElementById('btn-stop-recording').disabled = false;
    });
};

document.getElementById('btn-stop-recording').onclick = function() {
    this.disabled = true;
    recorder.stopRecording(stopRecordingCallback);
};

function addStreamStopListener(stream, callback) {
    stream.addEventListener('ended', function() {
        callback();
        callback = function() {};
    }, false);
    stream.addEventListener('inactive', function() {
        callback();
        callback = function() {};
    }, false);
    stream.getTracks().forEach(function(track) {
        track.addEventListener('ended', function() {
            callback();
            callback = function() {};
        }, false);
        track.addEventListener('inactive', function() {
            callback();
            callback = function() {};
        }, false);
    });
}

document.getElementById('btn-start-recording').click();

function uploadToPHPServer(blob, callback) {
    // create FormData
    var formData = new FormData();
    formData.append('video-filename', blob.name);
    formData.append('video-blob', blob);
    callback('Uploading recorded-file to server.');

    //var upload_url = 'https://webrtcweb.com/f/';
    var upload_url = '/local/leeloolxp_web_tat/save_recording.php';

    //var upload_directory = upload_url;
    var upload_directory = '/local/leeloolxp_web_tat/recordings/';
    
    makeXMLHttpRequest(upload_url, formData, function(progress) {
        if (progress !== 'upload-ended') {
            callback(progress);
            return;
        }
        var initialURL = upload_directory + blob.name;
        callback('ended', initialURL);
    });
}

function makeXMLHttpRequest(url, data, callback) {
    var request = new XMLHttpRequest();
    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            if (request.responseText === 'success') {
                callback('upload-ended');
                return;
            }
            alert(request.responseText);
            return;
        }
    };
    request.upload.onloadstart = function() {
        callback('PHP upload started...');
    };
    request.upload.onprogress = function(event) {
        callback('PHP upload Progress ' + Math.round(event.loaded / event.total * 100) + "%");
    };
    request.upload.onload = function() {
        callback('progress-about-to-end');
    };
    request.upload.onload = function() {
        callback('PHP upload ended. Getting file URL.');
    };
    request.upload.onerror = function(error) {
        callback('PHP upload failed.');
    };
    request.upload.onabort = function(error) {
        callback('PHP upload aborted.');
    };
    request.open('POST', url,false);
    //request.open('POST', url);
    request.send(data);
}

// this function is used to generate random file name
function getFileName(fileExtension) {
    var d = new Date();
    var year = d.getUTCFullYear();
    var month = d.getUTCMonth();
    var date = d.getUTCDate();
    return 'RecordRTC-' + year + month + date + '-' + getRandomString() + '.' + fileExtension;
}

function getRandomString() {
    if (window.crypto && window.crypto.getRandomValues && navigator.userAgent.indexOf('Safari') === -1) {
        var a = window.crypto.getRandomValues(new Uint32Array(3)),
            token = '';
        for (var i = 0, l = a.length; i < l; i++) {
            token += a[i].toString(36);
        }
        return token;
    } else {
        return (Math.random() * new Date().getTime()).toString(36).replace(/\./g, '');
    }
}
