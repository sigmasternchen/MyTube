function search(input, elements, contents) {
    for (let i = 0; i < contents.length; i++) {
        console.log(contents[i])
        contents[i] = contents[i].toLowerCase();
    }

    input.onkeyup = function () {
        let tokens = input.value;
        tokens = tokens.toLowerCase();
        tokens = tokens.split(" ");

        for (let i = 0; i < contents.length; i++) {
            let okay = true;
            for (let j = 0; j < tokens.length; j++) {
                if (contents[i].indexOf(tokens[j]) < 0) {
                    okay = false;
                    break;
                }
            }

            elements[i].style.display = okay ? "block" : "none";
        }
    }
}