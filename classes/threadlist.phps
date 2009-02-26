<?php
class ThreadList {
	public $topic_info = array(), $threads = array();

	public function __construct($id) {
		global $DB;
		$this->topic_info = Topics::getTopicInfo($id);
		Page::cache($this->topic_info['last_post']);
		$posts = $DB->q('SELECT post_info.id id, parent, author, ip, toc, body
			FROM post_info
				LEFT JOIN post_data ON post_info.id = post_data.id
			WHERE topic = ?', $id)->fetchAll();
		foreach($posts as $post) {
			$this->threads[$post['parent']][] = $post;
		}
	}
	
	protected function displayThread($parent) {
		$children = $this->threads[$parent];
		foreach($children as $key => $thread) {
			$thread_has_children = isset($this->threads[$thread['id']]);
			echo '<div class="post">';
			echo '<ul class="postinfo" id="m', $thread['id'], '">',
				'<li>By ', ($thread['author'] ? $thread['author'] : 'Anon'), ($this->topic_info['ip'] === $thread['ip'] ? ' <span class="tc-indicator">*</span>' : ''), '</li>',
				'<li>', Input::formatTime($thread['toc']), '</li>',
				'<li><a href="post.php?thread=', $thread['id'], '">Reply</a></li>',
				($parent !== null ? '<li class="nav"><a href="#m' . $parent . '">↖</a></li>' : ''),
				(isset($children[$key+1]) ? '<li class="nav"><a href="#m' . $children[$key+1]['id'] . '">↓</a></li>' : ''),
				(isset($children[$key-1]) ? '<li class="nav"><a href="#m' . $children[$key-1]['id'] . '">↑</a></li>' : ''),
				($thread_has_children ? '<li class="nav"><a href="#m' . $this->threads[$thread['id']][0]['id'] . '">↘<small><sup>1</sup></small></a></li>' : ''),
				'<li><small><a href="#m', $thread['id'], '">#', $thread['id'], '</a></small></li>',
				'</ul>',
				$thread['body'];
			if($thread_has_children) {
				echo '<div class="reply-wrap">';
					$this->displayThread($thread['id']);
				echo '</div>';
			}
			echo '</div>';
		}
	}

	public function display() {
		echo '<h1>', $this->topic_info['title'], '</h1>';
		$this->displayThread(null);
	}
}
