<?php
/*
 * @package    Wpf_Recipe_Costing\Includes
*/

class WpfRecipeCosting_OptionsManager {

    public function getOptionNamePrefix() {
        return get_class($this) . '_';
    }


    /**
     * Define your options meta data here as an array, where each element in the array
     * @return array of key=>display-name and/or key=>array(display-name, choice1, choice2, ...)
     * key: an option name for the key (this name will be given a prefix when stored in
     * the database to ensure it does not conflict with other plugin options)
     * value: can be one of two things:
     *   (1) string display name for displaying the name of the option to the user on a web page
     *   (2) array where the first element is a display name (as above) and the rest of
     *       the elements are choices of values that the user can select
     * e.g.
     * array(
     *   'item' => 'Item:',             // key => display-name
     *   'rating' => array(             // key => array ( display-name, choice1, choice2, ...)
     *       'CanDoOperationX' => array('Can do Operation X', 'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber'),
     *       'Rating:', 'Excellent', 'Good', 'Fair', 'Poor')
     */
    public function getOptionMetaData() {
        return array();
    }

    /**
     * @return array of string name of options
     */
    public function getOptionNames() {
        return array_keys($this->getOptionMetaData());
    }

    /**
     * Override this method to initialize options to default values and save to the database with add_option
     * @return void
     */
    protected function initOptions() {
        
    }

