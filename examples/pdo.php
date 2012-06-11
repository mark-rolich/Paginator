<?php
include '../Pager.php';
include '../PagerMySQL.php';

$pageNum = (isset($_GET['p']) && $_GET['p'] != '') ? (int)$_GET['p'] : 1;

$dbh = new PDO('mysql:host=localhost;dbname=pager_test', 'root', '');

$dataOptions = array(
    'handle' => $dbh,
    'query' => 'SELECT * FROM data'
);

$pager = new PagerMySQL($pageNum);
$data = $pager->getData($dataOptions);

$pagerNav = $pager->render();

$pageTitle = 'Pager - PDO example';
include 'example.tpl.php';
?>