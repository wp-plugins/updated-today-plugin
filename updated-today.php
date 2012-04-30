<?php
/*
Plugin Name: Updated Today Banner
Plugin URI: http://www.chriskdesigns.com/updated-today/
Description: This plug-in provides a banner in the left or right corner of the page that says "updated today" if your Wordpress Blog has been updated today.
Version: 2.5
Author: Chris Klosowski
Author URI: http://www.chriskdesigns.com/
License: GPL
*/

// Setup the hooks
if ( is_admin() ) { //Adds admin verification
	add_action( 'admin_menu', 'updated_menu' );
	add_action( 'admin_init', 'register_updated_settings' );
  add_action( 'admin_enqueue_scripts', 'ck_enqueue_admin_js' );
	register_deactivation_hook( 'updated-today-plugin/updated-today.php', 'de_register_settings_ut' );
	register_activation_hook( 'updated-today-plugin/updated-today.php', 'pre_register_settings_ut' );
} else { // Non admin hooks
  wp_enqueue_style( 'ck_updated_today_css',  plugins_url().'/updated-today-plugin/styles.css', NULL, '2.5', 'all');
	add_action( 'wp_head', 'ck_wp_head', 1 );
	add_action( 'wp_footer', 'ck_wp_footer', 1 );
}

// The Business Functions
function ck_wp_head () {
  $options = get_option( 'updated_today_options' );
  if ( $options['banner_pngfix'] == 'true' ) {
    wp_enqueue_script( 'ck_updated_today_pngfix', plugins_url().'/updated-today-plugin/pngfix.js', NULL, '2.5', TRUE );
  }
  if ( $options['banner_hook_option'] == 'header' ) {
		updated_banner();
	}
}


function ck_wp_footer () {
	$options = get_option( 'updated_today_options' );
	if ( $options['banner_hook_option'] == 'footer' ) {
		updated_banner();
	}
}

function updated_banner()
{
  global $table_prefix, $wpdb;
  $options = get_option( 'updated_today_options' );
  $today = date_i18n( "Y-m-d" );
  if ( $options['alert_on_published'] && $options['alert_on_modified'] ) {
  	$status = "(".$table_prefix."posts.post_date LIKE '".$today."%' OR ".$table_prefix."posts.post_modified LIKE '".$today."%')";
  }
  if ( $options['alert_on_published'] && !$options['alert_on_modified'] ) {
  	$status = "".$table_prefix."posts.post_date LIKE '".$today."%'";
  }
  if ( $options['alert_on_modified'] && !$options['alert_on_published'] ) {
  	$status = "".$table_prefix."posts.post_modified LIKE '".$today."%'";
  }
  if ( $options['alert_on_post'] && $options['alert_on_page'] ) {
  	$type = "(".$table_prefix."posts.post_type='post' OR ".$table_prefix."posts.post_type='page')";
  }
  if ( $options['alert_on_post'] && !$options['alert_on_page'] ) {
  	$type = "".$table_prefix."posts.post_type='post'";
  }
  if ( $options['alert_on_page'] && !$options['alert_on_post'] ) {
  	$type = "".$table_prefix."posts.post_type='page'";
  }
  $query = "SELECT post_date, id FROM ".$table_prefix."posts WHERE ".$status." AND ".$table_prefix."posts.post_status='publish' AND ".$type."";
  if ( $results = $wpdb->get_results( $query ) ) {
    $postid = $results[0]->id;
    $banner_image = ( !$options['banner_image'] ) ? 'updated.png': $options['banner_image'];
    $banner_image = plugins_url() . '/updated-today-plugin/banners/' . $banner_image;
    $image_info = getimagesize( $banner_image );
    ?>
  	<div id="updated" class="<?php echo $options['banner_position']; ?>" style="height:<?php echo $image_info[0]; ?>;width:<?php echo $image_info[1]; ?>">
  	  <?php if ( $options['link_banner'] ) {?>
        <a href="<?php echo get_permalink( $postid ); ?>" title="<?php echo get_the_title( $postid ); ?>">
      <?php }?>
      <img src="<?php echo $banner_image; ?>" border="0" />
      <?php if ( $options['link_banner'] ) {?>
      </a>
      <?php }?>
    </div>
  	<?php
  }
}


// Admin Menu functions
function ck_enqueue_admin_js() { 
  if ( $_GET['page'] == 'updated-today-plugin' ) {
    ?>
    <script>
    function show_confirm(link) {
      if( confirm( 'Confirm Delete?' ) ) location.href = link;
    }
    </script>
    <?php
  }
}

function updated_menu() {
  add_options_page( 'Updated Today', 'Updated Today', 8, 'updated-today-plugin', 'updated_menu_options' );
}

