<?php
class TopicList {
	protected $topics = array();
	protected $page = 0;
	const PER_PAGE = 100;

	public function __construct($page = 0) {
		global $DB;
		$this->page = $page;
		$offset = $this->page * self::PER_PAGE;
		$this->topics = $DB->query('SELECT topic_info.id id, title, is_sticky, last_post, author, replies
			FROM topic_info
				LEFT JOIN post_info ON topic_info.thread = post_info.id
			ORDER BY is_sticky DESC, last_post DESC
			LIMIT ' . $offset . ', ' . self::PER_PAGE)->fetchAll();
		if(count($this->topics)) {
			Page::cache($this->topics[0]['last_post']);
		}
	}
	
	public function display() {
		$affinity = 0;
		echo '<table class="topiclist"><thead><tr><th>Title</th><th>Replies</th><th>Author</th><th>Last Post</th></tr></thead><tbody>';
		foreach($this->topics as $topic) {
			echo '<tr class="', (++$affinity & 1 ? 'odd' : 'even') ,'">',
				'<td>', ($topic['is_sticky'] ? '<span class="sticky-symbol">!!!</span> ' : ''), '<a href="topic.php?id=', $topic['id'], '">', $topic['title'], '</a></td>',
				'<td>', $topic['replies'], '</td>',
				'<td>', ($topic['author'] ? $topic['author'] : 'Anon'), '</td>',
				'<td>', Input::formatTime($topic['last_post']), '</td>',
				'</tr>';
		}
		echo '</tbody></table>';
	}
	
	public function pagination() {
		$total = Topics::getTotal();
		$num_pages = (int)(($total - 1) / self::PER_PAGE);
		echo '<ul id="pages"><li title="', self::PER_PAGE , ' per page">Pages:</li>';
		for($cur_page = 0; $cur_page <= $num_pages; ++$cur_page) {
			echo '<li>[<a href="?page=', $cur_page, '">', $cur_page, '</a>]</li>';
		}
		echo '</ul>';
	}
}