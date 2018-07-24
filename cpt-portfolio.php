<?php
/**
 * Plugin Name: CPT Portfolio
 * Plugin URI: https://github.com/painttrimandmore/cpt-portfolio
 * Description: Portfolio Custom Post Type
 * Version: 0.1.0
 * Text Domain: cpt-portfolio
 * Author: Eric Defore
 * Author URI: https://realbigmarketing.com/
 * Contributors: d4mation
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'CPT_Portfolio' ) ) {

	/**
	 * Main CPT_Portfolio class
	 *
	 * @since	  1.0.0
	 */
	class CPT_Portfolio {
		
		/**
		 * @var			array $plugin_data Holds Plugin Header Info
		 * @since		1.0.0
		 */
		public $plugin_data;
		
		/**
		 * @var			array $admin_errors Stores all our Admin Errors to fire at once
		 * @since		1.0.0
		 */
		private $admin_errors;
		
		/**
		 * @var         RBM_CPT_Portfolio Holds our CPT
		 * @since       1.0.0
		 */
		public $cpt;

		/**
		 * Get active instance
		 *
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  object self::$instance The one true CPT_Portfolio
		 */
		public static function instance() {
			
			static $instance = null;
			
			if ( null === $instance ) {
				$instance = new static();
			}
			
			return $instance;

		}
		
		protected function __construct() {
			
			$this->setup_constants();
			$this->load_textdomain();
			
			if ( version_compare( get_bloginfo( 'version' ), '4.4' ) < 0 ) {
				
				$this->admin_errors[] = sprintf( _x( '%s requires v%s of %s or higher to be installed!', 'Outdated Dependency Error', 'cpt-portfolio' ), '<strong>' . $this->plugin_data['Name'] . '</strong>', '4.4', '<a href="' . admin_url( 'update-core.php' ) . '"><strong>WordPress</strong></a>' );
				
				if ( ! has_action( 'admin_notices', array( $this, 'admin_errors' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_errors' ) );
				}
				
				return false;
				
			}
			
			if ( ! class_exists( 'RBM_CPTS' ) ) {
				
				$this->admin_errors[] = sprintf( __( '%s requires %sRBP CPTs%s to be installed!', 'cpt-portfolio' ), '<strong>' . $this->plugin_data['Name'] . '</strong>', '<a href="https://github.com/realbig/rbm-cpts/" target="_blank">', '</a>' );
				
				if ( ! has_action( 'admin_notices', array( $this, 'admin_errors' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_errors' ) );
				}
				
				return false;
				
			}
			
			$this->require_necessities();
			
			// Register our CSS/JS for the whole plugin
			add_action( 'init', array( $this, 'register_scripts' ) );
			
		}

		/**
		 * Setup plugin constants
		 *
		 * @access	  private
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function setup_constants() {
			
			// WP Loads things so weird. I really want this function.
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}
			
			// Only call this once, accessible always
			$this->plugin_data = get_plugin_data( __FILE__ );

			if ( ! defined( 'CPT_Portfolio_VER' ) ) {
				// Plugin version
				define( 'CPT_Portfolio_VER', $this->plugin_data['Version'] );
			}

			if ( ! defined( 'CPT_Portfolio_DIR' ) ) {
				// Plugin path
				define( 'CPT_Portfolio_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'CPT_Portfolio_URL' ) ) {
				// Plugin URL
				define( 'CPT_Portfolio_URL', plugin_dir_url( __FILE__ ) );
			}
			
			if ( ! defined( 'CPT_Portfolio_FILE' ) ) {
				// Plugin File
				define( 'CPT_Portfolio_FILE', __FILE__ );
			}

		}

		/**
		 * Internationalization
		 *
		 * @access	  private 
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function load_textdomain() {

			// Set filter for language directory
			$lang_dir = CPT_Portfolio_DIR . '/languages/';
			$lang_dir = apply_filters( 'cpt_portfolio_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'cpt-portfolio' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'cpt-portfolio', $locale );

			// Setup paths to current locale file
			$mofile_local   = $lang_dir . $mofile;
			$mofile_global  = WP_LANG_DIR . '/cpt-portfolio/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/cpt-portfolio/ folder
				// This way translations can be overridden via the Theme/Child Theme
				load_textdomain( 'cpt-portfolio', $mofile_global );
			}
			else if ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/cpt-portfolio/languages/ folder
				load_textdomain( 'cpt-portfolio', $mofile_local );
			}
			else {
				// Load the default language files
				load_plugin_textdomain( 'cpt-portfolio', false, $lang_dir );
			}

		}
		
		/**
		 * Include different aspects of the Plugin
		 * 
		 * @access	  private
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function require_necessities() {
			
			// CPT functionality
			require_once __DIR__ . '/core/cpt/class-rbm-cpt-portfolio.php';
			$this->cpt = new RBM_CPT_Portfolio();
			
		}
		
		/**
		 * Show admin errors.
		 * 
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  HTML
		 */
		public function admin_errors() {
			?>
			<div class="error">
				<?php foreach ( $this->admin_errors as $notice ) : ?>
					<p>
						<?php echo $notice; ?>
					</p>
				<?php endforeach; ?>
			</div>
			<?php
		}
		
		/**
		 * Register our CSS/JS to use later
		 * 
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  void
		 */
		public function register_scripts() {
			
			wp_register_style(
				'cpt-portfolio',
				CPT_Portfolio_URL . 'assets/css/style.css',
				null,
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CPT_Portfolio_VER
			);
			
			wp_register_script(
				'cpt-portfolio',
				CPT_Portfolio_URL . 'assets/js/script.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CPT_Portfolio_VER,
				true
			);
			
			wp_localize_script( 
				'cpt-portfolio',
				'cPTPortfolio',
				apply_filters( 'cpt_portfolio_localize_script', array() )
			);
			
			wp_register_style(
				'cpt-portfolio-admin',
				CPT_Portfolio_URL . 'assets/css/admin.css',
				null,
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CPT_Portfolio_VER
			);
			
			wp_register_script(
				'cpt-portfolio-admin',
				CPT_Portfolio_URL . 'assets/js/admin.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CPT_Portfolio_VER,
				true
			);
			
			wp_localize_script( 
				'cpt-portfolio-admin',
				'cPTPortfolio',
				apply_filters( 'cpt_portfolio_localize_admin_script', array() )
			);
			
		}
		
	}
	
} // End Class Exists Check

/**
 * The main function responsible for returning the one true CPT_Portfolio
 * instance to functions everywhere
 *
 * @since	  1.0.0
 * @return	  \CPT_Portfolio The one true CPT_Portfolio
 */
add_action( 'plugins_loaded', 'cpt_portfolio_load', 999 );
function cpt_portfolio_load() {

	require_once __DIR__ . '/core/cpt-portfolio-functions.php';
	CPTPORTFOLIO();

}
