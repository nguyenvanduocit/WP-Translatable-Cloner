<?php
/**
 * Summary
 *
 * Description.
 *
 * @since 0.9.0
 *
 * @package
 * @subpackage 
 *
 * @author nguyenvanduocit
 */

namespace AdvancedCloner;
class Schedule {

	public function __construct(){
		add_action('ac_clone_new_post', array($this, 'doClone'));
	}

	public function doClone(){
		$postFinder = PostFinder::getInstance();
		$lastPost = $postFinder->getRandomPost();
		$url            = $lastPost->href;
		$advancedCloner = new PostCloner( $url, TRUE );
		$post           = $advancedCloner->getPost();
		if(!$this->is_post_exist($post->title))
		{
			$postArr = array(
				'post_title'    => $post->title,
				'post_content'  => $post->content,
				'post_status'   => 'publish',
				'post_author'   => 1
			);
			$postId = wp_insert_post($postArr);
			require_once(ABSPATH . 'wp-admin/includes/media.php');
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			media_sideload_image($post->thumbnail, $postId);
		}
	}

	protected function is_post_exist($title){
		global $wpdb;
		$slug = sanitize_title($title);
		$check_sql = "SELECT post_name FROM $wpdb->posts WHERE post_name = %s LIMIT 1";
		$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $slug));
		return $post_name_check;
	}
}