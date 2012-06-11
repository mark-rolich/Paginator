<?php
include '../Pager.php';
include '../PagerArray.php';
include 'data.php';

$pageNum = (isset($_GET['p']) && $_GET['p'] != '') ? (int)$_GET['p'] : 1;

$pager = new PagerArray($pageNum);
$data = $pager->getData($data);

$pagerNav = $pager->render();

$pageTitle = 'Pager - Array example';
include 'example.tpl.php';
?>