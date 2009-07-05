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
			Page::cache($this->determineLastPost());
		}
	}
	
	#this is not optimizaed
	protected function determineLastPost() {
		$last_post = 0;
		foreach($this->topics as $topic) {
			$last_post = max($topic['last_post'], $last_post);
			if(!$topic['is_sticky']) {
				break;
			}
		}
		return $last_post;
	}
	
	public function render($pagination) {
		global $Page;
		$Page->load('topiclist', array(
			'topics' => $this->topics
		));
		if($pagination === self::WITH_PAGINATION) {
			$this->renderPagination();
		}
	}
		
	protected static function makePaginationURI($page) {
		return Page::makeURI(Page::PAGE_INDEX, ($page !== 0 ? array('page' => $page) : array()));
	}
	
	protected function renderPagination() {
		$num_pages = (int)((Topics::count() - 1) / self::PER_PAGE);
		echo '<ul id="pages" class="inline-list"><li title="', self::PER_PAGE, ' per page">Pages:</li>';
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
		if($this->page < $num_pages) {
			echo '<li><a href="', self::makePaginationURI($this->page + 1), '">next</a></li>';
		}
		echo '<li id="forum-stats" class="float-right">',
			sprintf('displaying %d of %d topics', count($this->topics), Topics::count()),
		'</li>';
		echo '</ul>';
	}
}