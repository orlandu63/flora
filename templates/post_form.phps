<h2><?php echo $header ?></h2>
<form action="<?php echo $action_uri ?>" method="post">
<fieldset>
  <legend><?php echo $legend ?></legend>
  <label>Name: <?php echo sprintf($input_format, User::MAX_AUTHOR_LENGTH, $data['author'], 'author') ?></label>
    <small>(optional)</small><br/>
  <?php if($type === Page::FORM_TOPIC): ?>
  <label>Title: <?php echo sprintf($input_format, Topics::MAX_TITLE_LENGTH, $data['title'], 'title') ?></label><br/>
  <?php endif ?>
  <label>Body: (you may use <a href="http://en.wikipedia.org/wiki/Markdown">Markdown</a>)<br/>
    <textarea name="body" cols="80" rows="10"><?php echo $data['body'] ?></textarea>
  </label><br/>
  <input type="submit" value="<?php echo $submit_value ?>" name="submit"/>
 <input type="submit" value="Preview" name="preview"/>
</fieldset>
</form>