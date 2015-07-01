<?php
/**
 * Summary
 * Description.
 *
 * @since  0.9.0
 * @package
 * @subpackage
 * @author nguyenvanduocit
 */

namespace AdvancedCloner;


use scbAdminPage;

class AdminPage extends scbAdminPage {
	public function setup() {
		$this->args = array(
			'page_title' => 'Advanced Cloner',
		);
	}

	public function page_content() {
		echo html( 'h3', 'Schedule' );
		echo $this->form_table( array(
			array(
				'title' => 'Inteval in Second',
				'type'  => 'text',
				'name'  => 'schedule_inteval',
			)
		) );
		$args       = array(
			'hide_empty' => FALSE
		);
		$categories = get_categories( $args );
		$categoryField = array();
		foreach ( $categories as $category ) {
			$categoryField[ ] = array(
				'title' => $category->name,
				'type'  => 'text',
				'name'  => 'cat_' . $category->term_id,
			);
		}
		echo html( 'h3', 'Cateogry Map' );
		echo $this->form_table( $categoryField );
		$nextScheduleTime = (string)wp_next_scheduled( 'ac_clone_new_post');
		echo html('p', $nextScheduleTime);
	}
}