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
global $mybb;

global $templatelist; 
$templatelist .= 'bam_announcement, bam_announcement_container';

if(!defined("IN_MYBB")) {
	die("Hacking attempt detected. Server responded with 403. "); // direct access to this file not allowed. 
}

if ($mybb->settings['bam_enabled'] == 1) {
	$plugins->add_hook("global_start", "bam_announcements"); // don't load announcements unless the plugin is enabled. 
}

$plugins->add_hook("admin_config_menu", "bam_config_menu");
$plugins->add_hook("admin_config_action_handler", "bam_confighandler");

function bam_info() {
	global $lang;
	$lang->load('bam');
	return array(
		'name'			=> $lang->bam_title,
		'description'	=> $lang->bam_desc,
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
  				announcement varchar(1024) NOT NULL default '',				
				class varchar(40) NOT NULL default 'yellow',
				link varchar(160) default '',
				active int unsigned NOT NULL default 1,
				disporder int NOT NULL default 1,
				groups varchar(128) default '1, 2, 3, 4, 5, 6',
				date int(10) NOT NULL,
				pinned INT UNSIGNED DEFAULT 0,
				`global` INT UNSIGNED DEFAULT 0, 
				`random` INT UNSIGNED DEFAULT 0,
				additional_display_pages VARCHAR(512) DEFAULT NULL,
  				PRIMARY KEY (PID)
				) ENGINE=MyISAM
				".$db->build_create_table_collation().";"

				// ALTER TABLE `mybb_bam` ADD `display_mode` SET('global', 'index', 'special') NOT NULL DEFAULT 'index' //
				// AFTER `pinned`, // 
				// ADD `israndom` TINYINT NULL DEFAULT NULL AFTER `display_mode`, 
				// ADD `additional_display_pages` VARCHAR(256) NULL DEFAULT NULL AFTER `israndom`;
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
		// stylesheets or JS scripts, the trade off for good compatibility and maintainability is worthwhile. 
		//
		// Furthermore, because these are global templates, they will be compatible with any theme, regardless of whether 
		// the theme was installed after BAM was installed. In other words, compatibility is all-around much improved this way. 

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
		{$bam_custom_css}
	</style>
	
	<!-- Don\'t remove this. Needed for handling announcement dismissals. --> 
	<script>
		
			// Allow me to give credit. This was great:  https://lifeofadesigner.com/javascript/hide-dismissed-notifications-with-jquery-and-cookies
		
		$(document).ready(function () {
		  //alert(GetCookie("dismissed-notifications"));
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
		  // date.setMonth(date.getMonth()+1);
		  date.setTime(date.getTime() + ({$bam_cookie_expire_days} * 24 * 60 * 60 * 1000));
		  document.cookie += ("; expires=" + date.toUTCString()); 
		}
	
		function GetCookie(sName)
		{
		  var aCookie = document.cookie.split("; ");
		  for (var i=0; i < aCookie.length; i++)
		  {
			var aCrumb = aCookie[i].split("=");
			if (sName == aCrumb[0]) 
			  return unescape(aCrumb[1]);
		  }
		  return null;
		}
		});
	
	</script>
	
	<div class="bam_announcements">{$announcements}</div>';
		

		// Create the BAM announcement template used for each individual announcement. 

		$template['bam_announcement'] = '<p class="{$bam_unsticky} {$class}" id="announcement-{$announcement_id}">{$announcement} <span class="bam_date">{$date}</span>
		<span class=\'close_bam_announcement {$display_close}\'>x</span>
</p>'; 
	
		// Insert the templates into the database. 
		
		foreach($template as $title => $template_new){
			$template = array('title' => $db->escape_string($title), 'template' => $db->escape_string($template_new), 'sid' => '-1', 'dateline' => TIME_NOW, 'version' => '1800');
			$db->insert_query('templates', $template);
		}

		// Creates settings for BAM. 

		$setting_group = array(
			'name' => 'bam', 
			'title' => $db->escape_string($lang->bam_title),
			'description' => $db->escape_string($lang->bam_desc),
			'disporder' => $rows+3,
			'isdefault' => 0
		);
	
		$group['gid'] = $db->insert_query("settinggroups", $setting_group); // inserts new group for settings into the database. 

		$new_config = array();
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
/*
		$new_config[] = array(
			'name' => 'bam_enable_dismissal',
			'title' => $db->escape_string($lang->bam_enable_dismissal),
			'description' => $db->escape_string($lang->bam_enable_dismissal_desc),
			'optionscode' => 'yesno',
			'value' => '1',
			'disporder' => 2,
			'isdefault' => 1,
			'gid' => $group['gid']
		); */

