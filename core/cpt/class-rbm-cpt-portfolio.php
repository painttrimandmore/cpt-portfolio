<?php

/**
 * Class RBM_CPT_Portfolio
 *
 * Creates the post type.
 *
 * @since 1.0.0
 */
class RBM_CPT_Portfolio extends RBM_CPT {

	public $post_type = 'portfolio';
	public $label_singular = null;
	public $label_plural = null;
	public $labels = array();
	public $icon = 'images-alt';
	public $post_args = array(
		'hierarchical' => false,
		'supports'     => array( 'title', 'author', 'editor', 'thumbnail' ),
		'has_archive'  => true,
		'public'		=> true,
		'rewrite'      => array(
			'slug'       => 'portfolio',
			'with_front' => false,
			'feeds'      => false,
			'pages'      => true
		),
		'capability_type' => 'post',
	);

	/**
	 * RBM_CPT_Portfolio constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		// This allows us to Localize the Labels
		$this->label_singular = __( 'Portfolio Item', 'cpt-portfolio' );
		$this->label_plural   = __( 'Portfolio Items', 'cpt-portfolio' );

		$this->labels = array(
			'menu_name' => __( 'Portfolio', 'cpt-portfolio' ),
			'all_items' => __( 'All Portfolio Items', 'cpt-portfolio' ),
		);

		parent::__construct();
		
		//add_action( 'add_meta_boxes', array( $this, 'add_portfolio_metabox' ) );
		
	}
	
	/**
	 * Create Metaboxes for Portfolio Items
	 * 
	 * @since       1.0.0
	 * @return      void
	 */
	public function add_portfolio_metabox() {

		add_meta_box(
			'portfolio-meta',
			__( 'Portfolio Meta', 'cpt-portfolio' ),
			array( $this, 'metabox_content' ),
			'portfolio',
			'normal'
		);

	}
	
	/**
	 * Portfolio Items Metabox Content
	 * 
	 * @since       1.0.0
	 * @return      void
	 */
	public function metabox_content() {
		
		
		
	}
	
}