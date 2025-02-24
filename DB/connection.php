<?php
$serverName = "PC_DA_MARISA\\SQL"; // Nome do servidor (use duas barras invertidas)
$database = "BIQ"; // Nome da base de dados
$username = "sa"; // Usuário do SQL Server
$password = "123456789"; // Senha do SQL Server

try {
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexão com SQL Server realizada com sucesso!";
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>