    /**
     * Cleanup: remove all options from the DB
     * @return void
     */
    protected function deleteSavedOptions() {
        $optionMetaData = $this->getOptionMetaData();
        if (is_array($optionMetaData)) {
            foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
                $prefixedOptionName = $this->prefix($aOptionKey); // how it is stored in DB
                delete_option($prefixedOptionName);
            }
        }
    }

    /**
     * @return string display name of the plugin to show as a name/title in HTML.
     * Just returns the class name. Override this method to return something more readable
     */
    public function getPluginDisplayName() {
        return 'WP Recipe Costing';
    }

    /**
     * Get the prefixed version input $name suitable for storing in WP options
     * Idempotent: if $optionName is already prefixed, it is not prefixed again, it is returned without change
     * @param  $name string option name to prefix. Defined in settings.php and set as keys of $this->optionMetaData
     * @return string
     */
    public function prefix($name) {
        $optionNamePrefix = $this->getOptionNamePrefix();
        if (strpos($name, $optionNamePrefix) === 0) { // 0 but not false
            return $name; // already prefixed
        }
        return $optionNamePrefix . $name;
    }

    /**
     * Remove the prefix from the input $name.
     * Idempotent: If no prefix found, just returns what was input.
     * @param  $name string
     * @return string $optionName without the prefix.
     */
    public function &unPrefix($name) {
        $optionNamePrefix = $this->getOptionNamePrefix();
        if (strpos($name, $optionNamePrefix) === 0) {
            return substr($name, strlen($optionNamePrefix));
        }
        return $name;
    }

    /**
     * A wrapper function delegating to WP get_option() but it prefixes the input $optionName
     * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
     * @param $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @param $default string default value to return if the option is not set
     * @return string the value from delegated call to get_option(), or optional default value
     * if option is not set.
     */
    public function getOption($optionName, $default = null) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        $retVal = get_option($prefixedOptionName);
        if (!$retVal && $default) {
            $retVal = $default;
        }
        return $retVal;
    }

    /**
     * A wrapper function delegating to WP delete_option() but it prefixes the input $optionName
     * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
     * @param  $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @return bool from delegated call to delete_option()
     */
    public function deleteOption($optionName) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return delete_option($prefixedOptionName);
    }

    /**
     * A wrapper function delegating to WP add_option() but it prefixes the input $optionName
     * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
     * @param  $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @param  $value mixed the new value
     * @return null from delegated call to delete_option()
     */
    public function addOption($optionName, $value) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return add_option($prefixedOptionName, $value);
    }

    /**
     * A wrapper function delegating to WP add_option() but it prefixes the input $optionName
     * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
     * @param  $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @param  $value mixed the new value
     * @return null from delegated call to delete_option()
     */
    public function updateOption($optionName, $value) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return update_option($prefixedOptionName, $value);
    }

    /**
     * A Role Option is an option defined in getOptionMetaData() as a choice of WP standard roles, e.g.
     * 'CanDoOperationX' => array('Can do Operation X', 'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber')
     * The idea is use an option to indicate what role level a user must minimally have in order to do some operation.
     * So if a Role Option 'CanDoOperationX' is set to 'Editor' then users which role 'Editor' or above should be
     * able to do Operation X.
     * Also see: canUserDoRoleOption()
     * @param  $optionName
     * @return string role name
     */
    public function getRoleOption($optionName) {
        $roleAllowed = $this->getOption($optionName);
        if (!$roleAllowed || $roleAllowed == '') {
            $roleAllowed = 'Administrator';
        }
        return $roleAllowed;
    }

    /**
     * Given a WP role name, return a WP capability which only that role and roles above it have
     * http://codex.wordpress.org/Roles_and_Capabilities
     * @param  $roleName
     * @return string a WP capability or '' if unknown input role
     */
    protected function roleToCapability($roleName) {
        switch ($roleName) {
            case 'Super Admin':
                return 'manage_options';
            case 'Administrator':
                return 'manage_options';
            case 'Editor':
                return 'publish_pages';
            case 'Author':
                return 'publish_posts';
            case 'Contributor':
                return 'edit_posts';
            case 'Subscriber':
                return 'read';
            case 'Anyone':
                return 'read';
        }
        return '';
    }

    /**
     * @param $roleName string a standard WP role name like 'Administrator'
     * @return bool
     */
    public function isUserRoleEqualOrBetterThan($roleName) {
        if ('Anyone' == $roleName) {
            return true;
        }
        $capability = $this->roleToCapability($roleName);
        return current_user_can($capability);
    }

    /**
     * @param  $optionName string name of a Role option (see comments in getRoleOption())
     * @return bool indicates if the user has adequate permissions
     */
    public function canUserDoRoleOption($optionName) {
        $roleAllowed = $this->getRoleOption($optionName);
        if ('Anyone' == $roleAllowed) {
            return true;
        }
        return $this->isUserRoleEqualOrBetterThan($roleAllowed);
    }

    /**
     * see: http://codex.wordpress.org/Creating_Options_Pages
     * @return void
     */
    public function createSettingsMenu() {
        $pluginName = $this->getPluginDisplayName();
        //create new top-level menu
        add_menu_page($pluginName . ' Settings',
                      $pluginName,
                      'administrator',
                      get_class($this),
                      array(&$this, 'settingsPage')
        /*,plugins_url('/images/icon.png', __FILE__)*/); // if you call 'plugins_url; be sure to "require_once" it

        //call register settings function
        add_action('admin_init', array(&$this, 'registerSettings'));
    }

    public function registerSettings() {
        $settingsGroup = get_class($this) . '-settings-group';
        $optionMetaData = $this->getOptionMetaData();
        foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
            register_setting($settingsGroup, $aOptionMeta);
        }
    }

    /**
     * Creates HTML for the Administration page to set options for this plugin.
     * Override this method to create a customized page.
     * @return void
     */
    public function settingsPage() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'wpf-recipe-costing'));
        }

        $optionMetaData = $this->getOptionMetaData();
        
        //print_r( $optionMetaData );
        
