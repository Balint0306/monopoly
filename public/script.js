
document.addEventListener('DOMContentLoaded', () => {
    const boardElement = document.getElementById("board");
    const rollDiceBtn = document.getElementById("roll-dice-btn");
    const newGameBtn = document.getElementById('new-game-btn'); 
    const player1Money = document.querySelector('#player-1-details .player-money');
    const player2Money = document.querySelector('#player-2-details .player-money');
    const turnIndicator = document.getElementById('turn-indicator');

    let gameState = null;
    let pawn1 = null;
    let pawn2 = null;

    const API_URL = 'index.php';

    async function apiCall(action) {
        try {
            const response = await fetch(`${API_URL}?action=${action}`);
            if (!response.ok) {
                if (response.status === 404 && action === 'getGameState') {
                    // If no game is found, start a new one automatically.
                    return await apiCall('newGame');
                }
                throw new Error(`API call failed: ${response.statusText}`);
            }
            const data = await response.json();
            handleStateChange(data);
        } catch (error) {
            console.error(`Error during action '${action}':`, error);
        }
    }

    function handleStateChange(newState) {
        gameState = newState;
        if (!boardElement.children.length) {
            createBoard();
        }
        updateUI();

        if (gameState.currentTurn === 1) { // AI's turn
            setTimeout(() => apiCall('rollDice'), 1500);
        }
    }

    function createBoard() {
        boardElement.innerHTML = ''; // Clear previous board
        gameState.board.spaces.forEach((space, i) => {
            const cell = document.createElement("div");
            cell.id = `cell-${i}`;
            cell.className = "cell";
            cell.textContent = space.name;
            boardElement.appendChild(cell);
        });

        pawn1 = document.createElement("div");
        pawn1.className = "pawn";
        pawn1.id = "pawn-1";
        boardElement.appendChild(pawn1);

        pawn2 = document.createElement("div");
        pawn2.className = "pawn";
        pawn2.id = "pawn-2";
        boardElement.appendChild(pawn2);
    }

    function updateUI() {
        if (!gameState) return;

        // Update player money
        player1Money.textContent = `$${gameState.players[0].money}`;
        player2Money.textContent = `$${gameState.players[1].money}`;

        // Update property ownership borders
        gameState.board.spaces.forEach((space, i) => {
            const cell = document.getElementById(`cell-${i}`);
            if (space.owner) {
                const ownerName = space.owner.name;
                const player1Name = gameState.players[0].name;
                cell.style.border = `3px solid ${ownerName === player1Name ? '#ff0000' : '#0000ff'}`;
            } else {
                cell.style.border = '1px solid black';
            }
        });

        // Update pawn positions
        updatePawnPosition(pawn1, gameState.players[0].position, 10);
        updatePawnPosition(pawn2, gameState.players[1].position, 40);

        // Update turn indicator and button state
        const isPlayerTurn = gameState.currentTurn === 0;
        rollDiceBtn.disabled = !isPlayerTurn;
        turnIndicator.textContent = isPlayerTurn ? "Your Turn" : "AI's Turn";
        turnIndicator.style.color = isPlayerTurn ? 'green' : 'orange';
    }

    function updatePawnPosition(pawn, position, offset) {
        const cell = document.getElementById(`cell-${position}`);
        if (cell) {
            pawn.style.top = `${cell.offsetTop + offset}px`;
            pawn.style.left = `${cell.offsetLeft + offset}px`;
        }
    }

    // Event Listeners
    rollDiceBtn.addEventListener("click", () => apiCall('rollDice'));
    newGameBtn.addEventListener("click", () => apiCall('newGame'));

    // Initial load
    apiCall('getGameState');
});
