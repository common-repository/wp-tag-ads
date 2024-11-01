<?php   
/*
Plugin Name: WP Tag Ads
Plugin URI: http://www.wptagads.com
Description: Serves ads based on tags inside content
Version: 2.3
Author: Jorge A. Gonzalez
Author URI: http://www.irokm.com
*/
 
register_activation_hook(__FILE__,'wptagads_init');


function wptagads_init() {
	$wptagads_default_keyword = get_option('wptagads_default_keyword');
	if (!$wptagads_default_keyword)	update_option('wptagads_default_keyword', 'iphone');
}
	 
function wptagads_get_tags() {
	global $wptagads_tags,$post;	

	 if (is_single()) { 
	 	// Do we have a wptagads keyword for this content
			 $wptagads_post_default = get_post_meta($post->ID, 'wptagads_keywords_advanced'); 
			 if ($wptagads_post_default) $wptagads_tags = $wptagads_post_default[0];
			 
		 // If we don't have a default keyword for the post
		 // Use the tags in the post	
		 	if (!$wptagads_post_default) {
				 $posttags = get_the_tags($post->ID);
					if ($posttags) {
						foreach($posttags as $tag) {
							$all_tags_arr[] = $tag->name;  
						}
					 $wptagads_tags = strtolower(implode(",", $all_tags_arr));  
					}
				}
				
			// If we don't have default keywords and no post tags, use the default.
				if (!$wptagads_tags) {
					$wptagads_tags = get_option('wptagads_default_keyword');
				}
				
				 
	} 
	
	$wptagads_tags = strtolower($wptagads_tags);
	
	return $wptagads_tags; 
} 
	
  function wptagads_tags() {
	global $wptagads_tags;	
	return $wptagads_tags;
  }


##################################################################################################################
#	Widget stuff
#	We should add more here later
##################################################################################################################

function widget_wptagads_style() {	
	  echo '';
	}
	
function widget_wptagads_register() {
	if ( function_exists('register_sidebar_widget') ) :
	function widget_wptagads($args) {

	if (is_single() ) {	
	?> 
			<div style="margin:10px;"><center>
				<script type="text/javascript">
					<!--
					wptagads_ad_campaign = "<?=get_option('wptagads');?>";
					wptagads_ad_width = "180";
					wptagads_ad_height = "150";
					wptagads_ad_keywords =  "<?= wptagads_tags();?>";
					wptagads_color_border =  "82806e";
					wptagads_color_bg =  "ffffff";
					wptagads_color_heading =  "82806e";
					wptagads_color_text =  "82806e";
					wptagads_color_link =  "ffffff"; 
					-->
				</script>
				<script type="text/javascript" src="http://www.wptagads.com/core/ads.js"></script>
			</center></div>
		<?
		 } 
	}
 

	function widget_wptagads_control() {
		$options = $newoptions = get_option('widget_wptagads');
		if ( $_POST["wptagads-submit"] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST["wptagads-title"]));
			if ( empty($newoptions['title']) ) $newoptions['title'] = '';
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_wptagads', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
	?>
				<p><label for="wptagads-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="wptagads-title" name="wptagads-title" type="text" value="<?php echo $title; ?>" /></label></p>
				<input type="hidden" id="wptagads-submit" name="wptagads-submit" value="1" />
	<?
	}

	register_sidebar_widget('WP Tag Ads', 'widget_wptagads', null, 'wptagads');
	register_widget_control('WP Tag Ads', 'widget_wptagads_control', null, 75, 'wptagads');
	if ( is_active_widget('widget_wptagads') )
		add_action('wp_head', 'widget_wptagads_style');
	endif;
}

 

 



##################################################################################################################
# Controls for the configuration
#	We should add more here later
##################################################################################################################

function wptagads_config_page() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page('plugins.php', __('WP Tag Ads Configuration'), __('WP Tag Ads'), 'manage_options', '', 'wptagads_conf');

}


