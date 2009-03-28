<?php
class TopicList {
	protected $topics = array();
	protected $page = 0;
	const PER_PAGE = 100;

	public function __construct($page = 0) {
		global $DB;
		$this->page = $page;
		$offset = $this->page * self::PER_PAGE;
		$this->topics = Topics::getList($page, self::PER_PAGE);
		if(count($this->topics)) {
			Page::cache($this->topics[0]['last_post']);
		}
	}
	
	public function render() {
		echo '<table class="topiclist"><thead><tr>',
				'<th>Title</th><th>Replies</th><th>Author</th><th>Last Post</th>',
			'</tr></thead><tbody>';
		$affinity = 0;
		foreach($this->topics as $topic) {
			$classes = array();
			$classes[] = (++$affinity & 1 ? 'odd' : 'even');
			if($topic['is_sticky']) {
				$classes[] = 'sticky';
			}
			echo '<tr class="', implode(' ', $classes),'">',
				'<td><a href="', Topics::makeURI($topic['id']), '">', $topic['title'], '</a></td>',
				'<td>', $topic['replies'], '</td>',
				'<td>', User::author($topic['author']), '</td>',
				'<td>',
					'<a href="', Topics::makeURI($topic['id'], $topic['last_post_id']), '">',
						Page::formatTime($topic['last_post']),
					'</a> by ', User::author($topic['last_post_author']), 
				'</td>',
				'</tr>';
		}
		echo '</tbody></table>';
		if(Topics::getTotal() > self::PER_PAGE) {
			$this->renderPagination();
		}
	}
	
	public function renderPagination() {
		$num_pages = (int)((Topics::getTotal() - 1) / self::PER_PAGE);
		echo '<ul id="pages"><li title="', self::PER_PAGE , ' per page">Pages:</li>';
		for($cur_page = 0; $cur_page <= $num_pages; ++$cur_page) {
			echo '<li>[<a href="', Page::makeURI(Page::PAGE_INDEX, array('page' => $cur_page)), '">', $cur_page, '</a>]</li>';
		}
		echo '</ul>';
	}
}