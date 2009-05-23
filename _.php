<?php
define('VERSION', '0.9');
define('CLASS_DIR', 'classes/');

//these are located inside the include path
require 'db.php';
require 'stemplator.php';

require CLASS_DIR . 'user.php';
require CLASS_DIR . 'page.php';
require CLASS_DIR . 'posts.php';
require CLASS_DIR . 'topics.php';

$Page = new Page;
$DB = new DB('flora');
User::refresh();