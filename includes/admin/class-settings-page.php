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
        /**
     * Creates HTML for the Administration page to set options for this plugin.
     * Override this method to create a customized page.
     * @return void
     */
    public function settingsPage() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'wpf-recipe-costing'));
        }
        
        $displayName = $this->getPluginDisplayName();
        $optionMetaData = $this->getOptionMetaData();
        
        // AJAX
        //$plainUrl = $this->getAjaxUrl('ajaxCONVERTUNITS');
        //$urlWithId = $this->getAjaxUrl('ajaxCONVERTUNITS&id=MyId');

//        $myId=0;
//        $myLat='x';
//        $myLng='y';
        
        // More sophisticated:
//        $parametrizedUrl = $this->getAjaxUrl('ajaxCONVERTUNITS&id=%s&lat=%s&lng=%s');
//        $urlWithParamsSet = sprintf($parametrizedUrl, urlencode($myId), urlencode($myLat), urlencode($myLng));


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
        <div class="wrap container">
            <h2><?php _e($displayName . ' Settings', 'wpf-recipe-costing'); ?></h2>
            <div id="app">
              {{ message }}
            </div>
            <script>
                var app = new Vue({
                  el: '#app',
                  data: {
                    message: 'Hello Vue!'
                  }
                })
            </script>
            
            <div id="app"></div>
            <script src="build.js"></script>
            
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                  <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#recipes" role="tab">Recipes</a>
                  </li>
                  <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#costcards" role="tab">Cost Cards</a>
                  </li>
                  <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#settings" role="tab">Settings</a>
                  </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                    <div class="tab-pane" id="recipes" role="tabpanel">Recipes</div>
                    <div class="tab-pane" id="costcards" role="tabpanel">Cost Cards</div>
                    <div class="tab-pane active" id="settings" role="tabpanel">Settings

                        <form>
                              <div class="form-group">
                                    <label for="exampleInputEmail1">Email address</label>
                                    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
                                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                              </div>
                              <div class="form-group">
                                    <label for="exampleInputPassword1">Password</label>
                                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                              </div>
                              <div class="form-group">
                                    <label for="exampleSelect1">Example select</label>
                                    <select class="form-control" id="exampleSelect1">
                                          <option>1</option>
                                          <option>2</option>
                                          <option>3</option>
                                          <option>4</option>
                                          <option>5</option>
                                    </select>
                              </div>
                              <div class="form-group">
                                    <label for="exampleSelect2">Example multiple select</label>
                                    <select multiple class="form-control" id="exampleSelect2">
                                          <option>1</option>
                                          <option>2</option>
                                          <option>3</option>
                                          <option>4</option>
                                          <option>5</option>
                                    </select>
                              </div>
                              <div class="form-group">
                                    <label for="exampleTextarea">Example textarea</label>
                                    <textarea class="form-control" id="exampleTextarea" rows="3"></textarea>
                              </div>
                              <div class="form-group">
                                    <label for="exampleInputFile">File input</label>
                                        <input type="file" class="form-control-file" id="exampleInputFile" aria-describedby="fileHelp">
                                    <small id="fileHelp" class="form-text text-muted">This is some placeholder block-level help text for the above input. It's a bit lighter and easily wraps to a new line.</small>
                              </div>
                              <fieldset class="form-group">
                                <legend>Radio buttons</legend>
                                <div class="form-check">
                                      <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="optionsRadios" id="optionsRadios1" value="option1" checked>
                                            Option one is this and that&mdash;be sure to include why it's great
                                      </label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="optionsRadios" id="optionsRadios2" value="option2">
                                            Option two can be something else and selecting it will deselect option one
                                    </label>
                                </div>
                                <div class="form-check disabled">
                                    <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="optionsRadios" id="optionsRadios3" value="option3" disabled>
                                    Option three is disabled
                                  </label>
                                </div>
                              </fieldset>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input">
                                  Check me out
                                </label>
                              </div>
                              <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                    </div>
            </div>

        </div>
        <?php

    }

    
}