/*
		$new_config[] = array(
			'name' => 'bam_guest_dismissal_enable',
			'title' => $db->escape_string($lang->bam_guest_dismissal_enable),
			'description' => $db->escape_string($lang->bam_guest_dismissal_enable_desc),
			'optionscode' => 'yesno',
			'value' => '0',
			'disporder' => 7,
			'isdefault' => 1,
			'gid' => $group['gid']
		); */


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

		/*$new_config[] = array(
			'name' => 'bam_global',
			'title' => $db->escape_string($lang->bam_global),
			'description' => $db->escape_string($lang->bam_global_desc),
'optionscode' => 'select
= '.$lang->bam_global_disable.'
global_pinned= '.$lang->bam_global_pinned.'
global_all= '.$lang->bam_global_all,
			'value' => 'global_none',
			'disporder' => 7,
			'isdefault' => 1,
			'gid' => $group['gid']
		); // bad indentation intentional*/
		
		$new_config[] = array(
			'name' => 'bam_date_enable',
			'title' => $db->escape_string($lang->bam_date_enable),
			'description' => $db->escape_string($lang->bam_date_desc),
			'optionscode' => 'yesno',
			'value' => '1',
			'disporder' => 6,
			'isdefault' => 1,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'bam_random',
			'title' => $db->escape_string($lang->bam_random_enable),
			'description' => $db->escape_string($lang->bam_random_desc),
			'optionscode' => 'onoff',
			'value' => '0',
			'disporder' => 7,
			'isdefault' => 1,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'bam_random_dismissal',
			'title' => $db->escape_string($lang->bam_random_dismissal),
			'description' => $db->escape_string($lang->bam_random_dismissal_desc),
			'optionscode' => 'onoff',
			'value' => '0',
			'disporder' => 8,
			'isdefault' => 1,
			'gid' => $group['gid']
		);
				
		$new_config[] = array(
			'name' => 'bam_random_max',
			'title' => $db->escape_string($lang->bam_random_max),
			'description' => $db->escape_string($lang->bam_random_max_desc),
			'optionscode' => 'text',
			'value' => '1',
			'disporder' => 9,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'bam_random_group',
			'title' => $db->escape_string($lang->bam_random_group),
			'description' => $db->escape_string($lang->bam_random_group_desc),
			'optionscode' => 'groupselect',
			'value' => '-1',
			'disporder' => 10,
			'isdefault' => 1,
			'gid' => $group['gid']
		);
		
		$new_config[] = array(
			'name' => 'bam_index_page',
			'title' => $db->escape_string($lang->bam_index_page),
			'description' => $db->escape_string($lang->bam_index_page_desc),
			'optionscode' => 'text',
			'value' => 'index.php',
			'disporder' => 11,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'bam_custom_css',
			'title' => $db->escape_string($lang->bam_custom_css),
			'description' => $db->escape_string($lang->bam_custom_css_desc),
			'optionscode' => 'textarea',
			'value' => '/* Replace this field with any custom CSS classes. */',
			'disporder' => 12,
			'gid' => $group['gid']
		);

		// insert settings to the database. 

		foreach($new_config as $array => $setting) {
			$db->insert_query("settings", $setting);
		}
		rebuild_settings();

}

function bam_is_installed()
{
	global $db;
	if($db->table_exists('bam'))
	{
		return true;
	}
	return false;
}

// Uninstallation removes templates and drops the database table. 
function bam_uninstall()
{
	global $db;
	$info = bam_info();
	if($db->table_exists('bam'))
	{
		$db->drop_table('bam');
	}

	$templates = array('bam_announcement', 'bam_announcement_container'); // remove templates
	foreach($templates as $template) {
		$db->delete_query('templates', "title = '{$template}'");
	}
	
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
	find_replace_templatesets('header', '#{\$awaitingusers}#', '{$awaitingusers} <!-- BAM -->{$bam_announcements} {$bam_announcements_random}<!-- /BAM -->');
}

