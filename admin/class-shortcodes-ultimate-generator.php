<?php

/**
 * The class responsible for shortcode generator.
 *
 * @since        5.1.0
 * @package      Shortcodes_Ultimate
 * @subpackage   Shortcodes_Ultimate/admin
 */
final class Shortcodes_Ultimate_Generator {

	/**
	 * The path of the main plugin file.
	 *
	 * @since    5.1.0
	 * @access   private
	 * @var      string    $plugin_file    The path of the main plugin file.
	 */
	private $plugin_file;

	/**
	 * The current version of the plugin.
	 *
	 * @since    5.1.0
	 * @access   private
	 * @var      string    $plugin_version    The current version of the plugin.
	 */
	private $plugin_version;

	/**
	 * The path to the plugin folder.
	 *
	 * @since    5.1.0
	 * @access   private
	 * @var      string      $plugin_path   The path to the plugin folder.
	 */
	private $plugin_path;

	/**
	 * The URL of the plugin folder.
	 *
	 * @since    5.1.0
	 * @access   private
	 * @var      string    $plugin_url    The URL of the plugin folder.
	 */
	private $plugin_url;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  5.1.0
	 * @param string  $plugin_file    The path of the main plugin file.
	 * @param string  $plugin_version The current version of the plugin.
	 */
	public function __construct( $plugin_file, $plugin_version ) {

		$this->plugin_file    = $plugin_file;
		$this->plugin_version = $plugin_version;
		$this->plugin_path    = plugin_dir_path( $plugin_file );
		$this->plugin_url     = plugin_dir_url( $plugin_file );

	}

	/**
	 * Enqueue required assets.
	 *
	 * @since  5.1.0
	 */
	public function enqueue_scripts( $hook ) {

		// TODO: add actions to default admin pages (1)
		if ( ! did_action( 'su/generator/load_assets' ) ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_style(
			'shortcodes-ultimate-generator',
			$this->plugin_url . 'admin/css/generator.css',
			array(),
			$this->plugin_version
		);

		wp_enqueue_script(
			'shortcodes-ultimate-generator',
			$this->plugin_url . 'admin/js/generator.js',
			array( 'jquery' ),
			$this->plugin_version,
			true
		);

		wp_localize_script(
			'shortcodes-ultimate-generator',
			'ShortcodesUltimateGenerator',
			array()
		);

		do_action( 'su/generator/enqueue_scripts' );

	}

	public function load_assets( $hook ) {

		return true;

		// if ( 'post.php' )

	}

	/**
	 * Display Insert Shortcode button.
	 *
	 * @since 5.1.0
	 */
	public function insert_shortcode_button( $editor_id ) {

		$display_button = apply_filters( 'su/generator/display_button', true, $editor_id );

		if ( ! $display_button ) {
			return;
		}

		$this->the_template( 'admin/partials/generator/insert-shortcode-button', array( 'editor_id' => $editor_id ) );

		do_action( 'su/generator/button', $editor_id );

	}

	/**
	 * Utility function to get specified template by it's name.
	 *
	 * @since 5.1.0
	 * @param string  $name Template name without extension.
	 * @param mixed   $data Data to be available from within template.
	 * @return string       Template content. Returns empty string if template name is invalid or template file wasn't found.
	 */
	public function get_template( $name = '', $data = array() ) {

		// Validate template name
		if ( preg_match( "/^(?!-)[a-z0-9-_]+(?<!-)(\/(?!-)[a-z0-9-_]+(?<!-))*$/", $name ) !== 1 ) {
			return '';
		}

		// The full path to template file
		$file = $this->plugin_path . $name . '.php';

		// Look for a specified file
		if ( file_exists( $file ) ) {

			ob_start();
			include $file;
			$template = ob_get_contents();
			ob_end_clean();

		}

		return isset( $template ) ? $template : '';

	}


	/**
	 * Utility function to display specified template by it's name.
	 *
	 * @since 5.1.0
	 * @param string  $name Template name (without extension).
	 * @param mixed   $data Template data to be passed to the template.
	 */
	public function the_template( $name, $data = null ) {
		echo $this->get_template( $name, $data );
	}

}
