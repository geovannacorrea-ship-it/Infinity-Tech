<?php
require('conexao.php');

$id = filter_input(INPUT_GET, 'id');

if($id){

    $sql = "DELETE FROM cadastro WHERE id = :id";

    $statement = $pdo->prepare($sql);

    $statement->bindValue(':id', $id);

    $statement->execute();
}

header('Location: index.php');
?>