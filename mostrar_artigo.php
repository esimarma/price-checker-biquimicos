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

    <script>
        setTimeout(function() {
            window.location.href = "index.html"; // Redireciona para index.html após 5 segundos
        }, 5000);
    </script>
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
                <div class="family">
                    <p><?= htmlspecialchars($artigo['familia']) ?></p>
                </div>
                <div class="stock">
                    <p><?= intval($artigo['existencia']) ?></p>
                </div>
            </div>
            <div class="image_pvp">
                <div class="image">
                    <img src="<?= htmlspecialchars($imagemFinal) ?>" alt="Imagem do artigo">
                </div>
                <div class="pvp">
                    <div class="price_iva">
                        <span class="price"> <?= number_format(floatval($artigo['pvpciva']), 2, '.', '') ?>€</span>
                        <span class="iva"> C/IVA</span>
                    </div>
                    <p class="unvenda"><?= htmlspecialchars($artigo['unvenda']) ?></p>
                </div>
                <div class="logo">
                    <img src="<?= htmlspecialchars($config['caminhoImagens'] . "logo.svg") ?>" alt="Logo">
                </div>
            </div>
        </div>

    <?php else: ?>
        <p>Artigo não encontrado!</p>
    <?php endif; ?>
</body>
</html>