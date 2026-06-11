<?php
// conexao.php - Conexão PDO unificada
// Banco: infinity_tech | Host: 127.0.0.1 | Porta: 3306

$host   = '127.0.0.1';
$porta  = '3306';
$banco  = 'infinity_tech';
$usuario_db = 'root';
$senha_db   = '';

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$porta};dbname={$banco};charset=utf8mb4",
        $usuario_db,
        $senha_db,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // Em produção, nunca exiba detalhes do erro ao usuário
    error_log("Erro de conexão: " . $e->getMessage());
    die("Não foi possível conectar ao banco de dados. Tente novamente mais tarde.");
}
