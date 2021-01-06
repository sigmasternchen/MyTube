(function () {
    let toastCounter = 0;

    const toastTime = 3000;

    window.toast = function (str) {
        let id = "toast" + toastCounter++;
        let element = document.createElement("div");
        element.id = id;
        element.className = "customToast customToastStart";
        element.innerText = str;

        document.body.appendChild(element);
        window.setTimeout(function () {
            element.className = "customToast customToastMain";
        }, 0);
        window.setTimeout(function () {
            element.className = "customToast customToastStop";
        }, toastTime);
        window.setTimeout(function () {
            document.body.removeChild(element);
        }, toastTime + 1000)
    }
})();