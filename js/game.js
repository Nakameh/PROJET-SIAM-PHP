let board;
let myDeck;
let player1;
let player2;
let activePlayer;
let selectedPawnDeck;
let selectedBoardLine;
let selectedBoardColumn;
let myPawn;
let rotation;

let idGame;
let idUser;
let isAdmin;



function generateContent(gameId, userId) {
    fetch(`gameInformations.php?idGame=${gameId}`)
    .then(response => response.json())
    .then(game => {
        board = game.board;
        myDeck = game.player1 == userId ? game.deckPlayer1 : game.deckPlayer2;
        player1 = game.player1;
        activePlayer = game.activePlayer;
        player2 = game.player2;
        selectedPawnDeck = -1;
        selectedBoardLine = -1;
        selectedBoardColumn = -1;
        myPawn = game.player1 == userId ? "E" : "R";
        rotation = "S";

        updateUserName();
        
        document.getElementById("rotateLeft").addEventListener("click", rotateLeft);
        document.getElementById("rotateRight").addEventListener("click", rotateRight);

        document.getElementById("cancelSelection").addEventListener("click", cancelSelection);
        
        document.getElementById("rotatePicture").src = "../img/"+myPawn+rotation+".gif";
        document.getElementById("movePicture").src = "../img/"+myPawn+rotation+".gif";

        generatePlayerDeck(game.player1 == idUser ? game.deckPlayer1 : game.deckPlayer2);
        generateOpponentDeck(game.player1 == idUser ? game.deckPlayer2 : game.deckPlayer1);
        generateBoard(game.board);
        if (!myTurn()) {
            setTimeout(updateContent, 250);
        } else {
            brightDeck();
            brightMyPawn();
            getLastMove();
        }
    });
}


function generateOpponentDeck(opponentDeck) {
    let opponentDeckDiv = document.querySelector('.opponent-deck');
    for (let element of opponentDeck) {
        let div = document.createElement('div');
        let img = document.createElement('img');
        div.appendChild(img);
        div.classList.add('cardDeck');
        if (element == "E") {
            img.src = "../img/EN.gif";
        } else if (element == "R") {
            img.src = "../img/RN.gif";
        }
        opponentDeckDiv.appendChild(div);
    }
}

function generatePlayerDeck(playerDeck) {
    let playerDeckDiv = document.querySelector('.player-deck');
    for (let i = 0; i < playerDeck.length; i++) {
        let element =playerDeck[i];
        let div = document.createElement('div');
        let img = document.createElement('img');
        div.appendChild(img);
        div.classList.add('cardDeck');

        if (element == "E") {
            img.src = "../img/ES.gif";
        } else if (element == "R") {
            img.src = "../img/RS.gif";
        }
        playerDeckDiv.appendChild(div);
        div.addEventListener("click", clickOnDeckDiv(i));
    }
}

function generateBoard(board) {
    let pawnContainer = document.querySelector("#pawnContainer");

    for (let i = 0; i < board.length; i++) {
        let line = board[i];
        for (let j = 0; j < line.length; j++) {
            let pa = line[j][0];
            let rota = line[j][1];

            let div = document.createElement("div");
            div.style.gridRow = i + 1;
            div.style.gridColumn = j + 1;
            if (line[j] == "ROCK") {
                let img = document.createElement("img");
                img.src = "../img/rocher.gif";
                img.alt = "ROCK";
                div.appendChild(img);
            } else if (pa == "E" || pa == "R") {
                let img = document.createElement("img");
                img.src = `../img/${pa}${rota}.gif`;
                img.alt = pa;
                div.appendChild(img);
            }
            pawnContainer.appendChild(div);
            div.addEventListener("mousedown", clickOnBoardDiv(i, j));
        }
    
    }
}

function verifyGameExist(gameId) {
    fetch(`gameInformations.php?idGame=${gameId}&action=gameExist`)
        .then(response => response.json())
        .then(game => {
            if (game) {
                window.location.href = "../";
            }
        });
}


function clickOnBoardDiv(ligne, colonne) {
    return function (event) {
        if (!myTurn()) return;
        if (board[ligne][colonne][0] == myPawn && board[ligne][colonne] != "ROCK" && !(selectedPawnDeck != -1 && (event.shiftKey))) {
            if (selectedPawnDeck != -1) {
                document.querySelector(".player-deck").children[selectedPawnDeck].classList.remove("selectedCard");
                selectedPawnDeck = -1;
            }
            removeAllBright();

            document.getElementById("movementDiv").style.visibility = "visible";
            document.getElementById("confirmRotate").style.visibility = "visible";
            document.getElementById("rotationDiv").style.visibility = "visible";

            brightCardAround(ligne, colonne);

            rotation = board[ligne][colonne][1];
            document.getElementById("rotatePicture").src = "../img/"+myPawn+rotation+".gif";
            document.getElementById("movePicture").src = "../img/"+myPawn+rotation+".gif";

            selectedBoardLine = ligne;
            selectedBoardColumn = colonne;
        } else if (selectedPawnDeck != -1) {
            let pushDirection = "V";
            if (event.button == 2) {
                pushDirection = "H";
            }

            fetch(`play.php?idGame=${idGame}&action=addPawn&line=${ligne}&column=${colonne}&pawn=${myPawn}&rotation=${rotation}&userId=${idUser}&indexDeck=${selectedPawnDeck}&pushDirection=${pushDirection}`)
            .then(response => response.json())
            .then(game => {
                if (game) {
                    updateContent();
                } 
            });
        }
    }
}

