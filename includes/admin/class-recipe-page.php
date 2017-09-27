<?php
/*
 * @package    Wpf_Recipe_Costing\Includes\Admin
*/

class WpfRecipeCosting_SettingsPage {
    
       protected function addMenuPageToAdminMenu() {
        $this->requireExtraPluginFiles();
        $displayName = $this->getPluginDisplayName();
        add_menu_page($displayName,
                     $displayName,
                     'manage_options',
                     $this->getSettingsSlug(),
                      null,
                     'dashicons-editor-table',
                      90
                     );
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
            
            if ( array_key_exists('description', $aOptionMeta) )    { $elementName = $aOptionMeta['description']; }
            if (isset($elementName)) { $elementID = str_replace(' ','-',strtolower($elementName)); }
            
            if ( array_key_exists('type', $aOptionMeta) || isset($aOptionMeta['formElement']['type']) ) { 
                $elementType = $aOptionMeta['formElement']['type']; 
            }
                
            if ( array_key_exists('value', $aOptionMeta) || isset($aOptionMeta['formElement']['value']) ) { 
                $elementValue = $aOptionMeta['formElement']['value']; 
            }
            
            if ( array_key_exists('subtext', $aOptionMeta) )        { $elementSubtext = $aOptionMeta['formElement']['subtext']; }
            if ( array_key_exists('placeholder', $aOptionMeta) )    { $elementPlaceholder = $aOptionMeta['formElement']['placeholder']; }
            if ( array_key_exists('tooltip', $aOptionMeta) )        { $elementTooltip = $aOptionMeta['formElement']['tooltip']; }            
            
            $savedOptionValue = get_option( $this->prefix($aOptionKey) );
            
            $formElement        = '';
            
            if ( is_array( $elementValue ) ) {
                
                switch( $elementType ) {
                        
                    case 'select': 
                        $formElement = '<select name="'. $elementType .'" id="'. $aOptionKey .'">';
                        foreach ( $elementValue as $choice ) { 
                            $savedOptionValue = $this->getOptionValueI18nString( $choice );
                            $selected = ($choice == $savedOptionValue) ? 'selected' : ''; 
                            
                            $formElement = $formElement . '<option value="'. $choice .'" '. $selected
                                . '>'. $savedOptionValue .'</option>';
                        } 
                        $formElement = $formElement . '</select>';
                        break;
                    case 'radio':
                        foreach ( $elementValue as $value ) { 
                            
                            $savedOptionValue = $this->getOptionValueI18nString( $value );
                            $selected = ($value == $savedOptionValue) ? 'selected' : ''; 
                            
                            $formElement = $formElement . '<label class="custom-control custom-radio">
                                <input id="'. $aOptionKey .'" name="'. $elementType .'" type="'. $elementType .'" class="custom-control-input" '. $selected .'>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">'. $value .'</span>
                              </label>';
                        }
                        break;
                    case 'checkbox':
                        $formElement = '<label id="'. $aOptionKey .'" class="custom-control custom-checkbox">
                          <input type="'. $elementType .'" class="custom-control-input">
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">'. $elementValue .'</span>
                        </label>';
                        break;
                    default:
                        $formElement = '<select name="'. $elementType .'" id="'. $aOptionKey .'">';
                        foreach ( $elementValue as $choice ) { 
                            $savedOptionValue = $this->getOptionValueI18nString( $choice );
                            $selected = ($choice == $savedOptionValue) ? 'selected' : ''; 
                            
                            $formElement = $formElement . '<option value="'. $choice .'" '. $selected
                                . '>'. $savedOptionValue .'</option>';
                        } 
                        $formElement = $formElement . '</select>';                        
                }
            } else {
                
                switch( $elementType ) {
                    case 'text': 
                    case 'email':
                        $formElement = '<input type="'.$elementType.'" name="'. $elementName .'" id="'. $elementName .'" value="' . $savedOptionValue. '" autocomplete="on" />';
                        if ( isset($elementSubtext) ) {
                            $formElement = $formElement . '<small id="'. $elementSubtext .'">'. $elementSubtext .'</small>';
                        }
                        break;
                    case 'checkbox':
                        $formElement = '<label id="'. $aOptionKey .'" class="custom-control custom-checkbox">
                          <input type="'. $elementType .'" class="custom-control-input">
                          <span class="custom-control-indicator"></span>
                          <span class="custom-control-description">'. $elementValue .'</span>
                        </label>';
                        break;
                    case 'file':
                        $formElement = '<label id="'. $aOptionKey .'" class="custom-'. $aOptionKey .'"> 
                          <input type="'. $elementType .'" id="file" class="custom-file-input">
                          <span class="custom-file-control"></span>
                        </label>';
                        break;
                    default: 
                        $formElement = '<input id="'. $aOptionKey .'" type="'. $elementType .'" name="'. $elementType ;
                        if (isset($elementPlaceholder)) { 
                            $formElement = $formElement . 'placeholder="'.$elementPlaceholder.'" ';
                        }
                        $formElement = $formElement . '" value="'.  esc_attr($savedOptionValue) .'" />';            
                }
            }
            echo $formElement;
        } else {
            // this should not happen, $aOptionMeta should always be an array
            echo 'Uh oh! aOptionMeta should be an array. Check '.$this->getOptionNamePrefix(). '_Plugin.php to make sure your Options are configured correctly.';
        }
    }

}