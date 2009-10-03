<form action="<?php echo Page::makeURI(Page::PAGE_SEARCH) ?>" method="post">
<fieldset class="float-wrap">
<legend>Search</legend>
 <div class="float-left">
  <label>Query: <?php echo $form->input('query', Settings::get('input_thresholds/title/max_length')) ?></label>
  <input type="submit" name="submit" value="Search"/>
 </div>
 <div class="float-right">
  Query omits words &lt; <?php echo Settings::get('search/min_word_length') ?> characters and common words
 </div>
</fieldset>
</form>