function clickOnDeckDiv(index) {
    return function () {
        if (!myTurn()) return;
        if (myDeck[index] != myPawn) return;

        removeAllBright();
        selectedBoardLine = -1;
        selectedBoardColumn = -1;

        this.classList.add("selectedCard");
        if (selectedPawnDeck != -1) {
            let previousDiv = document.querySelector('.player-deck').children[selectedPawnDeck];
            previousDiv.classList.remove("selectedCard");
        }
        selectedPawnDeck = index;
        contoursBoardBright();

        document.getElementById("movementDiv").style.visibility = "hidden";
        document.getElementById("confirmRotate").style.visibility = "hidden";
        document.getElementById("rotationDiv").style.visibility = "visible";
    }
}


function removeAllBright() {
    let pawnContainer = document.querySelector("#pawnContainer");
    for (let child of pawnContainer.children) {
        child.classList.remove("brightCard");
        child.classList.remove("lastMovement");
    }

    let playerDeckDiv = document.querySelector('.player-deck');
    for (let child of playerDeckDiv.children) {
        child.classList.remove("brightCard");
    }
}



function contoursBoardBright() {
    let pawnContainer = document.querySelector("#pawnContainer");
    for (let i = 0; i < 5; i++) {
        pawnContainer.children[i].classList.add("brightCard");
        pawnContainer.children[i * 5].classList.add("brightCard");
        pawnContainer.children[i * 5 + 4].classList.add("brightCard");
        pawnContainer.children[5 * 5 - 1 - i].classList.add("brightCard");
    }
}


function rotateRight() {
    if (rotation == "S") {
        rotation = "E";
    } else if (rotation == "E") {
        rotation = "N";
    } else if (rotation == "N") {
        rotation = "W";
    } else if (rotation == "W") {
        rotation = "S";
    }
    document.getElementById("rotatePicture").src = "../img/"+myPawn+rotation+".gif";
    document.getElementById("movePicture").src = "../img/"+myPawn+rotation+".gif";
}

function rotateLeft() {
    if (rotation == "S") {
        rotation = "W";
    } else if (rotation == "W") {
        rotation = "N";
    } else if (rotation == "N") {
        rotation = "E";
    } else if (rotation == "E") {
        rotation = "S";
    }
    document.getElementById("rotatePicture").src = "../img/"+myPawn+rotation+".gif";
    document.getElementById("movePicture").src = "../img/"+myPawn+rotation+".gif";
}


function updateDeckPlayer(deck) {
    let playerDeckDiv = document.querySelector('.player-deck');
    for (let i = 0; i < deck.length; i++) {
        let element = deck[i];
        let div = playerDeckDiv.children[i];
        let img = div.children[0];
        if (element == " ") {
            div.removeChild(img);
            img = document.createElement("img");
            div.appendChild(img);
        }
        if (element == "E" || element == "R") {
            img.src = `../img/${element}S.gif`;
        }
    }
}

function updateDeckOpponent(deck) {
    let opponentDeckDiv = document.querySelector('.opponent-deck');
    for (let i = 0; i < deck.length; i++) {
        let element = deck[i];
        let div = opponentDeckDiv.children[i];
        let img = div.children[0];
        if (element == " ") {
            div.removeChild(img);
            img = document.createElement("img");
            div.appendChild(img);
        }
        if (element == "E" || element == "R") {
            img.src = `../img/${element}N.gif`;
        }
    }
}

function updateBoard(board) {
    let pawnContainer = document.querySelector("#pawnContainer");
    for (let i = 0; i < board.length; i++) {
        let line = board[i];
        for (let j = 0; j < line.length; j++) {
            let pawn = line[j][0];
            let rota = line[j][1];
            let div = pawnContainer.children[i * 5 + j];
            let img = null;
            if (div.childElementCount == 0 ) {
                img = document.createElement("img");
                div.appendChild(img);
            } else {
                img = div.children[0];
            }

            if (line[j] == "ROCK") {
                img.src = "../img/rocher.gif";
                img.alt = "ROCK";
            } else if (pawn == "E" || pawn == "R") {
                img.src = `../img/${pawn}${rota}.gif`;
                img.alt = pawn;
            } else {
                div.removeChild(img);
                img = document.createElement("img");
                div.appendChild(img);
            }
        }
    }
}


