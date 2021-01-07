function ajaxGet(url, callback) {
    var http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.onreadystatechange = function () {
        if (http.readyState === 4) {
            callback(http.responseText);
        }
    }
    http.send(null);
}

function ajaxPost(url, data, callback) {
    var http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.onreadystatechange = function () {
        if (http.readyState === 4) {
            callback(http.responseText);
        }
    }
    http.send(data);
}