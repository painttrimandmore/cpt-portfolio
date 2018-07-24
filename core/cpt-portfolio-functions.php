<?php
/**
 * Provides helper functions.
 *
 * @since	  1.0.0
 *
 * @package	CPT_Portfolio
 * @subpackage CPT_Portfolio/core
 */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Returns the main plugin object
 *
 * @since		1.0.0
 *
 * @return		CPT_Portfolio
 */
function CPTPORTFOLIO() {
	return CPT_Portfolio::instance();
}