// Reverse template modifications. 
function bam_deactivate () {
	global $db;
	require MYBB_ROOT.'/inc/adminfunctions_templates.php';
	find_replace_templatesets('header', '#\<!--\sBAM\s--\>(.+)\<!--\s/BAM\s--\>#is', '', 0);

}

// Primary BAM announcements function. Parses announcements on forum pages. 
function bam_announcements () {
	global $mybb, $db, $templates, $bam_announcements, $lang, $theme;
	// $lang->load("global");

	require_once MYBB_ROOT."/inc/class_parser.php";
	$parser = new postParser(); 
	
	// Set some variables that we use in the javascript to create the cookies. 
	// Cookies are used to save dismissed announcements so that they aren't loaded again.
	// Yes, I know. "Cookies are bad." But they work great for this. Otherwise, the forum's database would grow enourmous storing these dismissed announcements. 

	$bam_cookie_expire_days = (int) $mybb->settings['bam_dismissal_days'];
	$bam_cookie_path = $mybb->settings['cookiepath'];

	// In advanced mode, HTML is allowed. This allows administrators to have more control over the content of their announcements. 

	$allowHTML = "no"; 
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
    		'filter_badwords' => 'no',
    		'nl2br' => 'yes'
	);

	$class_select = array('green', 'yellow', 'red', 'blue', 'silver', 'magenta'); // list of programmed BAM classes. 

	$query = $db->query("
		SELECT *
		FROM ".TABLE_PREFIX."bam
		ORDER BY pinned DESC, disporder ASC, PID ASC");

	
	$data = array();
	$count = 0;
	$total_unpinned = 0; 
	$announcement = '';
	$announcements = '';
	$unpinned_ids = array();
	$count_unpinned = 0;

	// Initialize some javascript variables. 
	// This is set for an announcement that can be dismissed, and hides the dismiss button. 

	$display_close = "dismiss-notification";

	// This is a class that MUST be in the announcement's <p> tag for dismissal to work. On stickied announcements, this class 
	// is not included in the <p> tag to make it harder for a CSS bug and an accidentally displayed close button to actually be able
	// to dismiss the announcement. 

	$bam_unsticky = "bam-unsticky";

	// Fetch announcements from database and render them. 
	while($querydata = $db->fetch_array($query)) {

		// Get announcement ID for cookies. Used for saving dismissed announcements. 
		$announcement_id = (int) $querydata['PID'];

		// This feature is deprecated (not generally useful). It is still available with advanced mode, and therefore still implemented. 
		if(!empty($querydata['link'])) {
			$announcement = "[url=".$querydata['link']."]".$querydata['announcement']."[/url]";
		}
		else {
			// Parse the {$username} variable within announcements. Parses to "Guest" if the user is not logged in. 
			if(!empty($mybb->user['uid'])) {
			          $username = $mybb->user['username']; // allows {$username} to be replaced with the user's username. 
			}
			else {
				$username = $lang->guest; // user is not logged in. Parse as "Guest" instead. 
			}
			
			$announcement = str_replace('{$username}', $username, html_entity_decode($querydata['announcement']));
		}

		
		// If the announcement is stickied and dismissals are enabled, set whether dismissal closes the announcement permanently or temporarily.  
		// If the announcement is stickied, never allow dismissals.

		if (($querydata['pinned'] == 0) && (int) $mybb->settings['bam_enable_dismissal'] > 0) {
			$bam_unsticky = "bam-unsticky";

			// Set dismissals are permanent. 
			if ((int) $mybb->settings['bam_enable_dismissal'] == 1) {
				$display_close = "dissmiss-notification";
			}

			// Set dismissals as temporary. When dismissed, the announcement returns on the next page load. 
			else if ((int) $mybb->settings['bam_enable_dismissal'] == 2){
				$display_close = "bam-close-notification";
			}

			// BAM is set to dismiss with a cookie, but only if the user is logged in. This is the default setting.  
			else if ((int) $mybb->settings['bam_enable_dismissal'] == 3){
				if (!empty($mybb->user['uid'])) {
					$display_close = "dismiss-notification"; // close and dismiss with cookie. 
				}
				else {
					$display_close = "bam-close-notification"; // user is a guest. Close only. 
				}
			}
			// Invalid value defined in setting. Handle this by disabling dismissal.  
			else {
				$display_close = "bam_nodismiss";				
			}
		
		// If the announcement is "sticky," never show the dismissal button. 
		} else {
			$display_close = "bam_nodismiss";
			$bam_unsticky = "";
		}

		// New in BAM 2.0. Tags are now supported to enable announcements only for specific themes and languages. See documentation for details. 
		$themesEnabled = bamExplodeThemes($announcement);
		$languagesEnabled = bamExplodeLanguages($announcement);
		$announcement = preg_replace('/\[@themes:([a-zA-Z0-9_]*)\]/', "", $announcement);	
		$announcement = preg_replace('/\[@languages:([a-zA-Z0-9_]*)\]/', "", $announcement);		

		// Run announcements through the post parser to process BBcode, images, HTML (advanced mode), etc. 
		$announcement = $parser->parse_message($announcement, $parser_options); 
		$class = "bam_announcement " . htmlspecialchars($querydata['class']); // parse class/style

		if ($mybb->settings['bam_date_enable'] == 1) {
			// Technically, we should have some sort of plugin setting for the date since we aren't using the MyBB default, but to save space in announcements, this plugin doesn't display the year unless necessary. This solution seems to be working well enough for now. Perhaps a future version will "fix" this issue.  
			if (date("Y") != my_date ('Y', $querydata['date'])) { 	
				// Not the current year, display the year. 
				$date = '('.my_date('F d, Y', $querydata['date']).')';
			}	
			else { 
				// Current year, don't display year. 
				$date = '('.my_date('F d', $querydata['date']).')';
			}	
		}
		else {
			$date = null; 
		}
		
		// Save an array of unpinned announcements. This allows us to re-order and display these later without running another query. 
		$data[$count]['date'] = $date;
		$data[$count]['themesEnabled'] = $themesEnabled;
		$data[$count]['languagesEnabled'] = $themesEnabled;
		$data[$count]['class'] = $class;
		$data[$count]['display_close'] = $display_close;
		$data[$count]['bam_unsticky'] = $bam_unsticky; 
		$data[$count]['announcement'] = $announcement; // Parsed text for the announcement. 
		$data[$count]['PID'] = (int) $announcement_id; // Used to create an element ID. Needed for javascript cookies.
		$data[$count]['additional_display_pages'] = $querydata['additional_display_pages']; // Additional functionality in BAM 2.0. Used for advanced mode.  
		$data[$count]['random'] = (int) $querydata['random'];	// - added functionality in BAM 2.0
		$data[$count]['global'] = (int) $querydata['global'];   // - added functionality in BAM 2.0 

		// Random mode functionality. 
		if(($mybb->settings['bam_random'] == 1) && ($querydata['random'] == 1) && (bam_display_permissions($querydata['groups'])) && (checkAnnouncementDisplay($data[$count]))) {
			// This is a random announcement. Wait to render these until after standard announcements are displayed. 
			$unpinned_ids[] = $count;
			$total_unpinned++;	
		}

		// New in BAM 2.0: Random announcements are no longer rendered as normal announcements if random mode is disabled. 
		if((($querydata['random'] == 0) && (bam_display_permissions($querydata['groups']))) && (checkAnnouncementDisplay($data[$count]))) {
			
			// If the announcement isn't random, we need to check if the theme and language is enabled. If so, render. 
			if (bamThemeEnabled($data[$count]['themesEnabled']) && bamLanguageEnabled($data[$count]['languagesEnabled'])) {
				eval("\$announcements .= \"".$templates->get("bam_announcement")."\";");
			}
		}
		$count++; 
	}

	shuffle($unpinned_ids); // place unpinned announcements into a random order. 
	if (bam_display_permissions($mybb->settings['bam_random_group'])) {
		foreach ($unpinned_ids as $ID) {
			if (($count_unpinned >= $total_unpinned) || ($count_unpinned >= $mybb->settings['bam_random_max'])) {
				break; 
			}
			$date = $data[$ID]['date'];
			$announcement = $data[$ID]['announcement'];
			$class = $data[$ID]['class'];
			$announcement_id = $data[$ID]['PID'];

			// handle whether random announcements can be closed: 

			if ($mybb->settings['bam_random_dismissal'] == 1) {
				$bam_unsticky = "bam-unsticky";
				$display_close = "bam-close-notification"; // alternative close function used in javascript. 
			} else {
				// Dismissals of random announcements are disabled. Make sure we don't display close button. 
				$bam_unsticky = ""; 
				$display_close = "bam_nodismiss";
			}

			eval("\$announcements .= \"".$templates->get("bam_announcement")."\";");
			$count_unpinned++;
		}
	}

	$bam_custom_css = $mybb->settings['bam_custom_css']; 
	eval("\$bam_announcements = \"".$templates->get("bam_announcement_container")."\";");
}


