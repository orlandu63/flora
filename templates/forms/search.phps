<form action="<?php echo Page::makeURI(Page::PAGE_SEARCH) ?>" method="post">
<fieldset class="float-wrap">
<legend>Search</legend>
 <div class="float-left">
  <label>Query: <?php echo $form->input('query', Topics::MAX_TITLE_LENGTH) ?></label>
  <input type="submit" name="submit" value="Search"/>
 </div>
 <div class="float-right">
  Query omits words &lt; <?php echo InputValidation::SEARCH_MIN_WORD_LENGTH ?> characters and common words
 </div>
</fieldset>
</form>