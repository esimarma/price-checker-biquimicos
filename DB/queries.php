<?php
    include 'connection.php'; // Importa a conexão com o banco de dados

    function getArtigoPorCodigo($codigo) {
        global $conn, $config; // Usa a conexão e configurações globalmente
        $linha = $config['linhaPreco']; // Pega a linha do arquivo de configuração

        // Primeira tentativa (buscando pelo código do artigo)
        $sql1 = "SELECT TOP(1) b.codigo, b.nome, iva, familia, unvenda, c.pvpciva , c.pvpsiva , imagem
                    from wgccodbarras a inner join wgcartigos b 
                        on a.artigo = b.codigo
                        left join wgcartigoslinhasprecos c 
                        on b.codigo = c.artigo
                WHERE b.codigo = :codigo AND c.linha = :linha";

        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute([':codigo' => $codigo, ':linha' => $linha]);
        $artigo = $stmt1->fetch(PDO::FETCH_ASSOC);

        // Se não encontrou, tenta a segunda query (buscando pelo código de barras)
        if (!$artigo) {
            $sql2 = "SELECT b.codigo, b.nome, iva, familia, unvenda, c.pvpciva , c.pvpsiva , imagem
                        from wgccodbarras a inner join wgcartigos b 
                        on a.artigo = b.codigo
                        left join wgcartigoslinhasprecos c 
                        on b.codigo = c.artigo

                    WHERE codbarras = :codigo AND c.linha = :linha";

            $stmt2 = $conn->prepare($sql2);
            $stmt2->execute([':codigo' => $codigo, ':linha' => $linha]);
            $artigo = $stmt2->fetch(PDO::FETCH_ASSOC);
        }

        return $artigo; // Retorna os dados do artigo (ou null se não encontrar)
    }
?>