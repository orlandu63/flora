<?php
//i just realiezd that it's not really a thread list, more like a thread thread.. but am i that
// pedantic to change it?
class ThreadList {
	public $topic = array(), $children = array();
	protected $max_id_length;

	public function __construct($id) {
		$this->topic = Topics::getInfo($id);
		Page::HTTPCache($this->topic['last_post']);
		$posts = Posts::getOfTopic($id);
		$this->max_id_length = strlen($this->topic['last_post_id']);
		foreach($posts as $post) {
			$this->children[$post['parent']][] = $post;
		}
	}
	
	public function render() {
		global $Page;
		$Page->load('threadlist', array('Threadlist' => $this));
	}
	
	public function renderThread($parent) {
		static $sibling_stack = array();
		$children_of_parent = $this->children[$parent];
		foreach($children_of_parent as $position => $post_info) {
			$post_has_children = !empty($this->children[$post_info['id']]);
			$post_classes = Posts::generatePostClasses($post_info);
			$user_classes = $this->generateUserClasses($post_info);
			echo '<div class="', implode(' ', $post_classes), '" id="', Posts::htmlId($post_info['id']), '">',
				'<div class="post-info-wrap float-wrap">', '<ul class="post-info float-left inline-list">',
				'<li>by ', User::display($post_info['author'], $post_info['user_id'], $user_classes), '</li>',
				'<li>', Page::formatTime($post_info['toc']), '</li>',
				'<li>',
					sprintf('<a href="%s" title="reply to post">reply</a>',
						Page::makeURI(Page::PAGE_POST, array('post' => $post_info['id']))
					),
				'</li>',
				'</ul><ul class="nav float-right inline-list">';
				$nav_links = $this->generateNavLinks($post_info, $position);
				foreach($nav_links as $message_id => $info) {
					list($text, $title) = $info;
					printf('<li><a href="%s" title="go to %s post">%s</a></li>',
						Topics::makeURI($this->topic['id'], $message_id), $title, $text
					);
				}
				echo '</ul></div>',
				'<div class="post-body">', $post_info['body'], '</div>';
			if($post_has_children) {
				echo '<div class="replies">';
					$this->renderThread($post_info['id']);
				echo '</div>';
			}
			echo '</div>';
		}
	}
	
	protected function generateUserClasses(array $post_info) {
		$user_classes = array();
		if($this->topic['user_id'] === $post_info['user_id']) {
			$user_classes[] = 'tc';
		}
		return $user_classes;
	}
	
	protected function generateNavLinks(array $post_info, $position) {
		static $sibling_stack = array();
		$siblings = $this->children[$post_info['parent']];
		$post_has_children = !empty($this->children[$post_info['id']]);
		$nav_links = array();
		if($post_info['parent'] !== null) {
			$nav_links[$post_info['parent']] = array('↖', 'parent');
		}
		if(isset($siblings[$position-1])) {
			$nav_links[$siblings[$position-1]['id']] = array('↑', 'preceding');
		}
		if(isset($siblings[$position+1])) {
			$nav_links[$siblings[$position+1]['id']] = array('↓', 'proceeding');
			if($post_has_children) {
				$sibling_stack[] = $siblings[$position+1]['id'];
			}
		} elseif(!$post_has_children && !empty($sibling_stack)) {
			$next_logical_post = array_pop($sibling_stack);
			$nav_links[$next_logical_post] = array('↙', 'next logical');
		}
		if($post_has_children) {
			$first_child = $this->children[$post_info['id']][0]['id'];
			$nav_links[$first_child] = array('↘¹', 'first reply of');
		}
		$nav_links[$post_info['id']] = array(
			'#' . str_pad($post_info['id'], $this->max_id_length, '0', STR_PAD_LEFT), 'this'
		);
		return $nav_links;
	}
}