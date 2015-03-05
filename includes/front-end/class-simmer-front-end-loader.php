<?php
/**
 * Set up the front-end
 * 
 * @since 1.2.0
 * 
 * @package Simmer\Front_End
 */

// If this file is called directly, get outa' town.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Simmer_Front_End_Loader {
	
	/**
	 * Get the loader running.
	 * 
	 * @since 1.2.0
	 */
	public function load() {
		
		$this->load_files();
		
		$this->add_filters();
	}
	
	/**
	 * Load the necessary files.
	 * 
	 * @since  1.2.0
	 * @access private
	 */
	private function load_files() {
		
		/**
		 * The all-important template loader.
		 */
		require( plugin_dir_path( __FILE__ ) . 'class-simmer-template-loader.php' );
		
		/**
		 * The HTML classes class.
		 */
		require_once( plugin_dir_path( __FILE__ ) . 'class-simmer-front-end-classes.php' );
		
		/**
		 * The supporting functions.
		 */
		require( plugin_dir_path( __FILE__ ) . 'functions.php' );
		
		/**
		 * The supporting template functions.
		 */
		require( plugin_dir_path( __FILE__ ) . 'template-functions.php' );
	}
	
	/**
	 * Add the necessary filters.
	 * 
	 * @since  1.2.0
	 * @access private
	 */
	private function add_filters() {
		
		/**
		 * Setup the HTML classes.
		 */
		$html_classes = new Simmer_Front_End_Classes();
		add_filter( 'body_class', array( $html_classes, 'add_body_classes' ), 20, 1 );
		add_filter( 'post_class', array( $html_classes, 'add_recipe_classes' ), 20, 3 );
		
	}
}
