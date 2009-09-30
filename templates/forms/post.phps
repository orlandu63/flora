<h2><?php echo $header ?></h2>
<form action="<?php echo Page::makeURI(Page::PAGE_POST, $params) ?>" method="post">
<fieldset class="float-wrap">
<legend><?php echo $legend ?></legend>
 <div class="float-left">
 <label>Name: <?php printf(Page::$input_format, User::MAX_AUTHOR_LENGTH, $data['author'], 'author') ?></label>
  <small>(optional)</small><br/>
 <?php if($type === Page::FORM_TOPIC): ?>
 <label>Title: <?php printf(Page::$input_format, Topics::MAX_TITLE_LENGTH, $data['title'], 'title') ?></label><br/>
 <?php endif ?>
 <label>Body: (you may use Markdown →)<br/>
  <textarea name="body" cols="80" rows="10"><?php echo $data['body'] ?></textarea>
 </label><br/>
 <input type="submit" value="<?php echo $submit_value ?>" name="submit"/>
 <input type="submit" value="Preview" name="preview"/>
</div>
<div class="float-right">
<?php $this->load('markdown_help') ?>
</div>
</fieldset>
</form>