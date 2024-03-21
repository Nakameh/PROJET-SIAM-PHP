/**
 * @file vue.js
 * @brief Contient les fonctions permettant de mettre à jour l'interface graphique
 * 
 */





/**
 * @brief Fonction permettant d'ajouter la classe brightCard (fond vert) à tous les pions de la main du joueur
 */
function brightDeck() {
    let playerDeckDiv = document.querySelector('.player-deck');
    for (let child of playerDeckDiv.children) {
        if (child.firstChild.src != "") {
            child.classList.add("brightCard");
        }
    }
}






/**
 * @brief Fonction permettant d'ajouter la classe brightCard (fond vert) à tous les pions du joueur sur le plateau
 */
function brightMyPawn() {
    let pawnContainer = document.querySelector("#pawnContainer");
    for (let child of pawnContainer.children) {
        if (child.childElementCount > 0 && child.firstChild.alt == myPawn) {
            child.classList.add("brightCard");
        }
    }
}






/**
 * @brief Fonction permettant d'ajouter la classe brightCard (fond vert) à tous les pions autour de la case (line, column)
 * @param {number} line Ligne de la case
 * @param {number} column Colonne de la case
 */
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






/**
 * @brief Fonction permettant d'afficher les pions dans la main de l'adversaire
 * @param {Array} opponentDeck Tableau contenant les pions de l'adversaire
 */
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






/**
 * @brief Fonction permettant d'afficher les pions dans la main du joueur
 * @param {Array} playerDeck Tableau contenant les pions du joueur
 */
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






/**
 * @brief Fonction permettant de générer le plateau de jeu
 * @param {Array} board Tableau contenant les pions sur le plateau
 */
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






/**
 * @brief Fonction permettant de retirer le fond vert et bleu de tous les pions
 */
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






/**
 * @brief Fonction permettant d'ajouter la classe brightCard (fond vert) sur les cases les plus extérieures du plateau
 */
function contoursBoardBright() {
    let pawnContainer = document.querySelector("#pawnContainer");
    for (let i = 0; i < 5; i++) {
        pawnContainer.children[i].classList.add("brightCard");
        pawnContainer.children[i * 5].classList.add("brightCard");
        pawnContainer.children[i * 5 + 4].classList.add("brightCard");
        pawnContainer.children[5 * 5 - 1 - i].classList.add("brightCard");
    }
}






/**
 * @brief Fonction permettant de mettre à jour l'affichage de la main du joueur
 * @param {Array} deck Tableau contenant les pions du joueur
 */
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






/**
 * @brief Fonction permettant de mettre à jour l'affichage de la main de l'adversaire
 * @param {Array} deck Tableau contenant les pions de l'adversaire
 */
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






/**
 * @brief Fonction permettant de mettre à jour l'affichage du plateau
 * @param {Array} board Tableau contenant les pions sur le plateau
 */
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






/**
 * @brief Fonction permettant de mettre à jour l'affichage du nom du joueur entrain de jouer en demandant au serveur
 */
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