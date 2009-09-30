<h2><?php echo $header ?></h2>
<form action="<?php echo Page::makeURI(Page::PAGE_POST, $params) ?>" method="post">
<fieldset class="float-wrap">
<legend><?php echo $legend ?></legend>
 <div class="float-left">
 <label>Name: <?php echo $form->input('author', User::MAX_AUTHOR_LENGTH) ?></label>
  <small>(optional)</small><br/>
 <?php if($type === Page::FORM_TOPIC): ?>
 <label>Title: <?php echo $form->input('title', Topics::MAX_TITLE_LENGTH) ?></label><br/>
 <?php endif ?>
 <label>Body: (you may use Markdown →)<br/>
  <textarea name="body" cols="80" rows="10"><?php echo $form_data['body'] ?></textarea>
 </label><br/>
 <input type="submit" value="<?php echo $submit_value ?>" name="submit"/>
 <input type="submit" value="Preview" name="preview"/>
</div>
<div class="float-right">
<?php $this->load('markdown_help') ?>
</div>
</fieldset>
</form>