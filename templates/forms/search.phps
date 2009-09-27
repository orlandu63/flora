<form action="<?php echo Page::makeURI(Page::PAGE_SEARCH) ?>" method="post">
<fieldset class="float-wrap">
<legend>Search</legend>
 <div class="float-left">
  <label><tt>Query:</tt> <?php printf(Page::$input_format,
	Topics::MAX_TITLE_LENGTH,
	InputValidation::filter_input(INPUT_POST, 'query', FILTER_SANITIZE_SPECIAL_CHARS),
	'query') ?></label>
  <input type="submit" name="submit" value="Search"/>
 </div>
 <div class="float-right">
  Query omits words &lt; <?php echo InputValidation::SEARCH_MIN_WORD_LENGTH ?> characters and common words
 </div>
</fieldset>
</form>