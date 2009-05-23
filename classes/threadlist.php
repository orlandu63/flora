<?php
class ThreadList {
	public $topic = array(), $children = array();
	protected $max_id_length;

	public function __construct($id) {
		global $DB;
		$this->topic = Topics::getInfo($id);
		Page::cache($this->topic['last_post']);
		$posts = Posts::getOfTopic($id);
		$this->max_id_length = strlen($this->topic['last_post_id']);
		foreach($posts as $post) {
			$this->children[$post['parent']][] = $post;
		}
	}
	
	protected function renderThread($parent) {
		static $sibling_stack = array();
		$children_of_parent = $this->children[$parent];
		foreach($children_of_parent as $key => $post) {
			$post_has_children = !empty($this->children[$post['id']]);
			$user_classes = $this->generateUserClasses($post);
			echo '<div class="post" id="', Posts::htmlId($post['id']), '">',
				'<div class="post-info-wrap">', '<ul class="post-info">',
				'<li>by ',
					User::author($post['author'], $user_classes),
				'</li>',
				'<li>', Page::formatTime($post['toc']), '</li>',
				'<li>',
					sprintf('<a href="%s" title="reply to post">reply</a>',
						Page::makeURI(Page::PAGE_POST, array('post' => $post['id']))),
				'</li></ul><ul class="nav">';
				$nav_links = $this->generateNavLinks($post, $key);
				foreach($nav_links as $message_id => $info) {
					list($text, $title) = $info;
					echo '<li>',
						sprintf('<a href="%s" title="go to %s post">%s</a>',
							Topics::makeURI($this->topic['id'], $message_id), $title, $text),
					'</li>';
				}
				echo '</ul></div>',
				'<div class="post-body">', $post['body'], '</div>';
			if($post_has_children) {
				echo '<div class="reply-wrap">';
					$this->renderThread($post['id']);
				echo '</div>';
			}
			echo '</div>';
		}
	}
	
	protected function generateUserClasses(array $post) {
		$user_classes = array();
		if($this->topic['ip'] === $post['ip']) {
			$user_classes[] = 'tc';
		}
		return $user_classes;
	}
	
	protected function generateNavLinks(array $post, $key) {
		static $sibling_stack = array();
		$parent = $post['parent'];
		$children_of_parent = $this->children[$parent];
		$post_has_children = !empty($this->children[$post['id']]);
		$nav_links = array();
		if($parent !== null) {
			$nav_links[$parent] = array('↖', 'parent');
		}
		if(isset($children_of_parent[$key-1])) {
			$nav_links[$children_of_parent[$key-1]['id']] = array('↑', 'preceding');
		}
		if(isset($children_of_parent[$key+1])) {
			$nav_links[$children_of_parent[$key+1]['id']] = array('↓', 'proceeding');
			if($post_has_children) {
				$sibling_stack[] = $children_of_parent[$key+1]['id'];
			}
		} elseif(!$post_has_children && !empty($sibling_stack)) {
			$next_logical_post = array_pop($sibling_stack);
			$nav_links[$next_logical_post] = array('↙', 'next logical');
		}
		if($post_has_children) {
			$nav_links[$this->children[$post['id']][0]['id']] =
				array('↘¹', 'first reply of');
		}
		$nav_links[$post['id']] = array(
			'#' . str_pad($post['id'], $this->max_id_length, '0', STR_PAD_LEFT),
			'this'
		);
		return $nav_links;
	}
	
	public function render() {
		echo '<div id="threadlist">';
			$this->renderThread(null);
		echo '</div>';
	}
}