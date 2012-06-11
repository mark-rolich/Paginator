<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
  "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $pageTitle; ?></title>
	<link rel="stylesheet" type="text/css" href="example.css">
</head>
<body>
<?php if (!empty($data)) { ?>

<table class="data">
<?php foreach ($data as $row) { ?>
<tr><td><?php echo implode('</td><td>', $row); ?></td></tr>
<?php } ?>
</table>

<?php } else { ?>
<div>No records found</div>
<?php } ?>

<?php echo $pagerNav; ?>

</body>
</html>