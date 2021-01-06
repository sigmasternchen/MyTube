function clipboard(str) {
    var clipboardField = document.createElement("textarea");
    clipboardField.value = str;
    document.body.appendChild(clipboardField);
    clipboardField.select();
    clipboardField.setSelectionRange(0, 99999);
    document.execCommand("copy");
    document.body.removeChild(clipboardField);
    toast("Copied to clipboard.");
}