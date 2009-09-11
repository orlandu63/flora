<?php $post_classes = Posts::generatePostClasses($post_info) ?>
<div class="<?php echo implode(' ', $post_classes) ?>"
 <?php if(!empty($post_info['id'])) echo ' id="', Posts::htmlId($post_info['id']), '"' ?>>
<div class="post-info-wrap">
 <ul class="post-info inline-list">
  <li>by <?php echo User::display($post_info['author'], $post_info['ip']) ?></li>
  <li><?php echo Page::formatTime($post_info['toc']) ?></li>
  <?php if(isset($post_info['id'])): ?>
  <li><a href="<?php echo Topics::makeURI($post_info['topic'], $post_info['id']) ?>"
         title="view context of this post">context</a></li>
  <?php endif ?>
 </ul>
</div>
<div class="post-body"><?php echo $post_info['body'] ?></div>
</div>