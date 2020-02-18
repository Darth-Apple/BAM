<?php

 /*     This file is part of StatusFeed

    Status Feed is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Status Feed is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Status Feed.  If not, see <http://www.gnu.org/licenses/>.
*/
// Disallow direct access to this file for security reasons

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); */

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed. Please make sure IN_MYBB is defined.");
}
global $mybb;
if (isset($mybb->settings['statusfeed_enabled']) && $mybb->settings['statusfeed_enabled'] == 1) {
	global $lang;
	$lang->load('statusfeed');
	if ($mybb->settings['statusfeed_enabled_profile'] == 1) {
		$plugins->add_hook('member_profile_start', 'statusfeed_profile');
	}
	if ($mybb->settings['statusfeed_enabled_portal'] == 1) {
		$plugins->add_hook('portal_start', 'statusfeed_portal');
	}

	$plugins->add_hook('misc_start', 'statusfeed_push_status');
    $plugins->add_hook('index_start', 'statusfeed_portal');
    //$plugins->add_hook('global_start', 'statusfeed_portal');
	$plugins->add_hook('usercp_start', 'statusfeed_usercp');
	$plugins->add_hook('global_start', 'statusfeed_alert');	

	$plugins->add_hook("postbit", "statusfeed_postbit");
	$plugins->add_hook("postbit_pm", "statusfeed_postbit");
	$plugins->add_hook("postbit_announcement", "statusfeed_postbit");
}


