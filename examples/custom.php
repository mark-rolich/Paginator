<?php
include '../Pager.php';
include '../PagerMySQL.php';

$pageNum = (isset($_GET['page']) && $_GET['page'] != '') ? (int)$_GET['page'] : 1;

$dbh = mysql_connect('localhost', 'root', '');
mysql_select_db('pager_test', $dbh);

$dataOptions = array(
    'handle' => $dbh,
    'query' => 'SELECT * FROM data'
);

$displayOptions = array(
    'url'        => '?page=%d&param1=1&param2=2',
    'first'      => 1,
    'prev'       => 1,
    'next'       => 1,
    'last'       => 1,
    'firstLabel' => 'first',
    'prevLabel'  => 'previous',
    'nextLabel'  => 'next',
    'lastLabel'  => 'last'
);

$statsString = '<div class="range">Displaying: %d - %d</div>
                <div class="total">%d records found</div>';

$pager = new PagerMySQL($pageNum, 15, 5);
$data = $pager->getData($dataOptions);

$pagerNav = $pager->render($displayOptions);

$pageTitle = 'Pager - MySQL customized example';

$stats = $pager->renderStats($statsString);

include 'example.custom.tpl.php';
?>