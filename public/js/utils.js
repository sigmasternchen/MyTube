function removeClass(className, elements) {
    if (typeof elements == "string") {
        elements = [elements];
    }

    for (let i in elements) {
        let element = elements[i];
        element = document.querySelector(element);
        element.className = element.className.replaceAll(className, "");
    }
}