function filterHistory() {
    let from = document.getElementById('fromDate').value;
    let to = document.getElementById('toDate').value;

    document.querySelectorAll('.history-row').forEach(row => {
        let date = row.getAttribute('data-date');

        let show = true;

        if (from && date < from) show = false;
        if (to && date > to) show = false;

        row.style.display = show ? '' : 'none';
    });
}

function resetFilter() {
    document.getElementById('fromDate').value = '';
    document.getElementById('toDate').value = '';

    document.querySelectorAll('.history-row').forEach(row => {
        row.style.display = '';
    });

    function searchByName() {
    const input = document.getElementById('searchName').value.toLowerCase();
    const rows = document.querySelectorAll('.history-row');

    rows.forEach(row => {
        const name = row.children[1].textContent.toLowerCase();
        if (name.includes(input)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
}