<?php

session_start();

include 'db/connection.php';
include 'db/queries.php';

// Processa NIF enviado por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nif'])) {
    $nif = trim($_POST['nif']);
    $cliente = getDescontoCliente($nif); // ou outra função que devolva o cliente

    if ($cliente) {
        $_SESSION['cliente'] = $cliente;
        header("Location: leitor_produto_cliente.php");
        exit;
    } else {
        header("Location: cliente_not_found.html");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leitor de Produtos</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="container">
        <div class="header">LEITOR DE PRODUTOS</div>
        <input type="text" id="codigo" readonly inputmode="none" style="opacity: 100; position: absolute;"/>

        <div class="nif-bar">
            <span class="nif-label">É CLIENTE? LEIA O SEU CARTÃO OU INTRODUZA O NIF</span>
            <button class="nif-button" onclick="usarNIF()">USE O NIF</button>
        </div>

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
        const input = document.getElementById('codigo');

        // Remover readonly para permitir digitação do leitor
        input.removeAttribute('readonly');

        // Focar sem abrir teclado
        setTimeout(() => {
        input.focus();
        }, 100);

        const manterFoco = () => {
            const input = document.getElementById("codigo");
            const popupAtivo = document.getElementById("nif-popup");

            if (!popupAtivo && input) {
                input.focus();
            }
        };

        // Garante o foco inicial
        window.addEventListener("load", manterFoco);

        // Sempre que perder o foco, volta a focar
        document.addEventListener("click", () => {
            setTimeout(manterFoco, 50);
        });

        document.addEventListener("keydown", () => {
            setTimeout(manterFoco, 50);
        });
        let barcode = "";
        let timeout = null;

        document.addEventListener("keydown", function(event) {
            const popupAtivo = document.getElementById("nif-popup");
            if (popupAtivo) return;

            if (timeout) clearTimeout(timeout);

            if (event.key.length === 1) {
                barcode += event.key;
            }

            timeout = setTimeout(() => {
                if (event.key === "Enter") {
                    if (barcode.length < 13) {
                        window.location.href = "pagina_espera_cliente/pagina_espera_cliente.php?codigo=" + encodeURIComponent(barcode);
                    } else {
                        window.location.href = "wait_page/wait_page.html?codigo=" + encodeURIComponent(barcode) + "&tipo=default";
                    }
                    barcode = "";
                }
            }, 100);
        });

        function usarNIF() {
            showNifPopup();
        }

        function showNifPopup() {
            hideNifPopup();

            const popup = document.createElement("div");
            popup.id = "nif-popup";
            popup.innerHTML = `
                <div class="popup-overlay">
                    <div class="popup-box">
                        <form method="POST">
                            <p class="popup-text">Introduza o seu NIF:</p>
                            <input type="text" id="nifInput" name="nif" placeholder="Digite o NIF" required autofocus />
                            <div class="popup-buttons">
                                <button id="nif-cancelar" type="button" onclick="hideNifPopup()">CANCELAR</button>
                                <button id="nif-confirmar" type="submit">CONFIRMAR</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;


            document.body.appendChild(popup);
        }

        function hideNifPopup() {
            const popup = document.getElementById("nif-popup");
            if (popup) popup.remove();
        }
    </script>
</body>
</html>