<?php
include 'db/connection.php';
include 'db/queries.php';

$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : null;
$artigo = $codigo ? getArtigoPorCodigo($codigo) : null;

// Se o artigo não for encontrado, redireciona para not_found_page.html
if (!$artigo) {
    header("Location: not_found_page.html?tipo=cliente");
    exit;
}
else{
    session_start();
    if (!isset($_SESSION['cliente'])) {
        header("Location: not_found_page.html?tipo=nif");
        exit;
    }

    $cliente = $_SESSION['cliente']; 
    
    $desconto = min($artigo['descmax'], $cliente['Desconto']);

    if($artigo['precoPromocaoSIva']){
        $precoComDesconto = $artigo['precoPromocaoSIva'] * (1 - ($desconto / 100));
    }
    else{
        $precoComDesconto = $artigo['pvpsiva'] * (1 - ($desconto / 100));
    }
}
?>


<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Detalhes do Produto</title>
        <link rel="stylesheet" href="mostrar_artigo_cliente.css">

        <script>
            setTimeout(function() {
                window.location.href = "leitor_produto_cliente.php"; // Redireciona para index.php após 10 segundos
            }, 10000);
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

                $precoun = 0;
                if ($artigo['PrecoPor'] === 'Kg' || $artigo['PrecoPor'] === 'Lt') {
                    $precoun = ($precoComDesconto * 1000) / $artigo['CapacidadeUn'];
                    
                } else if ($artigo['PrecoPor'] === 'Ud' || $artigo['PrecoPor'] === 'Dose') {
                    $precoun = ($precoComDesconto * 1) / $artigo['CapacidadeUn'];
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
                            <span class="price"> <?= number_format(floatval($precoComDesconto), 2, '.', '') ?>€</span>
                            <div class="tax-info">
                                <span class="iva"> S/IVA </span>
                                <?php if ($artigo['precoPromocao']) : ?>
                                    <span class="desconto">C/Desconto</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <p class="unvenda"><?= htmlspecialchars($artigo['unvenda']) ?></p>
                        <p class="precoun"><?= number_format(floatval($precoun), 2, '.', '') ?>€ / <?= htmlspecialchars($artigo['PrecoPor']) ?></p>
                    </div>
                </div>
            </div>

            
        <?php else: ?>
            <p>Artigo não encontrado!</p>
        <?php endif; ?>

        <script>
            let barcode = "";
            let timeout = null;
    
            document.addEventListener("keydown", function (event) {
                if (timeout) clearTimeout(timeout);

                if (event.key.length === 1) {
                    barcode += event.key;
                }
    
                timeout = setTimeout(() => {
                    if (event.key === "Enter") {
                        if ( barcode.length >= 13 ) {
                            window.location.href = "wait_page/wait_page.html?codigo=" + encodeURIComponent(barcode) + "&tipo=cliente";
                        }
                        barcode = "";
                    }
                }, 100);
            });
        </script>
    </body>
</html>

