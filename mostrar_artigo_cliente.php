<?php
include 'db/connection.php';
include 'db/queries.php';

// Carrega config.json
$configPath = __DIR__ . '/config.json';
$config = json_decode(file_get_contents($configPath), true);

$codigo = isset($_GET['codigo']) ? $_GET['codigo'] : null;
$artigo = $codigo ? getArtigoPorCodigo($codigo) : null;

// Se o artigo não for encontrado, redireciona para pagina_nao_encontrada.html
if (!$artigo) {
    header("Location: pagina_nao_encontrada.html?tipo=cliente");
    exit;
} else {
    session_start();
    if (!isset($_SESSION['cliente'])) {
        header("Location: pagina_nao_encontrada.html?tipo=nif");
        exit;
    }

    $cliente = $_SESSION['cliente']; 
    $desconto = min($artigo['descmax'], $cliente['Desconto']);

    if ($artigo['precoPromocaoSIva']) {
        $precoComDesconto = $artigo['precoPromocaoSIva'] * (1 - ($desconto / 100));
    } else {
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
        setTimeout(function () {
            window.location.href = "leitor_produto_cliente.php";
        }, 10000);
    </script>
</head>
<body>
<?php if ($artigo): ?>
    <?php
        // Caminho para imagem
        $imagemFinal = "imagem.php?file=default.png";
        if (!empty($artigo['imagem'])) {
            $imagemFinal = "imagem.php?file=" . urlencode($artigo['imagem']);
        }

        $precoun = 0;
        if ($artigo['PrecoPor'] === 'Kg' || $artigo['PrecoPor'] === 'Lt') {
            $precoun = ($precoComDesconto * 1000) / $artigo['CapacidadeUn'];
        } elseif ($artigo['PrecoPor'] === 'Ud' || $artigo['PrecoPor'] === 'Dose') {
            $precoun = $precoComDesconto / $artigo['CapacidadeUn'];
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
                    <span class="price"><?= number_format(floatval($precoComDesconto), 2, '.', '') ?>€</span>
                    <div class="tax-info">
                        <span class="iva">S/IVA</span>
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
            <img src="<?= htmlspecialchars($config['caminhoImagens'] . "logo.svg") ?>" alt="Logo">
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

    document.addEventListener("keydown", function (event) {
        if (timeout) clearTimeout(timeout);

        if (event.key.length === 1) {
            barcode += event.key;
        }

        timeout = setTimeout(() => {
            if (event.key === "Enter") {
                if (barcode.length >= 13) {
                    window.location.href = "pagina_espera/pagina_espera.html?codigo=" + encodeURIComponent(barcode) + "&tipo=cliente";
                } else {
                    showOkPopup("O seu cartão de cliente já foi lido.");
                }
                barcode = "";
            }
        }, 100);
    });

    function showOkPopup(message) {
        if (document.getElementById("ok-popup")) return;

        const popup = document.createElement("div");
        popup.id = "ok-popup";
        popup.innerHTML = `
            <div class="popup-overlay">
                <div class="popup-box">
                    <p class="popup-text">${message}</p>
                    <div class="popup-buttons">
                        <button id="ok-btn">OK</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(popup);

        document.getElementById("ok-btn").onclick = () => {
            document.body.removeChild(popup);
        };
    }

    setTimeout(() => {
        removePopup();
    }, 7000);

    function removePopup() {
        const existing = document.getElementById("ok-popup");
        if (existing) {
            document.body.removeChild(existing);
        }
    }
</script>
</body>
</html>