<?php
    include 'connection.php'; // Importa a conexão com a base de dados

    function getArtigoPorCodigo($codigo) {
        global $conn, $config; // Usa a conexão e configurações globalmente
        $linha = $config['linhaPreco']; // Linha de preço do arquivo de configuração
        $armazem = $config['codigoArmazem']; // Código do armazém do arquivo de configuração

        // Primeira tentativa (busca pelo código do artigo + stock de um armazém específico)
        $sql1 = "SELECT TOP(1) 
                    b.codigo, 
                    b.nome, 
                    b.iva, 
                    b.familia, 
                    b.unvenda, 
                    c.pvpciva, 
                    c.pvpsiva, 
                    b.imagem,
                    s.existencia, 
                    s.armazem
                 FROM wgccodbarras a 
                 LEFT JOIN wgcartigos b ON a.artigo = b.codigo
                 LEFT JOIN wgcartigoslinhasprecos c ON b.codigo = c.artigo
                 LEFT JOIN wgcartarmazens s ON b.codigo = s.artigo 
                 WHERE b.codigo = :codigo 
                 AND c.linha = :linha
                 AND s.armazem = :armazem";

        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute([
            ':codigo' => $codigo, 
            ':linha' => $linha, 
            ':armazem' => $armazem
        ]);
        $artigo = $stmt1->fetch(PDO::FETCH_ASSOC);

        // Se não encontrou, tenta a segunda query (busca pelo código de barras + stock de um armazém específico)
        if (!$artigo) {
            $sql2 = "SELECT b.codigo, 
                            b.nome, 
                            b.iva, 
                            b.familia, 
                            b.unvenda, 
                            c.pvpciva, 
                            c.pvpsiva, 
                            b.imagem,
                            s.existencia, 
                            s.armazem
                     FROM wgccodbarras a 
                     INNER JOIN wgcartigos b ON a.artigo = b.codigo
                     LEFT JOIN wgcartigoslinhasprecos c ON b.codigo = c.artigo
                     LEFT JOIN wgcartarmazens s ON b.codigo = s.artigo 
                     WHERE a.codbarras = :codigo 
                     AND c.linha = :linha
                     AND s.armazem = :armazem";

            $stmt2 = $conn->prepare($sql2);
            $stmt2->execute([
                ':codigo' => $codigo, 
                ':linha' => $linha, 
                ':armazem' => $armazem
            ]);
            $artigo = $stmt2->fetch(PDO::FETCH_ASSOC);
        }

        return $artigo; // Retorna os dados do artigo (com estoque do armazém específico) ou null se não encontrar
    }
?>