<?php
include '../Pager.php';
include '../PagerMySQL.php';

$pageNum = (isset($_GET['p']) && $_GET['p'] != '') ? (int)$_GET['p'] : 1;

$dbh = new mysqli('localhost', 'root', '', 'pager_test');

$dataOptions = array(
    'handle' => $dbh,
    'query' => 'SELECT * FROM data'
);

$pager = new PagerMySQL($pageNum);
$data = $pager->getData($dataOptions);

$pagerNav = $pager->render();

$pageTitle = 'Pager - MySQLi example';
include 'example.tpl.php';
?>