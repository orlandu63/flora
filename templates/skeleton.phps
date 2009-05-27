<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id="<?php echo $page_id ?>">
<head>
 <title><?php if(isset($title)) echo $title, ' — '; echo Page::FORUM_NAME ?> Message Boards</title>
 <link rel="stylesheet" href="style.css" type="text/css"/>
</head>
<body>
 <h1 id="header"><a href="<?php echo Page::makeURI(Page::PAGE_INDEX) ?>"><?php echo Page::FORUM_NAME ?></a></h1>
 <p id="announcement"><?php echo $announcement ?></p>
 <?php echo $contents ?>
<p id="footer">exec(new <?php echo Page::FORUM_NAME ?>('<?php echo VERSION ?>')) →
 <?php echo $time_index ?>
</p>
</body>
</html>