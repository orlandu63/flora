<form action="<?php echo Page::makeURI(Page::PAGE_SEARCH) ?>" method="post">
<fieldset class="float-wrap">
<legend>Search</legend>
 <div class="float-left">
  <label>Query: <?php printf(Page::$input_format, Topics::MAX_TITLE_LENGTH, $query, 'query') ?></label>
  <input type="submit" name="submit" value="Search"/>
 </div>
 <div class="float-right">
  Query omits words &lt;= 4 characters and common words
 </div>
</fieldset>
</form>