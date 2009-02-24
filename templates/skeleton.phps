<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $title ?> — Flora Message Boards</title>
<link rel="stylesheet" href="style.css" type="text/css"/>
</head>
<body>
<h1 id="header"><a href=".">Flora</a></h1>
<p><?php echo coalesce(get($announcement), 'unmoderated anonymous message board') ?></p>
<?php echo $contents ?>
<hr/>
<p id="footer">Exec(new Flora('<?php echo VERSION ?>')) → <a href="?source"><?php echo round(xdebug_time_index(), 3) ?></a></p></body></html>