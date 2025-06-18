

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cartão do Cliente</title>
  <link rel="stylesheet" href="cartao_cliente.css"/>
</head>
<body>
  <div class="card">
    <div class="header">LEIA O CARTÃO OU INTRODUZA O SEU NIF</div>
    <div class="content">
      <input type="text" class="input-nif" placeholder="" autofocus />
      <img src="Imagens/codigo_barras.svg" alt="Código de barras" class="barcode-img" />
    </div>
  </div>

  <script>
    let nif = "";
    let timeout = null;

    document.addEventListener("keydown", function(event) {
        if (timeout) clearTimeout(timeout);

        if (event.key.length === 1 && /\d/.test(event.key)) {
            nif += event.key;
        }

        timeout = setTimeout(() => {
            if (nif.length >= 9) {
                console.log("NIF lido:", nif);
                window.location.href = "wait_page/wait_page.html?nif=" + encodeURIComponent(nif);
                nif = "";
            }
        }, 150);
    });
  </script>
</body>
</html>