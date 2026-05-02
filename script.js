const easterword = "screw";
let inputBuffer = "";

window.addEventListener('keydown', (e) => {
    inputBuffer += e.key.toLowerCase();

    if (inputBuffer.length > easterword.length) {
        inputBuffer = inputBuffer.substring(1);
    }

    if (inputBuffer === easterword) {
        easteregg();
    }
});

function easteregg() {
    document.getElementById('easteregg1').innerHTML = "🖕 Microsoft";
    document.getElementById('easteregg2').innerHTML = "Screw them →";


}