function updated_menu_options() {
  $current = get_option( 'updated_today_options' );
  if ( isset( $_POST ) && $_POST['Upload'] == 'Upload File' ) {
    $upload_dir = plugin_dir_path( __FILE__ ).'/banners/';
    if ( $_FILES['new_banner']['error'] == UPLOAD_ERR_OK && strpos( $_FILES['new_banner']['type'], 'image' ) !== FALSE ) { 
      $temp_name = $_FILES['new_banner']['tmp_name'];
      $name = $_FILES['new_banner']['name'];
      $move_successful = move_uploaded_file( $temp_name, $upload_dir.$name );
    }
  }
  if ( isset( $_GET['del_image'] ) && !isset( $_GET['settings-updated'] ) ) {
    $del_image = $_GET['del_image'];
    $dirname = plugin_dir_path( __FILE__ ).'/banners/';
    $images = scandir( $dirname );
    foreach( $images as $curimg ) {
      if ( $curimg === $del_image && $del_image !== 'updated.png' && $del_image !== 'updatedrt.png' ) {
        $delete_successful = unlink( $dirname.$del_image );
        if ( $delete_successful && $del_image == $current['banner_image'] ) {
          $current['banner_image'] = ( $current['banner_position'] == 'left' ) ? 'updated.png' : 'updatedrt.png';
          update_option( 'updated_today_options', $current);
          $current = get_option( 'updated_today_options' );
        }
      }
    }
  }
  ?>
  <div class="wrap">
    <div id="icon-options-general" class="icon32"></div><h2>Updated Today Options</h2>
    <?php if ( $move_successful ) { ?>
      <div id="setting-error-settings_updated" class="updated settings-error"><p><strong>Image Uploaded</strong></p></div>
    <?php } elseif ( $move_successful === FALSE ) { ?>
      <div id="setting-error-settings_updated" class="updated settings-error"><p><strong>Error Uploading Image, please verify permissions on directory</strong></p></div>
    <?php } ?>
    <?php if ( $delete_successful ) { ?>
      <div id="setting-error-settings_updated" class="updated settings-error"><p><strong>Image Deleted</strong></p></div>
    <?php } ?>
    <em><a href="#upload-new">Upload Images</a></em>
    <form method="post" action="options.php">
      <?php wp_nonce_field( 'update-options' ); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row">Position:</th>
          <td><input type="radio" name="updated_today_options[banner_position]" value="left" <?php if ( $current['banner_position'] == 'left' ) {?>checked="checked"<?php;}?> /> Left<br /><input type="radio" name="updated_today_options[banner_position]" value="right" <?php if ( $current['banner_position'] == 'right' ) {?>checked="checked"<?php;}?> /> Right</td>
        </tr>
  
        <tr valign="top">
          <th scope="row">Check:<br /><span style="font-size: x-small;">What would you like to notify visitors of?</span></th>
          <td><input type="checkbox" name="updated_today_options[alert_on_post]" value="post" <?php if ( $current['alert_on_post'] == 'post' ) {?>checked="checked"<?php;}?> /> Posts<br />
          <input type="checkbox" name="updated_today_options[alert_on_page]" value="page" <?php if ( $current['alert_on_page'] == 'page' ) {?>checked="checked"<?php;}?> /> Pages <br />for...<br />
          <input type="checkbox" name="updated_today_options[alert_on_published]" value="published" <?php if ( $current['alert_on_published'] == 'published' ) {?>checked="checked"<?php;}?> /> Published date<br />
          <input type="checkbox" name="updated_today_options[alert_on_modified]" value="modified" <?php if ( $current['alert_on_modified'] == 'modified' ) {?>checked="checked"<?php;}?> /> Modified date
          </td>
        </tr>
  
        <tr valign="top">
          <th scope="row">Insert into:<br /><span style="font-size: x-small;">Changing this can help display issues on some themes</span></th>
          <td><input type="radio" name="updated_today_options[banner_hook_option]" value="header" <?php if ( $current['banner_hook_option'] == 'header' ) {?>checked="checked"<?php;}?> /> Header<br /><input type="radio" name="updated_today_options[banner_hook_option]" value="footer" <?php if ( $current['banner_hook_option'] == 'footer' ) {?>checked="checked"<?php;}?> /> Footer <em>(Recommended)</em><br />Note: This <strong>does not</strong> place the banner at bottom of your page if you select 'Footer'. It is simply where, in the WordPress code, the plugin is executed. Choosing 'Header' can correct display issues but 'Footer' is recommended as it can allow for primary content to load slightly faster.</td>
        </tr>
  
        <tr valign="top">
          <th scope="row">Link Image to Post:<br /><span style="font-size: x-small;">This will create an anchor tag to the post updated</span></th>
          <td><input type="checkbox" name="updated_today_options[link_banner]" value="true" <?php if ( $current['link_banner'] == 'true' ) {?>checked="checked"<?php;}?> /></td>
        </tr>
  
        <tr valign="top">
          <th scope="row">Use PNGFix:<br /><span style="font-size: x-small;">Fixes transparent .png files for use with Internet Explorer 6.0</span></th>
          <td><input type="checkbox" name="updated_today_options[banner_pngfix]" value="true" <?php if ( $current['banner_pngfix'] == 'true' ) {?>checked="checked"<?php;}?> /></td>
        </tr>
  
        <tr valign="top">
            <th scope="row">Images:</th>
            <td>
            <?php
            $dirname = plugin_dir_path( __FILE__ ).'/banners/';
            $images = scandir( $dirname );
            foreach( $images as $curimg ) {
              if ( file_is_displayable_image( $dirname.$curimg ) ) {
                ?><input type="radio" name="updated_today_options[banner_image]" value="<?php echo $curimg; ?>" <?php if ( $current['banner_image'] == $curimg ) {?>checked="checked"<?php;}?> />
                <img src="<?php bloginfo( 'url' ); ?>/wp-content/plugins/updated-today-plugin/banners/<?php echo $curimg;?>" />
                <br />
                <?php echo $curimg; ?><?php if ( $curimg != 'updated.png'  && $curimg != 'updatedrt.png' ) { ?> - <a href="javascript:show_confirm('<?php echo get_bloginfo( 'url' ); ?>/wp-admin/options-general.php?page=updated-today-plugin&del_image=<?php echo $curimg; ?>');" href="">Delete Image</a><?php } ?>
                <br /><br />
                <?php
              }
            }
            ?>
            </td>
        </tr>
      </table>
  
      <input type="hidden" name="action" value="update" />
      <input type="hidden" name="page_options" value="updated_today_options" />
      
      <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
      </p>
      <?php settings_fields( 'updated-options' ); ?>
    </form>
  
    <div id="icon-upload" class="icon32"></div><h2 id="upload-new">Upload Banner Image</h2>
    <h3>Some tips on Banner Images</h3>
    <ul>
      <li>Keep it farily small, the default images are 120px by 120px</li>
      <li>Transparent PNG's work best</li>
    </ul>
    <form action="options-general.php?page=updated-today-plugin" method="post" enctype="multipart/form-data">
      <label for="file">Upload Banner Image:</label>
      <input type="file" name="new_banner" id="file" /><br /><em>If the filename exists, it will be overwritten.<br />Files are uploaded to /wp-content/plugins/updated-today-plugin/banners</em>
      <br />
      <input class="button-primary" type="submit" name="Upload" value="<?php _e('Upload File'); ?>" id="submitbuttom" />
    </form>
  </div>
  <?php
}

