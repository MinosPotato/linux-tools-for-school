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

const searchInput = document.getElementById('searchInput');
const categorySelect = document.getElementById('categorySelect');
const toolItems = document.querySelectorAll('.tool-item');

function filterTools() {
    const searchText = searchInput.value.toLowerCase();
    const selectedCat = categorySelect.value;

    toolItems.forEach(item => {
        const name = item.querySelector('.tool-name').textContent.toLowerCase();
        const desc = item.querySelector('.tool-desc').textContent.toLowerCase();
        const categories = item.getAttribute('data-categories').toLowerCase();

        const matchesSearch = name.includes(searchText) || desc.includes(searchText);
        const matchesCategory = (selectedCat === 'all' || categories.includes(selectedCat.toLowerCase()));

        item.style.display = (matchesSearch && matchesCategory) ? 'flex' : 'none';
    });
}

searchInput.addEventListener('input', filterTools);
categorySelect.addEventListener('change', filterTools);
