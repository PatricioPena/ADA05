<?php


include("QueryHandler.php");



$query = $_POST["queryString"];
$queryH = new QueryHandler($query);
//Muestra los resultados de la consulta que se realizó
$queryH->showQuery();



?>