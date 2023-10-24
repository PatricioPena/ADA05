<?php
include("QueryHandler.php");

$query = $_POST["queryString"];

$queryH = new QueryHandler($query);
$queryH->showQuery();

?>