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
    <title>Detalhes do Produto</title>
    <link rel="stylesheet" href="product_detail.css">
</head>
<body>
    <?php if ($artigo): ?>
        <?php
            $imagemFinal = $config['caminhoImagens'] . "default.png"; // Imagem padrão

            if (!empty($artigo['imagem'])) {
                $caminhoImagem = $config['caminhoImagens'] . $artigo['imagem'];
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $caminhoImagem)) {
                    $imagemFinal = $caminhoImagem;
                }
            }
        ?>

        <div class="container">
            <div class="info">
                <h2><?= htmlspecialchars($artigo['nome']) ?></h2>
                <p><?= htmlspecialchars($artigo['familia']) ?></p>
                <p><?= intval($artigo['existencia']) ?></p>
            </div>
            <div class="image">
                <img src="<?= htmlspecialchars($imagemFinal) ?>" alt="Imagem do artigo">
                <p class="price"><?= number_format(floatval($artigo['pvpciva']), 2, '.', '') ?>€</p>
                <p><?= htmlspecialchars($artigo['unvenda']) ?></p>
            </div>
        </div>
        
    <?php else: ?>
        <p>Artigo não encontrado!</p>
    <?php endif; ?>
</body>
</html>