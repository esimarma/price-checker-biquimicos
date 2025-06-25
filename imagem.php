<?php
$config = json_decode(file_get_contents('config.json'), true);

$imagem = isset($_GET['file']) ? basename($_GET['file']) : null;

$caminho = rtrim($config['caminhoImagensProdutos'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $imagem;

if ($imagem && file_exists($caminho)) {
    $mime = mime_content_type($caminho);
    header("Content-Type: $mime");
    readfile($caminho);
    exit;
} else {
    http_response_code(404);
    echo "Imagem não encontrada.";
}