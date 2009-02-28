<?php
class ThreadList {
	public $topic_info = array(), $children = array();

	public function __construct($id) {
		global $DB;
		$this->topic_info = Topics::getTopicInfo($id);
		Page::cache($this->topic_info['last_post']);
		$posts = $DB->q('SELECT post_info.id id, parent, author, ip, toc, body
			FROM post_info
				LEFT JOIN post_data ON post_info.id = post_data.id
			WHERE topic = ?', $id)->fetchAll();
		foreach($posts as $post) {
			$this->children[$post['parent']][] = $post;
		}
	}
	
	protected function renderThread($parent) {
		$children = $this->children[$parent];
		foreach($children as $key => $thread) {
			$thread_has_children = isset($this->children[$thread['id']]);
			echo '<div class="post">';
			echo '<ul class="postinfo" id="m', $thread['id'], '">',
				'<li>By ', ($thread['author'] ? $thread['author'] : 'Anon'), ($this->topic_info['ip'] === $thread['ip'] ? ' <span class="tc-indicator">*</span>' : ''), '</li>',
				'<li>', Input::formatTime($thread['toc']), '</li>',
				'<li><a href="post.php?thread=', $thread['id'], '">Reply</a></li>';
				$nav_links = array();
				if($parent !== null) {
					$nav_links[$parent] = '↖';
				}
				if(isset($children[$key+1])) {
					$nav_links[$children[$key+1]['id']] = '↓';
				}
				if(isset($children[$key-1])) {
					$nav_links[$children[$key-1]['id']] = '↑';
				}
				if($thread_has_children) {
					$nav_links[$this->children[$thread['id']][0]['id']] = '↘<small><sup>1</sup></small>';
				}
				$nav_links[$thread['id']] = '#' . $thread['id'];
				foreach($nav_links as $message_id => $text) {
					echo '<li class="nav"><a href="', Topics::link($this->topic_info['id'], $message_id), '">', $text, '</a></li>';
				}
				echo '</ul>',
				$thread['body'];
			if($thread_has_children) {
				echo '<div class="reply-wrap">';
					$this->renderThread($thread['id']);
				echo '</div>';
			}
			echo '</div>';
		}
	}

	public function render() {
		echo '<h1>', $this->topic_info['title'], '</h1>';
		$this->renderThread(null);
	}
}
