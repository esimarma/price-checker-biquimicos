<?php
    include 'connection.php'; // Importa a conexão com a base de dados


    function getArtigoPorCodigo($codigo) {
        global $conn, $config;
    
        $linha = $config['linhaPreco'];
        $armazem = $config['codigoArmazem'];
        $sectores = $config['sectores'];
    
        // Cria os ? dinamicamente para os sectores
        $placeholders = implode(',', array_fill(0, count($sectores), '?'));
    
        $sql = "SELECT TOP(1)
                    artigos.codigo, 
                    artigos.nome, 
                    artigos.iva, 
                    artigos.familia, 
                    artigos.unvenda, 
                    artigos.CapacidadeUn,
                    artigos.PrecoPor,
                    artigos.descmax,
                    linhasprecos.pvpciva, 
                    linhasprecos.pvpsiva, 
                    artigos.imagem,
                    ISNULL(armazens.existencia, 0) AS [existencia], 
                    ISNULL(armazens.armazem, 0) AS [armazem],
                    precos.Preco AS precoPromocao,
                    precos.PrecoSIva AS precoPromocaoSIva
                FROM wgcartigos artigos
                LEFT JOIN wgccodbarras codbarras 
                    ON codbarras.artigo COLLATE DATABASE_DEFAULT = artigos.codigo COLLATE DATABASE_DEFAULT
                LEFT JOIN wgcartigoslinhasprecos linhasprecos 
                    ON artigos.codigo COLLATE DATABASE_DEFAULT = linhasprecos.artigo COLLATE DATABASE_DEFAULT
                LEFT JOIN wgcartarmazens armazens 
                    ON artigos.codigo COLLATE DATABASE_DEFAULT = armazens.artigo COLLATE DATABASE_DEFAULT
                    AND (armazens.armazem = ? OR armazens.armazem IS NULL)
                LEFT JOIN wgcprecos precos 
                    ON precos.Artigo COLLATE DATABASE_DEFAULT = artigos.codigo COLLATE DATABASE_DEFAULT
                    AND precos.Activo = 1 
                    AND GETDATE() BETWEEN precos.DtInicio AND precos.DtFim
                    AND precos.Sector IN ($placeholders)
                WHERE 
                    (artigos.codigo COLLATE DATABASE_DEFAULT = ?
                    OR codbarras.codbarras = ?)
                AND linhasprecos.linha = ?";
    
        $stmt = $conn->prepare($sql);
    
        // Ordem dos parâmetros deve ser igual à ordem dos ? na query:
        // armazem, sectores..., codigo, codigo, linha
        $params = array_merge([$armazem], $sectores, [$codigo, $codigo, $linha]);
    
        $stmt->execute($params);
    
        $artigo = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $artigo ?: null;
    }


    function getDescontoCliente($clienteId) {
        global $conn;
    
        $sql = "SELECT 
                    t.codigo,
                    t.nome,
                    t.zona,
                    t.ncontrib,
                    d.DESC1 AS Desconto
                FROM wgcterceiros t
                LEFT JOIN (
                    SELECT zona, MAX(DESC1) AS DESC1
                    FROM wgcdescontos
                    WHERE zona IS NOT NULL AND zona <> ''
                    GROUP BY zona
                ) d ON t.zona = d.zona
                WHERE (t.ncontrib = :nif OR t.codigo = :numeroCliente)";
    
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nif' => $clienteId,
            ':numeroCliente' => $clienteId
        ]);
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
?>