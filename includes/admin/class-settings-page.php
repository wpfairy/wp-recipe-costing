<?php
/*
 * @package    Wpf_Recipe_Costing\Includes\Admin
*/

class WpfRecipeCosting_SettingsPage {

    /**
     * Puts the configuration page in the Plugins menu by default.
     * Override to put it elsewhere or create a set of submenus
     * Override with an empty implementation if you don't want a configuration page
     * @return void
     */
    public function addSettingsMenuPage() {
        $this->addSettingsSubMenuPageToAdminMenu();
    }
    
    protected function addSettingsMenuPageToAdminMenu() {
        $this->requireExtraPluginFiles();
        $displayName = $this->getPluginDisplayName();
        add_options_page(
            $displayName,
            $displayName,
            'manage_options',
            $this->getSettingsSlug(),
            array( &$this, 'settingsPage' ));
    }
    
}