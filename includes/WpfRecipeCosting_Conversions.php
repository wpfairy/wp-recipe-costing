<?php
/*
    "WP Recipe Costing" Copyright (C) 2017 WPFairy LLC  (email : admin@wpfairy.com)

    This file is part of WP Recipe Costing plugin.

    WordPress Plugin Template is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    WordPress Plugin Template is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contact Form to Database Extension.
    If not, see http://www.gnu.org/licenses/gpl-3.0.html
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