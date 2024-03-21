/**
 * @file game.js
 * @brief Contient les fonctions permettant de jouer une partie de SIAM
 */


// Variables globales
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







/**
 * @brief Fonction principale appelée au chargement de la page
 * @param {number} ig Identifiant de la partie
 * @param {number} iu Identifiant de l'utilisateur
 * @param {number} ia 1 si l'utilisateur est un administrateur, 0 sinon
 */
function main(ig, iu, ia) {
    return function() {
        idGame = ig;
        idUser = iu;
        isAdmin = ia;

        setInterval(gameFinished, 1000);
        setInterval(verifyGameExist, 1000, idGame);

        generateContent();

    }
}






/**
 * @brief Fonction permettant de définir les variables globales avec les informations de la base de données
 * @param {Object} game Objet contenant les informations de la partie de la base de données
 */
function setGlobalVariables(game) {
    board = game.board;
    myDeck = game.player1 == idUser ? game.deckPlayer1 : game.deckPlayer2;
    player1 = game.player1;
    activePlayer = game.activePlayer;
    player2 = game.player2;
    selectedPawnDeck = -1;
    selectedBoardLine = -1;
    selectedBoardColumn = -1;
    myPawn = game.player1 == idUser ? "E" : "R";
    rotation = "S";

    document.getElementById("rotatePicture").src = "../img/"+myPawn+rotation+".gif";
    document.getElementById("movePicture").src = "../img/"+myPawn+rotation+".gif";
}







/**
 * @brief Fonction permettant de créer les écouteurs d'événements pour les boutons de la partie
 */
function createEventListener() {
    document.getElementById("rotateLeft").addEventListener("click", rotateLeft);
    document.getElementById("rotateRight").addEventListener("click", rotateRight);
    document.getElementById("cancelSelection").addEventListener("click", cancelSelection);
    document.getElementById("confirmRotate").addEventListener("click", confirmRotate);
            
    document.getElementById("moveUp").addEventListener("click", move(0, -1));
    document.getElementById("moveDown").addEventListener("click", move(0, 1));
    document.getElementById("moveLeft").addEventListener("click", move(-1, 0));
    document.getElementById("moveRight").addEventListener("click", move(1, 0));

    document.addEventListener("contextmenu", function(e) {
        e.preventDefault();
    });
    
}







/**
 * @brief Fonction permettant de générer le contenu de la page depuis le contenu de la base de données au chargement de la page
 */
function generateContent() {
    fetch(`gameInformations.php?idGame=${idGame}`)
        .then(response => response.json())
        .then(game => {
            setGlobalVariables(game);

            updateUserName();
            createEventListener();

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







/**
 * @brief Fonction permettant de vérifier si la partie existe en demandant au serveur sinon redirige vers la page d'accueil
 */
function verifyGameExist(gameId) {
    fetch(`gameInformations.php?idGame=${gameId}&action=gameExist`)
        .then(response => response.json())
        .then(game => {
            if (game) {
                window.location.href = "../";
            }
        });
}







/**
 * @brief Fonction représentant le fait que le joueur a cliqué sur un pion de ses pions sur le plateau
 */
function selectPawnOnBoard(ligne, colonne) {
    if (selectedPawnDeck != -1) {
        document.querySelector(".player-deck").children[selectedPawnDeck].classList.remove("selectedCard");
        selectedPawnDeck = -1;
    }
    removeAllBright();

    selectedBoardLine = ligne;
    selectedBoardColumn = colonne;
    document.getElementById("movementDiv").style.visibility = "visible";
    document.getElementById("confirmRotate").style.visibility = "visible";
    document.getElementById("rotationDiv").style.visibility = "visible";
    brightCardAround(ligne, colonne);
    rotation = board[ligne][colonne][1];
    document.getElementById("rotatePicture").src = "../img/"+myPawn+rotation+".gif";
    document.getElementById("movePicture").src = "../img/"+myPawn+rotation+".gif";
    selectedBoardLine = ligne;
    selectedBoardColumn = colonne;
}







/**
 * @brief Fonction représentant le fait que le joueur a cliqué sur une case du plateau
 * @param {number} ligne Ligne de la case
 * @param {number} colonne Colonne de la case
 */
function clickOnBoardDiv(ligne, colonne) {
    return function (event) {
        if (!myTurn()) return;
        if (board[ligne][colonne][0] == myPawn && board[ligne][colonne] != "ROCK" && !(selectedPawnDeck != -1 && (event.shiftKey))) {
            selectPawnOnBoard(ligne, colonne);
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







/**
 * @brief Fonction représentant le fait que le joueur a cliqué sur un pion de ses pions dans son deck
 * @param {number} index Index du pion dans le deck
 */
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







/**
 * @brief Fonction permettant d'effectuer une rotation vers la droite du pion sélectionné
 */
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







/**
 * @brief Fonction permettant d'effectuer une rotation vers la gauche du pion sélectionné
 */
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







/**
 * @brief Fonction permettant de mettre à jour le contenu de la page en fonction des informations de la base de données
 */
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
            
            setGlobalVariables(game)

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







/**
 * @brief Fonction permettant de savoir si c'est le tour du joueur
 * @returns {boolean} true si c'est le tour du joueur, false sinon
 */
function myTurn() {
    if (activePlayer == 0) {
        return player1 == idUser;
    }
    return player2 == idUser;

}







/**
 * @brief Fonction permettant d'indiquer au serveur que le joueur a confirmé la rotation du pion en donnant les informations nécessaires
 */
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







/**
 * @brief Fonction permettant d'informer le serveur que le joueur veut déplacer le pion sélectionné en donnant les informations nécessaires
 * @param {number} pasX Pas de déplacement en X
 * @param {number} paxY Pas de dépacement en Y
 */
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







/**
 * @brief Fonction permettant appelée de façon asynchrone pour savoir si la partie est terminée et rediriger vers la page du gagnant
 */
function gameFinished() {
    fetch(`gameInformations.php?idGame=${idGame}&action=gameFinished`)
    .then(response => response.json())
    .then(game => {
        if (game) {
            window.location.href = "gamewinner.php?idGame="+idGame;
        }
    });
}







/**
 * @brief Fonction permettant d'annuler la sélection du pion effectuée par le joueur
 */
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







/**
 * @brief Fonction permettant de mettre en évidence le dernier coup jouer dans la partie
 */
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
