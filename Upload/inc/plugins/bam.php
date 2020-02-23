<?php

	/*    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
    
     */


	/*ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);*/
global $mybb;

global $templatelist;
$templatelist .= ',bam_announcement';
$templatelist .= ',bam_announcement_container';

if(!defined("IN_MYBB")) {
	die("Hacking attempt detected. Server responded with 403. "); // direct access to this file not allowed. 
}

// Create hooks. 
if ($mybb->settings['bam_enabled'] == 1) {
	if ($mybb->settings['bam_compatibility_mode'] != 1) {
		$plugins->add_hook("global_intermediate", "bam_announcements"); // don't load announcements unless the plugin is enabled. 
	} else {
		$plugins->add_hook("pre_output_page", "bam_announcements_compatibility"); 
	}
}

$plugins->add_hook("admin_config_menu", "bam_config_menu");
$plugins->add_hook("admin_config_action_handler", "bam_confighandler");

function bam_info() {
	global $lang, $mybb, $plugins, $cache;
	$lang->load('bam');
	$desc = $lang->bam_desc;

	// BAM has an in-place upgrader, which checks whether BAM requires database updates after the new version is uploaded. 
	// If we detect that BAM 2.0's files have been uploaded without running the upgrade script yet... 
	// ... We display a notice to the administrator, along with a one-click link to run the upgrade.  
	// See /inc/plugins/bam_upgrade/bam_upgrade.php for more information about how this works. 

	// No need to check if we've upgraded the DB if we're not installed. 
	if (bam_is_installed()) {
		if (!bam_is_updated()) {
			$desc = $lang->bam_info_upgrade . "<br />"; // Display link to database updater script
			$activePlugins = $cache->read("plugins"); 

			// Because bam_upgrade() launches from within /admin/modules/config/bam.php, we must make sure we are activated first. 
			if (in_array("bam" ,$activePlugins['active'])) {
				$desc = $lang->bam_info_upgrade_ready;
				$desc .= "<br /><b><a href='index.php?module=config-bam&action=upgrade&post_key=".$mybb->post_code."'>".$lang->bam_upgrade_link_text_plugins_panel."</a></b>"; 
			}
		}
	}

	return array(
		'name'			=> $lang->bam_title,
		'description'	=> $desc,
		'website'		=> 'http://www.makestation.net',
		'author'		=> 'Darth Apple',
		'authorsite'	=> 'http://www.makestation.net',
		'version'		=> '2.0',
		"compatibility"	=> "18*"
	);
}

function bam_install () {
	global $db, $lang;
	$lang->load('bam');	
	if(!$db->table_exists($prefix.'bam')) {

		$db->query("CREATE TABLE ".TABLE_PREFIX."bam (
				PID int unsigned NOT NULL auto_increment,
  				announcement varchar(1024) NOT NULL DEFAULT '',				
				class varchar(40) NOT NULL DEFAULT 'yellow',
				link varchar(160) DEFAULT '',
				active int unsigned NOT NULL DEFAULT 1,
				disporder INT NOT NULL DEFAULT 1,
				`groups` varchar(128) DEFAULT '1, 2, 3, 4, 5, 6',
				date int(10) NOT NULL,
				pinned INT UNSIGNED DEFAULT 0,
				`global` INT UNSIGNED DEFAULT 0, 
				`random` INT UNSIGNED DEFAULT 0,
				additional_display_pages VARCHAR(512) DEFAULT NULL,
				forums VARCHAR(256) DEFAULT NULL,
  				PRIMARY KEY (PID)
				) ENGINE=MyISAM
				".$db->build_create_table_collation().";"
		);

		$inserts = array(
			'announcement' => $db->escape_string($lang->bam_welcome),
			'date' => (int) time(),
			'class' => 'yellow',
			'disporder' => 1,
			'link' => ''
		);
		$db->insert_query('bam', $inserts);
	}

		$template = array();

		// We must create the templates. CSS and javascript for BAM go within the container template. 
		// This was done to improve compatibility with heavily modified themes that might have issues with including new 
		// stylesheets and scripts in the headerinclude file. Although this method is not ideal for large 
		// stylesheets or JS scripts, the trade off for good compatibility and maintainability is worth doing things this way. 

		$template['bam_announcement_container'] = '
<style>
	.bam_announcement.yellow {
		background: #FFF6BF;
		border: 1px solid #FFD324;
	}

	.bam_announcement.green {
		background: #D6ECA6;
		border: 1px solid #8DC93E;
	}

	.bam_announcement.orange {
		background: #f58f10;
		border: 1px solid #926c28;
		color: #fff;
	}

	.bam_announcement.blue {
		background: #ADCBE7;
		border: 1px solid #0F5C8E;
	}

	.bam_announcement.red {
		background: #FBE3E4;
		border: 1px solid #A5161A;
	}

	.bam_announcement.magenta {
		background: #ff64a4;
		border: 1px solid #46042f;
		color: #ffffff;
	}

	.bam_announcement.silver {
		background: #e9eaea;
		border: 1px solid #8291ab;
	}

	.bam_announcement {
		-moz-border-radius: 5px;
		-webkit-border-radius: 5px;
		border-radius: 5px; 
		text-align: center;
		margin: 10px auto;
		padding: 8px 12px;
		background: #EBEBEB;
		color: #000000;
		border: 1px solid #ADADAD;
	}

	.bam_date {
		color: #636161;
		font-size: 0.78em;
		margin-left: 6px;
	}	

	.close_bam_announcement {
		float:right;
		display:inline-block;
		padding-right: 2px;
		padding-left: 2px;
		margin-right: 6px;
		font-weight: bold;
	}
	
	.close_bam_announcement:hover {
		float:right;
		display:inline-block;
		color:#000;
	}

	.bam_nodismiss {
		display: none !important; 
	}

	.bam_slidedown {
		display: none;
	}

	.bam_wrapper {
		width: 100%; 
		display: inline-block;
		margin-bottom: 10px;
	}
	
	{$bam_custom_css}
