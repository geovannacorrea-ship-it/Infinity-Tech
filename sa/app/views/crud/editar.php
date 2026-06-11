<?php
require('conexao.php');

$id = filter_input(INPUT_GET, 'id');

$sql = "SELECT * FROM cadastro WHERE id = :id";

$statement = $pdo->prepare($sql);

$statement->bindValue(':id', $id);

$statement->execute();

$usuario = $statement->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
</head>
<body>

<h2>Atualizar Usuário</h2>

<form action="update.php" method="post">

    <input type="hidden" name="id" value="<?= $usuario['id']; ?>">

    <input type="text" name="nome" value="<?= $usuario['nome']; ?>">

    <input type="text" name="sobrenome" value="<?= $usuario['sobrenome']; ?>">

    <input type="date" name="datanasc" value="<?= $usuario['datanasc']; ?>">

    <input type="submit" value="Atualizar">

</form>

</body>
</html>