<?php
//todo: do something about this
$label_maxlen = max(array_map('strlen', $labels));
$align = function($label) use($label_maxlen) {
	return $label . ':' . str_repeat('&nbsp;', $label_maxlen - strlen($label));
};
$html_input = function($name, $max_length) use($data) {
	return sprintf(Page::$input_format, $name, $data[$name], $max_length);
}
?>
<h2><?php echo $header ?></h2>
<form action="<?php echo Page::makeURI(Page::PAGE_POST, $params) ?>" method="post">
<fieldset class="float-wrap">
<legend><?php echo $legend ?></legend>
 <div class="float-left">
 <label><tt><?php echo $align($labels['author']) ?></tt> <?php echo $html_input('author', User::MAX_AUTHOR_LENGTH) ?></label>
  <small>(optional)</small><br/>
 <?php if($type === Page::FORM_TOPIC): ?>
 <label><tt><?php echo $align($labels['title']) ?></tt> <?php echo $html_input('title', Topics::MAX_TITLE_LENGTH) ?></label><br/>
 <label style="color: grey"><tt><?php echo $align($labels['tags']) ?></tt> <?php echo $html_input('tags', 50) ?></label><br/>
 <?php endif ?>
 <label><tt>Body: (you may use Markdown →)</tt><br/>
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