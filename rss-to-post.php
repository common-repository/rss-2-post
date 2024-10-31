<?php
/**
 * @package RSS to Post
 * @author Gordon French
 * @version 1.0.3
 */
/*
Plugin Name: RSS-to-Post
Plugin URI: http://wordpress.gordonfrench.com/
Description: RSS-to-Post adds an RSS feed to each of your posts. This plugin allows the user to add an rss feed to the bottom of each post. There is a global setting for a default rss feed. Once the global feed url is set you can change, or disable the feed on individual posts. Rss-to-Post is a great way to keep new content coming to your posts, therefor helping with your Google Rating.

Author: Gordon French
Version: 1.0.3
Author URI: http://wordpress.gordonfrench.com
*/


// add the settings page to the main sidebar under settings
function my_plugin_menu() {
   //add_options_page('My Plugin Options', 'My Plugin', 'capability_required', 'your-unique-identifier', 'my_plugin_options');
   add_options_page('RSS-to-Post Options', 'RSS to Post', '8', 'RSS-to-Post-Settings', 'my_plugin_options');
   //add_submenu_page('post-new.php',...) 
   add_submenu_page('post-new.php', 'RSS to Post', '8', 'RSS-to-Post-Settings', 'my_plugin_options');
}
add_action('admin_menu', 'my_plugin_menu');



