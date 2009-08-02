<form action="<?php echo Page::makeURI(Page::PAGE_SEARCH) ?>" method="post">
 <label>Query (min 4 chars, will lower it later): <?php printf(Page::$input_format, Topics::MAX_TITLE_LENGTH, $query, 'query') ?></label>
 <input type="submit" name="submit" value="Search"/>
</form>