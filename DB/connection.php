<?php
    // Carregar o arquivo JSON e converter para um array associativo
    $config = json_decode(file_get_contents('config.json'), true);

    try {
        // Criar conexão PDO com SQL Server usando as configurações do config.php
        $conn = new PDO(
            "sqlsrv:Server={$config['serverName']};Database={$config['database']}",
            $config['username'],
            $config['password']
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erro de conexão: " . $e->getMessage());
    }
?>