</style>
	
	<!-- Don\'t remove this. Needed for handling announcement dismissals. --> 
<script>
		$(document).ready(function(){
			$(\'.bam_slidedown\').delay(125).slideDown(350);
		});	
		// Allow me to give credit. This was great:  https://lifeofadesigner.com/javascript/hide-dismissed-notifications-with-jquery-and-cookies
	
	$(document).ready(function () {
		if (GetCookie("dismissed-notifications")) {
			$(GetCookie("dismissed-notifications")).hide();
		}
		$(".dismiss-notification").click(function () {
			var alertId = $(this).closest(".bam-unsticky").attr("id"); 
			var dismissedNotifications = GetCookie("dismissed-notifications") + ",#" + alertId; 
			$(this).closest(".bam-unsticky").fadeOut("slow"); 
			SetCookie("dismissed-notifications",dismissedNotifications.replace("null,","")) //update cookie
		});

		// Same as above, but close only. Don\'t set a cookie. 
		$(".bam-close-notification").click(function () {
			var alertId = $(this).closest(".bam-unsticky").attr("id"); 
			var dismissedNotifications = GetCookie("dismissed-notifications") + ",#" + alertId; 
			$(this).closest(".bam-unsticky").fadeOut("slow"); 
		});

	function SetCookie(sName, sValue) {
		document.cookie = sName + "=" + escape(sValue);
		var date = new Date();
		date.setTime(date.getTime() + ({$bam_cookie_expire_days} * 24 * 60 * 60 * 1000));
		document.cookie += ("; expires=" + date.toUTCString()); 
	}

	function GetCookie(sName) {
		var aCookie = document.cookie.split("; ");
		for (var i=0; i < aCookie.length; i++) {
			var aCrumb = aCookie[i].split("=");
			if (sName == aCrumb[0]) 
				return unescape(aCrumb[1]);
		}
		return null;
	}
	});
</script>
<div class="bam_wrapper"><div class="bam_announcements {$slidedown}">{$announcements}</div></div>';
		
		// Create the BAM announcement template used for each individual announcement. 
		$template['bam_announcement'] = '<p class="{$bam_unsticky} {$class}" id="announcement-{$bcprefix}{$announcement_id}">{$announcement} <span class="bam_date">{$date}</span>
<span class=\'close_bam_announcement {$display_close}\'>x</span></p>'; 
	
		// Insert the templates into the database. 
		foreach($template as $title => $template_new){
			$template = array('title' => $db->escape_string($title), 'template' => $db->escape_string($template_new), 'sid' => '-1', 'dateline' => TIME_NOW, 'version' => '1800');
			$db->insert_query('templates', $template);
		}

		// Creates settings for BAM. 

		$new_config = array();
		$setting_group = array(
			'name' => 'bam', 
			'title' => $db->escape_string($lang->bam_title),
			'description' => $db->escape_string($lang->bam_desc),
			'disporder' => $rows+3,
			'isdefault' => 0
		);
	
		$group['gid'] = $db->insert_query("settinggroups", $setting_group); // inserts new group for settings into the database. 

		$new_config[] = array(
			'name' => 'bam_enabled',
			'title' => $db->escape_string($lang->bam_enable),
			'description' => $db->escape_string($lang->bam_enable_desc),
			'optionscode' => 'yesno',
			'value' => '1',
			'disporder' => 1,
			'isdefault' => 1,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'bam_advanced_mode',
			'title' => $db->escape_string($lang->bam_advanced_mode),
			'description' => $db->escape_string($lang->bam_advanced_mode_desc),
			'optionscode' => 'onoff',
			'value' => '0',
			'disporder' => 2,
			'isdefault' => 1,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'bam_enable_dismissal',
			'title' => $db->escape_string($lang->bam_enable_dismissal),
			'description' => $db->escape_string($lang->bam_enable_dismissal_desc),
'optionscode' => 'select
1= '.$lang->bam_dismissal_savecookie.'
3= '.$lang->bam_dismissal_savecookie_useronly.'
2= '.$lang->bam_dismissal_closeonly.'
0= '.$lang->bam_dismissal_disable,
			'value' => '1',
			'disporder' => 3,
			'isdefault' => 1,
			'gid' => $group['gid']
		); // bad indentation intentional

		$new_config[] = array(
			'name' => 'bam_dismissal_days',
			'title' => $db->escape_string($lang->bam_dismissal_days),
			'description' => $db->escape_string($lang->bam_dismissal_days_desc),
			'optionscode' => 'text',
			'value' => '30',
			'disporder' => 5,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'bam_slidedown_enable',
			'title' => $db->escape_string($lang->bam_slidedown_enable),
			'description' => $db->escape_string($lang->bam_slidedown_enable_desc),
			'optionscode' => 'yesno',
			'value' => '1',
			'disporder' => 6,
			'isdefault' => 1,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'bam_date_enable',
			'title' => $db->escape_string($lang->bam_date_enable),
			'description' => $db->escape_string($lang->bam_date_desc),
			'optionscode' => 'yesno',
			'value' => '1',
			'disporder' => 7,
			'isdefault' => 1,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'bam_random',
			'title' => $db->escape_string($lang->bam_random_enable),
			'description' => $db->escape_string($lang->bam_random_desc),
			'optionscode' => 'onoff',
			'value' => '1',
			'disporder' => 8,
			'isdefault' => 1,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'bam_random_dismissal',
			'title' => $db->escape_string($lang->bam_random_dismissal),
			'description' => $db->escape_string($lang->bam_random_dismissal_desc),
			'optionscode' => 'onoff',
			'value' => '0',
			'disporder' => 9,
			'isdefault' => 1,
			'gid' => $group['gid']
		);
				
		$new_config[] = array(
			'name' => 'bam_random_max',
			'title' => $db->escape_string($lang->bam_random_max),
			'description' => $db->escape_string($lang->bam_random_max_desc),
			'optionscode' => 'text',
			'value' => '1',
			'disporder' => 10,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'bam_random_group',
			'title' => $db->escape_string($lang->bam_random_group),
			'description' => $db->escape_string($lang->bam_random_group_desc),
			'optionscode' => 'groupselect',
			'value' => '-1',
			'disporder' => 11,
			'isdefault' => 1,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'bam_index_page',
			'title' => $db->escape_string($lang->bam_index_page),
			'description' => $db->escape_string($lang->bam_index_page_desc),
			'optionscode' => 'text',
			'value' => 'index.php',
			'disporder' => 12,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'bam_custom_css',
			'title' => $db->escape_string($lang->bam_custom_css),
			'description' => $db->escape_string($lang->bam_custom_css_desc),
			'optionscode' => 'textarea',
			'value' => '/* Replace this field with any custom CSS classes. */',
			'disporder' => 13,
			'gid' => $group['gid']
		);

		$cookiePrefix = rand(1, 999999); 
		$new_config[] = array(
			'name' => 'bam_cookie_id_prefix',
			'title' => $db->escape_string($lang->bam_cookie_id_prefix),
			'description' => $db->escape_string($lang->bam_cookie_id_prefix_desc),
			'optionscode' => 'numeric',
			'value' => $cookiePrefix,
			'disporder' => 14,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'bam_compatibility_mode',
			'title' => $db->escape_string($lang->bam_compatibility_mode),
			'description' => $db->escape_string($lang->bam_compatibility_mode_desc),
			'optionscode' => 'onoff',
			'value' => '0',
			'disporder' => 15,
			'isdefault' => 1,
			'gid' => $group['gid']
		);

		// insert settings to the database. 

		foreach($new_config as $array => $setting) {
			$db->insert_query("settings", $setting);
		}
		rebuild_settings();
}