function wptagads_conf() { 

	if ( isset($_POST['submit']) ) {
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die(__('Cheatin&#8217; uh?'));

		$key = preg_replace( '/[^a-z0-9]/i', '', $_POST['key'] );
		$wptagads_default_keyword = preg_replace( '/[^a-z][[:space:]]/i', '', $_POST['wptagads_default_keyword'] );

		if ( empty($key) ) {
			delete_option('wptagads');
			delete_option('wptagads_default_keyword');
		} 
		
		update_option('wptagads_default_keyword', $wptagads_default_keyword);
		update_option('wptagads', $key); 
 
	}


?>
<?php if ( !empty($_POST ) ) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('<a href=widgets.php>Add Widget to Sidebar</a>') ?></strong></p></div>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php endif; ?>
<div class="wrap">

<h2><?php _e('WP Tag Ads Configuration'); ?></h2>
<div class="narrow">

<form action="" method="post" id="wptagads-conf" style="margin: auto; ">

	<p>Making money on a WordPress blog has never been easier with <a href="http://www.wptagads.com" target="_blank">WP Tag Ads</a>.</p> 
	<p>With this plugin WP Tag Ads serves eBay products based on the tags assigned to a piece of content. Other similar ad solutions read you page and serve ads based on the discovery. Well with WP Tag Ads you are in control of what ads are being served. <a href="http://www.wptagads.com/category/blog/" target="_blank">WP Tag Ads Blog</a></p> 
	<p><b>Learn how to create an <a href="http://www.wptagads.com/how-to-create-an-ebay-campaign-id/" target="_blank">eBay Campaign ID</a> | <a href="http://affiliates.ebay.com/" target="_blank">Get an eBay Campaign ID</a></b> | <a href="http://www.wptagads.com/category/resources/" target="_blank">Need Help?</a></b></p>

	
	<p style="padding: .5em; background-color: #aa0; color: #fff; font-weight: bold;">eBay Campaign ID</p>
	<p><input id="key" name="key" type="text" size="30" maxlength="12" value="<?php echo get_option('wptagads'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" /></p>
	
 
	<p style="padding: .5em; background-color: #aa0; color: #fff; font-weight: bold;">Enter a Default Keyword</p>
	<p><input id="wptagads_default_keyword" name="wptagads_default_keyword" type="text" size="30" maxlength="20" value="<?php echo get_option('wptagads_default_keyword'); ?>" style="font-family: 'Courier New', Courier, mono; font-size: 1.5em;" /></p>
	<p>Keyword is only used when no post tag is present.</p>
 
	<p class="submit"><input type="submit" name="submit" value="<?php _e('Update options &raquo;'); ?>" /></p>
	
</form>


</div>
</div>
<?php
}



##################################################################################################################
#	We should add more here later
##################################################################################################################


// Custom fields for WP write panel
 
$wta_metaboxes = array(
		"image" => array (
			"name"		=> "wptagads_keywords_advanced",
			"default" 	=> "",
			"label" 	=> "Keyword(s)",
			"type" 		=> "text",
			"desc"      => "If you want to enter specific keyword(s) outside of the tags. This method is better at targeting specific keyword(s). This list should be comma delimited"
		)
	);
	
function wptagads_meta_box_content() {
	global $post, $wta_metaboxes;
	echo '<table>'."\n";
	foreach ($wta_metaboxes as $wta_metabox) {
		$wta_metaboxvalue = get_post_meta($post->ID,$wta_metabox["name"],true);
		if ($wta_metaboxvalue == "" || !isset($wta_metaboxvalue)) {
			$wta_metaboxvalue = $wta_metabox['default'];
		}
		echo "\t".'<tr>';
		echo "\t\t".'<th style="text-align: right;"><label for="'.$wta_metabox.'">'.$wta_metabox['label'].':</label></th>'."\n";
		echo "\t\t".'<td><input size="70" type="'.$wta_metabox['type'].'" value="'.$wta_metaboxvalue.'" name="'.$wta_metabox["name"].'" id="'.$wta_metabox.'"/></td>'."\n";
		echo "\t".'</tr>'."\n";
		echo "\t\t".'<tr><td></td><td><span style="font-size:11px">'.$wta_metabox['desc'].'</span></td></tr>'."\n";				
	}
	echo '</table>'."\n\n";
}

function wptagads_metabox_insert($pID) {
	global $wta_metaboxes;
	 
	foreach ($wta_metaboxes as $wta_metabox) {
		$var = "".$wta_metabox["name"];
		if (isset($_POST[$var])) {			
			if( get_post_meta( $pID, $wta_metabox["name"] ) == "" )
				add_post_meta($pID, $wta_metabox["name"], $_POST[$var], true );
			elseif($_POST[$var] != get_post_meta($pID, $wta_metabox["name"], true))
				update_post_meta($pID, $wta_metabox["name"], $_POST[$var]);
			elseif($_POST[$var] == "")
				delete_post_meta($pID, $wta_metabox["name"], get_post_meta($pID, $wta_metabox["name"], true));
		}
	}
}

function wptagads_meta_box() {
	if ( function_exists('add_meta_box') ) {
		add_meta_box('wptagads-settings','WP Tag Ads Custom Settings','wptagads_meta_box_content','post','normal');
		add_meta_box('wptagads-settings','WP Tag Ads Custom Settings','wptagads_meta_box_content','page','normal');
	}
}


function testing($test) {
	global $wp_query;
		$post = $wp_query->get_queried_object();
		$wptagads_tags = wptagads_get_tags($post->ID);
		echo $post_ID;
		exit;
		return $wptagads_tags;
}

##############################################################################################################################################################################

add_action('admin_menu', 'wptagads_meta_box');
add_action('wp_insert_post', 'wptagads_metabox_insert');

 
	# Adds the WPTagAds option to the submenu, this is important later.
	add_action('admin_menu', 'wptagads_config_page');
	
	# Adds the WPTagAds option to the submenu
	 add_option('wptagads', '', $deprecated, $autoload);	
 
 	# Put the keywords into the global space
	 add_action('template_redirect','wptagads_get_tags'); 

 	 add_action('init', 'widget_wptagads_register');
 	
 
?>