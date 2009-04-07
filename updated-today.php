<?php
/*
Plugin Name: Updated Today Banner
Plugin URI: http://www.chriskdesigns.com/updated-today
Description: This plug-in provides a banner in the upper left corner of the page that says "updated today" if your Wordpress Blog has been updated today.
Version: 1.6.1
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
if (!$conf_manual_placement) {
    add_action('wp_footer', 'ck_wp_footer');
}

function ck_wp_head ()
{
    global $conf_manual_style, $conf_use_pngfix;
    if (!$conf_manual_style) {
        echo '<style type="text/css" media="screen">#updated {
    position: absolute;
    display: block;
            top: 0px;
    left: 0px;
    height: 120px;
    width: 120px;
    z-index: 100;
        } </style>';
    }
    if ($conf_use_pngfix) {
        echo '<!--[if lt IE 7]>
<script defer type="text/javascript" src="wp-content/plugins/updated-today-plugin/pngfix.js"></script>
<![endif]-->';
    }
}

function updated_banner()
{
    $today = date("Y-m-d");
    $status = 'publish';
    $query = "SELECT post_date, id FROM wp_posts WHERE wp_posts.post_modified LIKE '".$today."%' AND wp_posts.post_status='publish'";
    $results = mysql_query($query);
    $num_results = mysql_num_rows($results);
    if ($num_results > 0) {
        $results_assoc = mysql_fetch_assoc($results);
        $postid = $results_assoc['id'];
	?>
	<div id="updated"><img src="<?php bloginfo('url'); ?>/wp-content/plugins/updated-today-plugin/updated.png" border="0" /></div>
	<?php
    }
}

function ck_wp_footer ()
{
    updated_banner();
}
?>