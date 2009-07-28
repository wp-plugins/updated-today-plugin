<?php
/*
Plugin Name: Updated Today Banner
Plugin URI: http://www.chriskdesigns.com/updated-today/
Description: This plug-in provides a banner in the left or right corner of the page that says "updated today" if your Wordpress Blog has been updated today.
Version: 2.0
Author: Chris Klosowski
Author URI: http://www.chriskdesigns.com/
License: GPL
*/

/* ### Configuration Variables ### */

$conf_manual_placement = false;
$conf_manual_style = false;
$conf_use_pngfix = true;

/* ### End Configuration Variables ### */

$banneradded = null;

add_action('wp_head', 'ck_wp_head');
add_action('wp_footer', 'ck_wp_footer');
if ( is_admin() ) { //Adds admin verification
add_action('admin_menu', 'updated_menu');
add_action('admin_init', 'register_updated_settings');
} else {
	// non-admin enqueues, actions, and filters
}

function ck_wp_head ()
{
    global $conf_manual_style, $conf_use_pngfix;
    ?>
    <style type="text/css" media="screen">
    #updated { clear: both; position: absolute; display: block; top: 0; <?php echo get_option('banner_position');?>: 0; height: <?php echo get_option('banner_height');?>px; width: <?php echo get_option('banner_width');?>px; z-index: 100; } 
    #updated img { padding: 0; margin: 0; }</style><?php
    if (get_option('banner_pngfix') == 'true') {
        echo '<!--[if lt IE 7]>
<script defer type="text/javascript" src="wp-content/plugins/updated-today-plugin/pngfix.js"></script>
<![endif]-->';
    }
}

function updated_banner()
{
    global $table_prefix, $wpdb;
    $today = date("Y-m-d");
    $status = 'publish';
    $query = "SELECT post_date, id FROM ".$table_prefix."posts WHERE (".$table_prefix."posts.post_date LIKE '".$today."%' OR ".$table_prefix."posts.post_modified LIKE '".$today."%') AND ".$table_prefix."posts.post_status='publish'";
   if ( $results = $wpdb->get_results($query) ) {
        $postid = $results[0]->id;
	?>
	<div id="updated"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/updated-today-plugin/banners/<?php echo get_option('banner_image');?>" border="0" /></div>
	<?php
    }
}

function updated_menu() {
  add_options_page('Updated Today', 'Updated Today', 8, 'updated-today-plugin', 'updated_menu_options');
}

function updated_menu_options() {
?>
<div class="wrap">
<h2>Updated Today Options</h2>
<em>Upload your images to wp-content/plugins/updated-today-plugin/banners</em>
<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

<table class="form-table">

<tr valign="top">
<th scope="row">Position</th>
<td><input type="radio" name="banner_position" value="left" <?php if (get_option('banner_position') == 'left') {?>checked="checked"<?php;}?> /> Left<br /><input type="radio" name="banner_position" value="right" <?php if (get_option('banner_position') == 'right') {?>checked="checked"<?php;}?> /> Right</td>
</tr>
 
<tr valign="top">
<th scope="row">Use PNGFix</th>
<td><input type="checkbox" name="banner_pngfix" value="true" <?php if (get_option('banner_pngfix') == 'true') {?>checked="checked"<?php;}?> /> <em>Fixes transparent .png files for use with Internet Explorer 6.0</em></td>
</tr>

<tr valign="top">
<th scope="row">Images</th>
<td><?php

$dirname = getcwd()."/../wp-content/plugins/updated-today-plugin/banners/";
$images = scandir($dirname);
foreach($images as $curimg){
if(file_is_displayable_image($dirname.$curimg)) {
?><input type="radio" name="banner_image" value="<?php echo $curimg; ?>" <?php if (get_option('banner_image') == $curimg) {?>checked="checked"<?php;}?> /><img src="<?php bloginfo('url'); ?>/wp-content/plugins/updated-today-plugin/banners/<?php echo $curimg;?>" /><br /><br />
<?php
};
}
?>
</td>
</tr>
</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="banner_position,banner_pngfix,banner_image, banner_width, banner_height" />

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
<?php settings_fields( 'updated-options' ); ?>
</form>
</div>

<?php

}

function register_updated_settings() { // whitelist options
  register_setting( 'updated-options', 'banner_position' );
  register_setting( 'updated-options', 'banner_pngfix' );
  register_setting( 'updated-options', 'banner_image' );
}


function ck_wp_footer ()
{
    updated_banner();
}
?>
