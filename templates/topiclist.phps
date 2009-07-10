<div id="topiclist">
<?php foreach($topics as $topic_info): ?>
<div class="<?php echo implode(' ', Topics::generateTopicClasses($topic_info)) ?>">
 <h3><a href="<?php echo Topics::makeURI($topic_info['id'], $topic_info['post']) ?>">
  <?php echo $topic_info['title'] ?>
 </a></h3>
 <ul class="topic-info inline-list">
  <li>by <?php echo User::author($topic_info['author']) ?></li>
  <li><?php echo $topic_info['replies'] ?> repl<?php echo ($topic_info['replies'] == 1 ? 'y' : 'ies') ?></li>
  <li>last post 
   <a href="<?php echo Topics::makeURI($topic_info['id'], $topic_info['last_post_id']) ?>">
    <?php echo Page::formatTime($topic_info['last_post']) ?>
   </a> by <?php echo User::author($topic_info['last_post_author']) ?>
  </li>
 </ul>
</div>
<?php endforeach ?>
</div>