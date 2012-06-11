<?php
include '../Pager.php';
include '../PagerCSV.php';

$pageNum = (isset($_GET['p']) && $_GET['p'] != '') ? (int)$_GET['p'] : 1;

$handle = fopen('data.csv', "r");

$pager = new PagerCSV($pageNum);
$data = $pager->getData($handle);

$pagerNav = $pager->render();

$pageTitle = 'Pager - CSV example';
include 'example.tpl.php';
?>