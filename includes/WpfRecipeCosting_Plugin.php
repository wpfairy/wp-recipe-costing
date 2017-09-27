<?php
/*
 * @package    Wpf_Recipe_Costing\Includes
*/


include_once('WpfRecipeCosting_LifeCycle.php');

class WpfRecipeCosting_Plugin extends WpfRecipeCosting_LifeCycle {

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        $options = array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            'CompanyName'       => array( 'description' => __( 'Company Name', $this->getPluginTextDomain() ),
                                        'formElement'   => array(
                                            'type'      => 'text',
                                            'value'     => ''
                                             ),
                                         'validationState'  => array(
                                             'has-success'  => 'Company Name saved.',
                                             'has-warning'  => 'You may want to rethink this name.',
                                             'has-danger'   => 'Company Name failed to save.'
                                            )
                                        ),
            'CompanyLogo'       => array( 'description' => __( 'Company Logo', $this->getPluginTextDomain() ),
                                        'formElement'   => array(
                                            'type'      => 'file',
                                            'value'     => ''
                                             )
                                        ),
            'TargetFoodCost'    => array( 'description' => __( 'Target food cost (percentage)', $this->getPluginTextDomain() ),
                                       'formElement'  => array(
                                            'type'      => 'number',
                                            'value'     => '',
                                            'disabled' => false,
                                            'max'      => '100',
                                            'maxlength'=> '',
                                            'min'      => '1',
                                            'pattern'  => '[0-9]',
                                            'readonly' => false,
                                            'required' => false,
                                            'size'     => '',
                                            'step'     => '',
                                            'value'    => '20'
                                            )
                                         ),
            'DeleteAllData'      => array( 'description' => __( 'Delete all WP Recipe Costing data', $this->getPluginTextDomain() ),
                                        'formElement'  => array(
                                            'type'          => 'checkbox',
                                            'subtext'       => 'Defaults to blogname.',
                                            'value'         => 'Delete All Data' ,
                                            'disabled' => false,
                                            'max'      => '1000',
                                            'maxlength'=> '4',
                                            'min'      => '1',
                                            'pattern'  => '5',
                                            'readonly' => false,
                                            'required' => false,
                                            'size'     => '1000',
                                            'step'     => '5'
                                             )
                                         ),
            'WPUR_enable'      => array( 'description' => __( 'Enable WP Ultimate Recipe', $this->getPluginTextDomain() ),
                                         'formElement'  => array(
                                            'type'      => 'radio',
                                            'value'   => array( 'Enable', 'Disable' )
                                             )
                                         ),
            'UserRole'          => array( 'description' => __( 'Minimum user role (for editing settings and data)', $this->getPluginTextDomain() ),
                                         'formElement'  => array(
                                            'type'      => 'select', 
                                            'value'   => array( 'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber', 'Anyone' )
                                             )
                                         )
        );
        return $options;
    }
  
    /**
     * getOptionMetaData()
     * @return array of cost card meta data.
     */
    public function getMetaData() {
        $meta = array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            'CompanyName'       => array( 'description' => __( 'Company Name', $this->getPluginTextDomain() )));
        
    }
    

    protected function getOptionValueI18nString($optionValue) {
        $i18nValue = parent::getOptionValueI18nString($optionValue);
        return $i18nValue;
    }

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

    public function getPluginTextDomain() {
        return 'wpf-recipe-costing';
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
            'add_new_item'          => __( 'Add New Cost Card', 'wpf-recipe-costing' ),
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
            'filter_items_list'     => __( 'Filter items list', 'wpf-recipe-costing' )
        );
        $args = array(
            'label'                 => __( 'Cost Card', 'wpf-recipe-costing' ),
            'description'           => __( 'Cost Card post type for WP Recipe Costing', 'wpf-recipe-costing' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'revisions', 'author', 'custom-fields' ),
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
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'has_archive'           => 'cost-card',
            'query_var'             => true,
            'taxonomies'  => array( 'category', 'post_tag' ),
            'rewrite'               => array(
                    'slug' => 'cost-card', // This controls the base slug that will display before each term
                    'with_front' => false // Don't display the category base before 
                )
        );
        register_post_type( 'cost-card', $args );

        $labels = array(
            'name'                  => _x( 'Recipes', 'Post Type General Name', 'wpf-recipe-costing' ),
            'singular_name'         => _x( 'Recipe', 'Post Type Singular Name', 'wpf-recipe-costing' ),
            'menu_name'             => __( 'Recipes', 'wpf-recipe-costing' ),
            'name_admin_bar'        => __( 'Recipe', 'wpf-recipe-costing' ),
            'archives'              => __( 'Item Archives', 'wpf-recipe-costing' ),
            'attributes'            => __( 'Item Attributes', 'wpf-recipe-costing' ),
            'parent_item_colon'     => __( 'Parent Item:', 'wpf-recipe-costing' ),
            'all_items'             => __( 'All Items', 'wpf-recipe-costing' ),
            'add_new_item'          => __( 'Add New Recipe', 'wpf-recipe-costing' ),
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
            'filter_items_list'     => __( 'Filter items list', 'wpf-recipe-costing' )
        );
        $args = array(
            'label'                 => __( 'Recipe', 'wpf-recipe-costing' ),
            'description'           => __( 'Recipe post type for WP Recipe Costing', 'wpf-recipe-costing' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'excerpt', 'author', 'thumbnail', 'revisions' ),
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
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'has_archive'           => 'wpf-recipe',
            'query_var'             => true,
            'taxonomies'  => array( 'category', 'post_tag' ),
            'rewrite'               => array(
                    'slug' => 'wpf-recipe', // This controls the base slug that will display before each term
                    'with_front' => false // Don't display the category base before 
                )
        );
        register_post_type( 'wpf-recipe', $args );
    }

    public function register_custom_fields() {
        
        if( function_exists('acf_add_local_field_group') ):

            acf_add_local_field_group(array (
                'key' => 'group_59c2e2b57a180',
                'title' => 'Recipe',
                'fields' => array (
                    array (
                        'key' => 'field_59c309f6185bb',
                        'label' => 'Ingredients',
                        'name' => 'ingredients',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array (
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => '',
                        'min' => 0,
                        'max' => 0,
                        'layout' => 'table',
                        'button_label' => '',
                        'sub_fields' => array (
                            array (
                                'key' => 'field_59c30a2b185bd',
                                'label' => 'quantity',
                                'name' => 'quantity',
                                'type' => 'number',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array (
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => 2,
                                'prepend' => '',
                                'append' => '',
                                'min' => '',
                                'max' => '',
                                'step' => '',
                            ),
                            array (
                                'key' => 'field_59c30a38185be',
                                'label' => 'unit',
                                'name' => 'unit',
                                'type' => 'select',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array (
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'choices' => array (
                                    'mm3' => 'mm3',
                                    'cm3' => 'cm3',
                                    'ml' => 'ml',
                                    'l' => 'l',
                                    'kl' => 'kl',
                                    'm3' => 'm3',
                                    'km3' => 'km3',
                                    'tsp' => 'tsp',
                                    'tbsp' => 'tbsp',
                                    'in3' => 'in3',
                                    'fl-oz' => 'fl-oz',
                                    'cup' => 'cup',
                                    'pnt' => 'pnt',
                                    'qt' => 'qt',
                                    'gal' => 'gal',
                                    'ft3' => 'ft3',
                                    'yd3' => 'yd3',
                                    'mcg' => 'mcg',
                                    'mg' => 'mg',
                                    'g' => 'g',
                                    'kg' => 'kg',
                                    'oz' => 'oz',
                                    'lb' => 'lb',
                                ),
                                'default_value' => array (
                                ),
                                'allow_null' => 0,
                                'multiple' => 0,
                                'ui' => 0,
                                'ajax' => 0,
                                'return_format' => 'value',
                                'placeholder' => '',
                            ),
                            array (
                                'key' => 'field_59c30a11185bc',
                                'label' => 'ingredient',
                                'name' => 'ingredient',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array (
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => 'parsley',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                            ),
                        ),
                    ),
                    array (
                        'key' => 'field_59c30ad3185bf',
                        'label' => 'Instructions',
                        'name' => 'instructions',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array (
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => '',
                        'min' => 0,
                        'max' => 0,
                        'layout' => 'table',
                        'button_label' => '',
                        'sub_fields' => array (
                            array (
                                'key' => 'field_59c325706a5ef',
                                'label' => 'Step',
                                'name' => 'step',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array (
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                            ),
                        ),
                    ),
                ),
                'location' => array (
                    array (
                        array (
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'wpf-recipe',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => 1,
                'description' => '',
            ));

            endif;
        
        
    }
    
    function enqueueScripts() {
        // Adding scripts & styles to all pages
        //wp_enqueue_script('wpf-recipe-costing-script', plugin_dir_url( __FILE__ ) . '/assets/js/main.js');
        wp_enqueue_script('wpf-recipe-costing-script', plugin_dir_url( __FILE__ ) . '/assets/js/build.js');
        //wp_enqueue_style('wpf-recipe-costing-style', plugin_dir_url( __FILE__ ) . '/assets/css/style.css'); 

        // enqueuing bootstrap-tabs.js file
//        wp_enqueue_scripts('bootstrap-tabs');
//        wp_register_script('bootstrap-tabs', 
//                    plugins_url('/assets/js/bootstrap-tabs.js'), 
//                    array ('jquery'), 
//                    false, false);
        
        // enqueuing vue.js file
//        wp_register_script('vue-js', plugins_url('/node_modules/dist/vue.js', __FILE__));
//        wp_enqueue_scripts('vue-js');
        
    }
    
    function registerTaxonomyForObjectType() {
            register_taxonomy_for_object_type( 'post_tag', 'cost-card' );
            register_taxonomy_for_object_type( 'category', 'cost-card' );
            register_taxonomy_for_object_type( 'post_tag', 'wpf-recipe' );
            register_taxonomy_for_object_type( 'category', 'wpf-recipe' );
    }

    public function addActionsAndFilters() {

        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action( 'admin_menu', array( &$this, 'addSettingsSubMenuPage' ) );

        // Add custom post_type
        add_action( 'init', array( &$this, 'register_custom_post_type' ) );
        //add_action( 'init', array( &$this, 'register_custom_fields' ) );

        // Example adding a script & style just for the options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false) {
            add_action( 'wp_enqueue_scripts', array($this, 'enqueueScripts') );
        }


        // Add Actions & Filters
        // http://plugin.michael-simpson.com/?page_id=37
        add_action( 'init', array( &$this, 'registerTaxonomyForObjectType' ) );
        
        
        
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