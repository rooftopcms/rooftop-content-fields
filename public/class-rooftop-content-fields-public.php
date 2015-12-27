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
     * Use register_rest_field to add a field to the response, with a
     * value populated by the given callback method (get_callback).
     *
     */
    public function add_fields() {
        $types = get_post_types(array(
            'public' => true
        ));

        foreach($types as $key => $type) {
            register_rest_field( $type,
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

    function add_menu_children_to_menus($menus) {

        $_this = $this;
        $menus = array_map(function($menu_item) use ($_this) {
            $wp_menu_items = wp_get_nav_menu_items($menu_item['id']);
            $rest_menu_items = [];
            foreach( $wp_menu_items as $item_object ) {
                $rest_menu_items[] = $_this->format_menu_item( $item_object );
            }

            $rest_menu_items = $this->nested_menu_items($rest_menu_items, 0);

            $menu_item['items'] = $rest_menu_items;
            return $menu_item;
        }, $menus);

        return $menus;
    }

    function add_menu_children_to_menu($menu_item) {

        $_this = $this;
        $wp_menu_items = wp_get_nav_menu_items($menu_item['id']);
        $rest_menu_items = [];
        foreach( $wp_menu_items as $item_object ) {
            $rest_menu_items[] = $_this->format_menu_item( $item_object );
        }

        $rest_menu_items = $this->nested_menu_items($rest_menu_items, 0);

        $menu_item['items'] = $rest_menu_items;
        return $menu_item;

    }


    /**
     * Format a menu item for REST API consumption.
     *
     * @param   object|array    $menu_item  the menu item
     * @param   bool            $children   get menu item children (default false)
     * @param   array           $menu       the menu the item belongs to (used when $children is set to true)
     *
     * @return  array   a formatted menu item for REST
     */
    private function format_menu_item($menu_item, $children = false, $menu = array()) {
        $item = (array) $menu_item;
        $menu_item = array(
            'id'       => abs( $item['ID'] ),
            'order'    => (int) $item['menu_order'],
            'parent'   => abs( $item['menu_item_parent'] ),
            'title'    => $item['title'],
            'url'      => $item['url'],
            'attr'     => $item['attr_title'],
            'target'   => $item['target'],
            'classes'  => implode( ' ', $item['classes'] ),
            'xfn'      => $item['xfn'],
            'description' => $item['description'],
            'object_id' => abs( $item['object_id'] ),
            'object'   => $item['object'],
            'type'     => $item['type'],
            'type_label' => $item['type_label'],
        );

        if ($item['type'] == 'post_type') {
            $post = get_post($item['object_id']);
            # For posts, get ancestors as a comma-delimited string
            $ancestors = get_ancestors($post->ID, $post->post_type);
            $menu_item['object_ancestor_ids'] = implode(array_reverse($ancestors));

            # Add the object slug
            $menu_item['object_slug'] = $post->post_name;

            # Add the object path if it's nested
            $ancestorCount = count($ancestors);
            if ($ancestorCount > 0) {
                $pathData = explode("/",$item['url']);
                array_pop($pathData);#we pop here because there is a trailing slash which generates a blank element
                $ancestorSlugs = array_slice($pathData, -$ancestorCount-1,-1);
                $menu_item['object_ancestor_slugs'] = implode(",",$ancestorSlugs);
            }

            # Remove the url - it's useless in Rooftop because we don't know the URL structure of the
            # client application
            unset($menu_item['url']);


        }

        if ( $children === true && ! empty( $menu ) ) {
            $menu_item['children'] = $this->get_nav_menu_item_children( $item['ID'], $menu );
        }

        return apply_filters( 'rest_menus_format_menu_item', $menu_item );
    }

    /**
     * Returns all child nav_menu_items under a specific parent.
     *
     * @param   int     $parent_id      the parent nav_menu_item ID
     * @param   array   $nav_menu_items navigation menu items
     * @param   bool    $depth          gives all children or direct children only
     *
     * @return  array   returns filtered array of nav_menu_items
     */
    private function get_nav_menu_item_children( $parent_id, $nav_menu_items, $depth = true ) {
        $nav_menu_item_list = array();

        foreach ( (array) $nav_menu_items as $nav_menu_item ) {
            if ( $nav_menu_item->menu_item_parent == $parent_id ) {
                $nav_menu_item_list[] = $this->format_menu_item( $nav_menu_item, true, $nav_menu_items );
                if ( $depth ) {
                    if ( $children = $this->get_nav_menu_item_children( $nav_menu_item->ID, $nav_menu_items ) ) {
                        $nav_menu_item_list = array_merge( $nav_menu_item_list, $children );
                    }
                }
            }
        }

        return $nav_menu_item_list;
    }

    /**
     *
     * Given a flat array of menu items, split them into parent/child items and
     * recurse over them to return children nexted in their parent
     *
     * @param $menu_items
     * @param null $parent
     * @return array
     */
    private function nested_menu_items( &$menu_items, $parent = null ) {
        $parents = array();
        $children = array();

        // separate menu_items into parents & children
        array_map(function($i) use ( $parent, &$children, &$parents ){
            if($i['id'] != $parent && $i['parent'] == $parent) {
                $parents[] = $i;
            }else {
                $children[] = $i;
            }
        }, $menu_items);

        foreach($parents as &$parent) {
            if($this->has_children( $children, $parent['id'] ) ) {
                $parent['children'] = $this->nested_menu_items( $children, $parent['id'] );
            }
        }

        return $parents;
    }

    /**
     * Does a collection of menu items contain an item that is the parent id of 'id'
     *
     * @param $items
     * @param $id
     * @return array
     */
    private function has_children ( $items, $id ){
        return array_filter($items, function( $i ) use ( $id ) {
            return $i['parent'] == $id;
        });
    }
}
