console.log("Started", self);

self.addEventListener("install", function (event) {
    self.skipWaiting();
    console.log("Installed", event);
});

self.addEventListener("activate", function (event) {
    console.log("Activated", event);
});
let messageListener = null;

self.addEventListener("stop_message", function (event) {
    clearInterval(messageListener);
});

self.addEventListener("message", function (event) {
    if (typeof event.data == "object") {
        var xhttp = new XMLHttpRequest();
        xhttp.open("GET", "http://localhost:8000/test", false);
        xhttp.send();
    } else {
        if (event.data == "stop_message") {
            clearInterval(messageListener);
        }
        if (event.data == "start_message") {
            var title = "Push message";
            var xhttp = new XMLHttpRequest();
            if (!messageListener) {
                messageListener = setInterval(function () {
                    xhttp.open(
                        "GET",
                        "http://localhost:8000/workerevent",
                        false
                    );
                    xhttp.send();
                    title = xhttp.responseText;
                    if (title.toLowerCase().includes("done")) {
                        clearInterval(messageListener);
                        messageListener = null;
                    }
                    console.log(title);
                    let data = {
                        action: "message",
                        text: title,
                    };
                    self.postMessage(data);
                }, 1500);
            }
        }
    }
});
