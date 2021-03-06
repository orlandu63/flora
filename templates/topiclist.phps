<div id="topiclist">
<?php foreach($topics as $topic_info): ?>
<div id="<?php echo Topics::htmlId($topic_info['id']) ?>" class="<?php echo implode(' ', Topics::generateTopicClasses($topic_info)) ?>">
 <h3><a href="<?php echo Topics::makeURI($topic_info['id'], $topic_info['post']) ?>">
  <?php echo $topic_info['title'] ?>
 </a></h3>
 <ul class="topic-info inline-list">
  <li>by <?php echo User::display($topic_info['author'], $topic_info['user_id']) ?></li>
  <li><?php echo $topic_info['replies'], ' ', ($topic_info['replies'] == 1 ? 'reply' : 'replies') ?></li>
  <li>last post 
   <a href="<?php echo Topics::makeURI($topic_info['id'], $topic_info['last_post_id']) ?>">
    <?php echo Page::formatTime($topic_info['last_post']) ?></a>
	by <?php echo User::display($topic_info['last_post_author'], $topic_info['last_post_user_id']) ?>
  </li>
 </ul>
</div>
<?php endforeach ?>
</div>