function bam_config_menu (&$sub_menu) {
	// create menu link in ACP
	global $lang;
	$lang->load("bam");
	$sub_menu[] = array(
		"id" => "bam",
		"title" => $lang->bam_announcements_menu,
		"link" => "index.php?module=config-bam"
	);
}	

function bam_confighandler(&$actions) {
	// direct ACP request to correct file. 
	$actions['bam'] = array('active' => 'bam', 'file' => 'bam.php');
	return $actions;
}


// Returns whether the user is using a theme that is in $themes.
// Themes list is generated by bamExplodeThemes, which checks for the [@themes:1,2,3] tag.  

function bamThemeEnabled($themes) {
	global $mybb; 
	$userTheme = $mybb->user['style'];
	if ($themes != null) {
		if (in_array($userTheme, $themes)) {
			return true;
		}
		else {
			return false;
		}
	}
	else { 
		return true;
	}
}

// Search the announcement's text for a theme tag. If so, return an array with a list of themes. 
function bamExplodeThemes($announcementText) { 
	$matched_themes_raw = "";
	if(preg_match('/\[@themes:([a-zA-Z0-9_]*)\]/', $announcementText, $matched_themes_raw)) {
		// echo "<br />Theme selector found: " . $matched_themes[0] . "<br />";
		$matched_themes_raw = str_replace("[@themes:", "", $matched_themes_raw[0]);
		$matched_themes_raw = str_replace("]", "", $matched_themes_raw);
		$explodedThemes = explode(',', $matched_themes_raw);
		$processedThemes = array_map('trim',$explodedThemes);
		return $processedThemes;
	}
	return null;
}


