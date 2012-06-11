<?php
include '../Pager.php';
include '../PagerMySQL.php';

$pageNum = (isset($_GET['p']) && $_GET['p'] != '') ? (int)$_GET['p'] : 1;

$dbh = new mysqli('localhost', 'root', '', 'pager_test');

$id = 1;
$country = 'A%';

$dataOptions = array(
    'handle' => $dbh,
    'query' => 'SELECT * FROM data WHERE id > ? AND country LIKE ?',
    'params' => array('is', &$id, &$country)
);

$pager = new PagerMySQL($pageNum);
$data = $pager->getData($dataOptions);

$pagerNav = $pager->render();

$pageTitle = 'Pager - MySQLi prepared example';
include 'example.tpl.php';
?>