// build the plugin option page
function my_plugin_options() { 
	//get the array
	$rssOptions = get_option("rssOptions");
	
	if (empty($rssOptions['feed-url'])){$rssOptions['feed-url'] = 'http://feeds.feedburner.com/PlannedDiet';}
	if (empty($rssOptions['feed-trunc'])){$rssOptions['feed-trunc'] = 100;}
	if (empty($rssOptions['feed-title'])){$rssOptions['feed-title'] = 'Helpful Articles';}
	if (empty($rssOptions['feed_amount'])){$rssOptions['feed_amount'] = '3';}
	
	// set the checked option for the radio buttons
	if ($rssOptions['feed-enabled'] == 'no'){
		$rssYesChecked = '';
		$rssNoChecked = 'CHECKED';
	} else if ($rssOptions['feed-enabled'] == 'yes') {
		$rssYesChecked = 'CHECKED';
		$rssNoChecked = '';
	}
	
	if ($_POST['rss-submit']){
		
		
		// lets validate this feed before we save it
		$feedVal = validateFeed($_POST['rss-feed']);
		if ($feedVal == 1){
			$rssOptions['feed-url'] = $_POST['rss-feed'];
		} else {
			echo '<div class="errorBox"><b>There is something wrong with that feed.</b></div>';
			$fail = true;
			$failRss = true;
		}
		
		// set enabled option
		$rssOptions['feed-enabled'] = $_POST['rss-enabled'];
		
		// set the checked option for the radio buttons
		if ($rssOptions['feed-enabled'] == 'no'){
			$rssYesChecked = '';
			$rssNoChecked = 'CHECKED';
		} else if ($rssOptions['feed-enabled'] == 'yes') {
			$rssYesChecked = 'CHECKED';
			$rssNoChecked = '';
		}
		
		//set the trancation
		$rssOptions['feed-trunc'] = $_POST['rss-trunc'];
		
		//set item amount
		$rssOptions['feed_amount'] = $_POST['rss_amount'];
		
		// set the title
		$rssOptions['feed-title'] = $_POST['rss-title'];
		update_option("rssOptions", $rssOptions);
		
		if (!$fail){
		echo '<div class="savedBox"><b>Your settings have been saved</b></div>';
		}
		
	} ?>
  		
       <style type="text/css"> 
       .settingsArea	{ background-color:#f1f1f1; padding:10px; width:500px; border:1px solid #e3e3e3; margin:10px 0px; position:relative; }
	   .savedBox		{ position:relative; width:500px; border:2px solid #229585; background-color:#c2f7f0; padding:10px;  margin:20px 0px 0px}
	   .errorBox		{ position:relative; width:500px; border:2px solid #f7a468; background-color:#f7d8c2; padding:10px; margin:20px 0px 0px}
	   .highlight		{ border:2px solid #f7a468; background-color:#f7d8c2}
	   
	   .rssNotes		{ background-color:#f5f6f7; border:1px solid #e3e3e3; padding:10px; font-size:90%; color:#666}
	   </style>
       
    
		<script type="text/javascript" src="<?php bloginfo('url'); ?>/wp-content/plugins/rss-2-post/scripts/jquery.js"></script> 
       <script language="JavaScript" type="text/javascript">
		$(document).ready(function(){
			$(".errorBox").animate( {opacity: 1.0}, 3000, function() {
				$(".errorBox").animate( {opacity: 0.5}, 2000, function() {
					$(".errorBox").slideUp("slow");
				});
			}); 
		
			$(".savedBox").animate( {opacity: 1.0}, 3000, function() {
				$(".savedBox").animate( {opacity: 0.5}, 2000, function() {
					$(".savedBox").slideUp("slow");
				});
			}); 
		});
		</script>
        
      <div class="wrap">
      <h2><img src="<?php bloginfo('url'); ?>/wp-content/plugins/rss-2-post/images/rss-icon30.png"/> RSS to Post Settings</h2>
	  <div class="settingsArea">
       <form name="socrates" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>"/>
       		
             <p><strong>Enabled:  &nbsp;  &nbsp;  &nbsp;</strong>
             Yes: <input type="radio" name="rss-enabled" value="yes" <?php echo $rssYesChecked; ?>/>  &nbsp;  &nbsp;
             No:  <input type="radio" name="rss-enabled" value="no" <?php echo $rssNoChecked; ?>/><br>
			 <small>Enable RSS to Post, you can disable on individual posts when creating a new post</small>.</p>
            
            <p>
			<b>Title:</b> <input type="text" name="rss-title" value="<?php  echo $rssOptions['feed-title']; ?>" /><br />
            <small>This title will appear before the rss feeds. Example: "Helpful Articles".</small></p>
            
            <p>
			<b>Truncate:</b> <input type="text" size="7" name="rss-trunc" value="<?php  echo $rssOptions['feed-trunc']; ?>" /><br />
            <small>Numerical amount to truncate the feeds(100).</small></p>
            
            <p>
			<b>Amount of Items:</b> 
			<input type="text" size="3" name="rss_amount" value="<?php  echo $rssOptions['feed_amount']; ?>" /><br />
            <small>Numerical value for amount of feed items to display (1-10)</small></p>
           
            <p>
            <b>Default RSS Feed:</b> <input type="text" size="50" name="rss-feed" class="<?php if ($failRss){ echo 'highlight'; } ?>" 
            		value="<?php  echo $rssOptions['feed-url']; ?>" /><br />
            <small>Default RSS Feed to display if one is not set during publication.</small></p>
            
            <div class="rssNotes">
            You can edit the style of your rss feeds with css using the classes below.<br />
            Simply add the classes to your style sheet and change as you see fit.<br />
			.rssEntree {}<br />
            .rssTitle {}<br />
            </div>
            
            <br />

            
            <input type="hidden" id="rss-submit" name="rss-submit" value="1" />        
            <input name="save" value="Save" type="submit" />
       </form>
      </div>
      

      <h2>Default Feed Data</h2>
      <div class="settingsArea">
      <?php

		// what are we parsing?
		$xml_file = $rssOptions['feed-url'];
		
		$xml = simplexml_load_file($xml_file);
		//print_r($xml);
		//echo $xml->channel; 
		$i = 1;
		foreach ($xml->channel->item as $post){
			
			if ($i <= $rssOptions['feed_amount']){
				$i++;
				echo '
					<div class="rssEntree">
						<h3><a href="'.$post->link.'">'.$post->title.' </a></h3>';
						
						 $shortdesc = myTruncate($post->description, $rssOptions['feed-trunc']);
						
				echo '<p>'.$shortdesc.' </p>
					</div>
				';
			}
		} // foreach		

     echo '</div></div>';
} 





// add the meta box to the sidebar in new-post
function rss_add_meta_box() {
	add_meta_box('rss_post_form', __('RSS to Post', 'rss-to-post'), 'rss_meta_box', 'post', 'side');
}
add_action('admin_init', 'rss_add_meta_box');


// build the option in new post.
function rss_meta_box() {
	//get the array
	$rssOptions = get_option("rssOptions");
	
	$post_ID = (isset($_GET['post']))?addslashes($_GET['post']):'';
	$rssFeed = get_post_meta($post_ID, 'rssFeedUrl', true);
	$feedEnabled = get_post_meta($post_ID, 'feedEnabled', true);
	
	echo "enabled = ".$feedEnabled;
	
	// set enabled option
	//$rssOptions['feed-enabled'] = $_POST['rss-enabled'];
	
	// set the checked option for the radio buttons
	if ($feedEnabled == 'no'){
		$rssYesChecked = '';
		$rssNoChecked = 'CHECKED';
	} else if ($feedEnabled == 'yes') {
		$rssYesChecked = 'CHECKED';
		$rssNoChecked = '';
	}
	
	if ($rssFeed == ''){
		$rssFeed = $rssOptions['feed-url'];
	}
     ?>
    
      <div class="wrap">
      <p><strong>Feed Url:</strong><br />
      <input type="hidden" name="post_id" value="<?php echo $post_ID; ?>"
      RSS Feed: <input type="text" size="38" name="rss-feed-value" value="<?php  echo $rssFeed; ?>" /><br />
      <small>Enter the <strong>complete url</strong> of the feed.</small></p>
      
      <p><strong>Enable:  &nbsp;  &nbsp;  &nbsp;</strong>
      Yes: <input type="radio" name="rss_enabled" value="yes" <?php echo $rssYesChecked; ?>/>  &nbsp;  &nbsp;
      No:  <input type="radio" name="rss_enabled" value="no" <?php echo $rssNoChecked; ?>/>
      </p>
        
      </div>
     
<?php 
	 do_action('rssOptions');
}






// creat a function to update the meta data
// we are getting post id from hidden form field
// and we are getting the value for the rss feed
// then add the values to the post_meta
function rss_store_post_options($post_ID) {	
	$post_ID = $_POST['post_id'];
	$rssMeta = $_POST['rss-feed-value'];
	$feedEnabled = $_POST['rss_enabled'];
	add_post_meta($post_ID, 'rssFeedUrl', $rssMeta, 'false') or update_post_meta($post_ID, 'rssFeedUrl', $rssMeta);
	add_post_meta($post_ID, 'feedEnabled', $feedEnabled, 'false') or update_post_meta($post_ID, 'feedEnabled', $feedEnabled); 
}
// the action call the function to update the post meta data
add_action('draft_post', 'rss_store_post_options');
add_action('publish_post', 'rss_store_post_options');
add_action('save_post', 'rss_store_post_options');
add_action('update_post', 'rss_store_post_options');





// add rss data to the end of the content
function rss_post_content($content, $sidebar = false){
	$rssOptions = get_option("rssOptions");
	if ($rssOptions['feed-enabled'] != 'no'){
		// get post id
		$post_ID = get_the_ID();
		
		// check the meta dat for this post
		$rssFeed = get_post_meta($post_ID, 'rssFeedUrl', true);
		$feedEnabled = get_post_meta($post_ID, 'feedEnabled', true);
		
		// check post meta to see if disabled on this post.
		if($feedEnabled != 'no'){
			
			// get the default feed if post meta is empty.
			if ($rssFeed == ''){
				$rssFeed = $rssOptions['feed-url'];
			}
			
			// what are we parsing?
			if (!empty($rssFeed) && is_single() ){
				$xml = simplexml_load_file($rssFeed);
				//print_r($xml);
				//echo $xml->channel; 
				
				
				$rssTitle = '<h3 class=\'rssTitle\'>'.$rssOptions['feed-title'].'</h3>';
				$rssData='';
				
				$i = 1;
				foreach ($xml->channel->item as $post){
					if ($i <= $rssOptions['feed_amount']){
						$i++;
						$shortdesc = myTruncate($post->description, $rssOptions['feed-trunc']);
						$rssData .=  '
							<div class="rssEntree" style="font-size:90%;">
								<h3><a rel="no follow" href="'.$post->link.'">'.$post->title.' </a></h3>
								<p>'.$shortdesc.' </p>
							</div>
						';
					}//if
				}// for each
				$content=$content.$rssTitle.$rssData;
			}// close parser
			
		}//if feed enabled
	} // if rss = no
	return $content;
}
add_filter('the_content', 'rss_post_content');


function myTruncate($string, $limit, $break=".", $pad="...") { 
// return with no change if string is shorter than $limit  
if(strlen($string) <= $limit) return $string; 
	// is $break present between $limit and the end of the string?  
	if(false !== ($breakpoint = strpos($string, $break, $limit))) { 
		if($breakpoint < strlen($string) - 1) { 
			$string = substr($string, 0, $breakpoint) . $pad; 
		}
	} 
	return $string;
}

function validateFeed( $sFeedURL ){
    $sValidator = 'http://feedvalidator.org/check.cgi?url=';
    if( $sValidationResponse = @file_get_contents($sValidator . urlencode($sFeedURL)) ){
        if( stristr( $sValidationResponse , 'This is a valid RSS feed' ) !== false ){
			return true;
        } else {
			return false;
        }
    } else {
		return false;
    }
}

?>