function bam_is_installed() {
	global $db;
	if($db->table_exists('bam')) {
		return true;
	}
	return false;
}

// Uninstallation removes templates and drops the database table. 
function bam_uninstall() {
	global $db;
	$info = bam_info();

	// Delete the table. 
	if($db->table_exists('bam')) {
		$db->drop_table('bam');
	}

	// Remove old templates. 
	$templates = array('bam_announcement', 'bam_announcement_container'); // remove templates
	foreach($templates as $template) {
		$db->delete_query('templates', "title = '{$template}'");
	}
	
	// Clear out old settings. 
	$query = $db->simple_select('settinggroups', 'gid', 'name = "bam"'); // remove settings
	$groupid = $db->fetch_field($query, 'gid');
	$db->delete_query('settings','gid = "'.$groupid.'"');
	$db->delete_query('settinggroups','gid = "'.$groupid.'"');
	rebuild_settings();	

}

// Activate: Create template modifications required for BAM to work. 
function bam_activate () {
	global $db;
	require MYBB_ROOT.'/inc/adminfunctions_templates.php';
	find_replace_templatesets('header', '#{\$awaitingusers}#', '{$awaitingusers} <!-- BAM -->{$bam_announcements}<!-- /BAM -->');
}

// Reverse template modifications. 
function bam_deactivate () {
	global $db;
	require MYBB_ROOT.'/inc/adminfunctions_templates.php';
	find_replace_templatesets('header', '#\<!--\sBAM\s--\>(.+)\<!--\s/BAM\s--\>#is', '', 0);
}

// This function manually checks if its database has been updated. 
// This is used to display the upgrade script link in the plugin's description if an update is required.

function bam_is_updated () {
	global $db;
	$query = $db->query("SHOW COLUMNS FROM ".TABLE_PREFIX."bam LIKE 'random';");
	if ($db->fetch_array($query)) {
		return true; 
	}
	return false; 
}

// This function checks if BAM is updated without the use of an additional query. 
// This does not work on the plugin info page, but saves a query everywhere else! 
function bam_is_updated_noquery () {
	global $mybb; 
	return (isset($mybb->settings['bam_advanced_mode']) && isset($mybb->settings['bam_enable_dismissal']));
}


// Primary BAM announcements function. Parses announcements on forum pages. 