function updateContent() {
    fetch(`gameInformations.php?idGame=${idGame}`)
    .then(response => response.json())
        .then(game => {
            removeAllBright();

            document.getElementById("movementDiv").style.visibility = "hidden";
            document.getElementById("confirmRotate").style.visibility = "hidden";
            document.getElementById("rotationDiv").style.visibility = "hidden";

            activePlayer = game.activePlayer;
            if (selectedPawnDeck != -1) {
                document.querySelector(".player-deck").children[selectedPawnDeck].classList.remove("selectedCard");
            }
            
            board = game.board;
            myDeck = game.player1 == idUser ? game.deckPlayer1 : game.deckPlayer2;
            player1 = game.player1;
            player2 = game.player2;
            selectedPawnDeck = -1;
            selectedBoardLine = -1;
            selectedBoardColumn = -1;
            rotation = "S";

            document.getElementById("rotatePicture").src = "../img/"+myPawn+rotation+".gif";
            document.getElementById("movePicture").src = "../img/"+myPawn+rotation+".gif";

            updateDeckPlayer(game.player1 == idUser ? game.deckPlayer1 : game.deckPlayer2);
            updateDeckOpponent(game.player1 == idUser ? game.deckPlayer2 : game.deckPlayer1);
            updateBoard(game.board);
            updateUserName();

            if (!myTurn()) {
                setTimeout(updateContent, 250);
                return ;
            }
            brightDeck();
            brightMyPawn();
            getLastMove();
        });
}

function myTurn() {
    if (activePlayer == 0) {
        return player1 == idUser;
    }
    return player2 == idUser;

}



function updateUserName() {
    fetch(`gameInformations.php?idGame=${idGame}&action=getUserName&id_user=${activePlayer == 0 ? player1 : player2}`)
    .then(response => response.json())
    .then(game => {
        if (game) {
            let userPlayingTitle = document.querySelector("#playerTurn");
            userPlayingTitle.textContent = "Tour : "+game['turn'] + " - " + game['username'];
            if (myTurn()) {
                userPlayingTitle.style.color = "green";
            } else {
                userPlayingTitle.style.color = "red";
            }
        }
        
    });
}

function brightCardAround(line, column) {
    let pawnContainer = document.querySelector("#pawnContainer");

    if ((line + 1) < 5) {
        pawnContainer.children[(line + 1) * 5 + column].classList.add("brightCard");
    }
    if ((line - 1) >= 0) {
        pawnContainer.children[(line - 1) * 5 + column].classList.add("brightCard");
    }
    if ((column + 1) < 5) {
        pawnContainer.children[line * 5 + column + 1].classList.add("brightCard");
    }
    if ((column - 1) >= 0) {
        pawnContainer.children[line * 5 + column - 1].classList.add("brightCard");
    }
}



function confirmRotate() {
    if (selectedBoardLine == -1 || selectedBoardColumn == -1) return;    
    fetch(`play.php?idGame=${idGame}&action=rotatePawn&line=${selectedBoardLine}&column=${selectedBoardColumn}&rotation=${rotation}&userId=${idUser}&pawn=${myPawn}`)
        .then(response => response.json())
        .then(game => {
            if (game) {
                updateContent();
            } 
        });
}

function move(pasX, paxY) {
    return function () {
        if (selectedBoardLine == -1 || selectedBoardColumn == -1) return;
        fetch(`play.php?idGame=${idGame}&action=movePawn&line=${selectedBoardLine}&column=${selectedBoardColumn}&pasX=${pasX}&pasY=${paxY}&userId=${idUser}&pawn=${myPawn}&rotation=${rotation}`)
            .then(response => response.json())
            .then(game => {
                if (game) {
                    updateContent();
                } 
            });
    }
}


function gameFinished() {
    fetch(`gameInformations.php?idGame=${idGame}&action=gameFinished`)
    .then(response => response.json())
    .then(game => {
        if (game) {
            window.location.href = "gamewinner.php?idGame="+idGame;
        }
    });
}


function cancelSelection() {
    selectedBoardLine = -1;
    selectedBoardColumn = -1;
    removeAllBright();
    document.getElementById("movementDiv").style.visibility = "hidden";
    document.getElementById("confirmRotate").style.visibility = "hidden";
    document.getElementById("rotationDiv").style.visibility = "hidden";
    if (selectedPawnDeck != -1) {
        document.querySelector(".player-deck").children[selectedPawnDeck].classList.remove("selectedCard");
        selectedPawnDeck = -1;
    }
    brightDeck();
    brightMyPawn();
    getLastMove();
}



function brightDeck() {
    let playerDeckDiv = document.querySelector('.player-deck');
    for (let child of playerDeckDiv.children) {
        if (child.firstChild.src != "") {
            child.classList.add("brightCard");
        }
    }
}

function brightMyPawn() {
    let pawnContainer = document.querySelector("#pawnContainer");
    for (let child of pawnContainer.children) {
        if (child.childElementCount > 0 && child.firstChild.alt == myPawn) {
            child.classList.add("brightCard");
        }
    }
}


function getLastMove() {
    fetch(`gameInformations.php?idGame=${idGame}&action=getLastMove`)
    .then(response => response.json())
    .then(game => {
        if (game) {
            let fm = game['position_finale_Movement'].split("-");
            let lm = parseInt(fm[0], 10);
            let cm = parseInt(fm[1], 10);

            let pawnContainer = document.querySelector("#pawnContainer");
            pawnContainer.children[lm * 5 + cm].classList.add("lastMovement");
        }
    });
}