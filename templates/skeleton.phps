<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php if(isset($title)) echo $title, ' — '; echo Page::FORUM_NAME ?> Message Boards</title>
<link rel="stylesheet" href="style.css" type="text/css"/>
</head>
<body>
<h1 id="header"><a href="<?php echo Page::PAGE_INDEX ?>"><?php echo Page::FORUM_NAME ?></a></h1>
<p><?php echo coalesce(get($announcement), Page::DEFAULT_ANNOUNCEMENT) ?></p>
<?php echo $contents ?>
<hr/>
<p id="footer">Exec(new <?php echo Page::FORUM_NAME ?>('<?php echo VERSION ?>')) → <a href="?source"><?php echo round(xdebug_time_index(), 3) ?></a></p></body></html>