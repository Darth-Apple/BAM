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
		'version'		=> '1.0',
		"compatibility"	=> "17*,18*"
	);
}

function bam_install () {

	global $db, $lang;
	$lang->load('bam');	
	if(!$db->table_exists($prefix.'bam')) {

		$db->query("CREATE TABLE ".TABLE_PREFIX."bam (
				PID int unsigned NOT NULL auto_increment,
  				announcement varchar(1024) NOT NULL default '',				
				class VARCHAR(40) NOT NULL DEFAULT 'yellow',
				link VARCHAR(160) default '',
				`active` INT UNSIGNED NOT NULL DEFAULT 1,
				`disporder` INT NOT NULL DEFAULT 1,
				`groups` VARCHAR(128) DEFAULT '1, 2, 3, 4, 5, 6',
				`date` INT(10) NOT NULL,
				`pinned` INT UNSIGNED NOT NULL DEFAULT 0,
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

	{$bam_custom_css}
</style>
<div class="bam_announcements">{$announcements}</div>';
		
		$template['bam_announcement'] = '<p class="{$class}">{$announcement} <span class="bam_date">{$date}</span></p>'; 
	
		foreach($template as $title => $template_new){
			$template = array('title' => $db->escape_string($title), 'template' => $db->escape_string($template_new), 'sid' => '-1', 'dateline' => TIME_NOW, 'version' => '1800');
			$db->insert_query('templates', $template);
		}


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

		$new_config[] = array(
			'name' => 'bam_date_enable',
			'title' => $db->escape_string($lang->bam_date_enable),
			'description' => $db->escape_string($lang->bam_date_desc),
			'optionscode' => 'yesno',
			'value' => '1',
			'disporder' => 2,
			'isdefault' => 1,
			'gid' => $group['gid']
		);
		
		$new_config[] = array(
			'name' => 'bam_random',
			'title' => $db->escape_string($lang->bam_random_enable),
			'description' => $db->escape_string($lang->bam_random_desc),
			'optionscode' => 'onoff',
			'value' => '0',
			'disporder' => 3,
			'isdefault' => 1,
			'gid' => $group['gid']
		);
				
		$new_config[] = array(
			'name' => 'bam_random_max',
			'title' => $db->escape_string($lang->bam_random_max),
			'description' => $db->escape_string($lang->bam_random_max_desc),
			'optionscode' => 'text',
			'value' => '1',
			'disporder' => 4,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'bam_random_group',
			'title' => $db->escape_string($lang->bam_random_group),
			'description' => $db->escape_string($lang->bam_random_group_desc),
			'optionscode' => 'groupselect',
			'value' => '-1',
			'disporder' => 5,
			'isdefault' => 1,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'bam_global',
			'title' => $db->escape_string($lang->bam_global),
			'description' => $db->escape_string($lang->bam_global_desc),
'optionscode' => 'select
= '.$lang->bam_global_disable.'
global_pinned= '.$lang->bam_global_pinned.'
global_all= '.$lang->bam_global_all,
			'value' => 'global_none',
			'disporder' => 6,
			'isdefault' => 1,
			'gid' => $group['gid']
		); // bad indentation intentional

		$new_config[] = array(
			'name' => 'bam_index_page',
			'title' => $db->escape_string($lang->bam_index_page),
			'description' => $db->escape_string($lang->bam_index_page_desc),
			'optionscode' => 'text',
			'value' => 'index.php',
			'disporder' => 7,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'bam_custom_css',
			'title' => $db->escape_string($lang->bam_custom_css),
			'description' => $db->escape_string($lang->bam_custom_css_desc),
			'optionscode' => 'textarea',
			'value' => '/* Insert Custom CSS Here */',
			'disporder' => 8,
			'gid' => $group['gid']
		);

		// insert settings
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


function bam_activate () {
	global $db;
	require MYBB_ROOT.'/inc/adminfunctions_templates.php';
	find_replace_templatesets('header', '#{\$awaitingusers}#', '{$awaitingusers} <!-- BAM -->{$bam_announcements}<!-- /BAM -->');
}

function bam_deactivate () {
	global $db;
	require MYBB_ROOT.'/inc/adminfunctions_templates.php';
	find_replace_templatesets('header', '#\<!--\sBAM\s--\>(.+)\<!--\s/BAM\s--\>#is', '', 0);

}


function bam_announcements () {
	global $mybb, $db, $templates, $bam_announcements, $lang;
	$lang->load("global");
	require_once MYBB_ROOT."/inc/class_parser.php";
	$parser = new postParser(); 
	$parser_options = array(
    		'allow_html' => 'no',
    		'allow_mycode' => 'yes',
    		'allow_smilies' => 'yes',
    		'allow_imgcode' => 'no',
    		'filter_badwords' => 'yes',
    		'nl2br' => 'yes'
	);

	$class_select = array('green', 'yellow', 'red', 'blue'); // list of programmed BAM classes. Future versions of BAM may add additional classes.

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

	while($querydata = $db->fetch_array($query)) {

		if(!empty($querydata['link'])) {
			$announcement = "[url=".$querydata['link']."]".$querydata['announcement']."[/url]";
		}
		else {
			if(!empty($mybb->user['uid'])) {
			          $username = $mybb->user['username']; // allows {$username} to be replaced with the user's username. 
			}
			else {
				$username = $lang->guest; // user is not logged in. Parse as "Guest" instead. 
			}

			$announcement = str_replace('{$username}', $username, html_entity_decode($querydata['announcement']));
		}

		$announcement = $parser->parse_message($announcement, $parser_options); 
		$class = "bam_announcement " . $querydata['class']; // parse class. 

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

		$data[$count]['date'] = $date;
		$data[$count]['class'] = $class;
		$data[$count]['announcement'] = $announcement;

		if(($mybb->settings['bam_random'] == 1) && ($querydata['pinned'] != 1) && (bam_display_permissions($querydata['groups'])) && (global_display($querydata['pinned']))) {
			$unpinned_ids[] = $count;
			$total_unpinned++;
		}
		if((($querydata['pinned'] == 1) || ($mybb->settings['bam_random'] == 0)) && (bam_display_permissions($querydata['groups'])) && (global_display($querydata['pinned']))) {
			eval("\$announcements .= \"".$templates->get("bam_announcement")."\";");
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

function bam_display_permissions ($display_groups) {
	global $mybb;
	
	if (empty($display_groups)) {
		return false; // no need to check for permissions if no groups are allowed. 
	}
	if ($display_groups == "-1") {
		return true; // no need to check for permissions if all groups are allowed. 
	}
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
	foreach ($allowed as $allowed_group) {
		if (in_array($allowed_group, $groups)) {
			return true;
		}
	}

	return false;
}

function global_display($pinned) {
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

