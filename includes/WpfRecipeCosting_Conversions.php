<?php
/*
 * @package    Wpf_Recipe_Costing\Includes
*/

class WpfRecipeCosting_Conversions (

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    function installDatabaseTables() {
        global $wpdb;
        $tableName = $this->prefixTableName('conversion_table');
        
        $sql = "CREATE TABLE IF NOT EXISTS `$tableName` (
            `id` INTEGER NOT NULL,
            `name` ,
            `type` ,
            `by` ,
            `parent`
            PRIMARY_KEY `id`"
        
        $wpdb->query($sql);
            
    }
    
    function jal_install_data() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'liveshoutbox';

        $wpdb->insert( 
            $table_name, 
            array( 
                'time' => current_time( 'mysql' ), 
                'name' => $welcome_name, 
                'text' => $welcome_text, 
            ) 
        );
    }

)