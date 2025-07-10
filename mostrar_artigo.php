<?php
include 'db/connection.php';
include 'db/queries.php';

// Carrega o config.json
$config = json_decode(file_get_contents('config.json'), true);

$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : null;
$artigo = $codigo ? getArtigoPorCodigo($codigo) : null;

// Se o artigo não for encontrado, redireciona para pagina_nao_encontrada.html
if (!$artigo) {
    header("Location: pagina_nao_encontrada.html?tipo=default");
    exit;
} else {
    if($artigo['precoPromocao']){
        $precoFinal = $artigo['precoPromocao'];
    } else {
        $precoFinal = $artigo['pvpciva'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Produto</title>
    <link rel="stylesheet" href="mostrar_artigo.css">

    <script>
        setTimeout(function() {
            window.location.href = "index.php";
        }, 10000);
    </script>
</head>
<body>
<?php if ($artigo): ?>
    <?php
        // Caminho para imagem
        $imagemFinal = "imagem.php?file=default.png";

        if (!empty($artigo['imagem'])) {
            $caminhoFisico = rtrim($config['caminhoImagensProdutos'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $artigo['imagem'];
            if (file_exists($caminhoFisico)) {
                $imagemFinal = "imagem.php?file=" . urlencode($artigo['imagem']);
            }
        }

        $precoun = 0;
        if ($artigo['PrecoPor'] === 'Kg' || $artigo['PrecoPor'] === 'Lt') {
            $precoun = ($precoFinal * 1000) / $artigo['CapacidadeUn'];
        } else if ($artigo['PrecoPor'] === 'Ud' || $artigo['PrecoPor'] === 'Dose') {
            $precoun = ($precoFinal * 1) / $artigo['CapacidadeUn'];
        }
    ?>

    <div class="container">
        <div class="info">
            <h2><?= htmlspecialchars($artigo['nome']) ?></h2>
            <div class="family">
                <p><?= htmlspecialchars($artigo['familia']) ?></p>
            </div>
        </div>
        <div class="image_pvp">
            <div class="image">
                <img src="<?= htmlspecialchars($imagemFinal) ?>" alt="Imagem do artigo">
            </div>
            <div class="pvp">
                <div class="price_iva">
                    <span class="price"> <?= number_format(floatval($precoFinal), 2, '.', '') ?>€</span>
                    <div class="tax-info">
                        <span class="iva">C/IVA (<?= number_format(intval($artigo['iva'])) ?>%)</span>
                        <?php if ($artigo['precoPromocao']) : ?>
                            <span class="desconto">C/Desconto</span>
                        <?php endif; ?>
                    </div>
                </div>
                <p class="unvenda"><?= htmlspecialchars($artigo['unvenda']) ?></p>
                <p class="precoun"><?= number_format(floatval($precoun), 2, '.', '') ?>€ / <?= htmlspecialchars($artigo['PrecoPor']) ?></p>
            </div>
        </div>

        <!-- Logo e stock -->
        <div class="logo">
            <img src="Imagens/logo.svg" alt="Logo">
            <div class="stock">
                <p><?= intval($artigo['existencia']) ?></p>
            </div>
        </div>  
    </div>
<?php else: ?>
    <p>Artigo não encontrado!</p>
<?php endif; ?>

<script>
    let barcode = "";
    let timeout = null;

    document.addEventListener("keydown", function(event) {
        if (timeout) clearTimeout(timeout);

        if (event.key.length === 1) {
            barcode += event.key;
        }

        timeout = setTimeout(() => {
            if (event.key === "Enter") {
                if (barcode.length < 13) {
                    window.location.href = "pagina_espera_cliente/pagina_espera_cliente.php?codigo=" + encodeURIComponent(barcode);
                } else {
                    window.location.href = "pagina_espera/pagina_espera.html?codigo=" + encodeURIComponent(barcode) + "&tipo=default";
                }
                barcode = "";
            }
        }, 100);
    });
</script>
</body>
</html>