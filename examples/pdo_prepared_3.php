<?php
include '../Pager.php';
include '../PagerMySQL.php';

$pageNum = (isset($_GET['p']) && $_GET['p'] != '') ? (int)$_GET['p'] : 1;

$dbh = new PDO('mysql:host=localhost;dbname=pager_test', 'root', '');

$id = 1;
$country = 'A%';

$dataOptions = array(
    'handle' => $dbh,
    'query' => 'SELECT * FROM data WHERE id > ? AND country LIKE ?',
    'params' => array(
        array(1, $id, PDO::PARAM_INT),
        array(2, $country, PDO::PARAM_STR)
    )
);

$pager = new PagerMySQL($pageNum);
$data = $pager->getData($dataOptions);

$pagerNav = $pager->render();

$pageTitle = 'Pager - PDO prepared example';
include 'example.tpl.php';
?>