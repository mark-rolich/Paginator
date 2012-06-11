<?php
include '../Pager.php';
include '../PagerMySQL.php';

$pageNum = (isset($_GET['p']) && $_GET['p'] != '') ? (int)$_GET['p'] : 1;

$dbh = mysql_connect('localhost', 'root', '');
mysql_select_db('pager_test', $dbh);

$dataOptions = array(
    'handle' => $dbh,
    'query' => 'SELECT * FROM data'
);

$pager = new PagerMySQL($pageNum);
$data = $pager->getData($dataOptions);

$pagerNav = $pager->render();

$pageTitle = 'Pager - MySQL example';
include 'example.tpl.php';
?>