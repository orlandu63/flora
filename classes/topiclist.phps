<?php
class TopicList {
	const PER_PAGE = 30;
	
	protected $topics = array();
	
	public function __construct(array $topics) {
		$this->topics = $topics;
		if(!empty($this->topics)) {
			Page::HTTPCache($this->determineLastPost());
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
	
	public function render() {
		global $Page;
		$Page->load('topiclist', array(
			'topics' => $this->topics
		));
	}
	
	protected static function makePaginationURI($page) {
		$query_args = array();
		if($page !== 0) {
			$query_args['page'] = $page;
		}
		return Page::makeURI(Page::PAGE_INDEX, $query_args);
	}
	
	public static function getNumPages($total) {
		return (int)(($total - 1) / self::PER_PAGE);
	}
	
	public function renderPagination($page, $total) {
		$num_pages = self::getNumPages($total);
		$offset = $page * self::PER_PAGE;
		echo '<ul id="pages" class="inline-list"><li title="', self::PER_PAGE, ' per page">Pages:</li>';
		if($page !== 0 ) {
			echo '<li><a href="', self::makePaginationURI($page - 1), '">prev</a></li>';
		}
		for($cur_page = 0; $cur_page <= $num_pages; ++$cur_page) {
			echo '<li>';
			if($cur_page === $page) {
				echo $cur_page;
			} else {
				echo '<a href="', self::makePaginationURI($cur_page), '">', $cur_page, '</a>';
			}
			echo '</li>';
		}
		if($page < $num_pages) {
			echo '<li><a href="', self::makePaginationURI($page + 1), '">next</a></li>';
		}
		echo '<li id="forum-stats" class="float-right">',
			sprintf('displaying %d-%d of %d topics', $offset + 1, $offset + count($this->topics), $total),
		'</li>';
		echo '</ul>';
	}
}