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
		foreach($children_of_parent as $key => $post_info) {
			$post_info_has_children = !empty($this->children[$post_info['id']]);
			$post_classes = Posts::generatePostClasses($post_info);
			$user_classes = $this->generateUserClasses($post_info);
			echo '<div class="', implode(' ', $post_classes), '" id="', Posts::htmlId($post_info['id']), '">',
				'<div class="post-info-wrap float-wrap">', '<ul class="post-info float-left inline-list">',
				'<li>by ',
					User::author($post_info['author'], $user_classes),
				'</li>',
				'<li>', Page::formatTime($post_info['toc']), '</li>',
				'<li>',
					sprintf('<a href="%s" title="reply to post">reply</a>',
						Page::makeURI(Page::PAGE_POST, array('post' => $post_info['id']))
					),
				'</li></ul>', '<ul class="nav float-right inline-list">';
				$nav_links = $this->generateNavLinks($post_info, $key);
				foreach($nav_links as $message_id => $info) {
					list($text, $title) = $info;
					echo '<li>',
						sprintf('<a href="%s" title="go to %s post">%s</a>',
							Topics::makeURI($this->topic['id'], $message_id), $title, $text
						),
					'</li>';
				}
				echo '</ul></div>',
				'<div class="post-body">', $post_info['body'], '</div>';
			if($post_info_has_children) {
				echo '<div class="replies">';
					$this->renderThread($post_info['id']);
				echo '</div>';
			}
			echo '</div>';
		}
	}
	
	protected function generateUserClasses(array $post_info) {
		$user_classes = User::generateUserClasses();
		if($this->topic['ip'] === $post_info['ip']) {
			$user_classes[] = 'tc';
		}
		return $user_classes;
	}
	
	protected function generateNavLinks(array $post_info, $key) {
		static $sibling_stack = array();
		$children_of_parent = $this->children[$post_info['parent']];
		$post_has_children = !empty($this->children[$post_info['id']]);
		$nav_links = array();
		if($post_info['parent'] !== null) {
			$nav_links[$post_info['parent']] = array('↖', 'parent');
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
			$nav_links[$next_logical_post] = array('↙', 'subsequent uncle of');
		}
		if($post_has_children) {
			$nav_links[$this->children[$post_info['id']][0]['id']] = array('↘¹', 'first reply of');
		}
		$nav_links[$post_info['id']] = array(
			'#' . str_pad($post_info['id'], $this->max_id_length, '0', STR_PAD_LEFT), 'this'
		);
		return $nav_links;
	}
	
	public function render() {
		echo '<div id="threadlist">';
			$this->renderThread(null);
		echo '</div>';
	}
}