//        echo '<ul>';
//        foreach ( $optionMetaData as $parent ) {
//            
//            //print_r( $parent );
//            //echo $optionMetaData[$parent];
//            foreach ( $parent as $key => $value ) {
//                if ( !is_array( $key ) ) {
//                    //print_r( $value );
//                    echo '<li>'.$key.': '.$value .'</li>';
//                    //$choices = 
//                } else {
//                    echo '<ul>';
//                    foreach ( $child as $key2 => $value2 ) {
//                        echo '<li>'.$key2.': '.$value2 .'</li>';
//                    }
//                    echo '</ul>';
//                }
//                
//            }
//            
//        }
//        echo '</ul>';
        
        //$iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($optionMetaData));
        

        function iterator(&$complex_array){

            foreach ($complex_array as $key => $value) {

                if (is_array($value)) {
                    
                    echo "<p>[".$key."]</p>";
                    iterator($value);
                    
                } else {
                    
                    echo "<li> [".$key."] ".$value .'</li>';
                    if ( $key == 'format') {
                        $aOptionFormat = $complex_array[$key];
                        echo $aOptionFormat;
                    }
                    
                }
            }
        }
        //iterator($optionMetaData);
        
        //var_dump(array_keys($this->getOptionMetaData()));
        //var_dump($this->getOptionMetaData());
        
        function recurseMetaArray(&$optionMetaData) {
            foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
                $aOptions = "";
                if (is_array($aOptionMeta)) {
                    echo "<p>[".$aOptionKey."]</p>";
                    //$aOptions = "";
                    recurseMetaArray($aOptionMeta);
                } else {
                    //echo "<li> [".$aOptionKey."] ".$aOptionMeta .'</li>';
                    if ( $aOptionKey == 'format' ) {
                        $aOptionFormat = $optionMetaData[$aOptionKey];
                        echo "[format]<li>".$aOptionFormat."</li>";
                    }
                    if ( $aOptionKey == 'choices' ) {
                        $aOptions = array($aOptions, $optionMetaData[$aOptionKey]);
                        echo '[choices] ';
                        foreach ( $aOptions as $k => $v ) {
                            echo "<li> [".$k."] ".$v .'</li>';
                        }
                    }
                }
            }
        }
        //recurseMetaArray($optionMetaData);
        

        // Save Posted Options
        if ($optionMetaData != null) {
            foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
                if (isset($_POST[$aOptionKey])) {
                    $this->updateOption($aOptionKey, $_POST[$aOptionKey]);
                }
            }
        }

        // HTML for the page
        $settingsGroup = get_class($this) . '-settings-group';
        ?>
        <div class="wrap">
            <h2><?php _e('WP Recipe Costing Settings', 'wpf-recipe-costing'); ?></h2>
            <table cellspacing="1" cellpadding="2"><tbody>
            <tr><td><?php _e('System', 'wpf-recipe-costing'); ?></td><td><?php echo php_uname(); ?></td></tr>
            <tr><td><?php _e('PHP Version', 'wpf-recipe-costing'); ?></td>
                <td><?php echo phpversion(); ?>
                <?php
                if (version_compare('5.2', phpversion()) > 0) {
                    echo '&nbsp;&nbsp;&nbsp;<span style="background-color: #ffcc00;">';
                    _e('(WARNING: This plugin may not work properly with versions earlier than PHP 5.2)', 'wpf-recipe-costing');
                    echo '</span>';
                }
                ?>
                </td>
            </tr>
            <tr><td><?php _e('MySQL Version', 'wpf-recipe-costing'); ?></td>
                <td><?php echo $this->getMySqlVersion() ?>
                    <?php
                    echo '&nbsp;&nbsp;&nbsp;<span style="background-color: #ffcc00;">';
                    if (version_compare('5.0', $this->getMySqlVersion()) > 0) {
                        _e('(WARNING: This plugin may not work properly with versions earlier than MySQL 5.0)', 'wpf-recipe-costing');
                    }
                    echo '</span>';
                    ?>
                </td>
            </tr>
            </tbody></table>

            <h2><?php echo $this->getPluginDisplayName(); echo ' '; _e('Settings', 'wpf-recipe-costing'); ?></h2>

            <form method="post" action="">
                <?php settings_fields($settingsGroup); ?>
                <style type="text/css">
                    table.plugin-options-table {width: 100%; }
                    table.plugin-options-table tr:nth-child(even) {background: #f9f9f9}
                    table.plugin-options-table tr:nth-child(odd) {background: #FFF}
                    table.plugin-options-table td { width: 50%}
                    table.plugin-options-table td > div > p {padding: 1em;}
                </style>
                <table class="plugin-options-table">
                    <tbody>
                    <?php
                    if ( $optionMetaData != null ) {
                        foreach ( $optionMetaData as $aOptionKey => $aOptionMeta ) {     
                            ?>
                            <tr valign="top">
                                <th scope="row">
                                    <div>
                                        <p><label for="<?php echo $aOptionKey ?>"><?php echo $aOptionMeta['description']; ?></label></p>
                                    </div>
                                </th>
                                <td>
                                    <?php $this->createFormElement( $aOptionKey, $aOptionMeta ); ?>
                                </td>
                            </tr>
                        
                        <?php
                        }                        
                    }
                    ?>
                    </tbody>
                </table>
                <div class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'wpf-recipe-costing') ?>"/>
                </div>
                                  
            </form>
        </div>
        <?php

    }

  
    /**
     * Helper-function outputs the correct form element (input tag, select tag) for the given item
     * @param  $aOptionKey string name of the option (un-prefixed)
     * @param  $aOptionMeta mixed meta-data for $aOptionKey (either a string display-name or an array(display-name, option1, option2, ...)
     * @param  $savedOptionValue string current value for $aOptionKey
     * @return void
     */
    protected function createFormControl( $aOptionKey, $aOptionMeta ) {
        
        $savedOptionValue = $this->getOption( $aOptionKey );

        if ( is_array( $aOptionMeta ) && count( $aOptionMeta ) >= 2 && array_key_exists('choices', $aOptionMeta) ) {
                
            $choices = $aOptionMeta['choices'];
            $savedOptionValue = $this->getOptionValueI18nString( $aChoice );
            
                switch( $aOptionMeta['formElement']['format'] ) {
                    case 'input': 
                        // Simple input field
                        ?>
                        <div id="" class="form-group">
                            <input type="text" name="<?php echo $aOptionKey ?>" id="<?php echo $aOptionKey ?>" value="<?php echo esc_attr($savedOptionValue) ?>" />
                        </div>
                        <?php break;
                    case 'select': 
                        // Drop-down list
                        ?>
                        <div id="" class="form-group">
                            <select name="<?php echo $aOptionKey; ?>" id="<?php echo $aOptionKey; ?>">
                            <?php foreach ( $choices as $aChoice ) { 
                            
                                $selected = ($aChoice == $savedOptionValue) ? 'selected' : '';?>
                                
                                <option value="<?php echo $aChoice; ?>" <?php echo $selected; ?>><?php echo $savedOptionValue; ?></option>
                                
                            <?php } ?>
                            </select>
                        </div>
                        <?php break;
                    case 'checkbox':
                        ?>
                        <div id="" class="form-group">
                        <label class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input">
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">Check this custom checkbox</span>
                        </label>
                        </div>
                        <?php break;
                    case 'radio':
                        ?>
                        <div id="" class="form-group">
                        <label class="custom-control custom-radio">
                          <input id="radio1" name="radio" type="radio" class="custom-control-input">
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">Toggle this custom radio</span>
                        </label>
                        <label class="custom-control custom-radio">
                          <input id="radio2" name="radio" type="radio" class="custom-control-input">
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">Or toggle this other custom radio</span>
                        </label>
                        </div>
                        <?php break;
                    default: // Simple input field
                        ?>
                        <div id="" class="form-group">
                            <input type="text" name="<?php echo $aOptionKey ?>" id="<?php echo $aOptionKey ?>" value="<?php echo esc_attr($savedOptionValue) ?>" />
                        </p>
                        <?php
                
            } 
        }
    }
    
    /**
     * Helper-function outputs the correct form element (input tag, select tag) for the given item
     * @param  $aOptionKey string name of the option (un-prefixed)
     * @param  $aOptionMeta mixed meta-data for $aOptionKey (either a string display-name or an array(display-name, option1, option2, ...)
     * @param  $savedOptionValue string current value for $aOptionKey
     * @return $formElementPrepend
     */
    protected function createFormElement( $aOptionKey, $aOptionMeta ) {

        if ( is_array( $aOptionMeta ) ) {
            var_dump($aOptionMeta);
            
            if ( array_key_exists('description', $aOptionMeta) )    { $elementName = $aOptionMeta['description']; }        
            if ( array_key_exists('type', $aOptionMeta) )           { $elementType = $aOptionMeta['formElement']['type']; }
            if ( array_key_exists('value', $aOptionMeta) )          { $elementValue = $aOptionMeta['formElement']['value']; }
            if ( array_key_exists('info', $aOptionMeta) )           { $elementInfo = $aOptionMeta['formElement']['info']; }
            if ( array_key_exists('subtext', $aOptionMeta) )        { $elementSubtext = $aOptionMeta['formElement']['subtext']; }
            if ( array_key_exists('placeholder', $aOptionMeta) )    { $elementPlaceholder = $aOptionMeta['formElement']['placeholder']; }
            if ( array_key_exists('tooltip', $aOptionMeta) )        { $elementTooltip = $aOptionMeta['formElement']['tooltip']; }
            
            
            //if ( !isset($elementValue) )    { $elementValue = $elementName; }
            //if ( !isset($elementType) )     { $elementType = 'text'; }
            
            
            $savedOptionValue = get_option( $this->prefix($aOptionKey) );
            $formElementPrepend = '<div class="form-group">';
            $elementPrepend     = '<div id="form-element-'. $elementName .'" class="form-group">';
            $elementAppend      = '</div>';
            $formElementAppend  = '</div>';
            
            $formGroupLabel     = '<label class="form-element-label" id="form-element-'.$aOptionKey.'-label">'.$elementName.'</label>';
            $formElement        = '<input id="'.$elementName.'" class="" type="">';
            
            
            if ( is_array( $elementValue ) ) {
                
                switch( $elementType ) {
                        
                    case 'select': 
                        // Drop-down list
                        ?>
                        <select name="<?php echo $elementType; ?>" id="form-element-<?php echo $aOptionKey; ?>">
                        <?php 

                        foreach ( $elementValue as $choice ) { 

                            $savedOptionValue = $this->getOptionValueI18nString( $choice );
                            $selected = ($choice == $savedOptionValue) ? 'selected' : ''; ?>

                            <option value="<?php echo $choice; ?>" <?php echo $selected; ?>><?php echo $savedOptionValue; ?></option>
                            
                        <?php } ?>
                            
                        </select>

                        <?php break;
                        
                    case 'radio':
                        ?>
                        <select name="<?php echo $elementType; ?>" id="form-element-<?php echo $aOptionKey; ?>">
                        <?php 

                        foreach ( $elementValue as $choice ) { 
                            
                            $savedOptionValue = $this->getOptionValueI18nString( $choice );
                            $selected = ($choice == $savedOptionValue) ? 'selected' : ''; ?>
                            
                            <label class="custom-control custom-radio">
                              <input id="" name="radio" type="radio" class="custom-control-input" <?php echo $selected; ?>><?php echo $savedOptionValue; ?>
                              <span class="custom-control-indicator"></span>
                              <span class="custom-control-description"><?php echo $elementValue ?></span>
                            </label>
                            
                        <?php } ?>
                        <?php break;
                        
                }
                
            } else {
                
                switch( $elementType ) {
                    case 'text': 
                    case 'email':
                        // input field
                        //$formElement = '<input type="'.$elementType.'" name="'. $elementName .'" id="'. $elementName .'" value="' . $elementValue. '" autocomplete="on" />';
                        ?>

                            <input type="<?php echo $elementType; ?>" 
                                   class="form-control" 
                                   id="form-input-<?php echo $aOptionKey; ?>" value="<?php echo $savedOptionValue; ?>"
                                   <?php 
                                    if (isset($elementPlaceholder)) { 
                                        echo 'placeholder="$elementPlaceholder" ';
                                   } ?>
                                   autocomplete="on" 
                                   >
                        
                        <?php if ( isset($elementSubtext) ) : ?>
                            
                        <small id="<?php echo $elementSubtext ?>"><?php echo $elementSubtext ?></small>
                        
                        <?php endif; ?>
                        <?php break;

                    case 'checkbox':
                        ?>
                        <label class="custom-control custom-checkbox">
                          <input type="checkbox" class="custom-control-input">
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">Check this custom checkbox</span>
                        </label>
                        <?php break;
                    //default: // input field
                        ?>
                        <input type="<?php echo $elementType ?>" name="<?php echo $elementType ?>" id="form-element-<?php echo $aOptionKey ?>" value="<?php echo esc_attr($savedOptionValue) ?>" />

                    <?php
                
                }
                
            
            
            
            }
            //return $formElementPrepend . $elementPrepend . $formGroupLabel . $formElement . $elementAppend . $formElementAppend;
        } else {
            // this should not happen, $aOptionMeta should always be an array
            echo 'Uh oh! aOptionMeta should be an array. Check '.$this->getOptionNamePrefix(). '_Plugin.php to make sure your Options are configured correctly.';
        }
    }

    /**
     * Override this method and follow its format.
     * The purpose of this method is to provide i18n display strings for the values of options.
     * For example, you may create a options with values 'true' or 'false'.
     * In the options page, this will show as a drop down list with these choices.
     * But when the the language is not English, you would like to display different strings
     * for 'true' and 'false' while still keeping the value of that option that is actually saved in
     * the DB as 'true' or 'false'.
     * To do this, follow the convention of defining option values in getOptionMetaData() as canonical names
     * (what you want them to literally be, like 'true') and then add each one to the switch statement in this
     * function, returning the "__()" i18n name of that string.
     * @param  $optionValue string
     * @return string __($optionValue) if it is listed in this method, otherwise just returns $optionValue
     */
    protected function getOptionValueI18nString($optionValue) {
        switch ($optionValue) {
            case 'true':
                return __('true', 'wpf-recipe-costing');
            case 'false':
                return __('false', 'wpf-recipe-costing');
            case 'Administrator':
                return __('Administrator', 'wpf-recipe-costing');
            case 'Editor':
                return __('Editor', 'wpf-recipe-costing');
            case 'Author':
                return __('Author', 'wpf-recipe-costing');
            case 'Contributor':
                return __('Contributor', 'wpf-recipe-costing');
            case 'Subscriber':
                return __('Subscriber', 'wpf-recipe-costing');
            case 'Anyone':
                return __('Anyone', 'wpf-recipe-costing');
        }
        return $optionValue;
    }

    /**
     * Query MySQL DB for its version
     * @return string|false
     */
    protected function getMySqlVersion() {
        global $wpdb;
        $rows = $wpdb->get_results('select version() as mysqlversion');
        if (!empty($rows)) {
             return $rows[0]->mysqlversion;
        }
        return false;
    }

    /**
     * If you want to generate an email address like "no-reply@your-site.com" then
     * you can use this to get the domain name part.
     * E.g.  'no-reply@' . $this->getEmailDomain();
     * This code was stolen from the wp_mail function, where it generates a default
     * from "wordpress@your-site.com"
     * @return string domain name
     */
    public function getEmailDomain() {
        // Get the site domain and get rid of www.
        $sitename = strtolower($_SERVER['SERVER_NAME']);
        if (substr($sitename, 0, 4) == 'www.') {
            $sitename = substr($sitename, 4);
        }
        return $sitename;
    }
}

