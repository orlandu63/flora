<?php
class ThreadList {
	public $topic = array(), $children = array();

	public function __construct($id) {
		global $DB;
		$this->topic = Topics::getInfo($id);
		Page::cache($this->topic['last_post']);
		$posts = Posts::getOfTopic($id);
		foreach($posts as $post) {
			$this->children[$post['parent']][] = $post;
		}
	}
	
	protected function renderThread($parent = null) {
		$children = $this->children[$parent];
		foreach($children as $key => $post) {
			$post_has_children = isset($this->children[$post['id']]);
			echo '<div class="post">',
				'<ul class="post-info" id="m', $post['id'], '">',
				'<li>By ',
					User::author($post['author']),
					($this->topic['ip'] === $post['ip'] ? ' <span class="tc-indicator">*</span>' : ''),
				'</li>',
				'<li>', Page::formatTime($post['toc']), '</li>',
				'<li><a href="', Page::PAGE_POST, '?post=', $post['id'], '">Reply</a></li>';
				$nav_links = array();
				if($parent !== null) {
					$nav_links[$parent] = '↖';
				}
				if(isset($children[$key-1])) {
					$nav_links[$children[$key-1]['id']] = '↑';
				}
				if(isset($children[$key+1])) {
					$nav_links[$children[$key+1]['id']] = '↓';
				}
				if($post_has_children) {
					$nav_links[$this->children[$post['id']][0]['id']] = '↘<small><sup>1</sup></small>';
				}
				$nav_links[$post['id']] = '#' . $post['id'];
				foreach($nav_links as $message_id => $text) {
					echo '<li class="nav"><a href="', Topics::link($this->topic['id'], $message_id), '">', $text, '</a></li>';
				}
				echo '</ul>',
				'<div class="post-body">', $post['body'], '</div>';
			if($post_has_children) {
				echo '<div class="reply-wrap">';
					$this->renderThread($post['id']);
				echo '</div>';
			}
			echo '</div>';
		}
	}
	
	public function render() {
		echo '<h1>', $this->topic['title'], '</h1>';
		$this->renderThread();
	}
}