window.onload = function () {
    if (window.navigator.geolocation) {
        window.navigator.geolocation.getCurrentPosition(console.log, console.log);
    }

    httpGetAsync('/', (response) => {
        parse_servers((JSON.parse(response)));
    });
}

function parse_servers(servers) {
    var table = document.getElementById('servers');

    document.getElementById('server-count').innerHTML += Object.keys(servers.servers_list).length;

    for (let [key, value] of Object.entries(servers.servers_list)) {
        var row = table.insertRow(1);
        var th = document.createElement('th');

        th.innerHTML = key;
        th.scope = 'row';
        row.appendChild(th);

        var cell1 = row.insertCell(1);
        var cell2 = row.insertCell(2);
        var cell3 = row.insertCell(3);

        $.ajax('https://ipapi.co/' + value[0] + '/json', function (data) {
            console.log(data)
        });

        cell1.innerHTML = 'not found';
        cell2.innerHTML = value[1];
        cell3.innerHTML = value[2] === 'w' ? 'Windows' : value[2] === 'l' ? 'Linux' : value[2];
    }
    document.getElementById('response-time').innerText += servers.time;
}

function httpPostAsync(params, theUrl, csrf, callback) {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open('POST', theUrl, true);

    xmlHttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            callback('success;' + xmlHttp.responseText);
        } else if (xmlHttp.readyState == 4) {
            callback('error;' + xmlHttp.responseText);
        }
    }
    if (csrf !== null)
        xmlHttp.setRequestHeader("X-CSRF-TOKEN", csrf);
    xmlHttp.send(params);
}

function httpGetAsync(theUrl, callback) {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
            callback(xmlHttp.responseText);
    }
    xmlHttp.open("GET", theUrl, true);
    xmlHttp.send(null);
}