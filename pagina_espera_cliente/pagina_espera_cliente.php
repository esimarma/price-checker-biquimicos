<?php
session_start();

include '../db/connection.php';
include '../db/queries.php';

// Processa CÓDIGO enviado por GET (substituindo o NIF via POST)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['codigo'])) {
    $codigo = trim($_GET['codigo']);
    $cliente = getDescontoCliente($codigo); // ou outra função que use o código

    if ($cliente) {
        $_SESSION['cliente'] = $cliente;
        header("Location: ../leitor_produto_cliente.php");
        exit;
    } else {
        header("Location: ../pagina_nao_encontrada.html");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aguarde...</title>
    <link rel="stylesheet" href="pagina_espera_style.css">
</head>
<body>
    <div class="container" id="container">
        <div class="mensagem">
            <p>POR FAVOR AGUARDE <br> O PRODUTO SER LIDO...</p>
        </div>
    </div>
</body>
</html>