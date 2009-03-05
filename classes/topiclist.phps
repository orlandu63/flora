<?php
class TopicList {
	protected $topics = array();
	protected $page = 0;
	const PER_PAGE = 100;

	public function __construct($page = 0) {
		global $DB;
		$this->page = $page;
		$offset = $this->page * self::PER_PAGE;
		$this->topics = $DB->query('SELECT * FROM topics
			ORDER BY is_sticky DESC, last_post_id DESC
			LIMIT ' . $offset . ', ' . self::PER_PAGE)->fetchAll();
		if(count($this->topics)) {
			Page::cache($this->topics[0]['last_post']);
		}
	}
	
	public function render($with_pagination = true) {
		echo '<table class="topiclist"><thead><tr>',
				'<th>Title</th><th>Replies</th><th>Author</th><th>Last Post</th>',
			'</tr></thead><tbody>';
		$affinity = 0;
		foreach($this->topics as $topic) {
			echo '<tr class="', (++$affinity & 1 ? 'odd' : 'even') ,'">',
				'<td>', ($topic['is_sticky'] ? '<span class="sticky-symbol">!!!</span> ' : ''),
					'<a href="', Topic::link($topic['id']), '">', $topic['title'], '</a></td>',
				'<td>', $topic['replies'], '</td>',
				'<td>', ($topic['author'] ? $topic['author'] : User::ANON_NAME), '</td>',
				'<td>',
					'<a href="', Topic::link($topic['id'], $topic['last_post_id']), '">',
						Page::formatTime($topic['last_post']),
					'</a> by ', $topic['last_post_author'], 
				'</td>',
				'</tr>';
		}
		echo '</tbody></table>';
		if($with_pagination) {
			$this->renderPagination();
		}
	}
	
	public function renderPagination() {
		$total = Topic::getTotal();
		if($total < self::PER_PAGE) {
			return;
		}
		$num_pages = (int)(($total - 1) / self::PER_PAGE);
		echo '<ul id="pages"><li title="', self::PER_PAGE , ' per page">Pages:</li>';
		for($cur_page = 0; $cur_page <= $num_pages; ++$cur_page) {
			echo '<li>[<a href="?page=', $cur_page, '">', $cur_page, '</a>]</li>';
		}
		echo '</ul>';
	}
}