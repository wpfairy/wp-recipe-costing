<?php


include_once('WpfRecipeCosting_LifeCycle.php');

class WpfRecipeCosting_Plugin extends WpfRecipeCosting_LifeCycle {

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31
        return array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            'wpfrc_Name' => array(__('Recipe Name', 'wpf-recipe-costing')),
            'wpfrc_Description' => array(__('Description', 'wpf-recipe-costing'), 'false', 'true'),
            'wpfrc_' => array(__('Which user role can do something', 'wpf-recipe-costing'),
                                        'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber', 'Anyone') 
        );
    }

//    protected function getOptionValueI18nString($optionValue) {
//        $i18nValue = parent::getOptionValueI18nString($optionValue);
//        return $i18nValue;
//    }

    protected function initOptions() {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr > 1)) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName() {
        return 'WP Recipe Costing';
    }

    protected function getMainPluginFileName() {
        return 'wpf-recipe-costing.php';
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //            `id` INTEGER NOT NULL");
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }
    

    // Register Custom Post Type
    public function register_custom_post_type() {

        $labels = array(
            'name'                  => _x( 'Cost Cards', 'Post Type General Name', 'wpf-recipe-costing' ),
            'singular_name'         => _x( 'Cost Card', 'Post Type Singular Name', 'wpf-recipe-costing' ),
            'menu_name'             => __( 'Cost Cards', 'wpf-recipe-costing' ),
            'name_admin_bar'        => __( 'Cost Card', 'wpf-recipe-costing' ),
            'archives'              => __( 'Item Archives', 'wpf-recipe-costing' ),
            'attributes'            => __( 'Item Attributes', 'wpf-recipe-costing' ),
            'parent_item_colon'     => __( 'Parent Item:', 'wpf-recipe-costing' ),
            'all_items'             => __( 'All Items', 'wpf-recipe-costing' ),
            'add_new_item'          => __( 'Add New Item', 'wpf-recipe-costing' ),
            'add_new'               => __( 'Add New', 'wpf-recipe-costing' ),
            'new_item'              => __( 'New Item', 'wpf-recipe-costing' ),
            'edit_item'             => __( 'Edit Item', 'wpf-recipe-costing' ),
            'update_item'           => __( 'Update Item', 'wpf-recipe-costing' ),
            'view_item'             => __( 'View Item', 'wpf-recipe-costing' ),
            'view_items'            => __( 'View Items', 'wpf-recipe-costing' ),
            'search_items'          => __( 'Search Item', 'wpf-recipe-costing' ),
            'not_found'             => __( 'Not found', 'wpf-recipe-costing' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'wpf-recipe-costing' ),
            'featured_image'        => __( 'Featured Image', 'wpf-recipe-costing' ),
            'set_featured_image'    => __( 'Set featured image', 'wpf-recipe-costing' ),
            'remove_featured_image' => __( 'Remove featured image', 'wpf-recipe-costing' ),
            'use_featured_image'    => __( 'Use as featured image', 'wpf-recipe-costing' ),
            'insert_into_item'      => __( 'Insert into item', 'wpf-recipe-costing' ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', 'wpf-recipe-costing' ),
            'items_list'            => __( 'Items list', 'wpf-recipe-costing' ),
            'items_list_navigation' => __( 'Items list navigation', 'wpf-recipe-costing' ),
            'filter_items_list'     => __( 'Filter items list', 'wpf-recipe-costing' ),
        );
        $args = array(
            'label'                 => __( 'Cost Card', 'wpf-recipe-costing' ),
            'description'           => __( 'Post type for WP Recipe Costing', 'wpf-recipe-costing' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'page-attributes', ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-list-view',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,		
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
            'show_in_rest'          => true,
        );
        register_post_type( 'cost_card', $args );

    }
    
    function enqueueScripts() {
        //wp_enqueue_script('wpf-recipe-costing-script', plugins_url('/assets/js/script.js', __FILE__));
        //wp_enqueue_style('wpf-recipe-costing-style', plugins_url('/assets/css/style.css', __FILE__)); 
        
            
    }

    public function addActionsAndFilters() {

        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action( 'admin_menu', array( &$this, 'addSettingsSubMenuPage' ) );

        // Add custom post_type
        //add_action( 'init', array( &$this, 'register_custom_post_type' ) );

        // Example adding a script & style just for the options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false) {
            add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
        }


        // Add Actions & Filters
        // http://plugin.michael-simpson.com/?page_id=37


        // Adding scripts & styles to all pages
        // Examples:
        //        wp_enqueue_script('jquery');
        //        wp_enqueue_style('wpf-recipe-costing-style', plugins_url('/assets/css/style.css', __FILE__));
        //        wp_enqueue_script('wpf-recipe-costing-script', plugins_url('/assets/js/script.js', __FILE__));
        // enqueuing Bootstrap css and js file
        wp_enqueue_scripts('bootstrap-js');
        wp_register_script('bootstrap-js', 
                    plugins_url('../vendor/bootstrap-4.0.0-alpha.6-dist/js/bootstrap.min.js'), 
                    array ('jquery'), 
                    false, false);
        wp_enqueue_style('bootstrap-css', plugins_url('../vendor/bootstrap-4.0.0-alpha.6-dist/css/bootstrap.min.css', __FILE__)); 


        // enqueuing ajax-tabs.js file
        wp_enqueue_scripts('ajax-tabs');
        wp_register_script('ajax-tabs', 
                    plugins_url('/assets/js/ajax-tabs.js'), 
                    array ('jquery'), 
                    false, false);

        // Register short codes
        // http://plugin.michael-simpson.com/?page_id=39


        // Register AJAX hooks
        // http://plugin.michael-simpson.com/?page_id=41
        add_action('wp_ajax_CONVERTUNITS', array(&$this, 'ajaxCONVERTUNITS'));
        add_action('wp_ajax_nopriv_CONVERTUNITS', array(&$this, 'ajaxCONVERTUNITS')); // optional

    }
    
    public function ajaxCONVERTUNITS() {
        if (!$this->canUserDoRoleOption('administrator')) {
            die(1);
        }
        // Don't let IE cache this request
        header("Pragma: no-cache");
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

        header("Content-type: text/plain");

        echo 'hello world';
        die();
    }

}