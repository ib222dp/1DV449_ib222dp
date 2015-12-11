"use strict";

function AjaxCon(url, callback) {

    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function() {
        if(xhr.readyState === 4) {
            if(xhr.status >= 200 && xhr.status < 300 || xhr.status === 304) {
                callback(xhr.responseText);
            }else {
                console.log("Läsfel, status: " + xhr.status);
            }
        }
    };

    xhr.open("get", url, true);

    xhr.send(null);
};