// Returns whether the user is using a language that is in $languages.
// Themes list is generated by bamExplodeThemes, which checks for the [@themes:1,2,3] tag.  

function bamLanguageEnabled($languages) {
	global $mybb; 
	$userLanguage = $mybb->user['language'];
	if ($tlanguage != null) {
		if (in_array($userLanguage, $languages)) {
			return true;
		}
		else {
			return false;
		}
	}
	else { 
		return true;
	}
}

// Search the announcement's text for a theme tag. If so, return an array with a list of themes. 
function bamExplodeLanguages($announcementText) { 
	$matched_languages_raw = "";
	if(preg_match('/\[@languages:([a-zA-Z0-9_]*)\]/', $announcementText, $matched_languages_raw)) {
		// echo "<br />Theme selector found: " . $matched_themes[0] . "<br />";
		$matched_languages_raw = str_replace("[@themes:", "", $matched_languages_raw[0]);
		$matched_languages_raw = str_replace("]", "", $matched_languages_raw);
		$explodedLanguages = explode(',', $matched_languages_raw);
		$processedLanguages = array_map('trim',$explodedlanguages);
		return $processedLanguages;
	}
	return null;
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
	if ($display_groups == "-1") {
		return true; 
	}

	// Create an array of all usergroups that the current user is a member of. 
	$usergroup = $mybb->user['usergroup'];
	$allowed = explode(",", $display_groups);
	$groups = array();
	$groups[0] = (int)$usergroup; 
	$add_groups = explode(",", $mybb->user['additionalgroups']);
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

// Function replaces deprecated global_display() in BAM 1.0. 
// Checks if a specific announcement is enabled on the current page that the user is browsing. 

function checkAnnouncementDisplay($announcement) {
	global $mybb, $current_page;
 
	// Check if the user has defined an alternative page. If so, run the check to see if this page is valid. 
	// If this alternative page is not valid, we don't display the page, regardless of whether it is global. 

	if (($mybb->settings['bam_advanced_mode'] == 1) && ($announcement['additional_display_pages'] != null)) {
		return isAlternatePageValid($announcement); 
	}

	// The announcement has a special page, but advanced mode is disabled. Don't display this announcement. 
	else if (($mybb->settings['bam_advanced_mode'] == 0) && ($announcement['additional_display_pages'] != null)) {
		return false;
	}

	// We aren't on a custom alternative page. So we will check if we are on a page that BAM is set to consider the index page. 
	// With no alternative page set: Announcements are always displayed on the index page, regardless of whether they are global, random, or otherwise. 
	else if (isIndexPage($announcement)) {
		return true; // this is the index page. No need to check for global announcement settings. 
	}
	else if ($announcement['global'] == 1) {
		return true;
	}
	// This announcement can't be displayed under any conditions.
	// We aren't on the index, the announcement isn't global, and we aren't on an alternative page. Return false. 
	else {
		return false; 
	}
}

// This function determines if the current page is considered an "index page" for the plugin. 
// New in BAM 2.0: You can now have multiple comma delimited values for the index page. 

function isIndexPage($otherPage=null) { 
	global $mybb, $current_page;

	if ($otherPage['additional_display_pages'] == null) {
		// echo "<br />Announcement: " + $otherPage['PID'] . " no alt page selected.<br />";
		$indexPage = $mybb->settings['bam_index_page']; 
	} else {
		echo "Non-fatal internal error. Alternative page was defined, but is handled in the wrong function isIndexPage(). <br />";
		echo var_dump($announcement);
		echo "<br />";
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
	// echo "<br /> Called function <br />";
	global $mybb, $current_page, $additional_page_parameters;

	// Developers: If you are using this plugin and your URL settings are not being accepted, you can add
	// new acceptable parameteres here. However, please be aware that this is a whitelist that is intended
	// to prevent unexpected or insecure behavior. This setting was explicitely ommitted on the ACP for 
	// this reason. Please be mindful and add parameters, but do not remove the whitelist for your forum. 
	$additional_page_parameters = array('fid', 'action', 'uid', 'tid');
	
	$explodedPages = explode(',', $announcement['additional_display_pages']);
	$processedPages = array_map('trim',$explodedPages);
	$acceptPage = false;

	// This parameter allows multiple URLs to be set. Check for each URL that is given. 
	foreach ($processedPages as $additional_display_page) {

		// This plugin explicitely parses the URL given by the announcement's settings to extract only the file name. 
		// This functionality should not be reverted. Otherwise, rogue URLs (index.php?fid=forumdisplay.php) could cause
		// this plugin to display on pages that it has not been designed to display on, and could be a possible security issue. 

		$url_parameters = parse_url($additional_display_page);
		$announcementFileName = basename($url_parameters["path"]);

		// First, we check to see if we are on the correct PHP file/page (e.g. index.php, forumdisplay.php, etc.)
		if ($announcementFileName == $current_page) {
 
			// By default, we assume that we found the required URL parameters. We then check to see if any do not match. 
			$paramCheck = true;

			// Loop through each whitelisted parameter and check for mismatches. 
			foreach ($additional_page_parameters as $parameter) {	

				// We first check if the $_GET parameter we are currently checking exists on the page/URL the user is visiting. 
				// If it does, we check to see if it matches the additional_page parameter's value. 

				if (isset($_GET[$parameter])) {
					
					// We found the parameter in the URL of this page. Get its value.  
					$paramValue = $_GET[$parameter]; 	
		
					// Next, we must check if the parameter was defined in the announcement's settings. 
					// If so, we check to see if it matches the URL that we are on. 
					// If it is not found, the announcement does not care about additional parameters that may exist. We ignore it.

					if (strpos($additional_display_page, $parameter)) {
						$paramSearchString = "$parameter=" . $paramValue;
						// The parameter exists both in the URL and in the setting. Check for a match.  
						if (!strpos($additional_display_page, $paramSearchString)) {
							$paramCheck = false; 
						}
					}
				}  
				
				// Else: The current $parameter being checked is not in the URL. 
				// Check to see if it's a part of the additional_display_pages setting. If so, reject the announcement. 
				else {
					$unsetURLParam = $parameter . "=";

					// Scan additional_display_pages to see if the URL parameter exists in the setting. If so, reject the announcement. 
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


// Thank you for using, developin for, and viewing BAM's source. If you have any questions or would like to contribute,
// please send me (Darth-Apple) a message on github or on the MyBB community forums!
// Regards, 
// -Darth Apple