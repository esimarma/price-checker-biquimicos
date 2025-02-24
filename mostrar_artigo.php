<?php
include 'db/connection.php';
include 'db/queries.php';

$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : null;
$artigo = $codigo ? getArtigoPorCodigo($codigo) : null;
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado da Busca</title>
    <link rel="stylesheet" href="style.css"> <!-- Importa o CSS -->
</head>
<body>

<div class="container">
    <h2>Resultado da Busca</h2>

    <?php if ($artigo): ?>
        <p><strong>Código:</strong> <?= htmlspecialchars($artigo['codigo']) ?></p>
        <p><strong>Nome:</strong> <?= htmlspecialchars($artigo['nome']) ?></p>
        <p><strong>IVA:</strong> <?= htmlspecialchars($artigo['iva']) ?>%</p>
        <p><strong>Família:</strong> <?= htmlspecialchars($artigo['familia']) ?></p>
        <p><strong>Unidade de Venda:</strong> <?= htmlspecialchars($artigo['unvenda']) ?></p>
        <p><strong>Preço com IVA:</strong> <?= number_format(floatval($artigo['pvpciva']), 2, '.', '') ?>€</p>
        <p><strong>Preço sem IVA:</strong> <?= number_format(floatval($artigo['pvpsiva']), 3, '.', '') ?>€</p>

        <?php if (!empty($artigo['imagem'])): ?>
            <p><img src="<?= $config['caminhoImagens'] . $artigo['imagem'] ?>" alt="Imagem do artigo" width="150"></p>
        <?php endif; ?>

    <?php else: ?>
        <p>Artigo não encontrado!</p>
    <?php endif; ?>

    <a href="index.html">Voltar</a>
</div>

</body>
</html>