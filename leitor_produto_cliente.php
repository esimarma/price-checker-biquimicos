<?php
session_start();
if (!isset($_SESSION['cliente'])) {
    header("Location: not_found_page.html?tipo=nif");
    exit;
}

$cliente = $_SESSION['cliente']; 
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Leitor de Produtos</title>
        <link rel="stylesheet" href="leitor_produto_cliente.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    </head>
    <body>
        <div class="container">
        <a href="logout.php" class="back-button" title="Terminar Sessão">
            <svg xmlns="http://www.w3.org/2000/svg" class="back-icon" width="24" height="24" viewBox="0 0 512 512">
                <path fill="#4D5252" d="M48,256c0,114.87,93.13,208,208,208s208-93.13,208-208S370.87,48,256,48,48,141.13,48,256Zm224-80.09L208.42,240H358v32H208.42L272,336.09,249.3,358.63,147.46,256,249.3,153.37Z"/>
            </svg>
        </a>
            </a>
            <div class="header">LEITOR DE PRODUTOS</div>
            <div class="welcome">BEM-VINDO, <?php echo htmlspecialchars($cliente['nome']); ?></div>
            <div class="box">
                <div class="content">
                    <div class="text">
                        <p>ACEDA AO <strong>PREÇO</strong> E <strong>DETALHES</strong> DE UM PRODUTO RAPIDAMENTE</p>
                        <p>AO PASSAR O PRODUTO PELO <strong>LEITOR</strong></p>
                    </div>
                    <img src="Imagens/leitor_icon.svg" alt="Ícone do leitor" class="icon">
                </div>
            </div>
        </div>
    
        <script>
            let barcode = "";
            let timeout = null;
    
            document.addEventListener("keydown", function (event) {
                if (event.key.length === 1) {
                    barcode += event.key;
                }
    
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    if (barcode.length > 3) {
                        console.log("Código de barras lido:", barcode);
                        window.location.href = "wait_page/wait_page.html?codigo=" + encodeURIComponent(barcode) + "&tipo=cliente";
                        barcode = "";
                    }
                }, 100);
            });
    
            // Inatividade + popup com timer
            let inactivityTimer;
            let confirmationTimer;
            const INACTIVITY_TIME = 5000;
            const CONFIRMATION_TIME = 10000;
            let countdownInterval;
            let timeLeft = CONFIRMATION_TIME / 1000;
    
            function resetInactivityTimer() {
                clearTimeout(inactivityTimer);
                hidePopup();
                inactivityTimer = setTimeout(showConfirmationPopup, INACTIVITY_TIME);
            }
    
            function showConfirmationPopup() {
                const popup = document.createElement("div");
                popup.id = "inactivity-popup";
                popup.innerHTML = `
                    <div class="popup-overlay">
                        <div class="popup-box">
                            <p class="popup-text">Gostaria de continuar a consultar preços? <span id="countdown">(10s)</span></p>
                            <div class="popup-buttons">
                                <button id="yes-btn">SIM</button>
                                <button id="no-btn">NÃO</button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(popup);
    
                document.getElementById("yes-btn").onclick = () => {
                    hidePopup();
                    resetInactivityTimer();
                };
    
                document.getElementById("no-btn").onclick = () => {  
                    window.location.href = "logout.php";
                };
    
                timeLeft = CONFIRMATION_TIME / 1000;
                document.getElementById("countdown").textContent = `(${timeLeft}s)`;
                countdownInterval = setInterval(() => {
                    timeLeft--;
                    document.getElementById("countdown").textContent = `(${timeLeft}s)`;
                    if (timeLeft <= 0) {
                        clearInterval(countdownInterval);
                    }
                }, 1000);
    
                confirmationTimer = setTimeout(() => {
                    window.location.href = "logout.php";
                }, CONFIRMATION_TIME);
            }
    
            function hidePopup() {
                const popup = document.getElementById("inactivity-popup");
                if (popup) {
                    popup.remove();
                    clearTimeout(confirmationTimer);
                    clearInterval(countdownInterval);
                }
            }
    
            // ✅ Apenas o clique reinicia o temporizador de inatividade
            document.addEventListener("click", resetInactivityTimer);
    
            // Inicializa na primeira vez
            resetInactivityTimer();
        </script>
    </body>
    </html>