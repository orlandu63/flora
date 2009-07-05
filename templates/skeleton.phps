<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id="<?php echo $page_id ?>">
<head>
 <title><?php if(isset($title)) echo $title, ' — '; echo Page::FORUM_NAME ?> Message Boards</title>
 <link rel="stylesheet" href="style.css" type="text/css"/>
 <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
</head>
<body>
 <h1 id="header"><a href="<?php echo Page::makeURI(Page::PAGE_INDEX) ?>"><?php echo Page::FORUM_NAME ?></a></h1>
 <p id="announcement"><?php echo $announcement ?></p>
 <ul id="site-nav" class="float-right inline-list">
 <?php foreach($site_nav as $text => $uri): ?>
  <li><a href="<?php echo $uri ?>"><?php echo $text ?></a></li>
 <?php endforeach ?>
 </ul>
 <h2><?php echo $header ?></h2>
 <?php echo $contents ?>
<p id="footer"><code>exec(new <?php echo SOFTWARE ?>('<?php echo VERSION ?>')) →
 <?php echo $time_index ?>s allocating <?php echo $memory_alloc ?> bytes
</code></p>
</body>
</html>