// Setting Registrations
function register_updated_settings() { // Whitelist options\
	register_setting( 'updated-options', 'updated_today_options' );
	if ( get_option( 'banner_position' ) ) {
		$import = array();
		$import['banner_position'] 		= get_option( 'banner_position' );
		$import['banner_pngfix']			= get_option( 'banner_pngfix' );
		$import['banner_image']				= get_option( 'banner_image' );
		$import['alert_on_post']			= get_option( 'alert_on_post' );
		$import['alert_on_page']			= get_option( 'alert_on_page' );
		$import['alert_on_modified']	= get_option( 'alert_on_modified' );
		$import['alert_on_published'] = get_option( 'alert_on_published' );
		$import['banner_hook_option'] = get_option( 'banner_hook_option' );
		update_option( 'updated_today_options', $import );
		delete_option( 'banner_position' );
  	delete_option( 'banner_pngfix' );
  	delete_option( 'banner_image' );
  	delete_option( 'alert_on_post' );
  	delete_option( 'alert_on_page' );
  	delete_option( 'alert_on_modified' );
  	delete_option( 'alert_on_published' );
  	delete_option( 'banner_hook_option' );	
	}
}

function pre_register_settings_ut() { // Upon Activation set defaults
	$set_options = array();
	$set_options['banner_position'] 		= 'left';
	$set_options['banner_pngfix']				= 'false';
	$set_options['banner_image']				= 'updated.png';
	$set_options['alert_on_post']				= 'post';
	$set_options['alert_on_page']				= '';
	$set_options['alert_on_modified']		= '';
	$set_options['alert_on_published']  = 'published';
	$set_options['banner_hook_option']  = 'footer';
	$set_options['link_banner']					= 'true';
  update_option( 'updated_today_options', $set_options );
}

function de_register_settings_ut() { // Delete options from db on deactivate
	delete_option( 'updated_today_options' );
}

// Helpers
// Defines scandir() if using PHP 4.x thanks to http://abeautifulsite.net/notebook/59
if( !function_exists('scandir') ) {
  function scandir( $directory, $sorting_order = 0 ) {
    $dh  = opendir( $directory );
    while( false !== ( $filename = readdir( $dh ) ) ) {
      $files[] = $filename;
    }
    if( $sorting_order == 0 ) {
      sort( $files );
    } else {
      rsort( $files );
    }
    return( $files );
  }
}
