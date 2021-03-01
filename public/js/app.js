
function nextTurn() {
    fetch('/next-turn').then(
        response => response.json())
        .then(json => updateContent(json))
        .catch(error => console.log(error));
}

function updateContent(json) {
    updateTable(json.status);
    addSummaryRow(json.summary);
}

function updateTable(array) {
    let statusRow = document.querySelector("tbody");
    let html = `
                <tr>
                    <th>Nom</th>
                    <th>PV</th>
                    <th>Poison</th>
                </tr>                
                `;
    statusRow.innerHTML = "";
    array.forEach(element => {
        html += `
                <tr>
                    <td>${element.name}</td>
                    <td>${element.health}</td>
                    <td>${element.plague ? 'yes' : 'no'}</td>
                </tr>
                ` ;
    }
    );
    statusRow.innerHTML = html;
}

function addSummaryRow(str) {
    let summaryRow = document.querySelector("#summary");
    let p = document.createElement("p");
    p.innerText = str;
    summaryRow.appendChild(p);

}
