<?php
class TopicList {
	const PER_PAGE = 30;
	
	const WITH_PAGINATION = 1;
	const WITHOUT_PAGINATION = 2;
	
	protected $topics = array();
	protected $page = 0;
	
	public function __construct($page = 0) {
		global $DB;
		$this->page = (int)$page;
		$offset = $this->page * self::PER_PAGE;
		$this->topics = Topics::getList($page, self::PER_PAGE);
		if(!empty($this->topics)) {
			Page::cache($this->topics[0]['last_post']);
		}
	}
	
	protected function renderTopics() {
		$affinity = 0;
		foreach($this->topics as $topic) {
			$classes = array();
			#$classes[] = (++$affinity & 1 ? 'odd' : 'even');
			if($topic['is_sticky']) {
				$classes[] = 'sticky';
			}
			echo '<div class="', implode(' ', $classes) , '">',
				'<h3><a href="', Topics::makeURI($topic['id']) , '">', $topic['title'], '</a></h3>',
				'<ul class="topic-info">',
					'<li>by ', User::author($topic['author']), '</li>',
					'<li>', $topic['replies'], ' replies</li>',
					'<li>last post ',
						'<a href="', Topics::makeURI($topic['id'], $topic['last_post_id']), '">',
							Page::formatTime($topic['last_post']),
						'</a> by ', User::author($topic['last_post_author']),
					'</li>',
				'</ul>',
			'</div><hr/>';
		}
	}
	
	public function render($pagination) {
		echo '<div class="topicslist">';
			$this->renderTopics();
			if($pagination === self::WITH_PAGINATION) {
				$this->renderPagination();
			}
		echo '</div>';
	}
		
	protected static function makePaginationUri($page) {
		return Page::makeURI(Page::PAGE_INDEX, array('page' => $page));
	}
	
	protected function renderPagination() {
		$num_pages = (int)((Topics::count() - 1) / self::PER_PAGE);
		echo '<ul id="pages"><li title="', self::PER_PAGE , ' per page">Pages:</li>';
		if($this->page !== 0 ) {
			echo '<li><a href="', self::makePaginationUri($this->page - 1), '">prev</a></li>';
		}
		for($cur_page = 0; $cur_page <= $num_pages; ++$cur_page) {
			echo '<li>';
			if($cur_page === $this->page) {
				echo $cur_page;
			} else {
				echo '<a href="', self::makePaginationUri($cur_page), '">', $cur_page, '</a>';
			}
			echo '</li>';
		}
		if($this->page !== $num_pages) {
			echo '<li><a href="', self::makePaginationUri($this->page + 1), '">next</a></li>';
		}
		echo '</ul>';
	}
}