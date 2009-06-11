<?php
class TopicList {
	const PER_PAGE = 30;
	
	const WITH_PAGINATION = 1;
	const WITHOUT_PAGINATION = 2;
	
	protected $topics = array();
	protected $page = 0;
	
	public function __construct($page = 0) {
		$this->page = (int)$page;
		$offset = $this->page * self::PER_PAGE;
		$this->topics = Topics::getList($page, self::PER_PAGE);
		if(!empty($this->topics)) {
			Page::cache($this->topics[0]['last_post']);
		}
	}
	
	protected function renderTopics() {
		$affinity = 0;
		foreach($this->topics as $topic_info) {
			$topic_classes = $this->generateTopicClasses($topic_info);
			echo '<div class="', implode(' ', $topic_classes) , '">',
				'<h3><a href="', Topics::makeURI($topic_info['id'], $topic_info['post']) , '">', $topic_info['title'], '</a></h3>',
				'<ul class="topic-info">',
					'<li>by ', User::author($topic_info['author']), '</li>',
					'<li>', $topic_info['replies'], ' replies</li>',
					'<li>last post ',
						'<a href="', Topics::makeURI($topic_info['id'], $topic_info['last_post_id']), '">',
							Page::formatTime($topic_info['last_post'], $topic_info['last_post_date']),
						'</a> by ', User::author($topic_info['last_post_author']),
					'</li>',
				'</ul>',
			'</div>';
		}
	}
	
	protected function generateTopicClasses(array $topic_info) {
		$classes = array('topic');
		if($topic_info['is_sticky']) {
			$classes[] = 'sticky';
		}
		return $classes;
	}
	
	public function render($pagination) {
		echo '<div id="topiclist">';
			$this->renderTopics();
			if($pagination === self::WITH_PAGINATION) {
				$this->renderPagination();
			}
		echo '</div>';
	}
		
	protected static function makePaginationURI($page) {
		return Page::makeURI(Page::PAGE_INDEX, ($page !== 0 ? array('page' => $page) : array()));
	}
	
	protected function renderPagination() {
		$num_pages = (int)((Topics::count() - 1) / self::PER_PAGE);
		echo '<ul id="pages"><li title="', self::PER_PAGE , ' per page">Pages:</li>';
		if($this->page !== 0 ) {
			echo '<li><a href="', self::makePaginationURI($this->page - 1), '">prev</a></li>';
		}
		for($cur_page = 0; $cur_page <= $num_pages; ++$cur_page) {
			echo '<li>';
			if($cur_page === $this->page) {
				echo $cur_page;
			} else {
				echo '<a href="', self::makePaginationURI($cur_page), '">', $cur_page, '</a>';
			}
			echo '</li>';
		}
		if($this->page !== $num_pages) {
			echo '<li><a href="', self::makePaginationURI($this->page + 1), '">next</a></li>';
		}
		echo '<li id="forum-stats">',
			sprintf('displaying %d of %d topics', count($this->topics), Topics::count()),
		'</li>';
		echo '</ul>';
	}
}