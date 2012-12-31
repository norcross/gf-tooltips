<?php
/*
Plugin Name: Gravity Forms Tooltips
Plugin URI: http://andrewnorcross.com/plugins/gravity-tooltips/
Description: Convert the Gravity Forms description field into tooltips
Author: Andrew Norcross
Version: 1.0
Requires at least: 3.0
Author URI: http://andrewnorcross.com
*/
/*  Copyright 2012 Andrew Norcross

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License (GPL v2) only.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if(!defined('GFT_BASE'))
    define('GFT_BASE', plugin_basename(__FILE__) );

if(!defined('GFT_VER'))
    define('GFT_VER', '1.0');

class GF_Tooltips
{

    /**
     * This is our constructor
     *
     * @return GF_Tooltips
     */
    public function __construct() {
        add_action                  ( 'admin_init',                 array( $this, 'reg_settings'        )           );
        add_action                  ( 'admin_notices',              array( $this, 'gf_active_check'     ),  10      );
        add_action                  ( 'admin_enqueue_scripts',      array( $this, 'admin_scripts'       ),  10      );
        add_action                  ( 'wp_enqueue_scripts',         array( $this, 'scripts_styles'      ),  10      );
        add_filter                  ( 'gform_pre_render',           array( $this, 'form_wrap_class'     )           );
        add_action                  ( 'gform_field_css_class',      array( $this, 'field_auto_class'    ),  10, 3   );
        add_action                  ( 'gform_field_css_class',      array( $this, 'field_manu_class'    ),  10, 3   );
        add_action                  ( 'gform_field_css_class',      array( $this, 'field_hide_class'    ),  10, 3   );
        add_filter                  ( 'plugin_action_links',        array( $this, 'quick_link'          ),  10, 2   );
        add_filter                  ( 'gform_addon_navigation',     array( $this, 'create_menu'         )           );
    }

    /**
     * check for GF being active
     *
     * @return GF_Tooltips
     */

    public function gf_active_check() {
        $screen = get_current_screen();

        if ($screen->parent_file !== 'plugins.php' )
            return;

        if(is_plugin_active('gravityforms/gravityforms.php') )
            return;

        echo '<div id="message" class="error fade below-h2"><p><strong>This plugin requires Gravity Forms to function.</strong></p></div>';

    }

    /**
     * show settings link on plugins page
     *
     * @return GF_Tooltips
     */

    public function quick_link( $links, $file ) {

        static $this_plugin;

        if (!$this_plugin) {
            $this_plugin = GFT_BASE;
        }

        // check to make sure we are on the correct plugin
        if ($file == $this_plugin) {

            $settings_link  = '<a href="'.menu_page_url( 'gf-tooltips', 0 ).'">Settings</a>';

            array_unshift($links, $settings_link);
        }

        return $links;

    }

    /**
     * Add attribute to form tag for positioning
     *
     * @return GF_Tooltips
     */

    public function form_wrap_class($form) {

        // grab option field
        $tooltip    = get_option('gf_tooltips');

        $location   = (isset($tooltip['layout']) ? $tooltip['layout'] : 'topRight' );

        $form['cssClass'] .= $location;
        return $form;
    }

    /**
     * Add class for auto generated tooltip based on options
     *
     * @return GF_Tooltips
     */


    public function field_auto_class($classes, $field, $form){

        // grab option field
        $tooltip = get_option('gf_tooltips');

        // bail if options haven't been set
        if (!isset($tooltip['display']) || !isset($tooltip['position']) )
            return $classes;

        // bail if option was set to manual
        if (isset($tooltip['display']) && $tooltip['display'] == 'manual' )
            return $classes;

        // add class for label tooltip
        if (isset($tooltip['position']) && $tooltip['position'] == 'label' )
            $classes .= ' gf-tooltip gf-tooltip-label';

        // add class for icon tooltip
        if (isset($tooltip['position']) && $tooltip['position'] == 'icon' )
            $classes .= ' gf-tooltip gf-tooltip-icon';

        return $classes;
    }

    /**
     * Add class for manually loaded tooltip based on options
     *
     * @return GF_Tooltips
     */


    public function field_manu_class($classes, $field, $form){

        // grab option field
        $tooltip = get_option('gf_tooltips');

        // bail if options haven't been set
        if (!isset($tooltip['desc']) || !isset($tooltip['position']) )
            return $classes;

        // bail if option was set to auto
        if (isset($tooltip['display']) && $tooltip['display'] == 'auto' )
            return $classes;

        // add class for label tooltip
        if (isset($tooltip['position']) && $tooltip['position'] == 'label' )
            $classes .= ' gf-tooltip gf-tooltip-label-manual';

        // add class for icon tooltip
        if (isset($tooltip['position']) && $tooltip['position'] == 'icon' )
            $classes .= ' gf-tooltip gf-tooltip-icon-manual';

        return $classes;
    }

    /**
     * Add class for hiding description field
     *
     * @return GF_Tooltips
     */


    public function field_hide_class($classes, $field, $form){

        // grab option field
        $tooltip = get_option('gf_tooltips');

        // bail if options haven't been set
        if (!isset($tooltip['desc']) || !isset($tooltip['desc']) || !isset($tooltip['position']) )
            return $classes;

        // add auto hide if option was set to manual
        if (isset($tooltip['desc']) && $tooltip['display'] == 'auto' )
            $classes .= ' gf-desc-hide-auto';

        // add manual hide if option was set to manual
        if (isset($tooltip['desc']) && $tooltip['display'] == 'manual' )
            $classes .= ' gf-desc-hide-manual';

        return $classes;
    }

    /**
     * Register settings
     *
     * @return GF_Tooltips
     */

    public function reg_settings() {
        register_setting( 'gf_tooltips', 'gf_tooltips');

    }

    /**
     * add submenu option for tooltips
     *
     * @return GF_Tooltips
     */


    public function create_menu($menus){

        $menus[] = array(
            'name'      => 'gf-tooltips',
            'label'     => __('Tooltips'),
            'callback'  => array( $this, 'gf_tooltip_page' )
        );

        return $menus;
    }

    /**
     * Display main options page structure
     *
     * @return GF_Tooltips
     */

    public function gf_tooltip_page() {
        if (!current_user_can('manage_options') )
            return;
        ?>

        <div class="wrap">
            <img alt="" src="<?php echo plugins_url( '/lib/img/gravity-edit-icon-32', __FILE__ ); ?>" style="float:left; margin:7px 7px 0 0;"/>
            <h2><?php _e('Gravity Forms Tooptips') ?></h2>

        <?php
        if ( isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' )
            echo '<div id="message" class="updated below-h2"><p><strong>Settings have been saved.</strong></p></div>';
        ?>


            <div id="poststuff" class="metabox-holder has-right-sidebar">

            <?php
            echo $this->settings_side();
            echo $this->settings_open();
            ?>

                <form method="post" action="options.php">
                <?php
                settings_fields( 'gf_tooltips' );
                $tooltip = get_option('gf_tooltips');
                // option index checks
                $gf_position    = isset($tooltip['position'])   ? $tooltip['position']  : 'icon';
//              $gf_layout      = isset($tooltip['layout'])     ? $tooltip['layout']    : 'topRight';
                $gf_display     = isset($tooltip['display'])    ? $tooltip['display']   : 'auto';
                $gf_descs       = isset($tooltip['desc'])       ? $tooltip['desc']      : 'false';
                ?>
                <table class="form-table gf-tooltip-table">
                <tbody>

                    <tr>
                        <th scope="row"><?php _e('Tooltip Position') ?></th>
                        <td>
                        <input id="gf-option-label" class="gf-tooltip-position" type="radio" name="gf_tooltips[position]" value="label" <?php checked( $gf_position, 'label', true ); ?> />
                        <label for="gf-option-label"><?php _e('Apply tooltip to existing label') ?></label>
                        <br />
                        <input id="gf-option-icon" class="gf-tooltip-position" type="radio" name="gf_tooltips[position]" value="icon" <?php checked( $gf_position, 'icon', true ); ?> />
                        <label for="gf-option-icon"><?php _e('Insert tooltip icon next to label') ?></label>
                        </td>
                    </tr>
<!-- this will be included at a later date
                    <tr>
                        <th scope="row"><?php _e('Tooltip Location') ?></th>
                        <td>
                        <select name="gf_tooltips[layout]" id="gf-option-layout">

                            <option value="topRight" <?php selected( $gf_layout, 'topRight' ); ?>><?php _e('Top Right') ?></option>
                            <option value="bottomRight" <?php selected( $gf_layout, 'bottomRight' ); ?>><?php _e('Bottom Right') ?></option>
                            <option value="topLeft" <?php selected( $gf_layout, 'topLeft' ); ?>><?php _e('Top Left') ?></option>
                            <option value="bottomLeft" <?php selected( $gf_layout, 'bottomLeft' ); ?>><?php _e('Bottom Left') ?></option>
                            <option value="topMiddle" <?php selected( $gf_layout, 'topMiddle' ); ?>><?php _e('Top Middle') ?></option>
                            <option value="bottomMiddle" <?php selected( $gf_layout, 'bottomMiddle' ); ?>><?php _e('Bottom Middle') ?></option>
                        </select>

                        <label for="gf-option-layout" class="description"><?php _e('Select where the tooltip should be anchored relative to the label'); ?></label>
                        </td>
                    </tr>
-->
                    <tr>
                        <th scope="row"><?php _e('Tooltip Display') ?></th>
                        <td>
                        <input id="gf-display-auto" class="gf-tooltip-display" type="radio" name="gf_tooltips[display]" value="auto" <?php checked( $gf_display, 'auto', true ); ?> />
                        <label for="gf-display-auto"><?php _e('Insert tooltips automatically') ?></label>
                        <br />
                        <input id="gf-display-manual" class="gf-tooltip-display" type="radio" name="gf_tooltips[display]" value="manual" <?php checked( $gf_display, 'manual', true ); ?> />
                        <label for="gf-display-manual"><?php _e('Insert tooltips manually') ?></label>
                        <p class="description gf-manual-desc" style="display:none;"><?php _e('<strong>Note:</strong> Requires adding the <code>gf-tooltip-manual</code> class to each appropriate field') ?></p>

                        </td>
                    </tr>

                    <tr>
                        <th><label for="gf-desc"><?php _e('Description Field Display') ?></label></th>
                        <td>
                        <input type="checkbox" name="gf_tooltips[desc]" id="gf-desc" value="true" <?php checked( $gf_descs, 'true' ); ?> />
                        <span><em><?php _e('Hide the standard description field when tooltip is present') ?></em></span>
                        </td>
                    </tr>

                </tbody>
                </table>

                <p><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
                </form>

            </div>

            <?php echo $this->settings_close(); ?>

        </div>
    </div>

    <?php }

    /**
     * load scripts and styles for front end
     *
     * @return GF_Tooltips
     */

    public function scripts_styles() {

        wp_enqueue_style( 'gf-tooltips', plugins_url('/lib/css/gf-tooltips.css', __FILE__), array(), GFT_VER, 'all' );

        wp_enqueue_script( 'tooltips', plugins_url('/lib/js/jquery.qtip.min.js', __FILE__), array('jquery'), '1.0', true );
        wp_enqueue_script( 'gf-tooltips', plugins_url('/lib/js/gf-tooltips.js', __FILE__), array('jquery'), GFT_VER, true );
        wp_localize_script('gf-tooltips', 'tooltip_vars', array(
            'icon' => '<img src="'.plugins_url('/lib/img/tooltip-icon.png', __FILE__).'">'
            )
        );
    }

    /**
     * load scripts and styles for admin page
     *
     * @return GF_Tooltips
     */

    public function admin_scripts() {
        $screen = get_current_screen();

        if ($screen->base !== 'forms_page_gf-tooltips' )
            return;

        wp_enqueue_script( 'admin-tooltips', plugins_url('/lib/js/tooltips.admin.init.js', __FILE__), array('jquery'), GFT_VER, true );

    }

    /**
     * Some extra stuff for the settings page
     *
     * this is just to keep the area cleaner
     *
     * @return GF_Tooltips
     */

    public function settings_side() { ?>

        <div id="side-info-column" class="inner-sidebar">
            <div class="meta-box-sortables">
                <div id="admin-about" class="postbox">
                    <h3 class="hndle" id="about-sidebar"><?php _e('About the Plugin') ?></h3>
                    <div class="inside">
                        <p>Talk to <a href="http://twitter.com/norcross" target="_blank">@norcross</a> on twitter or visit the <a href="http://wordpress.org/support/plugin//" target="_blank">plugin support form</a> for bugs or feature requests.</p>
                        <p><?php _e('<strong>Enjoy the plugin?</strong>') ?><br />
                        <a href="http://twitter.com/?status=I'm using @norcross's PLUGIN NAME - check it out! http://l.norc.co//" target="_blank"><?php _e('Tweet about it') ?></a> <?php _e('and consider donating.') ?></p>
                        <p><?php _e('<strong>Donate:</strong> A lot of hard work goes into building plugins - support your open source developers. Include your twitter username and I\'ll send you a shout out for your generosity. Thank you!') ?><br />
                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="hidden" name="hosted_button_id" value="11085100">
                        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                        </form></p>
                    </div>
                </div>
            </div>

            <div class="meta-box-sortables">
                <div id="admin-more" class="postbox">
                    <h3 class="hndle" id="about-sidebar"><?php _e('Links') ?></h3>
                    <div class="inside">
                        <ul>
                        <li><a href="http://wordpress.org/extend/plugins//" target="_blank">Plugin on WP.org</a></li>
                        <li><a href="https://github.com/norcross/" target="_blank">Plugin on GitHub</a></li>
                        <li><a href="http://wordpress.org/support/plugin/" target="_blank">Support Forum</a><li>
                        </ul>
                    </div>
                </div>
            </div>
        </div> <!-- // #side-info-column .inner-sidebar -->

    <?php }

    public function settings_open() { ?>

        <div id="post-body" class="has-sidebar">
            <div id="post-body-content" class="has-sidebar-content">
                <div id="normal-sortables" class="meta-box-sortables">
                    <div id="about" class="postbox">
                        <div class="inside">

    <?php }

    public function settings_close() { ?>

                        <br class="clear" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php }

/// end class
}

$GF_Tooltips = new GF_Tooltips();