function statusfeed_info()
{

	return array(
		"name"			=> "MyBB Status Feed",
		"description"	=> "Allows users to update their statuses on their profile and/or the portal",
		"website"		=> "http://makestation.net",
		"author"		=> "Darth-Apple",
		"authorsite"	=> "http://makestation.net",
		"version"		=> "1.01",
		"guid" 			=> "",
		"compatibility" => "18*,17*,16*"
	);
}

	function statusfeed_install() {	
		global $db;
	
		if(!$db->table_exists($prefix.'statusfeed'))
		{
			$db->query("CREATE TABLE ".TABLE_PREFIX."statusfeed (
				PID int unsigned NOT NULL auto_increment,
				status varchar(1025) NOT NULL default '',
				title varchar(100) NOT NULL default '',
				UID int NOT NULL default '-1',
				wall_id int NOT NULL,
				shown int unsigned NOT NULL default 0,
				self int unsigned NOT NULL default 0,
				parent int default -1,
				numcomments int default 0,				
				date int(10) NOT NULL,
  				PRIMARY KEY (PID)
				) ENGINE=MyISAM
				".$db->build_create_table_collation().";");
		}

		if(!$db->table_exists($prefix.'statusfeed_alerts'))
		{
			$db->query("CREATE TABLE ".TABLE_PREFIX."statusfeed_alerts (
				PID int unsigned NOT NULL auto_increment,
				sid INT NOT NULL,
				parent INT default -1, 
				uid INT NOT NULL,
				to_uid INT NOT NULL,
				`marked_read` INT default 0,
				`type` INT NOT NULL,				
				date int(10) NOT NULL,
  				PRIMARY KEY (PID)
				) ENGINE=MyISAM
				".$db->build_create_table_collation().";");
		}

		$db->write_query("ALTER TABLE `".TABLE_PREFIX."users` ADD `sf_unreadcomments` INT(10) NOT NULL DEFAULT '0';");

        $db->write_query("ALTER TABLE `".TABLE_PREFIX."users` ADD `sf_currentstatus` VARCHAR(1025) DEFAULT '';");
				
		$template = array();


		$template['statusfeed_post_mini'] = '
<tbody id="status_{$SID}">
	<tr>
		<td class="trow2" rowspan="2" align="center" width="3%" valign="top" style="border: none;">
			<img src="{$avatar}" alt="Avatar of User" width="{$avatar_parems[\'width\']}" height="{$avatar_parems[\'height\']}" style="border: 1px solid #b6b6b6; border-radius: 3px; -moz-border-radius: 3px; padding: 2px; margin: 2px;">
		</td>
	</tr>
	<tr>
		<td class="trow2" style="padding-bottom: 0px; border: none;" valign="top">
			<div class="smalltext" style="font-size: 10px; border: none; color: #4A4A4A;">
				{$userlink}
				<span class="smalltext float_right">
					{$date}
				</span>
			</div>
			<div class="smalltext" style="padding-top: 2px;">
				{$status}
			</div>
			<div class="smalltext" style="padding-top: 1px; padding-bottom: 3px;">
				{$replies}&nbsp;{$edit}
			</div>
		</td>
	</tr>


<tr id="comments_container_{$SID}" style="display: {$display_comments}; border: none; border-top: 1px solid #d6d6d6; " colspan="2">
      	<!-- <td class="trow2" style="border-left: none; border-bottom: none; border-top: 1px solid #d6d6d6;"></td> -->
	<td colspan="2" class="trow1" style="border-right: none; border-bottom: none; border-top: 1px solid#d6d6d6; padding-left: 5px;">
		<div id="comments_{$SID}">
			{$comments}
		</div>
	</td>
</tr>

<tr>
	<td class="trow2" style="padding: 0px; border: none;" colspan="2">
		<div style="border-bottom: 1px solid #d6d6d6;"></div>
	</td>
</tr>
</tbody>';	




		
		$template['statusfeed_post_full'] = '
<tbody id="status_{$SID}">
	<tr>
  		<td class="trow2" rowspan="3" align="center" width="3%" valign="top" style="border-right: none;">
      		<img src="{$avatar}" alt="Avatar of User" width="{$avatar_parems[\'width\']}" height="{$avatar_parems[\'height\']}" style="border: 1px solid #b6b6b6; border-radius: 3px; -moz-border-radius: 3px; padding: 2px; margin: 2px;">
		</td>
	</tr>
	<tr>
		<td class="trow2" style="padding-bottom: 0px; border-left: none; border-bottom: none;" valign="top">
		  
    		<div style="position:relative; overflow: auto; height: 100%;">
    			<div class="smalltext" style="font-size: 10px; border: none; color: #4A4A4A; padding-top: 2px;">
     				{$lang->statusfeed_posted_by} {$userlink}
   					<span class="smalltext float_right">
						{$edit}&nbsp;{$date}
					</span>
				</div>
    			<div style="padding-top: 1px; font-size: 13px;">
      				{$status}
				</div>
			</div>
		</td>
	</tr>

    <tr>
       	<td class="trow2" style="padding-bottom: 0px; border-top: none; border-left: none;" valign="bottom">         
    		<div class="smalltext" style="padding-top: 1px; padding-bottom: 5px;">
     	 		{$replies}
			</div>
		</td>
	</tr>
         
    
	<tr id="comments_container_{$SID}" style="display: {$display_comments};  ">
		<td class="trow2"> </td>
		<td class="trow1" style="padding: 0px; padding-left: 4px;">
			<div id="comments_{$SID}">
				{$comments}
			</div>
		</td> 
	</tr>
</tbody> 
';
		
$template['statusfeed_comments_container'] = '
<table border="0" cellspacing="0" cellpadding="2" style="width: 100%; width: 100%; border-left: 3px solid #E2E2E2; padding-left: 5px;">
	{$viewall}
	{$feed}
  
  
  	<tr colspan="1">
		<td class="trow1" rowspan="1" align="center" width="3%" style="padding: 4px; border-right: none; ">
			<img src="{$viewer_avatar}" width="{$comment_parems[\'width\']}" height="{$comment_parems[\'height\']}" style="border: 1px solid #b6b6b6; border-radius: 3px; -moz-border-radius: 3px; padding: 2px;"/>
		</td>
		<td colspan="1" class="trow1" style="border-left: none;">
			<form action="misc.php?action=update_status" method="post">
				<input name="status" rows="2" value="{$lang->statusfeed_post_comment_textbox}" style="width: 100%; line-height: {$avatar_parems[\'height\']}px; -webkit-border-radius: 0px; -moz-border-radius: 0px; border-radius: 0px; color: #636363;" onfocus="if(this.value == \'{$lang->statusfeed_post_comment_textbox}\') {this.value=\'\';}" onblur="if(this.value==\'\') {this.value=\'{$lang->statusfeed_post_comment_textbox}\';}"><br />
				<input type="hidden" name="reply_id" value="{$parent}">
				<input type="hidden" name="post_key" value="{$mybb->post_code}">
				<!-- <input type="submit" value="{$lang->statusfeed_post_comment}" class="button" style="-webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px;"> -->
				<!-- <span id="viewall_{$parent}" style="display: inline; float: right; padding-right: 8px; padding-top: 6px;">{$viewall}</span> -->
			</form>
		</td>
	</tr>
</table>
';
		
		$template['statusfeed_comment_mini'] = '
<tr>
  	<td class="trow1" rowspan="2" align="center" width="2%" valign="top" style="border: none;">
        <img src="{$avatar}" alt="Avatar of User" width="24" height="24"  style="border: 1px solid #b6b6b6; border-radius: 3px; -moz-border-radius: 3px; padding: 2px; margin: 2px;">
	</td>
</tr>
<tr>
	<td class="trow1" style="padding-bottom: 0px; border: none;" valign="top">
    	<div class="smalltext" style="font-size: 10px; border: none; color: #4A4A4A;">
     		{$userlink}
   			<span class="smalltext float_right">
				{$date}
			</span>
		</div>
    	<div class="smalltext" style="padding-top: 2px;">
      		{$status}
    	</div>
    	<div class="smalltext" style="padding-top: 1px; padding-bottom: 3px;">
     		{$edit}
    	</div>
	</td>
</tr>

<tr><td class="trow1" style="padding: 0px; border: none;" colspan="2"><div style="border-bottom: 1px solid #d6d6d6;"></div></td></tr>';

		$template['statusfeed_comment_full'] = '
<tr>
  	<td class="trow1" rowspan="2" align="center" width="2%" valign="top" style="border-right: none; {$border_fix}">
        		<img src="{$avatar}" alt="Avatar of User" width="{$avatar_parems[\'width\']}" height="{$avatar_parems[\'height\']}"  style="border: 1px solid #b6b6b6; border-radius: 3px; -moz-border-radius: 3px; padding: 2px; margin: 2px;">
	</td>
</tr>
<tr>
  <td class="trow1" style="padding-bottom: 0px; border-left: none; {$border_fix}" valign="top">
    <div class="smalltext" style="font-size: 10px; border: none; color: #4A4A4A;">
     {$userlink}
   			<span class="smalltext float_right">
			{$edit}&nbsp;&nbsp;{$date}
		</span>
	</div>
    <div class="smalltext" style="padding-top: 2px; font-size: 12px;">
      {$status}
    </div>
    <div class="smalltext" style="padding-top: 1px; padding-bottom: 3px;">
     	 
    </div>
</td>
</tr>';

		$template['statusfeed_postbit'] = '<div class="statusfeed_postbit">{$lang->statusfeed_postbit}: {$userstatus}</div>';	
		
			$template['statusfeed_edit'] = '
	
			<tr colspan="2">
				<td class="trow1" colspan="2">
					<form action="misc.php?action=edit_status" method="post" style="display: inline;">
						<textarea type="text" name="status" style="width: 75%; height: 40px;">{$status}</textarea><br />
						<input type="hidden" name="ID" value="{$ID}">
						<input type="hidden" name="UID" value="{$UID}">
						<input type="hidden" name="post_key" value="{$mybb->post_code}"><br />
						<input type="submit" style="display: inline;" value="{$lang->statusfeed_submit}" class="button">
					</form>
					
					<form action="misc.php?action=statusfeed_delete_status" method="post" style="display: inline;">
						<input type="hidden" name="ID" value="{$ID}">
						<input type="hidden" name="UID" value="{$UID}">
						<input type="hidden" name="post_key" value="{$mybb->post_code}">
						<input type="hidden" name="reply_id" value="{$parent}">
						<input type="submit" value="{$lang->statusfeed_delete}" onclick="return confirm(\'{$lang->statusfeed_delete_confirm}\');" class="button">
					</form>
				</td>
			</tr>	

			';	
	
		$template['statusfeed_profile'] = '
<table border="0" cellspacing="0" cellpadding="4" class="tborder">
		<tr>
			<td colspan="2" class="thead">
				<div>
					<strong>{$lang->statusfeed_updates}</strong>
				</div>
			</td>
		</tr>
		{$status_updates} 
		<tr>
			<td class="trow2" colspan="2">
			{$pagination}
				<form action="misc.php?action=update_status" method="post">
					<div style="padding: 10px">
<textarea name="status" rows="2" style="width: 100%; color: #636363; width: 100%; -webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px;" onfocus="if(this.value == \'{$lang->statusfeed_update_status_textbox}\') {this.value=\'\';}" onblur="if(this.value==\'\') {this.value=\'{$lang->statusfeed_update_status_textbox}\';}">{$lang->statusfeed_update_status_textbox}</textarea>
						<input type="hidden" name="wall_id" value="{$profile_UID}">
						<input type="hidden" name="post_key" value="{$mybb->post_code}">
<input type="submit" value="{$lang->statusfeed_update_status}" style="width: 100%; " class="button" style="-webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px;">
					</div>
				</form>
			</td>
		</tr>
</table>
<br />';
		
		$template['statusfeed_all'] = '
<html>
	<head>
		<title>{$mybb->settings[\'bbnamr\']}</title>
		{$headerinclude}	
	</head>
	<body>
		{$header} 
		<br />
		<table border="0" cellspacing="0" cellpadding="4" class="tborder">
			<tr>
				<td colspan="2" class="thead">
					<div>
						<strong>{$lang->statusfeed_updates}</strong>
					</div>
				</td>
			</tr>
			{$status_updates} 
			<tr>
				<td class="trow2" colspan="2">
					{$pagination}
					<br />
					<form action="misc.php?action=update_status&redirect=statusfeed" method="post">
						<div style="padding: 10px;">
							<textarea name="status" rows="2" style="width: 100%; color: #636363; width: 100%; -webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px;" onfocus="if(this.value == \'{$lang->statusfeed_update_status_textbox}\') {this.value=\'\';}" onblur="if(this.value==\'\') {this.value=\'{$lang->statusfeed_update_status_textbox}\';}">{$lang->statusfeed_update_status_textbox}</textarea>
							<input type="hidden" name="wall_id" value="{$profile_UID}">
							<input type="hidden" name="reply_id" value="-1">
							<input type="hidden" name="post_key" value="{$mybb->post_code}">
							<input type="submit" value="{$lang->statusfeed_update_status}" class="button" style="-webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px;">
						</div>
					</form>
				</td>
			</tr>
		</table>
		{$footer}
	</body>
</html>		
		';

		$template['statusfeed_portal'] = '	
<table border="0" cellspacing="0" cellpadding="3" class="tborder tborder_portal" style="background: #ffffff; min-width: 230px; " id="statusfeed">
		<tr>
			<td colspan="2" class="thead thead_portal" style="padding-bottom: 5px;">
				<div>
					<strong>{$lang->statusfeed_updates}</strong>

				</div>
			</td>
		</tr>
		{$status_updates} 
		<tr>
			<td class="trow1" colspan="2">
			<form action="misc.php?action=update_status" method="post">
					<div style="padding: 10px;">
						<textarea name="status" rows="2" style="width: 100%; color: #636363; width: 100%; -webkit-border-radius: 0px; -moz-border-radius: 0px; border-radius: 0px;" onfocus="if(this.value == \'{$lang->statusfeed_update_status_textbox}\') {this.value=\'\';}" onblur="if(this.value==\'\') {this.value=\'{$lang->statusfeed_update_status_textbox}\';}">{$lang->statusfeed_update_status_textbox}</textarea>
						<input type="hidden" name="reply_id" value="-1">
						<input type="hidden" name="post_key" value="{$mybb->post_code}">
<input type="submit" value="{$lang->statusfeed_update_status}" style="width: 100%; -webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px; " class="button">
					</div>
				</form>
			{$statusfeed_viewall}
			</td>
		</tr>
</table> 
<br />
		';

		$template['statusfeed_notifications_container'] = '
<html>
	<head>
		<title>{$mybb->settings[\'bbname\']} - {$lang->statusfeed_usercp_link}</title>
		{$headerinclude}
	</head>
	<body>
		{$header}
		<form action="statusfeed.php?action=mark_all" method="post">
			<table width="100%" border="0" align="center">
				<tr>
					{$usercpnav}
					<td valign="top">
						{$pagination}
						<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
							<tr>
								<td class="thead" colspan="3"><strong>{$lang->statusfeed_usercp_link}</strong></td>
							</tr>
							<tr>
								<td class="tcat" width="70%"><strong>{$lang->statusfeed_alert}</strong></td>
								<td class="tcat" width="18%"><strong><center>{$lang->statusfeed_date}</center></strong></td>
								<td class="tcat" width="12%"><strong><center>{$lang->statusfeed_actions}</center></strong></td>
							</tr>
							{$notifications}
						</table>
						<br />
						<div align="center">
							<input type="hidden" name="post_key" value="{$mybb->post_code}">
							<input type="submit" class="button" name="submit" value="{$lang->statusfeed_mark_all}" />
						</div>
					</td>
				</tr>
		    </table>
		</form>
	{$footer}
	</body>
</html>
		';	

		$template['statusfeed_notification'] = '
<tr>
	<td class="{$altbg}" style="font-weight: {$fontweight};">
		{$text}
	</td>
	<td class="{$altbg}">
		<center>{$date}</center>
	</td>
	<td class="{$altbg}">
		<center>{$mark}</center>
	</td>
</tr>';
	
		foreach($template as $title => $template_new){
			$template = array('title' => $db->escape_string($title), 'template' => $db->escape_string($template_new), 'sid' => '-1', 'version' => '140', 'dateline' => TIME_NOW);
			$db->insert_query('templates', $template);
		}


		$new_groupconfig = array(
			'name' => 'statusfeed', 
			'title' => 'MyBB Status Feed ',
			'description' => 'Settings and configuration for MyBB Status Feed',
			'disporder' => $rows+2,
			'isdefault' => 0
		);
	
		$group['gid'] = $db->insert_query("settinggroups", $new_groupconfig);
		$new_config = array();
	
		$new_config[] = array(
			'name' => 'statusfeed_enabled',
			'title' => 'Enable/Disable MyBB Status Feed',
			'description' => 'This setting allows the Status Feed to be enabled or disabled without deactivating or activating the plugin. ',
			'optionscode' => 'yesno',
			'value' => '1',
			'disporder' => 1,
			'isdefault' => 1,
			'gid' => $group['gid']
		);
		
		$new_config[] = array(
			'name' => 'statusfeed_enabled_portal',
			'title' => 'Enable/Disable Portal Block',
			'description' => 'Enable/disable the status updates block on the portal. ',
			'optionscode' => 'yesno',
			'value' => '1',
			'disporder' => 2,
			'isdefault' => 1,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'statusfeed_enabled_profile',
			'title' => 'Enable/Disable on Profiles',
			'description' => 'Enable/disable the status update block on user profiles. ',
			'optionscode' => 'yesno',
			'value' => '1',
			'disporder' => 3,
			'isdefault' => 1,
			'gid' => $group['gid']
		);	
		
		$new_config[] = array(
			'name' => 'statusfeed_avatarsize_full',
			'title' => 'Avatar Size (Profile)',
			'description' => 'Avatar size for statuses on user profiles (in pixels)',
			'optionscode' => 'text',
			'value' => '48x48',
			'disporder' => 4,
			'gid' => $group['gid']
		);
		
		$new_config[] = array(
			'name' => 'statusfeed_avatarsize_mini',
			'title' => 'Avatar Size (Portal & Comments)',
			'description' => 'Avatar size for avatars on the portal page (in pixels)',
			'optionscode' => 'text',
			'value' => '32x32',
			'disporder' => 5,
			'gid' => $group['gid']
		);		
		
		$new_config[] = array(
			'name' => 'statusfeed_rowsperpage',
			'title' => 'Status Updates per Page (Profile/Portal)',
			'description' => 'Defines how many results will be displayed per page on the profile and portal blocks. ',
			'optionscode' => 'text',
			'value' => '5',
			'disporder' => 6,
			'gid' => $group['gid']
		);
		
		$new_config[] = array(
			'name' => 'statusfeed_rowsperpage_all',
			'title' => 'Statuses per Page (Community Status Feed) ',
			'description' => 'Defines how many statuses will be displayed per page on the community status feed. ',
			'optionscode' => 'text',
			'value' => '20',
			'disporder' => 7,
			'gid' => $group['gid']
		);		
	
		$new_config[] = array(
			'name' => 'statusfeed_comments_enable',
			'title' => 'Enable/Disable Comments',
			'description' => 'Enabled or disable comments for status updates',
			'optionscode' => 'yesno',
			'value' => '1',
			'disporder' => 8,
			'gid' => $group['gid']
		);
		
		$new_config[] = array(
			'name' => 'statusfeed_alerts_enable',
			'title' => 'Enable/Disable Alerts',
			'description' => 'Enable to alert users when new statuses are posted on their profiles by other users, or when users comment on statuses. It is recommended that this setting be left enabled unless you are using an integration with an alternative alerts plugin.',
			'optionscode' => 'yesno',
			'value' => '1',
			'disporder' => 9,
			'gid' => $group['gid']
		);


		$new_config[] = array(
			'name' => 'statusfeed_alertsperpage',
			'title' => 'Alerts per Page',
			'description' => 'Defines how many alerts will be displayed per page in the alerts notifications panel. ',
			'optionscode' => 'text',
			'value' => '10',
			'disporder' => 11,
			'gid' => $group['gid']
		);		

	
		$new_config[] = array(
			'name' => 'statusfeed_commentsperpage',
			'title' => 'Comments per Page',
			'description' => 'Defines how many comments will be displayed by default. ',
			'optionscode' => 'text',
			'value' => '7',
			'disporder' => 12,
			'gid' => $group['gid']
		);
		
		$new_config[] = array(
			'name' => 'statusfeed_useredit',
			'title' => 'Allow users to edit their own status?',
			'description' => 'Enable to allow users to edit or delete their own statuses. ',
			'optionscode' => 'yesno',
			'value' => '1',
			'disporder' => 13,
			'isdefault' => 1,
			'gid' => $group['gid']
		);
		
		$new_config[] = array(
			'name' => 'statusfeed_moderator_groups',
			'title' => 'Moderator Usergroups',
			'description' => 'Define all usergroups with moderator privileges. Separate each group with a comma.',
			'optionscode' => 'groupselect',
			'value' => '3, 4',
			'disporder' => 14,
			'gid' => $group['gid']
		);
	
		$new_config[] = array(
			'name' => 'statusfeed_maxlength',
			'title' => 'Maximum Comment or Status Update Length',
			'description' => 'Max length for status updates and comments. (Maximum: 1024)',
			'optionscode' => 'text',
			'value' => '512',
			'disporder' => 15,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'statusfeed_mini_truncate',
			'title' => 'Truncate large statuses on portal? ',
			'description' => 'Enable/disable the truncating of large statuses on the portal page. ',
			'optionscode' => 'yesno',
			'value' => '1',
			'disporder' => 16,
			'isdefault' => 1,
			'gid' => $group['gid']
		);

		$new_config[] = array(
			'name' => 'statusfeed_mini_truncate_length',
			'title' => 'Portal Truncate Length',
			'description' => 'Truncate length for status updates on the portal. (Mimimum: 64)',
			'optionscode' => 'text',
			'value' => '144',
			'disporder' => 17,
			'gid' => $group['gid']
		);
		
		$new_config[] = array(
			'name' => 'statusfeed_max_comments',
			'title' => 'Maximum Number of Comments',
			'description' => 'Maximum number of comments for a status update. ',
			'optionscode' => 'text',
			'value' => '50',
			'disporder' => 18,
			'gid' => $group['gid']
		);		
	
		foreach($new_config as $array => $content) {
			$db->insert_query("settings", $content);
		}
		rebuild_settings();
	
	} // end install
	
	
	
	function statusfeed_uninstall () {
		global $db;
		$info = statusfeed_info();
		
		if($db->table_exists('statusfeed')) {
			$db->drop_table('statusfeed');
		}
		
		if($db->table_exists('statusfeed_alerts')) {
			$db->drop_table('statusfeed_alerts');
		}
		
		$db->write_query("ALTER TABLE `".TABLE_PREFIX."users` DROP `sf_unreadcomments`;");
        
        $db->write_query("ALTER TABLE `".TABLE_PREFIX."users` DROP `sf_currentstatus`;");
		
		$templates_to_remove = array('statusfeed_portal', 'statusfeed_profile', 'statusfeed_comment_mini', 'statusfeed_comment_full', 'statusfeed_notifications_container', 'statusfeed_notification', 'statusfeed_postbit', 'statusfeed_edit', 'statusfeed_all', 'statusfeed_post_full', 'statusfeed_post_mini', 'statusfeed_comments_container');
		foreach($templates_to_remove as $data) {
			$db->delete_query('templates', "title = '{$data}'");
		}
	
		$query = $db->simple_select('settinggroups', 'gid', 'name = "statusfeed"');
		$groupid = $db->fetch_field($query, 'gid');
		$db->delete_query('settings','gid = "'.$groupid.'"');
		$db->delete_query('settinggroups','gid = "'.$groupid.'"');
		rebuild_settings();		
	} // end uninstall
	
	function statusfeed_activate () {
		global $db;
		require MYBB_ROOT.'/inc/adminfunctions_templates.php'; 
		find_replace_templatesets('member_profile', '#{\$adminoptions}#', '{\$adminoptions}<!-- StatusFeed -->{$statusfeed_profile}<!-- /StatusFeed -->');
		find_replace_templatesets('portal', '#{\$pms}#', '{\$pms}<!-- StatusFeed -->{$statusfeed}<!-- /StatusFeed -->');
		find_replace_templatesets('header', '#{\$unreadreports}#', '{$unreadreports}<!-- StatusFeed --> {$unread_statuses} <!-- /StatusFeed -->');
		find_replace_templatesets('usercp_nav_misc', '#{\$lang->ucp_nav_editlists}</a></td></tr>#', '{\$lang->ucp_nav_editlists}</a></td></tr><!-- StatusFeed --><tr><td class="trow1 smalltext"><a href="usercp.php?action=statusfeed" class="usercp_nav_item usercp_nav_viewprofile">{\$lang->statusfeed_usercp_link}</a></td></tr><!-- /StatusFeed -->');
	
		find_replace_templatesets("postbit_classic", '#'.preg_quote('{$post[\'groupimage\']}').'#', '{$post[\'groupimage\']} {$post[\'statusfeed\']}');	
		find_replace_templatesets("postbit", '#'.preg_quote('{$post[\'groupimage\']}').'#', '{$post[\'groupimage\']} {$post[\'statusfeed\']}');		
	}
	
	function statusfeed_deactivate() {
		global $db;
		require MYBB_ROOT.'/inc/adminfunctions_templates.php';

		find_replace_templatesets('member_profile', '#\<!--\sStatusFeed\s--\>\{\$([a-zA-Z_]+)?\}<!--\s/StatusFeed\s--\>#is', '', 0);
		find_replace_templatesets('portal', '#\<!--\sStatusFeed\s--\>\{\$([a-zA-Z_]+)?\}<!--\s/StatusFeed\s--\>#is', '', 0);
		find_replace_templatesets('usercp_nav_misc', '#\<!--\sStatusFeed\s--\>(.+)\<!--\s/StatusFeed\s--\>#is', '', 0);
		find_replace_templatesets('header', '#\<!--\sStatusFeed\s--\>(.+)\<!--\s/StatusFeed\s--\>#is', '', 0);

		find_replace_templatesets("postbit_classic", '#'.preg_quote('{$post[\'statusfeed\']}').'#', '',0);
		find_replace_templatesets("postbit", '#'.preg_quote(' {$post[\'statusfeed\']}').'#', '',0);
	}
	
	function statusfeed_is_installed()
{
	global $db;
	if($db->table_exists('statusfeed'))
	{
		return true;
	}
	return false;
}


	function statusfeed_profile () {
		
		global $mybb, $templates, $statusfeed_profile, $db, $lang;
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
	
		if (isset($mybb->input['uid'])) {
			$profile_UID = (int)$mybb->input['uid'];			
		}	
		else {
			$profile_UID = (int)$mybb->user['uid']; // if no UID is defined, user is viewing profile of self. 
		}	

		$avatar_size = $mybb->settings['statusfeed_avatarsize_full'];
		
		if (isset ($mybb->input['status_mode']) && $mybb->input['status_mode'] == "edit") {
			statusfeed_edit ();
			return;
		}
		
		// define the number of rows per page. If no value is defined, default to 10. 
		if ($mybb->settings['statusfeed_rowsperpage'] != 0 && (int)$mybb->settings['statusfeed_rowsperpage'] != null) {
			$rowsperpage = (int)$mybb->settings['statusfeed_rowsperpage'];
		}
		else {
			$rowsperpage = 10;
		}
		
		if (isset($mybb->input['page'])) {
			$mybb->input['page'] = (int) $mybb->input['page'];
		}
		
		$query = $db->simple_select("statusfeed", "COUNT(PID) AS nodes", "wall_id = '$profile_UID' AND shown=1 AND parent < 1");
		$numrows = $db->fetch_field($query, "nodes");
		$totalpages = ceil($numrows / $rowsperpage);
		
		if (isset($mybb->input['page']) && is_numeric($mybb->input['page'])) {
			$currentpage = (int) $mybb->input['page'];
		} else {
			$currentpage = 1;
		} 

		if ($currentpage > $totalpages) {
			$currentpage = $totalpages;
		} 
		if ($currentpage < 1) {
			$currentpage = 1;
		}
		 
		$offset = ($currentpage - 1) * $rowsperpage;			
		$query = $db->query("
			SELECT 
				s.*, 
				u.username AS fromusername,
				u.avatar,
				w.username AS tousername
			FROM ".TABLE_PREFIX."statusfeed s
			LEFT JOIN " . TABLE_PREFIX . "users u ON (u.uid = s.UID)
			LEFT JOIN " . TABLE_PREFIX . "users AS w ON (w.uid = s.wall_id)
			WHERE shown=1 AND wall_id = $profile_UID AND parent < 1
			ORDER BY PID DESC
			LIMIT $offset, $rowsperpage
		");		
		$data = array();
		$count = 0;
		
		while($querydata = $db->fetch_array($query)) {
			if($querydata['parent'] > -1) {
				continue; // these are comments to statuses, and don't need to be loaded now. 
			}

			$options['style'] = "full";
			if ((isset($mybb->input['expanded'])) && ($mybb->input['expanded'] == $querydata['PID'])) {
					$options['expanded'] = true; 
			}	
			else {
					$options['expanded'] = false;
			}	
			
			$feed .= statusfeed_render_status($querydata, $options);
			$count++;	
		}
		
		if ($count == 0) {
			$feed = "<tr><td><div class='pm_alert'>".$lang->statusfeed_none_found."</div></td></tr>";
		}
		$status_updates = $feed;
		
		
		if ($totalpages > 0) {
			/*
			$pagination = "<strong><span style='font-size: 10px;'>".$lang->statusfeed_pages." (".$totalpages."):</span></strong><div class='pagination' style='display: inline;'>";
			$range = 1;
			if ($currentpage > 1) {
				if ($currentpage != 2) {
					$pagination = $pagination. " <a href='member.php?action=profile&uid=$profile_UID&comment_page=1' class='pagination_page'>1</a> "; // bug fix
				}
				if ($currentpage > 3) {
					$pagination .= "... ";
				}				
				$prevpage = $currentpage - 1;
			} 

			for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
				if (($x > 0) && ($x <= $totalpages)) {
					if ($x == $currentpage) {
						$pagination = $pagination. " <span style='font-weight: bold;' class='pagination_current'>$x</span> ";
					} else {
						$pagination = $pagination. " <a href='member.php?action=profile&uid=$profile_UID&comment_page=$x' class='pagination_page'>$x</a> ";
					} 
				} 
			}     
			if ($currentpage != $totalpages) {
				$nextpage = $currentpage + 1;
				if ($totalpages - $currentpage != 1) {
					if ($totalpages - $currentpage > 1) {
						$pagination .= "... ";
					}
					$pagination = $pagination. "<a href='member.php?action=profile&uid=$profile_UID&comment_page=$totalpages' class='pagination_page'>".$totalpages."</a> "; // bug fix
				}
			} 
			echo "</div>"; */
			$pagination = multipage($numrows, $rowsperpage, $currentpage, "member.php?action=profile&uid=$profile_UID");
		}		
		
		eval("\$statusfeed_profile = \"".$templates->get("statusfeed_profile")."\";");
		
	}
	
	
	function statusfeed_portal () {
		global $templates, $statusfeed, $db, $mybb, $lang;
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

		$feed = "";
		$avatar_size = $mybb->settings['statusfeed_avatarsize_mini'];
		
		// define the number of rows per page. If no value is defined, default to 10. 
		if ($mybb->settings['statusfeed_rowsperpage'] != 0 && (int)$mybb->settings['statusfeed_rowsperpage'] != null) {
			$rowsperpage = (int)$mybb->settings['statusfeed_rowsperpage'];
		}
		else {
			$rowsperpage = 10;
		}
		$query = $db->simple_select("statusfeed", "COUNT(PID) AS nodes", "shown=1 AND (UID = wall_id) AND (parent = -1)");
		$numrows = $db->fetch_field($query, "nodes");
		$totalpages = ceil($numrows / $rowsperpage);
		if (isset($mybb->input['comment_page']) && is_numeric($mybb->input['comment_page'])) {
			$currentpage = (int) $mybb->input['comment_page'];
		} else {
			$currentpage = 1;
		} 

		if ($currentpage > $totalpages) {
			$currentpage = $totalpages;
		} 
		if ($currentpage < 1) {
			$currentpage = 1;
		} 	
		$offset = ($currentpage - 1) * $rowsperpage;			
		
		$query = $db->query("
			SELECT 
				s.*, 
				u.username AS fromusername,
				u.avatar,
				w.username AS tousername
			FROM ".TABLE_PREFIX."statusfeed s
			LEFT JOIN " . TABLE_PREFIX . "users u ON (u.uid = s.UID)
			LEFT JOIN " . TABLE_PREFIX . "users AS w ON (w.uid = s.wall_id)
			WHERE shown=1 AND (s.UID = s.wall_id) AND (s.parent = -1)
			ORDER BY PID DESC
			LIMIT $offset, $rowsperpage
		");		
		
		$data = array();
		$count = 0;
		
		while($querydata = $db->fetch_array($query)) {	
			if ($querydata['parent'] > 0) {
				continue; // no need to fetch replies to statuses. These are fetched by ajax on demand. 
			}

			$options['style'] = "mini";
			if ((isset($mybb->input['expanded'])) && ($mybb->input['expanded'] == $querydata['PID'])) {
					$options['expanded'] = true; 
			}	
			else {
					$options['expanded'] = false;
			}	
			
			$feed .= statusfeed_render_status($querydata, $options);
			$count++;		
		}
		
		if ($count == 0) {
			$feed = "<tr><td><div class='pm_alert'>".$lang->statusfeed_none_found."</div></td></tr>";
		}
		$status_updates = $feed;
			
		// $pagination = multipage($numrows, $rowsperpage, $currentpage, "member.php?action=profile&uid=$profile_UID");
		$statusfeed_viewall = '<center><a href="statusfeed.php">View All Updates</a></center>';
		eval("\$statusfeed = \"".$templates->get("statusfeed_portal")."\";");
	}	
	
	

	function statusfeed_push_status() {
		global $mybb, $db, $lang;

		if (($mybb->input['action'] == "update_status") && ($mybb->request_method=="post")) {
			verify_post_check($mybb->input['post_key']);
			
			if (($mybb->user['uid'] == 0) || !isset ($mybb->user['uid'])) {
				error($lang->statusfeed_guest);
			}	
		
			if ((strlen($mybb->input['status']) > $mybb->settings['statusfeed_maxlength']) || strlen($mybb->input['status']) > 1024) {
				error($lang->statusfeed_comment_too_long);
			}
			
			$user = (int) $mybb->user['uid'];
			$status = htmlspecialchars($db->escape_string($mybb->input['status']), ENT_QUOTES); // Yep. Sanitize this thing. 
			
			// Check to make sure we have the required parameters. 
			if(isset($mybb->input['wall_id']) && ($mybb->request_method=="post")) {
				$wall_id = (int)$mybb->input['wall_id']; 
				if ($wall_id == (int) $mybb->user['uid']) {
					// We are posting to our own wall. 
					$self = 1;
				}
				else {
					// This message is going to someone else. 
					$self = 0;
				}
				if ($wall_id == null) {
					error($lang->statusfeed_generic_error);
					die(); // wall ID was defined, but was not an integer. 
				}
			}
			
				// We aren't making a reply. Make this post on our own wall. 
			if ($mybb->input['reply_id'] < 0) {
				$wall_id = (int) $mybb->user['uid']; // no specific wall ID defined, post to the poster's wall. 
				$self = 1;
			}
			else {
				// We aren't posting this on a wall. Set to null until set later. 
					$wall_id = null; 
			}
		
			
			// Check if we need to process a few notifications. 

			// If this is a comment to a user's status, need to create a notification for the author. 
			if(($mybb->input['reply_id'] > 0) && ($mybb->request_method=="post")) {
				$self = 0;
				$reply_id = (int)$mybb->input['reply_id'];
				$query = $db->query("
					SELECT u.uid
					FROM ".TABLE_PREFIX."statusfeed s
					LEFT JOIN " . TABLE_PREFIX . "users u ON u.uid = s.UID
					WHERE PID=".(int) $reply_id);

				$data = array();
				$querydata = $db->fetch_array($query);

				// Add the author of the status to notification receivers. 

				$notification_receiver = (int) $querydata['uid'];
				// die("Notification receiver: " . $notification_receiver);
				$wall_id = $notification_receiver;
						
			}
			else {
				$reply_id = -1; // This status is not a reply to another status. 
				// Add a notification to the user's wall where the status was posted. 
				$notification_receiver = $wall_id;
			}

			// Insert the actual status into the statusfeed. 

			$inserts = array(
				'status' => $status, // Sanitized already
				'UID' => (int) $mybb->user['uid'],
				'shown' => 1,
				'wall_id' => (int) $wall_id,
				'self' => (int) $self,
				'parent' => (int) $reply_id,
				'date' => time()
				);
			$db->insert_query('statusfeed', $inserts); // insert status
			$insert_ID = (int) $db->insert_id('statusfeed', 'PID');

			// Generate some alerts if this is a post on someone's profile. 

			if (($reply_id < 0) && ($notification_receiver != $mybb->user['uid'])) {
				if (($mybb->user['uid'] != $wall_id)) { // user is commenting on someone else's profile. 
					$inserts = array(
						'sid' => $insert_ID, 
						'uid' => (int) $mybb->user['uid'],
						'to_uid' => (int) $wall_id,
						'type' => 0,
						'date' => time()
					);
					$db->insert_query('statusfeed_alerts', $inserts); // insert alert for status. OLD: wall ID
					$db->query("UPDATE ".TABLE_PREFIX."users SET sf_unreadcomments=sf_unreadcomments+1 WHERE uid=".(int) $wall_id); 
				}
			}
			
			else if (($reply_id > 0) && ($notification_receiver != $mybb->user['uid'])){ // user is commenting on a status
				// die("Notif: " . $notification_receiver);
				$inserts = array(
					'sid' => (int) $db->insert_id('statusfeed', 'PID'), 
					'parent' => (int) $reply_id,
					'uid' => (int) $mybb->user['uid'],
					'to_uid' => (int) $notification_receiver,
					'type' => 1,
					'date' => time()
				);

				// Insert alert and update count. 
				$db->insert_query('statusfeed_alerts', $inserts); 
				$db->query("UPDATE ".TABLE_PREFIX."users SET sf_unreadcomments=sf_unreadcomments+1 WHERE uid=".(int) $notification_receiver);

				$db->query("SELECT * FROM ".TABLE_PREFIX."statusfeed_alerts WHERE parent = ".(int) $reply_id." AND marked_read = 0");
				$notifications_cache_existing = array();
				while ($querydata = $db->fetch_array($query)) {
					if ($querydata['type'] == 2) {
						$notifications_cache_existing[] = (int) $querydata['to_uid']; // avoid creating multiple notifications. 
					}
				}
				
				// Get maximum comments and make sure not to select more. 
				$select_limit = intval($mybb->settings['statusfeed_max_comments']) ? "50" : intval($mybb->settings['statusfeed_max_comments']); 
				$query = $db->query("SELECT * FROM ".TABLE_PREFIX."statusfeed WHERE parent=".(int) $reply_id." LIMIT $select_limit");
				$notifications_cache = array($notification_receiver); // create an array, insert the author of the parent status. 

				while($querydata = $db->fetch_array($query)) {
					if ((!in_array((int)$querydata['UID'], $notifications_cache)) && (!in_array((int) $querydata['UID'], $notifications_cache_existing))) {

						/* Yes, this is very much done in a less-than-ideal manner for now. The downside to the simple approach used here is that it so happens to
 						generate an unholy number of queries in some situations, up to two queries for each comment. Future performance improvements 
						and/or restructuring of update and insert queries are planned. */

						
						$notification_receiver = (int) $querydata['UID'];

						if ($notification_receiver != $mybb->user['uid']) {	
							$db->query("UPDATE ".TABLE_PREFIX."users SET sf_unreadcomments=sf_unreadcomments+1 WHERE uid=".(int) $notification_receiver);
						}
						$inserts = array(
							'sid' => (int) $db->insert_id('statusfeed', 'PID'), 
							'parent' => (int) $reply_id,
							'uid' => (int) $mybb->user['uid'],
							'to_uid' => (int) $notification_receiver,
							'type' => 2,
							'date' => time()
						);
						if ($notification_receiver != $mybb->user['uid']) {
							$db->insert_query('statusfeed_alerts', $inserts); // insert alert for status
							$notifications_cache[] .= $notification_receiver; // prevent inserting multiple notifications to one user.  
						}
					} 
				}			
				// $db->query("UPDATE ".TABLE_PREFIX."users SET sf_unreadcomments=sf_unreadcomments+1 WHERE uid=".(int) $notification_receiver); 
			}

			if($reply_id != -1) {
				$db->query("UPDATE ".TABLE_PREFIX."statusfeed SET numcomments=numcomments+1 WHERE PID=$reply_id");
			}

			// If the user is posting to their own wall, update their latest status to display on posts. 
			if (($self == 1) && $reply_id == -1) {
				$postbitInserts = array(
					'sf_currentstatus' => $status // Already sanitized
				);
				$db->update_query('users', $postbitInserts, 'uid = ' . (int) $mybb->user['uid'], 1);				
			}


			if ($mybb->input['redirect'] == "statusfeed") {
				redirect("statusfeed.php?expanded=$insert_ID", $lang->statusfeed_update_success); // bug fix
			}
			else if ($reply_id != -1) {
				$url = $_SERVER['HTTP_REFERER'];
				$query = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
				$url .= ($query ? '&' : '?') . 'expanded='.$reply_id; // properly append URL
				redirect(htmlspecialchars($url, ENT_QUOTES), $lang->statusfeed_update_success); // bug fix
			}
			else {
				redirect(htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES), $lang->statusfeed_update_success);
			}
		
		}
		
		else if (isset($mybb->input["action"]) && $mybb->input['action'] == "edit_status") {
			statusfeed_edit_push();
		}
		
		else if (isset($mybb->input["action"]) && $mybb->input['action'] == "statusfeed_delete_status") {
			statusfeed_delete_status();
		}
		
	
	}
	
	
	function statusfeed_edit () {
		global $templates, $statusfeed, $mybb, $db, $lang;
		
		if (!isset($mybb->input['status_id'])) {
			error($lang->statusfeed_no_comment);
			die();
		}
		$ID = (int)$mybb->input['status_id'];
		
		if (!isset($mybb->input['uid'])) {
			// error("no user defined");
			// die();
		}
		$UID = (int)$mybb->input['uid'];		
		if (sf_moderator_confirm_permissions($mybb->user['usergroup'], $mybb->user['additionalgroups'], $ID) == false) {
			error($lang->statusfeed_permission_denied);
			die ();
		}
		$query = $db->query("
			SELECT *
			FROM ".TABLE_PREFIX."statusfeed s
			LEFT JOIN " . TABLE_PREFIX . "users u ON u.uid = s.UID
			WHERE PID=$ID 
		");
		$data = array();
		$count = 0;
		while($querydata = $db->fetch_array($query))
		{		
			$status = $querydata['status'];
			$parent = $querydata['parent'];
		}
		
		
		eval("\$statusfeed = \"".$templates->get("statusfeed_edit")."\";");
		return $statusfeed;
	}
	
	function statusfeed_edit_push () {
		global $templates, $statusfeed, $mybb, $db, $lang;
		verify_post_check($mybb->input['post_key']);
		
		if ($mybb->request_method != "post") {
			error ($lang->statusfeed_generic_error);
		}	
		
		if ((strlen($mybb->input['status']) > $mybb->settings['statusfeed_maxlength']) || strlen($mybb->input['status']) > 1024) {
			error($lang->statusfeed_comment_too_long);
		}

		else {
			$user = (int)$mybb->user['uid'];
			$status = htmlspecialchars($db->escape_string($mybb->input['status']));
		
			if (!isset($mybb->input['ID'])) {
				error($lang->statusfeed_no_user);
			}
			
			$ID = (int)$mybb->input['ID'];
			if (!isset($mybb->input['UID'])) {
				error($lang->statusfeed_no_user);
			}
			
			$UID = (int)$mybb->input['UID']; // for redirect purposes
			if (sf_moderator_confirm_permissions($mybb->user['usergroup'], $mybb->user['additionalgroups'], $ID) == false) {
				error($lang->statusfeed_permission_denied);
				die ();
				// user does not have permission to edit this status
			}
			

			// Edit the value that is stored for the postbit. Make sure it displays the correct new value. 
			// First, we need to get the uid from the announcement that we are updating. 
			$query = $db->query("
				SELECT *
				FROM ".TABLE_PREFIX."statusfeed s
				LEFT JOIN " . TABLE_PREFIX . "users u ON u.uid = s.UID
				WHERE PID=$ID 
			");
			$querydata = $db->fetch_array($query);

			// Make sure that, for this user, the status we updated is the most recent one. 
			$mostRecent = getMostRecent((int) $querydata['UID']);
			
			// If it's the most recent status, update the postbit. 
			if ($mostRecent['PID'] == (int) $querydata['PID']) {
				$postbitInserts = array(
					'sf_currentstatus' => $status // Already sanitized
				);
				$db->update_query('users', $postbitInserts, 'uid = ' . (int) $mostRecent['UID'], 1);				
			}

			$values['status'] = $status;
			$db->update_query('statusfeed', $values, 'PID = ' . $ID, 1);
			redirect("statusfeed.php?sid=$ID&expanded=true", $lang->statusfeed_edit_success);
		}	
	
	}
	
	function statusfeed_delete_status () {
		global $templates, $statusfeed, $mybb, $db, $lang;
		verify_post_check($mybb->input['post_key']);
		
		if (!isset($mybb->input['ID'])) {
			error($lang->statusfeed_no_comment);
		}
		
		if ($mybb->request_method != "post") {
			error ($lang->statusfeed_generic_error);
		}	
		$ID = (int)$mybb->input['ID'];
		
		if((isset($mybb->input['reply_id'])) && ($mybb->input['reply_id'] > 0)) {
			$reply_id = (int)$mybb->input['reply_id'];
		}
		else {
			$reply_id = null;
		}

		if (!isset($mybb->input['UID'])) {
			error($lang->statusfeed_no_user);
			die();
		}
		$UID = (int)$mybb->input['UID'];
		if (sf_moderator_confirm_permissions($mybb->user['usergroup'], $mybb->user['additionalgroups'], $ID) == false) {
			error($lang->statusfeed_statusfeed_permission_denied);
			die (); // user does not have permission to delete this status
		}

		// Get the announcement's UID for the announcement we must delete. 

		$userQuery = $db->query("SELECT * FROM ".TABLE_PREFIX."statusfeed WHERE PID = ".(int)$mybb->input['ID'].";");
		$userOfAnnouncement = $db->fetch_array($userQuery);
		
		// Now we need to get rid of the most recent status in the user's postbit and reset it. 
		$mostRecent = getMostRecent((int) $userOfAnnouncement['UID']);
		
		$deletedMostRecent = false;
		// We need to check if this announcement if the ID of the most recent announcement we fetched is the same as the ID of what we deleted. 
		if ($mostRecent['PID'] == (int) $mybb->input['ID']) {
			$deletedMostRecent = true;
			$postbitInserts = array(
				'sf_currentstatus' => "" // Already sanitized
			);
			$db->update_query('users', $postbitInserts, 'uid = ' . (int) $mostRecent['UID'], 1);				
		}
	




		$db->delete_query("statusfeed", "PID = $ID", 1);
		if(isset($reply_id)) {
			$db->query("UPDATE ".TABLE_PREFIX."statusfeed SET numcomments=numcomments-1 WHERE PID=$reply_id"); // fix comment count. 
		}
		else if (($reply_id < 1) || (!isset($reply_id))) {
			$db->delete_query("statusfeed", "parent = $ID", 1); // if a status update is being deleted, delete all replies. 
		}

		// Now that we've deleted the old most recent postbit status, we need to fetch the new one and set it accordingly. 
		$newMostRecent = getMostRecent((int) $userOfAnnouncement['UID']);

		// Make sure we have a recent status to push. Otherwise, leave it blank as set before. 
		if (isset($newMostRecent['status']) && $deletedMostRecent == true) {
			$postbitInserts = array(
				'sf_currentstatus' => $db->escape_string($newMostRecent['status']) // Already sanitized
			);
			$db->update_query('users', $postbitInserts, 'uid = ' . (int) $mostRecent['UID'], 1);
		}

		redirect("statusfeed.php", $lang->statusfeed_delete_success);
	}
	
	function sf_moderator_permissions ($usergroup, $additionalgroups, $status_uid) {
		global $mybb; 
		
		$mod_groups = $mybb->settings['statusfeed_moderator_groups'];
		$allowed = explode(",", $mod_groups);
		$groups = array();
		$groups[0] = (int)$usergroup; 
		$add_groups = explode(",", $additionalgroups);
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
		
		if (($status_uid == $mybb->user['uid']) && $mybb->settings['statusfeed_useredit'] == 1) {
			return true; // user can edit or delete their own statuses.
		}

		return false;
	}
	
	function sf_moderator_confirm_permissions ($usergroup, $additionalgroups, $status_id) {
		global $mybb, $db;
		// Only users within groups defined to be moderators can edit statuses that they don't own. 
		
		$mod_groups = $mybb->settings['statusfeed_moderator_groups'];
		$allowed = explode(",", $mod_groups);
		$groups = array();
		$groups[0] = (int)$usergroup; 
		$add_groups = explode(",", $additionalgroups);
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
		
		// Users can also edit their own status. Check to see if the user is attempting to edit a status that they authored. 
		$SID = (int)$status_id;
		$query = $db->query("
			SELECT *
			FROM ".TABLE_PREFIX."statusfeed
			WHERE PID='$SID'
		");
		while($querydata = $db->fetch_array($query))
		{
			$status_uid = (int)$querydata['UID'];
		}
		if (($status_uid == $mybb->user['uid']) && $mybb->settings['statusfeed_useredit'] == 1) {
			return true; // user can edit or delete their own statuses.
		}
		return false;
	}	

		
	function statusfeed_alert () {
		global $mybb, $db, $unread_statuses, $lang;
		$unread_statuses = null;
		if ($mybb->settings['statusfeed_alerts_enable'] == 1) {
			$userwall = (int) $mybb->user['uid'];
			$query = $db->query("
				SELECT *
				FROM ".TABLE_PREFIX."users
				WHERE uid=$userwall
			");
			while($data = $db->fetch_array($query))
				{
					$unread = $data['sf_unreadcomments'];
				}
		
			if ($unread > 0) {
				if ($unread == 1) {
					$unread_statuses = '<div class="pm_alert" id="status_notice">'.$lang->statusfeed_unread_single.'<a href="usercp.php?action=statusfeed" style="font-weight: bold;">'.$lang->statusfeed_click_view_1.'</a></div>';
				}
				else {
					$unread_statuses = '<div class="pm_alert" id="status_notice">'.$lang->statusfeed_unread_multiple_p1.$unread.$lang->statusfeed_unread_multiple_p2.'<a href="usercp.php?action=statusfeed" style="font-weight: bold;">'.$lang->statusfeed_click_view_1.'</a></div>';
				}
		
			}
			return;
		}
		else {
			return;
		}
	}


	function statusfeed_usercp () {
		global $mybb, $templates, $lang, $header, $headerinclude, $footer, $theme, $usercpnav, $db, $statusfeed;	
		if ($mybb->input['action'] == "statusfeed") {

			if (!empty($mybb->user['uid'])) {
				$values['sf_unreadcomments'] = 0;
				$userID = (int) $mybb->user['uid'];
				$db->update_query('users', $values, 'uid = ' . $userID, 1); // set unread comments count to 0. 
			}
			else {
				error($lang->statusfeed_notifications_guest);
			}

			// define the number of rows per page. If no value is defined, default to 10. 
			if ($mybb->settings['statusfeed_alertsperpage'] != 0 && (int)$mybb->settings['statusfeed_alertsperpage'] != null) {
				$rowsperpage = (int)$mybb->settings['statusfeed_alertsperpage'];
			}
			else {
				$rowsperpage = 10;
			}
			$query = $db->simple_select("statusfeed_alerts", "COUNT(PID) AS nodes", "to_uid = $userID");
			$numrows = $db->fetch_field($query, "nodes");
			$totalpages = ceil($numrows / $rowsperpage);

			if (isset($mybb->input['page'])) {
				$mybb->input['page'] = $mybb->input['page'];

			}
		
			if (isset($mybb->input['page']) && is_numeric($mybb->input['page'])) {
				$currentpage = (int) $mybb->input['page'];
			} else {
				$currentpage = 1;
			} 
	
			if ($currentpage > $totalpages) {
				$currentpage = $totalpages;
			} 
			if ($currentpage < 1) {
				$currentpage = 1;
			}
			 
			$offset = ($currentpage - 1) * $rowsperpage;			
			
			$query = $db->query("
				SELECT *
				FROM ".TABLE_PREFIX."statusfeed_alerts s
				LEFT JOIN " . TABLE_PREFIX . "users u ON u.uid = s.uid
				WHERE s.to_uid='$userID'
				ORDER BY s.PID DESC
				LIMIT $offset, $rowsperpage
			");
			$data = array();
			$count = 0;
			
			while($querydata = $db->fetch_array($query)) {	
				// type 0: status posted on your profile
				// type 1: new reply to your status
				// type 2: new reply to a status you replied to (low priority)

				if($querydata['marked_read'] == 1) {
					$read = $lang->statusfeed_read;
					$mark = "<a href='statusfeed.php?action=unread&id=".$querydata['PID']."&post_key=".$mybb->post_code."'>".$lang->statusfeed_mark_unread."</a>";
					$fontweight = "normal"; 
				}
				else {
					$read = $lang->statusfeed_unread;
					$mark = "<a href='statusfeed.php?action=read&id=".$querydata['PID']."&post_key=".$mybb->post_code."'>".$lang->statusfeed_mark_read."</a>";
					$fontweight = "bold"; // unread announcements are bold. 
				}

				if ($querydata['type'] == 0) {
					$url = "statusfeed.php?sid=".(int) $querydata['sid'];
					$text = $lang->sprintf($lang->statusfeed_notification_0, $url, $querydata['username'])." ($read)";
				}	
				else if ($querydata['type'] == 1){
					$url = "statusfeed.php?sid=".(int) $querydata['parent']."&expanded=true";
					$text = $lang->sprintf($lang->statusfeed_notification_1, $url, $querydata['username'])." ($read)";					
				}
				else {
					$url = "statusfeed.php?sid=".(int) $querydata['parent']."&expanded=true";
					$text = $lang->sprintf($lang->statusfeed_notification_2, $url)." ($read)";	
				}
				
				$date = my_date($mybb->settings['dateformat'], $querydata['date']).' '.my_date($mybb->settings['timeformat'], $querydata['date']);

				$count++;
				if ($count % 2 == 0) {
					$altbg = "trow2";
				}
				else {
					$altbg = "trow1";
				}

				eval("\$notifications .= \"".$templates->get("statusfeed_notification")."\";");
			}

			if ($count == 0) {
				$notifications = '<tr><td colspan="3"><div class="pm_alert">'.$lang->statusfeed_no_notifications.'</div></td></tr>';
			}
			
			$pagination = multipage($numrows, $rowsperpage, $currentpage, "usercp.php?action=statusfeed");
			eval("\$statusfeed = \"".$templates->get("statusfeed_notifications_container")."\";");
			output_page($statusfeed);
		}

	}



	function statusfeed_render_comments($ajax = true, $style="full", $SID = null, $limit = 7) {
		// this function is the function that renders comments. This function is often called via ajax requests, but can be called directly as well. For example, if a new comment is posted for a status, the parent status is automatically expanded on redirect and comments are displayed. This function does not use statusfeed_statusfeed_render_status() at this time due to the altered functionality of comment rendering. Although statuses and comments are treated as one and the same by the database/code, the user will not see them as so from a standpoint of user friendliness.  
		global $mybb, $db, $templates, $lang;
		require_once MYBB_ROOT."/inc/class_parser.php";
		$parser = new postParser(); 
		
		if ($limit == "all") {
			$fetch_limit = (int) $mybb->settings['statusfeed_max_comments'];
		}
		else {
			$fetch_limit = (int) $mybb->settings['statusfeed_commentsperpage']; 
		}
		
		$parser_options = array(
    			'allow_html' => NULL,
    			'allow_mycode' => 'yes',
    			'allow_smilies' => 'yes',
    			'allow_imgcode' => NULL,
    			'filter_badwords' => 'yes',
    			'nl2br' => 'yes'
		); 
		
		if ($ajax == true) {
			$parent = (int) $mybb->input['parent'];
		}
		else {
			$parent = (int) $SID;
		}
		
		if($parent == null) {
			echo "<em>".$lang->statusfeed_generic_error."</em>";
			return;
		}
		
		$query = $db->simple_select("statusfeed", "COUNT(PID) AS comments", "shown=1 AND parent=$parent");
		$totalcomments = $db->fetch_field($query, "comments");
	//	$offset = $totalcomments - (int) $mybb->settings['statusfeed_commentsperpage'];
		
		$query = $db->query("
			SELECT 
				s.*, 
				u.username AS fromusername,
				u.avatar,
				w.username AS tousername
			FROM ".TABLE_PREFIX."statusfeed s
			LEFT JOIN " . TABLE_PREFIX . "users u ON (u.uid = s.UID)
			LEFT JOIN " . TABLE_PREFIX . "users AS w ON (w.uid = s.wall_id)
			WHERE shown=1 AND parent=$parent
			ORDER BY PID DESC
			LIMIT $fetch_limit");		
		
		$count = 0;
		$avatar_parems = statusfeed_avatar_parems("mini"); // get avatar paremeters
		
		if (empty($mybb->user['avatar'])) {
			$viewer_avatar = $mybb->settings['useravatar']; // I'm surprised this is necessary. 
		} 
		else {
			$viewer_avatar = $mybb->user['avatar'];
		}			

		while($row = $db->fetch_array($query)) {
			$results[] = $row;
		}
		
		if (!empty($results)) {
			
			$results = array_reverse($results); // Reverse order so that newest comments display on the bottom. 
			foreach ($results as $querydata)  {
				
				$SID = (int) $querydata['PID'];
				$UID = (int) $querydata['UID'];
				if (sf_moderator_permissions($mybb->user['usergroup'], $mybb->user['additionalgroups'], $querydata['UID']) == true) {			
					$edit = "<a href='statusfeed.php?uid=$UID&status_mode=edit&status_id=".$querydata['PID']."'>".$lang->statusfeed_edit."</a>";
				}	
				else {
					$edit = null;
				}
	
				$username = $querydata['username'];
				
				if ($querydata['avatar'] != null) {
					$avatar = $querydata['avatar'];
				} 
				else {
					$avatar = $mybb->settings['useravatar'];
				}			
	
				if ($count == 0) $border_fix = "border-top: none"; // this is what happens when you nest too many tables and borders get complicated. 
				else {
					$border_fix = null;
				}	
				
				$TOUID = $querydata['wall_id'];
				
				$userlink = build_profile_link($querydata['fromusername'], $querydata['UID']); // build user profile link. 
				$status = $parser->parse_message($querydata['status'], $parser_options); 

				$date = my_date($mybb->settings['dateformat'], $querydata['date']);
				$time = my_date($mybb->settings['timeformat'], $querydata['date']);
				$comment_num = (int)$querydata['numcomments'];
				$replies = null;
				if ($style == "mini") {
					eval("\$feed .= \"".$templates->get("statusfeed_comment_mini")."\";");
				}
				else {
					eval("\$feed .= \"".$templates->get("statusfeed_comment_full")."\";");
				}
				
				$count++;	
			}
		}
		else {
			$feed = "<tr><td colspan='2' class='trow1' style='border-top: none; border-left: none; border-right: none; padding-top: 5px;'><div class='pm_alert'>".$lang->statusfeed_no_comments."</div></td></tr>";			
		}	
		
		if ($totalcomments > $mybb->settings['statusfeed_commentsperpage']) {
			if ($limit != "all") {
				$viewall = '<a href="javascript:;" onclick=\'$("#comments_'.$parent.'").load("statusfeed.php?ajax=true&parent='.$parent.'&viewall=true"); \'>'.$lang->statusfeed_view_all_comments.'('.$totalcomments.')'.'</a> ';	
			}
		}
		
		$comment_parems = statusfeed_avatar_parems("mini");
		eval("\$container = \"".$templates->get("statusfeed_comments_container")."\";");

		if ($ajax == true) {
			echo $container;
			return;
		}
		return $container; // if ajax parameter is defined as false. 
	}
	
	
	function statusfeed_render_status ($array, $options) {
		global $mybb, $templates, $lang; 
		// this function performs basic prcessing on data and parses a status. This is not used to generate the query. 
		require_once MYBB_ROOT."/inc/class_parser.php";
		$parser = new postParser(); 
		$parser_options = array(
    			'allow_html' => NULL,
    			'allow_mycode' => 'yes',
    			'allow_smilies' => 'yes',
    			'allow_imgcode' => NULL,
    			'filter_badwords' => 'yes',
    			'nl2br' => 'yes'
		); 
		
		if (isset($options['style']) && $options['style'] == "mini") {
			$style = "mini"; 
		}
		else {
			$style = "full";
		}	
				
		$SID = (int) $array['PID'];
		$UID = (int) $array['UID'];
		if (sf_moderator_permissions($mybb->user['usergroup'], $mybb->user['additionalgroups'], $array['UID']) == true) {			
			// $edit = "<a href='statusfeed.php?uid=$UID&status_mode=edit&status_id=$SID'>".$lang->statusfeed_edit."</a> ";
		$edit = '<a href="javascript:;" onclick=\'$("#status_'.$SID.'").load("statusfeed.php?uid='.$UID.'&status_mode=edit&status_id='.$SID.'"); \'>'.$lang->statusfeed_edit.'</a> ';
		}	
		else {
			$edit = null;
		}

		// old: remove htmlspecialchars
	
		$username = htmlspecialchars($array['username']);
		$avatar_parems = statusfeed_avatar_parems ($style);
		// $comment_parems = statusfeed_avatar_parems("mini");
		
		if ($array['avatar'] != null) {
			$avatar = htmlspecialchars($array['avatar']);
		} 
		else {
			$avatar = htmlspecialchars($mybb->settings['useravatar']);
		}			
		
		$to_userlink = build_profile_link(htmlspecialchars($array['tousername']), (int) $array['wall_id']); // build user profile link. 
		$author_userlink = build_profile_link(htmlspecialchars($array['fromusername']), (int) $array['UID']); // build user profile link. 
	
		$status = $parser->parse_message($array['status'], $parser_options); 
		if ($array['fromusername'] != $array['tousername']) {
			$userlink = $author_userlink." → ".$to_userlink;
		}
		else {
			$userlink = $author_userlink; // initialize variable
		}
		
		$date = my_date($mybb->settings['dateformat'], $array['date']);
		$time = my_date($mybb->settings['timeformat'], $date);
		$numcomments = (int)$array['numcomments'];

		if ($mybb->settings['statusfeed_comments_enable'] == 1) {
			$replies = '<a href="javascript:;" onclick=\'$("#comments_'.$SID.'").load("statusfeed.php?ajax=true&parent='.$SID.'&style='.$style.'"); $("#comments_container_'.$SID.'").toggle(425);\'>'.$lang->statusfeed_replies.' ('.$numcomments.')</a> ';
		}
		else {
			$replies = null;
		}	

		if (isset($options['expanded']) && $options['expanded'] == true) {
			$display_comments = "table-row"; // display as expanded
			$comments = statusfeed_render_comments (false, $style, $SID); // load comments. 
		}
		else {
			$display_comments = "none"; // default to collapsed. 
		}
		
		if ($style == "full") {
			eval("\$status_update = \"".$templates->get("statusfeed_post_full")."\";");
		}
		else {
			eval("\$status_update = \"".$templates->get("statusfeed_post_mini")."\";");
		}		
		
		return $status_update;
	}
	

	function statusfeed_avatar_parems ($style) {
		global $mybb;
		if (in_array($style, array("full", "mini"))) {
			$avatar_parem = explode("x", strtolower($mybb->settings['statusfeed_avatarsize_'.$style]));
			foreach ($avatar_parem as $parem) {
				if(isset($avatar_parems['width'])) {
					$avatar_parems['height'] = (int) $parem;
				}
				else {
					$avatar_parems['width'] = (int) $parem;
				}
			}
		}
		else {
			if($style == "comment_mini") {
				$avatar_parems['width'] = $avatar_parems['height'] = 24; 			
			}
		}	

		if (($avatar_parems['width'] == null) || ($avatar_parems['height'] == null)) {
			if ($style == "mini") {
				$avatar_parems['width'] = $avatar_parems['height'] = 32; // user initialization failed, reset to default. 
			}
			else {
				$avatar_parems['width'] = $avatar_parems['height'] = 64; // user initialization failed, reset to default. 
			}	
		}
		$avatar_parems['indent_width'] = $avatar_parems['width'] + 8 . 'px'; // account for padding by avatar container box when indenting comments. This solution is somewhat of a workaround. 
		return $avatar_parems; 
	}
    
    // Check if the status that a user is trying to edit or delete is the most recent status.
    function isMostRecent($wallID_pass, $userID) {
        global $db, $mybb;
        $wallID = (int) $wallID_pass;
        
        $query = $db->query("
        SELECT
            s.*,
            u.username AS fromusername,
            u.avatar,
            w.username AS tousername
        FROM ".TABLE_PREFIX."statusfeed s
        LEFT JOIN " . TABLE_PREFIX . "users u ON (u.uid = s.UID)
        LEFT JOIN " . TABLE_PREFIX . "users AS w ON (w.uid = s.wall_id)
                        WHERE shown=1 AND (s.UID = s.wall_id) AND (s.parent = -1) AND (s.wall_id == ".$wallID.")
        ORDER BY PID DESC
        LIMIT 0, 1");
        
        $data = array();
        $count = 0;
        
        while($querydata = $db->fetch_array($query)) {
            if ($querydata['PID'] == $wallID_pass) {
				echo "announcement is leading announcement";
				return true;     
				
            }
            else {
				return "not leading announcement";
                return false;
            }
		}
		echo "not an announcement at all";
        return false;
	}
	
    // Check if the status that a user is trying to edit or delete is the most recent status.
    function getMostRecent($wallID_pass) {
        global $db, $mybb;
        $wallID = (int) $wallID_pass;

		/*
        $query = $db->query("
        SELECT
            s.*,
            u.username AS fromusername, u.sf_currentstatus,
            u.avatar,
            w.username AS tousername
        FROM ".TABLE_PREFIX."statusfeed s
        LEFT JOIN " . TABLE_PREFIX . "users u ON (u.uid = s.UID)
        LEFT JOIN " . TABLE_PREFIX . "users AS w ON (w.uid = s.wall_id)
                        WHERE shown=1 AND (s.UID = s.wall_id) AND (s.parent = -1) AND (s.wall_id = ".$wallID.")
        ORDER BY PID DESC
		LIMIT 0, 1"); */
		
		$query = $db->query("SELECT * FROM ".TABLE_PREFIX."statusfeed WHERE (self = 1) AND (shown=1) AND (parent = -1)
			AND (UID = ".$wallID.")
			ORDER BY `PID` DESC
			LIMIT 1");
		
		$queryData = $db->fetch_array($query);
		return $queryData; 
    }

	function statusfeed_postbit (&$post) {
		// $post['message'] = "test";
		global $templates, $lang;

		$status_raw = getSanitizedStatusArray($post['sf_currentstatus']);
		$userstatus = $status_raw;

		eval("\$statusfeed = \"".$templates->get("statusfeed_postbit")."\";");
		if ($userstatus != '' && $userstatus != null) {
			// $post['statusfeed'] = $lang->statusfeed_postbit . ": ". $statusfeed;
			$post['statusfeed'] = $statusfeed;
		}
		else {
			$post['statusfeed'] = $lang->statusfeed_no_status;
		}
	}

	// This function cuts out unnecessary database fields and sanitizes the rest. 
	// This prevents a rogue variable from being accessed by an addition to the template. 
	function getSanitizedStatusArray($status) {
		global $mybb;
		require_once MYBB_ROOT."/inc/class_parser.php";
		// global $parser;

		$parser = new postParser(); 
		$parser_options = array(
    			'allow_html' => 'no',
    			'allow_mycode' => 'yes',
    			'allow_smilies' => 'yes',
    			'allow_imgcode' => 'no',
    			'filter_badwords' => 'yes',
    			'nl2br' => 'no'
		); 

		$returnArray = array(); 

		// Truncate this if it is a large status.
		if (strlen($status) > $mybb->settings['statusfeed_mini_truncate_length']) {
			$status = substr($status,0,$mybb->settings['statusfeed_mini_truncate_length']).'...';
		}

		$returnArray = $parser->parse_message($status, $parser_options);
		return $returnArray;
	}