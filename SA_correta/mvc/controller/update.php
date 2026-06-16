<?php
require('conexao.php');

$id = filter_input(INPUT_POST, 'id');
$nome = filter_input(INPUT_POST, 'nome');
$sobrenome = filter_input(INPUT_POST, 'sobrenome');
$datanasc = filter_input(INPUT_POST, 'datanasc');

$sql = "UPDATE cadastro 
SET nome = :nome,
sobrenome = :sobrenome,
datanasc = :datanasc
WHERE id = :id";

$statement = $pdo->prepare($sql);

$statement->bindValue(':id', $id);
$statement->bindValue(':nome', $nome);
$statement->bindValue(':sobrenome', $sobrenome);
$statement->bindValue(':datanasc', $datanasc);

$statement->execute();

header('Location: index.php');
exit();
?>