document.getElementById("fight").addEventListener('click', function (event) {

});

function nextTurn() {
    fetch('/next-turn').then(response => response.json())
        .then(json => console.log(json))
        .catch(error => console.log(error));
}

function getPlayersStatus() {
    fetch('/status').then(function (response) {
        //console.log(response);
    }).catch(function (error) {
        console.log('error: ');
    })
}