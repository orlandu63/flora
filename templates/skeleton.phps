<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id="<?php echo implode('-', $page_id) ?>">
<head>
 <title><?php if(isset($title)) echo $title, ' :: '; echo $forum_name ?> Message Board</title>
 <link rel="stylesheet" href="<?php echo Page::makeFingerprintURI('style.css') ?>" type="text/css"/>
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
</head>
<body>
 <h1 id="header"><a href="<?php echo Page::makeURI(Page::PAGE_INDEX) ?>"><?php echo $forum_name ?></a></h1>
 <p id="announcement"><?php echo $announcement ?></p>
 <ul id="site-nav" class="float-right inline-list">
 <?php foreach($site_nav as $text => $uri): ?>
  <li><a href="<?php echo $uri ?>"><?php echo $text ?></a></li>
 <?php endforeach ?>
 </ul>
 <h2><?php echo $header ?></h2>
 <!-- start contents -->
 <?php echo $contents ?>
 <!-- end contents -->
 <p id="who" title="this is your anonymous identity">
  you are <?php echo User::display(User::$name, User::$id) ?> {<?php echo User::$id ?>}
 </p>
 <p id="footer">
  <code>
   <?php printf('exec(new %s(\'%s\')) → %.2fs allocating %s using %d query(s)',
	Settings::get('software'), VERSION, $time_index, $memory_alloc, DB::$num_queries) ?>
  </code>
  <a class="float-right" href="/">scrap.ath.cx:99</a>
 </p>
</body>
</html>