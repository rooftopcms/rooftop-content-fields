<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://errorstudio.co.uk
 * @since      1.0.0
 *
 * @package    Rooftop_Content_Fields
 * @subpackage Rooftop_Content_Fields/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rooftop_Content_Fields
 * @subpackage Rooftop_Content_Fields/public
 * @author     Error <info@errorstudio.co.uk>
 */
class Rooftop_Content_Fields_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rooftop_Content_Fields_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rooftop_Content_Fields_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rooftop-content-fields-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rooftop_Content_Fields_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rooftop_Content_Fields_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rooftop-content-fields-public.js', array( 'jquery' ), $this->version, false );

	}

    /**
     *
     * Called by rest_api_init hook
     * Use register_api_field to add a field to the response, with a
     * value populated by the given callback method (get_callback).
     *
     */
    public function add_fields() {
        $types = get_post_types(array(
            'public' => true
        ));

        foreach($types as $key => $type) {
            register_api_field( $type,
                'taxonomies',
                array(
                    'get_callback'    => array( $this, 'add_taxonomies' ),
                    'update_callback' => null,
                    'schema'          => null,
                )
            );
        }
    }

    /**
     * @param $object
     * @param $field
     * @param $request
     * @return array
     *
     * Add any taxonomies associated with this post to the response
     * Returns an associative array (taxonomy_type => [item, item...])
     */
    function add_taxonomies($object, $field, $request) {
        $taxonomies = get_post_taxonomies($object['id']);
        $terms = array();
        foreach($taxonomies as $taxonomy) {
            $post_terms = get_the_terms($object['id'], $taxonomy);

            if($post_terms){
                $terms[$taxonomy][] = $post_terms;
            }
        }

        return $terms;
    }
}
