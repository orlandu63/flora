<div class="<?php echo Posts::generatePostClasses($post_info) ?>"<?php if($id) echo ' id="', $id, '"' ?>>
<div class="post-info-wrap">
 <ul class="post-info inline-list">
  <li>by <?php echo User::author($post_info['author'], User::generateUserClasses($post_info)) ?></li>
  <li><?php echo Page::formatTime($post_info['toc'], (isset($post_info['date']) ? $post_info['date'] : null)) ?></li>
  <?php if(isset($post_info['id'])): ?>
  <li><a href="<?php echo Topics::makeURI($post_info['topic'], $post_info['id']) ?>"
         title="view context of this post">context</a></li>
  <?php endif ?>
 </ul>
</div>
<div class="post-body"><?php echo $post_info['body'] ?></div>
</div>