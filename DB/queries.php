<?php
    include 'connection.php'; // Importa a conexão com a base de dados

    function getArtigoPorCodigo($codigo) {
        global $conn, $config; // Usa a conexão e configurações globalmente
        $linha = $config['linhaPreco']; // Linha de preço do arquivo de configuração
        $armazem = $config['codigoArmazem']; // Código do armazém do arquivo de configuração

        // Primeira tentativa (busca pelo código do artigo + stock de um armazém específico)
        $sql1 = "SELECT TOP(1) 
                    artigos.codigo, 
                    artigos.nome, 
                    artigos.iva, 
                    artigos.familia, 
                    artigos.unvenda, 
                    linhasprecos.pvpciva, 
                    linhasprecos.pvpsiva, 
                    artigos.imagem,
                ISNULL(armazens.existencia, 0) as 'existencia', 
                ISNULL(armazens.armazem,0) as 'armazem'
                FROM wgcartigos artigos
                LEFT JOIN wgccodbarras codbarras  ON codbarras.artigo = artigos.codigo
                LEFT JOIN wgcartigoslinhasprecos linhasprecos ON artigos.codigo = linhasprecos.artigo
                LEFT JOIN wgcartarmazens armazens ON artigos.codigo = armazens.artigo AND (armazens.armazem = :armazem OR armazens.armazem IS NULL)
                WHERE artigos.codigo = :codigo 
                AND linhasprecos.linha = :linha";

        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute([
            ':codigo' => $codigo, 
            ':linha' => $linha, 
            ':armazem' => $armazem
        ]);
        $artigo = $stmt1->fetch(PDO::FETCH_ASSOC);

        // Se não encontrou, tenta a segunda query (busca pelo código de barras + stock de um armazém específico)
        if (!$artigo) {
            $sql2 = "SELECT TOP(1) 
                        artigos.codigo, 
                        artigos.nome, 
                        artigos.iva, 
                        artigos.familia, 
                        artigos.unvenda, 
                        linhasprecos.pvpciva, 
                        linhasprecos.pvpsiva, 
                        artigos.imagem,
                    ISNULL(armazens.existencia, 0) as 'existencia', 
                    ISNULL(armazens.armazem,0) as 'armazem'
                    FROM wgcartigos artigos
                    LEFT JOIN wgccodbarras codbarras  ON codbarras.artigo = artigos.codigo
                    LEFT JOIN wgcartigoslinhasprecos linhasprecos ON artigos.codigo = linhasprecos.artigo
                    LEFT JOIN wgcartarmazens armazens ON artigos.codigo = armazens.artigo AND (armazens.armazem = :armazem OR armazens.armazem IS NULL)
                    WHERE codbarras.codbarras = :codigo 
                    AND linhasprecos.linha = :linha";

            $stmt2 = $conn->prepare($sql2);
            $stmt2->execute([
                ':codigo' => $codigo, 
                ':linha' => $linha, 
                ':armazem' => $armazem
            ]);
            $artigo = $stmt2->fetch(PDO::FETCH_ASSOC);
        }

        return $artigo; // Retorna os dados do artigo (com stock do armazém específico) ou null se não encontrar
    }

    function getDescontoCliente($clienteId) {
        global $conn;
    
        $sql = "SELECT 
                    terceiros.NumeroCliente,
                    terceiros.Nome,
                    terceiros.Zona,
                    escontos.DESC1 AS Desconto
                FROM Wgcterceiros terceiros
                JOIN Wgcdescontos escontos ON terceiros.Zona = escontos.Zona
                WHERE terceiros.NumeroCliente = :clienteId";
    
        $stmt = $conn->prepare($sql);
        $stmt->execute([':clienteId' => $clienteId]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retorna false se não existir o cliente na tabela
    }
?>