function bam_announcements ($compatibility = null) {
	global $mybb, $db, $templates, $bam_announcements, $lang; //, $theme; //, $bam_announcement_container;

	require_once MYBB_ROOT.'/inc/class_parser.php';
	$parser = new postParser(); 
	
	// Set some variables that we use in the javascript to create the cookies. 
	$bam_cookie_expire_days = (int) $mybb->settings['bam_dismissal_days'];
	$bam_cookie_path = $mybb->settings['cookiepath'];

	// Determine whether BAM's settings allow HTML. New setting in BAM 2.0. Bug fix from BAM 1. 
	$allowHTML = null; 
	if ($mybb->settings['bam_advanced_mode'] == 1) {
		$allowHTML = "yes";
	}

	// Use the parser for what it does best. 
	// It supports BBcode, automatic newline to break codes, MyCode, and optionally, HTML. 
	$parser_options = array(
    		'allow_html' => $allowHTML,
    		'allow_mycode' => 'yes',
    		'allow_smilies' => 'yes',
    		'allow_imgcode' => 'yes',
    		'filter_badwords' => '',
    		'nl2br' => 'yes'
	);

	$class_select = array('green', 'yellow', 'red', 'blue', 'silver', 'magenta', 'orange'); // list of programmed BAM classes. 
	$query = $db->query("
		SELECT *
		FROM ".TABLE_PREFIX."bam
		ORDER BY pinned DESC, disporder ASC, PID ASC");

	
	$data = array();
	$count = 0;
	$announcement = '';
	$announcements = '';
	$unpinned_ids = array();
	$announcementCount = 0; // used to cache templates. 

	// Fetch announcements from database and render them. 
	while($querydata = $db->fetch_array($query)) {

		// Parse directives that can determine specific conditions for displaying announcments. 
		$tagParser = bam_build_directives($querydata['announcement']);

		// Only parse the announcement if it is allowed to be displayed. 
		if (bam_display_conditions($querydata, $tagParser[1])) {
			$announcement = $tagParser[0]; // announcement. 

			// Run announcements through the post parser to process BBcode, images, HTML (if enabled), etc. 
			$announcement = $parser->parse_message(html_entity_decode($announcement), $parser_options); 
			
			// Get announcement ID for cookies. Used for saving dismissed announcements. 
			$announcement_id = (int) $querydata['PID'];
			$bcprefix = (int) $mybb->settings['bam_cookie_id_prefix']; // Used to reset dismissals if BAM is reinstalled.

			// Make the announcement a link if it has a URL field defined.  
			if(!empty($querydata['link'])) {
				$announcement = '<a href=\''.htmlspecialchars($querydata['link'], ENT_QUOTES).'\'>'.htmlspecialchars($querydata['announcement'], ENT_QUOTES)."</a>";
			}

			$announcement = bam_parse_user_variables($announcement, $querydata['link']);
			$dismissClass = bam_build_dismiss_class($querydata);

			$display_close = $dismissClass[0];
			$bam_unsticky = $dismissClass[1];
			$announcementTemplate = 'bam_announcement';

			$class = 'bam_announcement ' . htmlspecialchars($querydata['class'], ENT_QUOTES); // parse class/style
			$forums = $querydata['forums']; // fetch forum list, if enabled. 

			$date = bam_parse_date($querydata); 
			
			// Save an array of unpinned announcements. This allows us to re-order and display these later without running another query. 
			
			$data[$count]['date'] = $date;
			$data[$count]['themesEnabled'] = $tagParser[1]['themesEnabled'];
			$data[$count]['languagesEnabled'] = $tagParser[1]['languagesEnabled'];
			$data[$count]['class'] = $class;
			$data[$count]['display_close'] = $display_close;
			$data[$count]['template'] = $tagParser[1]['template'];
			$data[$count]['forums'] = $forums; // list of forums enabled, if set. 
			$data[$count]['bam_unsticky'] = $bam_unsticky; 
			$data[$count]['announcement'] = $announcement; // Parsed text for the announcement. 
			$data[$count]['PID'] = (int) $announcement_id; // Used to create an element ID. Needed for javascript cookies.
			$data[$count]['additional_display_pages'] = $querydata['additional_display_pages']; // Additional functionality in BAM 2.0. Used for advanced mode.  
			$data[$count]['random'] = (int) $querydata['random'];	// - added functionality in BAM 2.0
			$data[$count]['global'] = (int) $querydata['global'];   // - added functionality in BAM 2.0 

			// Detect if BAM is running on BAM 1's database or on BAM 2's database. 
			// This allows BAM 2.0 to render announcements properly even if the database hasn't been migrated. 
			// This is required because BAM must be activated to upgrade. This prevents interuptions on the forum! 

			if (!bam_is_updated_noquery()) {
				if (($querydata['pinned'] == 0) && $mybb->settings['bam_random'] == 1) {
					$unpinned_ids[] = $count;
					// $total_unpinned++;
				}
				if(($querydata['pinned'] == 1) || ($mybb->settings['bam_random'] == 0)) {
					eval("\$announcements .= \"".$templates->get("bam_announcement")."\";");
				}
			}
			// Render announcements normally. BAM 2.0 has been properly installed and upgraded. 
			else {
				// Are we trying to render a random mode announcement? 
				if(($mybb->settings['bam_random'] == 1) && ($querydata['random'] == 1)) {
					$unpinned_ids[] = $count;	
				}
				// Are we rendering a normal announcement? 
				else {
						eval("\$announcements .= \"".$templates->get($tagParser[1]['template'])."\";");
				}
			}

			$count++; 
		}
	}

	// Remaining announcements that passed bam_display_conditions() are random. Shuffle these and select one randomly. 
	$count_unpinned = 0;
	shuffle($unpinned_ids); // place unpinned announcements into a random order. 
	if (bam_display_permissions($mybb->settings['bam_random_group'])) {
		foreach ($unpinned_ids as $ID) {
			// if (($count_unpinned >= $total_unpinned) || ($count_unpinned >= $mybb->settings['bam_random_max'])) {
			if (($count_unpinned >= count($unpinned_ids)) || ($count_unpinned >= $mybb->settings['bam_random_max'])) {
				break; 
			}
			$date = $data[$ID]['date'];
			$announcement = $data[$ID]['announcement'];
			$class = $data[$ID]['class'];
			$announcement_id = $data[$ID]['PID'];

			// handle whether random announcements can be closed: 

			if ($mybb->settings['bam_random_dismissal'] == 1) {
				$bam_unsticky = 'bam-unsticky';
				$display_close = 'bam-close-notification'; // alternative close function used in javascript. 
			} else {
				// Dismissals of random announcements are disabled. Make sure we don't display close button. 
				$bam_unsticky = ''; 
				$display_close = 'bam_nodismiss';
			}
			
			eval("\$announcements .= \"".$templates->get($data[$ID]['template'])."\";");
			
			$count_unpinned++;
		}
	}
	
	// Enable or disable jquery slidedown effect. 

	$slidedown = "";
	if ($mybb->settings['bam_slidedown_enable'] == 1) {
		$slidedown = "bam_slidedown";
	}
	$bam_custom_css = $mybb->settings['bam_custom_css']; 
	
	eval('$bam_announcements = "'.$templates->get('bam_announcement_container').'";');
	
	// Check if we are using the pre_output_page hook instead. 
	// This forces announcements into the page and guesses if it can't find the variable. 
	// This gives less control on where announcements get posted, but can get BAM working without further template modifications on themes where the activation modifications fail.
	if ($compatibility != null) {
		return $bam_announcements;
	}
}

// Compatibility function that uses pre_output_page hook. 

function bam_announcements_compatibility (&$page) {

	// Replace page's final output and add announcements. 
	$announcements = bam_announcements(1);
	$bam_page = strtr($page, array('<!-- BAM -->' => $announcements));
	
	// Check if the replacement failed. If so, try to guess on where to put announcements. 
	if ($bam_page == $page) {
		$bam_page = strtr($page, array('<!-- start: nav -->' => $announcements . ' <!-- start: nav -->')); 
	}
	return $bam_page; 
}

function bam_parse_date($querydata) {
	global $mybb; 

	if ($mybb->settings['bam_date_enable'] == 1) {
		// Technically, we should have some sort of plugin setting for the date since we aren't using the MyBB default, but to save space in announcements, this plugin doesn't display the year unless necessary. This solution seems to be working well enough for now. Perhaps a future version will "fix" this issue.  
		if (date("Y") != my_date ('Y', $querydata['date'])) { 	
			// Not the current year, display the year. 
			return ('('.my_date('F d, Y', htmlspecialchars($querydata['date'], ENT_QUOTES)).')');
		}	
		else { 
			// Current year, don't display year. 
			return ('('.my_date('F d', htmlspecialchars($querydata['date'], ENT_QUOTES)).')');
		}	
	}
	return ''; 
}


function bam_build_dismiss_class ($querydata) {
	global $mybb;
	// If the announcement is not stickied and dismissals are enabled, set whether dismissal closes the announcement permanently or temporarily.  
	// If the announcement is stickied, never allow dismissals.
	if (($querydata['pinned'] == 0) && (int) $mybb->settings['bam_enable_dismissal'] > 0) {
		$bam_unsticky = 'bam-unsticky';

		// Set dismissals are permanent. 
		if ((int) $mybb->settings['bam_enable_dismissal'] == 1) {
			$display_close = 'dismiss-notification';
		}

		// Set dismissals as temporary. When dismissed, the announcement returns on the next page load. 
		else if ((int) $mybb->settings['bam_enable_dismissal'] == 2){
			$display_close = 'bam-close-notification';
		}

		// BAM is set to dismiss with a cookie, but only if the user is logged in. This is the default setting.  
		else if ((int) $mybb->settings['bam_enable_dismissal'] == 3){
			if (!empty($mybb->user['uid'])) {
				$display_close = 'dismiss-notification'; // close and dismiss with cookie. 
			}
			else {
				$display_close = 'bam-close-notification'; // user is a guest. Close only. 
			}
		}
		// Invalid value defined in setting. Handle this by disabling dismissal.  
		else {
			$display_close = 'bam_nodismiss';				
		}
	
	// If the announcement is "sticky," never show the dismissal button. 
	} else {
		$display_close = 'bam_nodismiss';
		$bam_unsticky = '';
	}	

	return array($display_close, $bam_unsticky);
}

// This function checks the user's permissions, and determines if the user's group is in $display_groups
// Returns true to display the announcement. False if the user is not permitted to view it. 

function bam_display_permissions ($display_groups) {
	global $mybb;
	
	// No need to check for permissions if no groups are allowed. 
	if (empty($display_groups)) {
		return false; 
	}

	// No need to check for permissions if all groups are allowed. 
	if ($display_groups == '-1') {
		return true; 
	}

	// Create an array of all usergroups that the current user is a member of. 
	$usergroup = $mybb->user['usergroup'];
	$allowed = explode(',', $display_groups);
	$groups = array();
	$groups[0] = (int)$usergroup; 
	$add_groups = explode(',', $mybb->user['additionalgroups']);
	$count = 1;
	foreach($add_groups as $new_group) {
		$groups[$count] = $new_group;
		$count++;
	}

	// Check if the user is in a member of an allowed group for this announcement. Return True if permitted. 
	foreach ($allowed as $allowed_group) {
		if (in_array($allowed_group, $groups)) {
			return true;
		}
	}
	// User is not in a valid usergroup to view this announcement. Return false. 
	return false;
}

// Checks ALL display conditions for whether an announcement in $querydata, with directives $directives, should be displayed. 
// This calls checkAnnouncementDisplay for global/index/special page checks, and also checks usergroup permissions and directives

function bam_display_conditions ($querydata, $directives) {
	global $mybb;

	// We are running on BAM 1's database and templates. Do a legacy check for whether to display the announcement. 
	if (!bam_is_updated_noquery()) {
		if(bam_display_permissions($querydata['groups']) && (global_display($querydata['pinned']))) {
			return true; 
		}
		return false; 
	} 
	
	// We are running on a properly updated BAM 2 database and template set. Enable additional features. 
	else {
		if ($directives['disabled']) {
			return false;  
		}	
		// New in BAM 2.0: Random announcements are no longer rendered as normal announcements if random mode is disabled. 
		if(bam_display_permissions($querydata['groups']) && checkAnnouncementDisplay($querydata)) {
			// Check if there are theme tags or langage tags. 
			if (bamThemeEnabled($directives['themesEnabled']) && bamLanguageEnabled($directives['languagesEnabled'])) {
				return true; 
				
			}
		}
		return false; 
	}
}

// New in BAM 2.0. Tags are now supported to enable announcements only for specific themes and languages. 
// These preg_replace statements remove the tag itself once its value has been parsed. 
// We only attempt to check for directives if we have [@ in an announcement. Performance optimization. 

function bam_build_directives ($announcement) { 
	$themesEnabled = 0;
	$languagesEnabled = 0;
	$announcementTemplate = 'bam_announcement';
	$disabled = false; 
	
	if (strpos("-".$announcement, '[@')) {
		$themesEnabled = bamExplodeThemes($announcement);
		$languagesEnabled = bamExplodeLanguages($announcement);
		$announcement = preg_replace('/\[@themes:([a-zA-Z0-9 ,_]*)\]/', "", $announcement);	
		$announcement = preg_replace('/\[@languages:([a-zA-Z0-9 ,_]*)\]/', "", $announcement);
		
		// Parse a special directive that disables an announcement. Unofficial feature. 
		if (strpos("-".$announcement, '[@disabled]')) {
			$disabled = true;
		}	

		// Directive allows you to define a different template for this announcement. Useful if you need javascript in announcement. 
		if (strpos("-".$announcement, '[@template:')) { 
			$announcementTemplate = bamExplodeTemplates($announcement); 
			$announcement = preg_replace('/\[@template:([a-zA-Z0-9_ ]*)\]/', "", $announcement);	
		}
	}

	$directives = array(
		'themesEnabled' => $themesEnabled,
		'languagesEnabled' => $languagesEnabled,
		'disabled' => $disabled, 
		'template' => $announcementTemplate,
	);
	
	return array($announcement, $directives);
}

// Function replaces deprecated global_display() in BAM 1.0. 
// Checks if a specific announcement is enabled on the current page that the user is browsing. 

function checkAnnouncementDisplay($announcement) {
	global $mybb, $current_page;
 
	// Handle random mode announcements, which only display on the index page. 
	if ($announcement['random'] == 1) {
		if (isIndexPage($announcement)) {
			return true;
		} 
		return false;
	}

	// Check if the user has defined an alternative page. If so, run the check to see if this page is valid. 
	// If this alternative page is not valid, we don't display the page, regardless of whether it is global. 

	if (($announcement['additional_display_pages'] != null)) {
		return isAlternatePageValid($announcement); 
	}

	// Check if announcement is in forum-display mode. (global = 2)
	// If so, we need to check if this announcement is in a forum ID that is enabled. 
	else if (($announcement['forums'] != null)  && ($announcement['global'] == 2)) {
		
		// Check if all forums are enabled. If so, enable the announcement. 
		if (($announcement['forums'] == '*' || $announcement['forums'] == '-1') && ((int) $_GET['fid'] != null)) {
			return true; 
		}

		// User hasn't enabled announcement for every board. Check if the board we are on is in the list of enabled boards. 
		else {
			$explodedForums = explode(',', $announcement['forums']);
			if (in_array((int) $_GET['fid'], $explodedForums)) {
				return true; // This board is enabled.
			}
			else {
				return false; // This board isn't enabled. Return false. 
			}
		}
	}

	// We aren't on a custom alternative page or forum mode. So we will check if we are on the index page. 
	// With no alternative page set: Announcements are always displayed on the index page, regardless of whether they are global, random, or otherwise. 
	
	else if (isIndexPage($announcement)) {
		return true; // this is the index page. No need to check for global announcement settings. 
	}
	else if ($announcement['global'] == 1) {
		return true;
	}

	// This announcement can't be displayed under any conditions.
	// We aren't on the index, no forums match, the announcement isn't global, and we aren't on an alternative page. Return false. 
	else {
		return false; 
	}
}

// This function determines if the current page is considered an "index page" for the plugin. 
// New in BAM 2.0: You can now have multiple comma delimited values for the index page. 

function isIndexPage($otherPage=null) { 
	global $mybb, $current_page;

	if ($otherPage['additional_display_pages'] == null) {
		$indexPage = $mybb->settings['bam_index_page']; 
	} 

	// Get an array of all pages BAM considers the index page. BAM 2.0 now allows more than one page to be set as an index. 
	$explodedIndexPage = explode(',', $indexPage);
	$processed_indexValues = array_map('trim',$explodedIndexPage);

	if (in_array($current_page, $processed_indexValues)) {
		return true; 
	}
	return false; 	
}


// New in BAM 2.0: Determines if an announcement is set to display on the current page that the user has visited. 
// Only called if BAM is in advanced mode and the additional_url_parameters setting is set with a value.

function isAlternatePageValid($announcement) { 
	global $mybb, $current_page, $additional_page_parameters;

	// Developers: If you are using this plugin and your URL settings are not being accepted, you can add
	// new acceptable parameteres here. However, please be aware that this is a whitelist that is intended
	// to prevent unexpected or insecure behavior. This setting was explicitely ommitted on the ACP for 
	// this reason. Please be mindful and add parameters as needed, but do not remove the whitelist for your forum. 
	$additional_page_parameters = array('fid', 'action', 'uid', 'tid', 'gid');
	
	$explodedPages = explode(',', $announcement['additional_display_pages']);
	$processedPages = array_map('trim',$explodedPages);
	$acceptPage = false;

	// This parameter allows multiple URLs to be set. Check for each URL that is given. 
	foreach ($processedPages as $additional_display_page) {

		// This plugin explicitely parses the URL given by the announcement's settings to extract only the file name. 
		// This functionality should not be reverted. Otherwise, rogue URLs (index.php?fid=forumdisplay.php) could cause
		// this plugin to display on pages that it has not been designed to display on. 

		$url_parameters = parse_url($additional_display_page);
		$announcementFileName = basename($url_parameters['path']);

		// First, we check to see if we are on the correct PHP file/page (e.g. index.php, forumdisplay.php, etc.)
		if ($announcementFileName == $current_page) {
 
			// By default, we assume that we found the required URL parameters. We then check to see if any do not match. 
			$paramCheck = true;

			// Loop through each whitelisted parameter and check for mismatches. 
			foreach ($additional_page_parameters as $parameter) {	

				// We first check if the $_GET parameter we are currently checking exists on the page/URL the user is visiting. 
				// If it does, we check to see if it matches the additional_page parameter's value. 
				// MyBB->input sometimes sets these even if they don't exist in the URL, so we must use $_GET directly. 

				if (isset($_GET[$parameter])) {
					
					// We found the parameter in the URL of this page. Get its value.  
					$paramValue = $_GET[$parameter]; 	
		
					// Next, we must check if the parameter was defined in the announcement's settings. 
					// If so, we check to see if it matches the URL that we are on. 
					// If it is not found, the announcement does not care about additional parameters that may exist. We ignore it.

					if (strpos($additional_display_page, $parameter)) {
						$paramSearchString = "$parameter=" . $paramValue;
						 
						if (!strpos($additional_display_page, $paramSearchString)) {
							$paramCheck = false; 
						}
					}
				}  
				
				// Check to see if an unset parameter is a part of the additional_display_pages setting. If so, reject the announcement. 
				else {
					
					// Scan additional_display_pages to see if the URL parameter exists in the setting. If so, reject the announcement. 
					$unsetURLParam = $parameter . '=';
					if (strstr($additional_display_page, $unsetURLParam, false)) {
						$paramCheck = false; 
					}
				}
			}

			// Check to see if we found a valid match within the announcement's settings for this page. If not, keep checking. 
			if ($paramCheck == true) {
				$acceptPage = true;
				break; // We found a valid page. Not necessary to keep checking other pages.  
			}
		}
	} // End loop for URLs. 

	return $acceptPage;
}

/* VARIABLES, THEMES, DIRECTIVES, LANGUAGES */ 

// Returns whether the user is using a theme that is in $themes.
// Themes list is generated by bamExplodeThemes, which checks for the [@themes:1,2,3] tag.  

function bamThemeEnabled($themes) {
	global $mybb, $cache; 
	// return true;
	if (!isset($mybb->user['style'])) {
		$userTheme = $cache->read('default_theme');
		$userTheme = (int) $userTheme['tid'];
	}
	else {
		$userTheme = $mybb->user['style'];
	}
	if ($themes != null) {
		if (in_array($userTheme, $themes)) {
			return true;
			
		}
		return false;
	} 
	return true;
}

// Search the announcement's text for a theme tag. If so, return an array with a list of themes. 

function bamExplodeThemes($announcementText) { 
	$matched_themes_raw = "";
	if(preg_match('/\[@themes:([a-zA-Z0-9 ,_]*)\]/', $announcementText, $matched_themes_raw)) {
		$matched_themes_raw = strtr($matched_themes_raw[0], array('[@themes:' => '', ']' => ''));
		return array_map('trim',explode(',', $matched_themes_raw));
	}
	return null;
}


// Returns whether the user is using a language that is in $languages.
// Themes list is generated by bamExplodeThemes, which checks for the [@themes:english, espanol, etc] tag.  

function bamLanguageEnabled($languages) {
	global $mybb; 
	// $userLanguage = $mybb->user['language'];

	// If the user is on the default language and this language is set for the announcement, display the announcement. 
	if (!isset($mybb->user['language']) || $mybb->user['language'] == null) {
		if ($languages != null) {
			if (in_array($mybb->settings['bblanguage'], $languages)) {
				return true; 
			}
			// user is on default language, but announcement specifies a different language. 
			return false;
		}
	}

	// Check if the user's board language matches an enabled language in the announcement
	if ($languages != null) {
		if (in_array($mybb->user['language'], $languages)) {
			return true;
		}
		return false;
	}
	return true; 
}

// Search the announcement's text for a theme tag. If so, return an array with a list of themes. 

function bamExplodeLanguages($announcementText) { 
	$matched_languages_raw = "";
	if(preg_match('/\[@languages:([a-zA-Z0-9 ,_]*)\]/', $announcementText, $matched_languages_raw)) {
		$matched_languages_raw = strtr($matched_languages_raw[0], array('[@languages:' => '', ']' => ''));
		$explodedLanguages = explode(',', $matched_languages_raw);
		return array_map('trim',$explodedLanguages);
	}
	return null;
}

// Search the announcement's text for a templates tag. If so, return an array with a single template. 

function bamExplodeTemplates($announcementText) { 
	$matched_template_raw = '';
	
	if(preg_match('/\[@template:([a-zA-Z0-9 _]*)\]/', $announcementText, $matched_template_raw)) {
		$matched_template = strtr($matched_template_raw[0], array('[@template:' => '', ']' => ''));

		// Remove non alphanumeric characters for security. 
		return trim(preg_replace( '/[\W]/', '', $matched_template));
	}
	return null;
}

// Parses {username} and {newmember*} variables. 

function bam_parse_user_variables ($announcement, $link) {
	global $lang, $mybb;

	// Parse username and newmember variables. 
	// We only need to check for variables if we have { in the announcement. Performance optimizaiton.  

	if (strpos('-'.$announcement, '{')) {
		// Parse the {$username} variable within announcements. Parses to "Guest" if the user is not logged in. 
		if(!empty($mybb->user['uid'])) {
			$username = htmlspecialchars($mybb->user['username'], ENT_QUOTES); // allows {$username} to be replaced with the user's username. 
		}
		else {
			$username = $lang->guest; // user is not logged in. Parse as "Guest" instead. 
		}

		if (strpos("-" . $announcement, "{newestmember")) { // Added character at beginning before searching to fix bug. (Allows strpos to return true even if the tag begins at the first character)
			$newUser = getNewestMember(); 
			$announcement = strtr($announcement, array('{newestmember}' => htmlspecialchars($newUser['username'], ENT_QUOTES)));			$announcement = strtr($announcement, array('{newestmember_uid}' => (int) $newUser['uid']));
			
			// Prevent BAM from trying to nest a link inside of a link. 
			if (!$link) {
				$announcement = strtr($announcement, array('{newestmember_link}' => "<a href='member.php?action=profile&uid=".(int) $newUser['uid']."'>".htmlspecialchars($newUser['username'])."</a>"));			}
			else {
				$announcement = strtr($announcement, array('{newestmember_link}' => htmlspecialchars($newUser['username'], ENT_QUOTES)));
			}
		}

		// Parse additional variables. 
		$announcement = strtr($announcement, array('{username}' => $username));
		$announcement = parseThreadVariables($announcement);	// parses {threadreplies} to thread reply count. 
	}	
	return $announcement;	
}

// This function is only called on showthread.php, and parses some extra variables. 
// Currently, {threadreplies} and {countingthread} are parsed. These are experimental, but work as expected.  

function parseThreadVariables($announcementText) {
	global $current_page, $mybb; 

	// Check to make sure we are on showthread.php and we have a thread to display. 
	if ($current_page == 'showthread.php' && (int) $_GET['tid'] != null) {

		// Get the thread from the database. 
		$threadID = (int) $mybb->input['tid'];
		$thread = get_thread($threadID);
		
		// Parse number of replies in thread. Primarily useful for forum games. 
		if (strpos("-".$announcementText, '{threadreplies}')) {
			return strtr($announcementText, array('{threadreplies}' => number_format((int) $thread['replies'])));
		}

		// Parse the counting thread. This is similar to above, but attempts to correct invalid counts.
		else if (strpos("-".$announcementText, '{countingthread}')) {

			// We are going to try to determine the correct count for the counting thread based on previous replies. 
			// This is an easter egg feature! Very useful for forum games where users frequently get off count. 

			$threadData = getThreadData($threadID);
			$arrayofNumbers = array();
			$maxLen = 0;
			$leadingNumber = 0;

			// We need to extract the number from each post generated from the getThreadData query. 
			// If a number doesn't exist, it simply gets put in as a 0 in the array. 
			// This function depends on counts being in every post. It can handle one missing count, but behaves unpredictably if more are missing. 

			foreach ($threadData as $post) { 
				$arrayofNumbers[] = parseForumGameCounter($post);
			}
			
			// Next, we must explode these into arrays of consecutive numbers. 
			$results = getConsecutiveNumbers($arrayofNumbers);
			foreach ($results as $row) {

				// We must fetch the largest set of consecutive numbers from recent posts. This will serve as the basis for the correct count. 
				if (count($row) > $maxLen) {
					$maxLen = count($row);
					$leadingRow = $row; 
				}
			}

			// Get the correct count based on offsets from the largest consecutive set. Parse the variable for the announcement. 
			$leadingKey = array_search($leadingRow[0], $arrayofNumbers);
			$numPostsAway = count($arrayofNumbers) - $leadingKey; 
			$finalValue = number_format((int) ($arrayofNumbers[$leadingKey] + $leadingKey));
			return strtr($announcementText, array('{countingthread}' => $finalValue));
		}
	}
	
	// No replacements. Return the announcement unchanged. 
	return $announcementText;
}


/* HELPER FUNCTIONS */

function getNewestMember() {
    global $db;
    $querydata = $db->fetch_array($db->query('SELECT uid FROM mybb_users ORDER BY uid DESC LIMIT 1'));
    return get_user($querydata['uid']);
}

// Credit: https://stackoverflow.com/questions/28614124/php-number-of-consecutive-elements-in-array

function getConsecutiveNumbers($array) {

	// This function creates a multidimensional array of lists of consecutive numbers from the input $array. 
	// This can be used to determine the correct count in counting threads, and to correct the wrong count being posted. 

	$array = array_unique($array);
	$previous = null; 
	$result = array();
	$consecutiveArray = array();

	// Get consecutive sequences where the next number is exactly 1 less than the previous number. 
	foreach($array as $number) {
		if ($number == $previous - 1) {
			$consecutiveArray[] = $number;
		} else {
			$result[] = $consecutiveArray;
			$consecutiveArray = array($number);
		}
		$previous = $number;
	}
	$result[] = $consecutiveArray;
	return $result;
}

function parseForumGameCounter($post) {
	// This function extracts a number/count from a post in counting threads. It returns the number found, or 0 if not found. 
	$match = "";

	preg_match ('/([0-9]+)/', $post['message'], $match);
	if ($match[0] != 0 ) {
		return (int) $match[0];
	}
	return 0;
}


// Helper function that returns the thread data. Used for parseThreadVariables (above)
function getThreadData($threadID) {
    global $db;
    $tid = (int) $threadID; 

	// Get the most recent posts from the database by thread ID.  

    return $db->query("
    SELECT p.message, p.tid, p.dateline
    FROM ".TABLE_PREFIX."posts p WHERE p.tid='$tid'
    ORDER BY p.dateline DESC LIMIT 0,50");
}


/* ADMIN CP HOOKS */

function bam_config_menu (&$sub_menu) {
	global $lang;
	$lang->load("bam");
	$sub_menu[] = array(
		"id" => "bam",
		"title" => $lang->bam_announcements_menu,
		"link" => "index.php?module=config-bam"
	); // create memnu link in ACP 
}	

// Hook for ACP menu. 
function bam_confighandler(&$actions) {
	$actions['bam'] = array('active' => 'bam', 'file' => 'bam.php');
	return $actions;
}

// Legacy function that is not used in BAM 2.0. Only used if BAM 2.0 is uploaded to a server and the upgrade script has not run. 
// This allows BAM 2.0 to properly display old BAM 1 announcements before they are migrated. 
// See comments in inc/plugins/bam_upgrade/bam_upgrade.php for more info on how this works. 

function global_display($pinned) {
	// echo "<br /> I'm active.";
	global $mybb, $current_page;
	if ($current_page == $mybb->settings['bam_index_page']) {
		return true; // this is the index page. No need to check for global announcement settings. 
	}

	if ($mybb->settings['bam_global'] == 'global_all') {
		return true;
	}
	else if (($mybb->settings['bam_global'] == 'global_pinned') && ($pinned == "1")) {
		return true; 
	}
	else {
		return false; 
	}
}


// Thank you for using, developing for, and viewing BAM's source. If you have any questions or would like to contribute,
// please send me (Darth-Apple) a message on github or on the MyBB community forums!
// Regards, 
// -Darth Apple
