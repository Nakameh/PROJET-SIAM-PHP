function generateContent(gameId, userId) {
    fetch(`gameInformations.php?idGame=${gameId}`)
        .then(response => response.json())
        .then(game => {
            if (game.player1 == userId) {
                generateOpponentDeck(game.deckPlayer2);
                generatePlayerDeck(game.deckPlayer1);
            } else {
                generateOpponentDeck(game.deckPlayer1);
                generatePlayerDeck(game.deckPlayer2);
            }
            generateBoard(game.board);
        });
}


function generateOpponentDeck(opponentDeck) {
    let opponentDeckDiv = document.querySelector('.opponent-deck');
    for (let element of opponentDeck) {
        let img = document.createElement('img');
        img.classList.add('cardDeck');
        if (element == "E") {
            img.src = "/img/elephantS.gif";
        } else if (element == "R") {
            img.src = "/img/rhinoS.gif";
        }
        opponentDeckDiv.appendChild(img);
    }
}

function generatePlayerDeck(playerDeck) {
    let playerDeckDiv = document.querySelector('.player-deck');
    for (let element of playerDeck) {
        let img = document.createElement('img');
        img.classList.add('cardDeck');

        if (element == "E") {
            img.src = "/img/elephantS.gif";
        } else if (element == "R") {
            img.src = "/img/rhinoS.gif";
        }
        playerDeckDiv.appendChild(img);
    }
}

function generateBoard(board) {
    let pawnContainer = document.querySelector("#pawnContainer");

    for (let i = 0; i < board.length; i++) {
        let line = board[i];
        for (let j = 0; j < line.length; j++) {
            let pawn = line[j];
            let div = document.createElement("div");
            div.style.gridRow = i + 1;
            div.style.gridColumn = j + 1;
            if (pawn == "E") {
                let img = document.createElement("img");
                img.src = "/img/elephantS.gif";
                img.alt = "E";
                div.appendChild(img);
            } else if (pawn == "R") {
                let img = document.createElement("img");
                img.src = "/img/rhinoS.gif";
                img.alt = "R";
                div.appendChild(img);
            } else if (pawn == "ROCK") {
                let img = document.createElement("img");
                img.src = "/img/rocher.gif";
                img.alt = "ROCK";
                div.appendChild(img);
            }
            pawnContainer.appendChild(div);
        }
    
    }
}
