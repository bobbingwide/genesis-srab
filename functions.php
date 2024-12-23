<?php
/**
 * Genesis Solent Roofing and Building.
 *
 * This file adds functions to the Genesis Solent Roofing and Building Theme.
 *
 * @package Genesis Solent Roofing and Building
 * @author  BobbingWide
 * @license GPL-2.0-or-later
 * @link    https://www.studiopress.com/
 */


add_action( 'after_setup_theme', 'genesis_srab_after_setup_theme');
function genesis_srab_after_setup_theme() {
// Starts the engine.
	require_once get_template_directory() . '/lib/init.php';

// Sets up the Theme.
	require_once get_stylesheet_directory() . '/lib/theme-defaults.php';

	add_action( 'after_setup_theme', 'genesis_srab_localization_setup' );
	/**
	 * Sets localization (do not remove).
	 *
	 * @since 1.0.0
	 */
	function genesis_srab_localization_setup() {

		//load_child_theme_textdomain( genesis_get_theme_handle(), get_stylesheet_directory() . '/languages' );

	}

// Adds helper functions.
	require_once get_stylesheet_directory() . '/lib/helper-functions.php';

// Adds image upload and color select to Customizer.
	require_once get_stylesheet_directory() . '/lib/customize.php';

// Includes Customizer CSS.
	require_once get_stylesheet_directory() . '/lib/output.php';

// Adds WooCommerce support.
//require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-setup.php';

// Adds the required WooCommerce styles and Customizer CSS.
//require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-output.php';

// Adds the Genesis Connect WooCommerce notice.
//require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-notice.php';

	//add_action( 'after_setup_theme', 'genesis_child_gutenberg_support' );
	/**
	 * Adds Gutenberg opt-in features and styling.
	 *
	 * @since 2.7.0
	 */
	//function genesis_child_gutenberg_support() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- using same in all child themes to allow action to be unhooked.
		require_once get_stylesheet_directory() . '/lib/gutenberg/init.php';
	//}

// Registers the responsive menus.
	if ( function_exists( 'genesis_register_responsive_menus' ) ) {
		genesis_register_responsive_menus( genesis_get_config( 'responsive-menus' ) );
	}

	add_action( 'wp_enqueue_scripts', 'genesis_srab_enqueue_scripts_styles' );
	/**
	 * Enqueues scripts and styles.
	 *
	 * @since 1.0.0
	 */
	function genesis_srab_enqueue_scripts_styles() {

		$appearance=genesis_get_config( 'appearance' );

		wp_enqueue_style(
			genesis_get_theme_handle() . '-fonts',
			$appearance['fonts-url'],
			array(),
			genesis_get_theme_version()
		);

		wp_enqueue_style( 'dashicons' );

		if ( genesis_is_amp() ) {
			wp_enqueue_style(
				genesis_get_theme_handle() . '-amp',
				get_stylesheet_directory_uri() . '/lib/amp/amp.css',
				array( genesis_get_theme_handle() ),
				genesis_get_theme_version()
			);
		}

	}

	add_action( 'after_setup_theme', 'genesis_srab_theme_support', 9 );
	add_action( 'after_setup_theme', 'genesis_srab_oik_clone_support' );
	/**
	 * Add desired theme supports.
	 *
	 * See config file at `config/theme-supports.php`.
	 *
	 * @since 3.0.0
	 */
	function genesis_srab_theme_support() {

		$theme_supports=genesis_get_config( 'theme-supports' );

		foreach ( $theme_supports as $feature=>$args ) {
			add_theme_support( $feature, $args );
		}
	}

	function genesis_srab_oik_clone_support() {
		$feature='clone';
		add_post_type_support( 'oik_testimonials', $feature );
		add_post_type_support( 'post', $feature );
		add_post_type_support( 'page', $feature );
		add_post_type_support( 'attachment', $feature );
	}

	add_filter( 'genesis_seo_title', 'genesis_srab_header_title', 10, 3 );
	/**
	 * Removes the link from the hidden site title if a custom logo is in use.
	 *
	 * Without this filter, the site title is hidden with CSS when a custom logo
	 * is in use, but the link it contains is still accessible by keyboard.
	 *
	 * @param string $title The full title.
	 * @param string $inside The content inside the title element.
	 * @param string $wrap The wrapping element name, such as h1.
	 *
	 * @return string The site title with anchor removed if a custom logo is active.
	 * @since 1.2.0
	 *
	 */
	function genesis_srab_header_title( $title, $inside, $wrap ) {

		if ( has_custom_logo() ) {
			$inside=get_bloginfo( 'name' );
		}

		return sprintf( '<%1$s class="site-title">%2$s</%1$s>', $wrap, $inside );

	}

// Adds image sizes.
	add_image_size( 'sidebar-featured', 75, 75, true );

// Removes header right widget area.
	unregister_sidebar( 'header-right' );

// Removes secondary sidebar.
	unregister_sidebar( 'sidebar-alt' );

// Removes site layouts.
	genesis_unregister_layout( 'content-sidebar-sidebar' );
	genesis_unregister_layout( 'sidebar-content-sidebar' );
	genesis_unregister_layout( 'sidebar-sidebar-content' );

	add_filter( 'genesis_customizer_theme_settings_config', 'genesis_srab_remove_customizer_settings' );
	/**
	 * Removes output of header and front page breadcrumb settings in the Customizer.
	 *
	 * @param array $config Original Customizer items.
	 *
	 * @return array Filtered Customizer items.
	 * @since 2.6.0
	 *
	 */
	function genesis_srab_remove_customizer_settings( $config ) {

		unset( $config['genesis']['sections']['genesis_header'] );
		unset( $config['genesis']['sections']['genesis_breadcrumbs']['controls']['breadcrumb_front_page'] );

		return $config;

	}

// Displays custom logo.
	add_action( 'genesis_site_title', 'the_custom_logo', 0 );

// Repositions primary navigation menu.
	remove_action( 'genesis_after_header', 'genesis_do_nav' );
	add_action( 'genesis_header', 'genesis_do_nav', 12 );

// Repositions the secondary navigation menu.
	remove_action( 'genesis_after_header', 'genesis_do_subnav' );
	add_action( 'genesis_footer', 'genesis_do_subnav', 10 );

	add_filter( 'wp_nav_menu_args', 'genesis_srab_secondary_menu_args' );
	/**
	 * Reduces secondary navigation menu to one level depth.
	 *
	 * @param array $args Original menu options.
	 *
	 * @return array Menu options with depth set to 1.
	 * @since 2.2.3
	 *
	 */
	function genesis_srab_secondary_menu_args( $args ) {

		if ( 'secondary' !== $args['theme_location'] ) {
			return $args;
		}

		$args['depth']=1;

		return $args;

	}

	add_filter( 'genesis_author_box_gravatar_size', 'genesis_srab_author_box_gravatar' );
	/**
	 * Modifies size of the Gravatar in the author box.
	 *
	 * @param int $size Original icon size.
	 *
	 * @return int Modified icon size.
	 * @since 2.2.3
	 *
	 */
	function genesis_srab_author_box_gravatar( $size ) {

		return 90;

	}

	add_filter( 'genesis_comment_list_args', 'genesis_srab_comments_gravatar' );
	/**
	 * Modifies size of the Gravatar in the entry comments.
	 *
	 * @param array $args Gravatar settings.
	 *
	 * @return array Gravatar settings with modified size.
	 * @since 2.2.3
	 *
	 */
	function genesis_srab_comments_gravatar( $args ) {

		$args['avatar_size']=60;

		return $args;

	}

	add_filter( 'genesis_pre_get_option_footer_text', "genesis_srab_footer_creds_text" );

	/**
	 * Display footer credits for the genesis-hm theme
	 */
	function genesis_srab_footer_creds_text( $text ) {
		do_action( "oik_add_shortcodes" );
		$text="[bw_wpadmin]";
		$text.='<br />';
		$text.="[bw_copyright]";
		$text.='<hr />';
		//$text .= 'Website designed and developed by [bw_link text="Herb Miller" herbmiller.me] of';
		//$text .= ' <a href="//www.bobbingwide.com" title="Bobbing Wide - web design, web development">[bw]</a>';
		//$text .= '<br />';
		//$text .= '[bw_power]';
		return ( $text );
	}

}