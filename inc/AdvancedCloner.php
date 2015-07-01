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


use scbOptions;

class AdvancedCloner {
	protected $option;
	protected $schedule;
	public function __construct() {
		if ( is_admin() ) {
			$default    = array(
				'schedule_inteval' => 1
			);
			$args       = array(
				'hide_empty' => FALSE
			);
			$categories = get_categories( $args );
			foreach ( $categories as $category ) {
				$default[ 'cat_' . $category->term_id ] = '';
			}
			$this->option = new scbOptions( 'ac_options', AC_FILE, $default );  // OK
			new AdminPage( AC_FILE, $this->option );
		}
		$this->initCron();
	}

	public function initCron(){
		$args = array(
			'action'=>'ac_clone_new_post',
			'schedule'=>10*MINUTE_IN_SECONDS,
			'interval'=>10*MINUTE_IN_SECONDS
		);
		$this->schedule = new \scbCron(AC_FILE, $args);
		new Schedule();
		$this->schedule->do_now();
	}
}