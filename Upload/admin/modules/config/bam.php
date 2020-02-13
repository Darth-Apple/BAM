<?php

/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); */

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
	if(!defined("IN_MYBB")) {
		die("Hacking attempt."); // direct access to this file not allowed. 
	}

	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);
	//error_reporting(E_ALL);
	
	$lang->load('bam');
	
	$class_select = array(
		"green" => $lang->bam_green,
		"yellow" => $lang->bam_yellow,
		"red" => $lang->bam_red,
		"blue" => $lang->bam_blue,
		"magenta" => $lang->bam_magenta,
		"silver" => $lang->bam_silver,
		"bam_custom" => $lang->bam_custom
	); // list of programmed classes for BAM announcements. This list may expand in future versions of this plugin. 

	// list of locations for location selector. 
	$location_select = array(
		"global" => $lang->bam_list_display_global,
		"index" => $lang->bam_list_display_index,
		"forums" => $lang->bam_list_display_forums,
		"special" => $lang->bam_list_display_special
	); // list of programmed classes for BAM announcements. This list may expand in future versions of this plugin.
	
	$announcement_type = array(
		"standard" => $lang->bam_standard_select,
		"random" => $lang->bam_random_select
	); // list of programmed classes for BAM announcements. This list may expand in future versions of this plugin. 
	

	$tags_button = " <button id='showtags_link' onclick='showAnnouncementTags();'>".$lang->bam_form_tags_link." </button>";

	global $class_select;

	/***** Add breadcrumbs and tabs *****/
	
	if($mybb->input['action'] == "edit") {
		$page->add_breadcrumb_item($lang->bam_edit_announcement);
	}
	else if ($mybb->input['action'] == "add") {
		$page->add_breadcrumb_item($lang->bam_add_announcement);
	}

	/* New in 2.0. Move to new page in admin CP. This only shows if random mode is enabled. */ 
	else if ($mybb->input['action'] == "manage_random") {
		$page->add_breadcrumb_item($lang->bam_manage_random);
	}
	else {
		$page->add_breadcrumb_item($lang->bam_manage_announcements);
	}

	// The normal manage page has a different description if random mode is disabled. Load the correct description based on the setting.
	$bam_random = false;
	$normal_manage_description = $lang->bam_manage_desc_norandom;

	if ($mybb->settings['bam_random'] == 1) {
		$bam_random = true;
		$normal_manage_description = $lang->bam_manage_desc;
	}
	// Create tabs that always display. 
	$sub_tabs['bam_manage'] = array(
		'title' => $lang->bam_manage,
		'link' => "index.php?module=config-bam",
		'description' => $normal_manage_description
	);

		if ($bam_random) {
			$sub_tabs['bam_manage_random'] = array(
				'title' => $lang->bam_manage_random,
				'link' => "index.php?module=config-bam&action=manage_random",
				'description' => $lang->bam_manage_random_desc
			);
		}

	// Display a different description if the user hasn't enabled advanced mode. 
	$bam_add_description = $lang->bam_add_announcement_desc;
	if ($mybb->settings['bam_advanced_mode'] == 0) {
		$bam_add_description = $lang->bam_add_announcement_noadvance_desc;
	}

	$sub_tabs['bam_add'] = array(
		'title' => $lang->bam_add_announcement,
		'link' => "index.php?module=config-bam&action=add",
		'description' => $bam_add_description
	);

	$page->output_header($lang->bam_title);

	// create tabs that display only under certain conditions (such as edit), and set highlighted tab. 

	if ($mybb->input['action'] == "edit") {
		$sub_tabs['bam_edit'] = array(
			'title' => $lang->bam_edit,
			'link' => "index.php?module=config-bam&action=edit",
			'description' => $lang->bam_edit_desc
		);
		$page->output_nav_tabs($sub_tabs, 'bam_edit');
	}

	else if ($mybb->input['action'] == "add") {
		$page->output_nav_tabs($sub_tabs, 'bam_add');
	}

	else if ($mybb->input['action'] == "manage_random") {
		$page->output_nav_tabs($sub_tabs, 'bam_manage_random');
	}

	else {
		$page->output_nav_tabs($sub_tabs, 'bam_manage');
		
		/* $page->output_nav_tabs($sub_tabs, 'bam_manage_random'); */
	}

	/***** Process requests *****/

		
	if(($mybb->input['action'] == 'order') && ($mybb->request_method=="post") && (is_array($mybb->input['disporder']))) {
		// update announcement display orders
		verify_post_check($mybb->input['my_post_key']);
		$count = 0;
		 
		foreach($mybb->input['disporder'] as $update_pid => $order)
		{
			$db->update_query("bam", array('disporder' => intval($order)), "PID='".intval($update_pid)."'");
			$count++;
		}
					
		if ($count > 0) {
			flash_message($lang->bam_order_success, 'success');
		}
		else {
			flash_message($lang->bam_no_announcement, 'error');
		}
		if ($mybb->input['mode'] == "random") {
			admin_redirect("index.php?module=config-bam&action=manage_random");
		} else {
			admin_redirect("index.php?module=config-bam");			
		}
	}


	if(($mybb->input['action'] == 'pin') && ($mybb->request_method=="get") && !empty($mybb->input['id'])) {
		// Process pin announcement request
		$key = verify_post_check($mybb->input['my_post_key'], true); 
		if ($key == false) {
			flash_message($lang->bam_invalid_post_code, 'error');
			admin_redirect("index.php?module=config-bam");
		}
		
		$id = (int)$mybb->input['id'];
		$db->update_query("bam", array('pinned' => 1), "PID='$id'");
		flash_message($lang->bam_pin_success, 'success');
		admin_redirect('index.php?module=config-bam');
	}

	// Turns this announcement into a random mode announcement. Redirects to the edit page afterwards. 
	if(($mybb->input['action'] == 'make_random') && ($mybb->request_method=="get") && !empty($mybb->input['id'])) {
		$key = verify_post_check($mybb->input['my_post_key'], true); 
		if ($key == false) {
			flash_message($lang->bam_invalid_post_code, 'error');
			admin_redirect("index.php?module=config-bam");
		}
		
		$id = (int)$mybb->input['id'];
		$db->update_query("bam", array('random' => 1), "PID='$id'");
		flash_message($lang->bam_make_random_success, 'success');
		admin_redirect('index.php?module=config-bam&action=edit&id='.(int) $id);
	}

	// Process the "make standard mode" link. This resets the announcement to standard mode and displays the edit page. 
	if(($mybb->input['action'] == 'make_standard') && ($mybb->request_method=="get") && !empty($mybb->input['id'])) {
		$key = verify_post_check($mybb->input['my_post_key'], true); 
		if ($key == false) {
			flash_message($lang->bam_invalid_post_code, 'error');
			admin_redirect("index.php?module=config-bam");
		}
		
		$id = (int)$mybb->input['id'];
		$db->update_query("bam", array('random' => 0), "PID='$id'");
		flash_message($lang->bam_make_standard_success, 'success');
		admin_redirect('index.php?module=config-bam&action=edit&id='.(int) $id);
	}


	if(($mybb->input['action'] == 'unpin') && ($mybb->request_method=="get") && !empty($mybb->input['id'])) {
		// Process unpin announcement request
		$key = verify_post_check($mybb->input['my_post_key'], true); 
		if ($key == false) {
			flash_message($lang->bam_invalid_post_code, 'error');
			admin_redirect("index.php?module=config-bam");
		}
		
		$id = (int)$mybb->input['id'];
		$db->update_query("bam", array('pinned' => 0), "PID='$id'");
		flash_message($lang->bam_unpin_success, 'success');
		admin_redirect('index.php?module=config-bam');
	}



	if($mybb->input['action'] == "edit") {
		// generate the announcement edit page. 

		if (empty($mybb->input['id'])) { 	
			// no announcement defined
			admin_redirect('index.php?module=config-bam');
			$page->output_inline_error(array($lang->bam_no_announcement));
		}

		$id = (int)$mybb->input['id'];
		$query = $db->query("
			SELECT *
			FROM ".TABLE_PREFIX."bam
			WHERE PID = '$id'");
		$data = array();
		$page->add_breadcrumb_item($lang->bam_edit_announcement, "index.php");

		while($querydata = $db->fetch_array($query)) {	
			$data['PID'] = $querydata['PID'];
			$data['announcement'] = $querydata['announcement'];
			$data['class'] = $querydata['class'];
			$data['pinned'] = (int)$querydata['pinned'];
			$data['forums'] = create_selectedForumArray(htmlspecialchars($querydata['forums']));
			$data['disporder'] = (int)$querydata['disporder'];
			$data['link'] = $querydata['link'];
			$data['global'] = (int)$querydata['global']; // deprecated. Will remove in next beta. 
			$data['random'] = (int)$querydata['random'];
			$data['location'] = (int)$querydata['global'];
			$data['additional_pages'] = $querydata['additional_display_pages'];
			$data['usergroup'] = $querydata['groups'];
			$data['usergroup'] = explode(',', $querydata['groups']);
		}

		$form = new Form("index.php?module=config-bam", "post");
		$form_container = new FormContainer($lang->bam_edit_announcement);

		// Used to enable javascript for announcement add/edit forms. 
		echo $form->generate_hidden_field("announcementPageFlag", "true", array("id"=>"announcementPageFlag"));

		if ($data['location'] == 2) {
			$announcementLocation = "forums"; // set to specific forums. Specific forums is 2 in MyBB selector.  
		}
		else if ($data['location'] == 1) {
			$announcementLocation = "global"; // select to global. Global = 0 in MyBB selector.  
		}
		else if ($data['location'] == 0) {
			$announcementLocation = "index"; // Set to index only. Index only is 1 in MyBB selector. 
		}
		else {
			$announcementLocation = "special"; // Set to other. 3 in MyBB selector element. 
		}


		// Display a random mode selector if random mode is enabled. 
		if ($mybb->settings['bam_random'] == 1) {
			
			if ($data['random'] == 1) {
				$currentType = "random";
			} else {
				$currentType = "standard";
			}

			$form_container->output_row($lang->bam_announcement_type, $lang->bam_announcement_type_desc, $form->generate_select_box('announcement_type', $announcement_type, $currentType, array('id' => 'announcementType')), 'announcement_type');
			
		} else {
			echo $form->generate_hidden_field("announcement_type", "standard", array("id"=>"announcementTypeHidden"));
		}

		// Show the currect description text depending on whether BAM is in advanced or standard mode. 
		$edit_announcement_description = $lang->bam_form_announcement_desc; 
		if ($mybb->settings['bam_advanced_mode'] == 1) {
			$edit_announcement_description = $lang->bam_form_announcement_advanced_desc; 
		}

		$edit_announcement_description .= $tags_button;

		// Generate input fields. 
		$form_container->output_row($lang->bam_form_pinned,  $lang->bam_form_pinned_desc, $form->generate_yes_no_radio('pinned', $data['pinned'], array("id" => "sticky_select", "class" => "remove_on_random")), 'sticky_select_row');

		$form_container->output_row($lang->bam_display_mode, $lang->bam_display_mode_desc, $form->generate_select_box('location', $location_select, $announcementLocation, array('id' => 'location')), 'location');
		$form_container->output_row($lang->bam_forum_select,  $lang->bam_forum_select_desc, $form->generate_forum_select('forum_select', $data['forums'], array("id" => "forum_select", "class" => "forum_select", 'size' => 6, 'multiple' => true)), 'forum_select_row');
		$form_container->output_row($lang->bam_additional_pages, $lang->bam_additional_pages_desc, $form->generate_text_box("additional_pages", html_entity_decode($data['additional_pages']), array("class" => "text_input", "id" => 'additional_pages', "style" => "width: 75%;")), 'additional_pages');

		// $form_container->output_row($lang->bam_make_global,  $lang->bam_make_global_desc, $form->generate_yes_no_radio('global', $data['global'], array("id" => "global_select", "class" => "remove_on_random")), 'global_select_row');
		$form_container->output_row($lang->bam_form_announcement, $edit_announcement_description, $form->generate_text_area("announcement", html_entity_decode($data['announcement']), array("class" => "text_input align_left", "style" => "width: 75%;", "id" => "announcement_text")), 'announcement');
		
		echo $form->generate_hidden_field("id", intval($id));
		echo $form->generate_hidden_field("action", "submit_edit");
							
		if (array_key_exists($data['class'], $class_select)) {
			$class_select_active = $data['class'];
			$custom_class = null;
		}
		else {
			$class_select_active = 'bam_custom';
			$custom_class = $data['class'];
		}

		$form_container->output_row($lang->bam_form_style, $lang->bam_form_style_desc, $form->generate_select_box('class', $class_select, $class_select_active, array('id' => 'style', 'value' => 'bam_custom')), 'class');
		$form_container->output_row($lang->bam_form_class_custom, $lang->bam_form_class_custom_desc, $form->generate_text_box("custom_class", html_entity_decode($custom_class), array("class" => "text_input", "style" => "width: 25%;", "id" => "custom_class", 'value' => $data['class'])), 'custom_class');	
		
		$options = array();
		$query = $db->simple_select("usergroups", "gid, title", null, array('order_by' => 'title'));
		
		while($usergroup = $db->fetch_array($query))
		{
			$options[(int)$usergroup['gid']] = $usergroup['title'];
		}

		$form_container->output_row($lang->bam_form_groups, $lang->bam_form_groups_desc, $form->generate_select_box('usergroup[]', $options, $data['usergroup'], array('id' => 'usergroup', 'multiple' => true, 'size' => 5)), 'usergroup');
		// $form_container->output_row($lang->bam_form_url, $lang->bam_form_url_desc, $form->generate_text_box("url", html_entity_decode($data['link']), array("class" => "text_input align_right", "style" => "width: 25%;")), 'url');
		// $form_container->output_row($lang->bam_form_pinned, $lang->bam_form_pinned_desc, $form->generate_yes_no_radio('pinned', (int)$data['pinned']));
			// Announcement URL mode is deprecated in version 2.0 due to the fact that BBcode can easily create links. Advanced mode brings it back. 
		
		if ($mybb->settings['bam_advanced_mode'] == 1) {
			$form_container->output_row($lang->bam_form_order, $lang->bam_form_order_desc, $form->generate_text_box("disporder", $data['disporder'], array("class" => "text_input align_right", "style" => "width: 25%;")), 'disporder');
		}
		else {
			// echo $form->generate_hidden_field("url", "");
			echo $form->generate_hidden_field("additional_pages", "");
		}	

		$form_container->output_row($lang->bam_form_url, $lang->bam_form_url_desc, $form->generate_text_box("url", html_entity_decode($data['link']), array("class" => "text_input align_right", "style" => "width: 25%;")), 'url');

		$buttons[] = $form->generate_submit_button($lang->bam_form_edit_submit);
		$form_container->end();
		$form->output_submit_wrapper($buttons);
		$form->end();
		echo "<br />";

		// Run the form update function to ensure that the form does not display invalid fields if the announcement is set to random.
		// echo "<script>deleteRandomElements();</script>";
	}


	if($mybb->input['action'] == 'submit_edit' && $mybb->request_method=="post") {
		// process edit announcement form
		verify_post_check($mybb->input['my_post_key']); 

		$id = (int)$mybb->input['id'];
		$url = null;
		$disporder = 1;
		$pinned = 0;

		// We're editing a non-random-mode announcement. 
		if ($mybb->input['announcement_type'] == "standard") {
			$isRandom = 0; 
		} else {
			$isRandom = 1; 
		}

		$forumList = null; 
		// This is repurposed to a full drop down select box. 1 = global. 0 = index. 2 = forum select boards. 
		if ($mybb->input['location'] == "global") {
			$isGlobal = 1;
		}
		// Activate forum select box. Repurposed in drop down global selector. 
		else if ($mybb->input['location'] == "special") { 
			$isGlobal = 3;
		}
		else if ($mybb->input['location'] == "index") {
			$isGlobal = 0; // display index only. 
		}
		else {
			$isGlobal = 2; // 
		}

		if ($mybb->input['additional_pages'] != null) {
			$additionalPages = $db->escape_string(htmlspecialchars($mybb->input['additional_pages']), ENT_QUOTES);
		}
		else {
			$additionalPages = null;
		}

		if (($mybb->input['custom_class'] != null)) {
			$class = $db->escape_string(htmlspecialchars($mybb->input['custom_class'], ENT_QUOTES));
		}
		else {
			$class = $db->escape_string(htmlspecialchars($mybb->input['class'], ENT_QUOTES));
		}

		// Get forum select data. 
		if((!isset($mybb->input['forum_select'])) || (empty($mybb->input['forum_select'])) || (in_array('*', $mybb->input['forum_select']))) {
			$mybb->input['forum_select'] = '*';
		}
		else {
			$mybb->input['forum_select'] = implode(',', array_map('intval', $mybb->input['forum_select']));
		}

		$forumList = $mybb->input['forum_select'];

		/*
		if (($mybb->input['custom_class'] != null)) {
			$class = $db->escape_string(htmlspecialchars($mybb->input['custom_class'], ENT_QUOTES));
		}
		else {
			$class = $db->escape_string(htmlspecialchars($mybb->input['class'], ENT_QUOTES));
		} */
		
		if ($mybb->input['url'] != null) {
			$url = $db->escape_string(htmlspecialchars($mybb->input['url'], ENT_QUOTES));
		}
		
		// BAM 2.0: Pinned is now repurposed for undismissable announcements. 
		if ($mybb->input['pinned'] != 0) {
			$pinned = 1;
		}

		// Process usergroups and create a database-friendly string. 

		if((!isset($mybb->input['usergroup'])) || (empty($mybb->input['usergroup'])) || (in_array('*', $mybb->input['usergroup']))) {
			$mybb->input['usergroup'] = '*';
		}
		else {
			$mybb->input['usergroup'] = implode(',', array_map('intval', $mybb->input['usergroup']));
		}
		$usergroups = $db->escape_string($mybb->input['usergroup']);
		$disporder = (int)$mybb->input['disporder'];
		$announcement = $db->escape_string(htmlspecialchars($mybb->input['announcement'], ENT_QUOTES));

		$db->update_query("bam", array('pinned' => $pinned, 'disporder' => $disporder, 'random' => $isRandom, 'global' => $isGlobal, 'additional_display_pages' => $additionalPages, 'announcement' => $announcement, 'groups' => $usergroups, 'link' => $url, 'class' => $class, 'forums' => $forumList), "PID='$id'");

		flash_message($lang->bam_edit_success, 'success');
		if ($isRandom == 0) {
			admin_redirect('index.php?module=config-bam');
		} else {
			admin_redirect('index.php?module=config-bam&action=manage_random');
		}
	}

	if($mybb->input['action'] == 'submit_add' && $mybb->request_method=="post") {
		// process new announcement form		
		verify_post_check($mybb->input['my_post_key']); 

		$url = null;
		$disporder = 1;
		$pinned = 0; // Code refactored from version one. Ths is the sticky setting. 

		// We're adding a non-random-mode announcement. 
		if ($mybb->input['announcement_type'] == "standard") {
			$isRandom = 0; 
		} else {
			$isRandom = 1; 
		}

		// Activate forum select box. 
		$forumList = null;
		if ($mybb->input['location'] == "global") {
			$isGlobal = 1;
		}
		// Activate forum select box. Repurposed in drop down global selector. 
		else if ($mybb->input['location'] == "special") { 
			$isGlobal = 3;
		}
		else if ($mybb->input['location'] == "index") {
			$isGlobal = 0; // display index only. 
		}
		else {
			$isGlobal = 2; 
		}

		// Get forum selector data. 
		if((!isset($mybb->input['forum_select'])) || (empty($mybb->input['forum_select'])) || (in_array('*', $mybb->input['forum_select']))) {
			$mybb->input['forum_select'] = '*';
		}
		else {
			$mybb->input['forum_select'] = implode(',', array_map('intval', $mybb->input['forum_select']));
		}
		$forumList = $mybb->input['forum_select'];




		if ($mybb->input['additional_pages'] != null) {
			$additionalPages = $db->escape_string(htmlspecialchars($mybb->input['additional_pages']), ENT_QUOTES);
		}
		else {
			$additionalPages = null;
		}

		if (($mybb->input['custom_class'] != null)) {
			$class = $db->escape_string(htmlspecialchars($mybb->input['custom_class'], ENT_QUOTES));
		}
		else {
			$class = $db->escape_string(htmlspecialchars($mybb->input['class'], ENT_QUOTES));
		}

		// Deprecated. Still available through advanced mode. 
		if ($mybb->input['url'] != null) {
			$url = $db->escape_string(htmlspecialchars($mybb->input['url'], ENT_QUOTES));
		}
		
		if ($mybb->input['pinned'] != 0) {
			$pinned = 1;
		}

		if((!isset($mybb->input['usergroup'])) || (empty($mybb->input['usergroup'])) || (in_array('*', $mybb->input['usergroup']))) {
			$mybb->input['usergroup'] = '*';
		}
		else {
			$mybb->input['usergroup'] = implode(',', array_map('intval', $mybb->input['usergroup']));
		}

		// Create array for inserting new edits into the database

		$inserts = array(
			'announcement' => $db->escape_string(htmlentities($mybb->input['announcement'])),
			'class' => $class,
			'link' => $url,
			'pinned' => $pinned,
			'global' => $isGlobal,
			'random' => $isRandom,
			'forums' => $forumList,
			'additional_display_pages' => $additionalPages,
			'date' => time(),
			'disporder' => (int)$mybb->input['disporder'],
			'groups' => $db->escape_string($mybb->input['usergroup'])
			);
		$db->insert_query('bam', $inserts);
		flash_message($lang->bam_add_success, 'success');
		if ($isRandom == 0) {
			admin_redirect('index.php?module=config-bam');
		} else {
			admin_redirect('index.php?module=config-bam&action=manage_random');
		}
	}


	
	if($mybb->input['action'] == 'delete' && $mybb->request_method=="get") {
		// process delete announcement
		$key = verify_post_check($mybb->input['my_post_key'], true); 
		if ($key == false) {
			flash_message($lang->bam_invalid_post_code, 'error');
			admin_redirect("index.php?module=config-bam");
		}		

		// Fetch the announcement ID from the user's input. 
		$PID = (int) $mybb->input['id'];
		if($PID == null) {
			// no announcement was defined. 
			flash_message($lang->bam_delete_error, 'error');
			admin_redirect('index.php?module=config-bam');
		}
	
		// Check to make sure that the announcement exists before attempting to delete it. 
		$query = $db->simple_select('bam', '*', "PID='{$PID}'");
		$querydata = $db->fetch_array($query);
	
		if(!$querydata['PID']) {
			// The announcement was defined, but did not exist. 
			flash_message($lang->bam_delete_error, 'error');
			admin_redirect('index.php?module=config-bam');
		}
		
		$db->delete_query('bam', "PID='{$PID}'");		
		flash_message($lang->bam_delete_success, 'success');
		admin_redirect('index.php?module=config-bam');
	}
		

	/* This page handles the add announcement functionality... */

	if ($mybb->input['action'] == "add") {
		// generate add announcement form. 

		$form = new Form("index.php?module=config-bam", "post");
		$form_container = new FormContainer($lang->bam_form_add);

		// Used to enable javascript for announcement add/edit forms. 
		echo $form->generate_hidden_field("announcementPageFlag", "true", array("id"=>"announcementPageFlag"));

		// Initialize custom class variable. 
		if (isset($mybb->input['custom_class'])) {
			$customClass = htmlspecialchars($mybb->input['custom_class']);
		}
		else {
			$customClass = "";
		}

		// Check if advanced mode is enabled before initializing special pages. 
		if (isset($mybb->input['additional_pages'])) {
			$additionalPages = htmlspecialchars($mybb->input['additional_pages']);
		}
		else {
			$additionalPages = "";
		}

		$fieldType = ""; // $mybb->input['fieldtype'] 
		// $fieldType = $mybb->input['fieldtype']; 
		echo $form->generate_hidden_field("action", "submit_add");
		

		// Display a random mode selector if random mode is enabled. 
		if ($mybb->settings['bam_random'] == 1) {

			if (isset($_GET['make_random'])) {
				$fieldType = "random";
			}
			else {
				$fieldType = "";
			}
			$form_container->output_row($lang->bam_announcement_type, $lang->bam_announcement_type_desc, $form->generate_select_box('announcement_type', $announcement_type, $fieldType, array('id' => 'announcementType')), 'announcement_type');
		} else {
			echo $form->generate_hidden_field("announcement_type", "standard", array("id"=>"announcementTypeHidden"));
		}
		
		// Show the currect description text depending on whether BAM is in advanced or standard mode. 
		$add_announcement_description = $lang->bam_form_announcement_desc; 
		if ($mybb->settings['bam_advanced_mode'] == 1) {
			$add_announcement_description = $lang->bam_form_announcement_advanced_desc; 
		}
		
		//$add_announcement_description .= $lang->bam_form_tags_link;

		$add_announcement_description .= $tags_button;
		// Generate input fields. 
		$form_container->output_row($lang->bam_form_pinned,  $lang->bam_form_pinned_desc, $form->generate_yes_no_radio('pinned', 0, array("id" => "sticky_select", "class" => "remove_on_random")), 'sticky_select_row');
		
		$form_container->output_row($lang->bam_display_mode, $lang->bam_display_mode_desc, $form->generate_select_box('location', $location_select, 'index', array('id' => 'location')), 'location');
		$form_container->output_row($lang->bam_forum_select,  $lang->bam_forum_select_desc, $form->generate_forum_select('forum_select', 0, array("id" => "forum_select", "class" => "forum_select", 'size' => 6, 'multiple' => true, 'main_option' => $lang->all_forums)), 'forum_select_row');
		$form_container->output_row($lang->bam_additional_pages, $lang->bam_additional_pages_desc, $form->generate_text_box("additional_pages", $additionalPages, array("class" => "text_input", "style" => "width: 75%;", "id" => "additional_pages")), 'additionalPages');

		// $form_container->output_row($lang->bam_make_global,  $lang->bam_make_global_desc, $form->generate_yes_no_radio('global', 0, array("id" => "global_select", "class" => "remove_on_random")), 'global_select_row');
		$form_container->output_row($lang->bam_form_announcement, $add_announcement_description, $form->generate_text_area("announcement", '', array("class" => "text_input align_left", "style" => "width: 75%;", "id" => "announcement_text")), 'announcement');	
		$form_container->output_row($lang->bam_form_style, $lang->bam_form_style_desc, $form->generate_select_box('class', $class_select, $fieldType, array('id' => 'style')), 'class');
		$form_container->output_row($lang->bam_form_class_custom, $lang->bam_form_class_custom_desc, $form->generate_text_box("custom_class", $customClass, array("class" => "text_input", "style" => "width: 25%;", "id" => "custom_class")), 'custom_class');	

		// Generate usergroup select box. 
		$options = array();
		$query = $db->simple_select("usergroups", "gid, title", null, array('order_by' => 'title'));
		while($usergroup = $db->fetch_array($query))
		{
			$options[(int)$usergroup['gid']] = $usergroup['title'];
			$default_usergroups[] = (int) $usergroup['gid'];
		}

		$form_container->output_row($lang->bam_form_groups, $lang->bam_form_groups_desc, $form->generate_select_box('usergroup[]', $options, $default_usergroups, array('id' => 'usergroup', 'multiple' => true, 'size' => 5)), 'usergroup');
		
		$query = $db->query("SELECT disporder FROM ".TABLE_PREFIX."bam ORDER BY disporder DESC LIMIT 1"); // select last announcement by display order. 
		$last = $db->fetch_array($query);

		if ($mybb->settings['bam_advanced_mode'] == 1) {		
			$form_container->output_row($lang->bam_form_url, $lang->bam_form_url_desc, $form->generate_text_box("url", $mybb->input['url'], array("class" => "text_input align_right", "style" => "width: 25%;")), 'url');
		}
		else {
			echo $form->generate_hidden_field("url", "");
			// echo $form->generate_hidden_field("additional_pages", "");
		}	
		$form_container->output_row($lang->bam_form_order, $lang->bam_form_order_desc, $form->generate_text_box("disporder", ((int) $last['disporder'] + 1), array("class" => "text_input align_right", "style" => "width: 25%;")), 'disporder');		

		$buttons[] = $form->generate_submit_button($lang->bam_form_add_submit);
		$form_container->end();
		$form->output_submit_wrapper($buttons);
		$form->end();
		echo "<br />";
	}

	else if ($mybb->input['action'] == "manage_random" ){
		generate_manage_page("random");
	}
	
	if (empty($mybb->input['action'])) {
		generate_manage_page("standard"); 
	}

		// list announcements for management. 
	function generate_manage_page($type) {
		global $mybb, $db, $page, $lang;

		require_once MYBB_ROOT."/inc/class_parser.php";
		$parser = new postParser(); 

		// If advanced mode is enabled, HTML is allowed by the post parser. 
		
		$allowHTML = "no";
		if ($mybb->settings['advanced_mode'] == 1) {
			$allowHTML = "yes";
		}
		$parser_options = array(
    			'allow_html' => $allowHTML,
    			'allow_mycode' => 'yes',
    			'allow_smilies' => 'yes',
    			'allow_imgcode' => 'yes',
    			'filter_badwords' => 'no',
    			'nl2br' => 'yes'
		);

		// We create different fields, depending on whether random mode is enabled or not. 

		if ($type == "random") {
			$form_t = new Form("index.php?module=config-bam&action=add&make_random=1", "post");
			$table = new FormContainer($lang->bam_manage_random_form_container);
			echo $form_t->generate_hidden_field("mode", "random");
		}
		else {
			$form_t = new Form("index.php?module=config-bam", "post");
			$table = new FormContainer($lang->bam_manage);
			echo $form_t->generate_hidden_field("mode", "standard");
		}

		echo $form_t->generate_hidden_field("action", "order");
		
		$table->output_row_header($lang->bam_manage_announcement, array('width' => '64%'));
		$table->output_row_header($lang->bam_manage_class, array('width' => '12%'));
		
		// Output the correct table header depending on the announcement's type. 
		if ($type == "random") {
			$table->output_row_header($lang->bam_make_standard_header, array('width' => '11%')); 
		} else {
			$table->output_row_header($lang->bam_manage_order, array('width' => '11%')); 
		}
		$table->output_row_header($lang->bam_manage_actions, array('width' => '13%', 'text-align' => 'center')); 
		
		if ($type == "random") {
			$query = $db->query("
			SELECT *
			FROM ".TABLE_PREFIX."bam
			WHERE random = 1 ORDER BY pinned DESC, disporder ASC, PID ASC
		");
		} else {
			$query = $db->query("
			SELECT *
			FROM ".TABLE_PREFIX."bam
			WHERE random = 0 ORDER BY pinned DESC, disporder ASC, PID ASC
		");
		}
	
		$data = array();
		$count = 0;
		$prefixVal = "";
		while($querydata = $db->fetch_array($query))
		{	
			if (($querydata['additional_display_pages'] != null)) {
				// $prefixVal = "<img src='../images/jump.png' alt='{$lang->bam_has_additional_pages}' title='{$lang->bam_has_additional_pages}'> &nbsp;&nbsp;";
				$prefixVal = "<div class=\"float_right\"><img src='../images/jump.png' title='{$lang->bam_has_additional_pages}'alt='{$lang->bam_has_additional_pages}' /></div>";
			}
			else {
				$prefixVal = "";
			}

			if ($querydata['link'] != null) {
				$announcementText = $parser->parse_message("[url=".$querydata['link']."]".html_entity_decode($querydata['announcement'])."[/url]", $parser_options);
			}
			else {
				// $data[$count]['announcement'];
				$announcementText = $parser->parse_message(html_entity_decode($querydata['announcement']), $parser_options); // parse bbcode
			}

			// echo "<br /><br /> announcementText" . $announcementText;
			$data[$count]['announcement'] = $prefixVal . $announcementText;
			$data[$count]['PID'] = $querydata['PID'];
			$data[$count]['class'] = $querydata['class'];
			$data[$count]['pinned'] = $querydata['pinned'];
			$data[$count]['disporder'] = $querydata['disporder'];
			$count++;
		}
		
	
		if ($count==0) {
			$table->output_cell($lang->bam_manage_null); // no announcements found
			$table->output_cell("");
			$table->output_cell("");
			$table->output_row("");
		}
		else {
			$i = 0;
			while ($i <= $count) {
				if ((isset($data[$i]['PID'])) && ($data[$i]['PID'] != null)) {
					$table->output_cell($data[$i]['announcement']);
					$table->output_cell($data[$i]['class']);
					//$table->output_cell("<center><input type='text' name=\"disporder[".$data[$i]['PID']."]\" value='".$data[$i]['disporder']."' /></center>");
					if ($type == "random") {
						$table->output_cell("<center><a href=\"index.php?module=config-bam&action=make_standard&id=".(int) $data[$i]['PID']. "&my_post_key=".$mybb->post_code . "\" onclick=\"confirm('".$lang->bam_make_standard_confirm."');\">" . $lang->bam_make_standard . "</a></center>");
					} else {
						$table->output_cell("<center><input type='text' name=\"disporder[".$data[$i]['PID']."]\" value='".$data[$i]['disporder']."' /></center>");
					}
					// $table->output_cell("");

					// Generate the options menu. 
					
					$popup = generate_announcement_controls($data[$i]['PID'], $data[$i]['pinned']);
					$table->output_cell($popup->fetch(), array("class" => "align_center"));
					$table->construct_row();
				}	
				$i++;		
			}
		
		}

		$buttons = array();

		// Because this form submits with post, we have issues with setting additional parameters on the add page. 
		// We must create a link and style it like a button. 
		if ($type == "random") {
			$style = "
				border: 1px solid #999;
				padding: 4px 7px;
				background: #e3e3e3 url(images/submit_bg.png) repeat-x top !important;
				color: #444;
				font-weight: bold;
				font-family: 'Lucida Grande', Tahoma, Verdana, Arial, sans-serif;
				margin-right: 3px;
				font-size: 1.1em;
				border-radius: 5px;
				-moz-border-radius: 5px;
				-webkit-border-radius: 5px;
				outline: 0;		
			";
			$buttons[] = "<a style='$style' href='index.php?module=config-bam&action=add&make_random=1'>".$lang->bam_add_new_random."</a>";
		} else {
			// Not managing random mode display page. Generate a normal button for display orders. 
			$buttons[] = $form_t->generate_submit_button($lang->bam_manage_order_submit);
		}

		$table->end();			
		$form_t->output_submit_wrapper($buttons);
	}


function generate_announcement_controls ($id, $ispinned) {
	global $mybb, $lang; 

	$id = (int) $id;

	$popup = new PopupMenu("announcement_{$id}", $lang->bam_manage_popupmenu);
	$popup->add_item($lang->bam_manage_edit, 'index.php?module=config-bam&action=edit&id=' . $id);
	$popup->add_item($lang->bam_manage_delete,  "index.php?module=config-bam&action=delete&id=" . $id . "&my_post_key=".$mybb->post_code, "return confirm('".$lang->bam_manage_delete_confirm."');");
	
	// Add the link to make an announcement random if random mode is enabled and we are on the standard page. 
	if (($_GET['action'] != "manage_random") && $mybb->settings['bam_random'] != 0) {
		$popup->add_item($lang->bam_make_random,  "index.php?module=config-bam&action=make_random&id=" . $id . "&my_post_key=".$mybb->post_code, "return confirm('".$lang->bam_make_random_confirm."');");
		
		if ($ispinned) {
			$popup->add_item($lang->bam_manage_unpin,  "index.php?module=config-bam&action=unpin&id=" . $id . "&my_post_key=".$mybb->post_code);
		} else {
			$popup->add_item($lang->bam_manage_pin,  "index.php?module=config-bam&action=pin&id=" . $id . "&my_post_key=".$mybb->post_code);
		}	
	}

	return $popup;
}

// For the edit page. Processes the data in the database and turns it into a format that can be sent to the multi-select box. 
function create_selectedForumArray($forums) { 
	$explodedForums = explode(',', $forums);
	return array_map('trim',$explodedForums);
}

// We need to output some javascript for the add and edit announcement pages. 
// This removes fields that don't pertain to random mode announcements, based on the select box for announcement type. 
// If the user re-selects "standard," the fields return. 

$form_javascript = " 
<script>
	const isEmpty = str => !str.trim().length;
	// if (document.getElementById('announcementPageFlag' != null)) {
	if (document.getElementById('announcementType') != null) {
		document.getElementById('location').onchange = function() {manageDisplayModes(changed='true')}
		manageDisplayModes();
		correctForumSelector(); 

		document.getElementById('style').onchange = function() {setCustomClass()}
		setCustomClass();
		manageDisplayModes();
	}

	// Bug fix. MyBB's default forum selector does not properly create a multiple select item. 
	// So we ghetto rig it because this works. And we can select multiple forums now. 

	function correctForumSelector() {
		forumSelector = document.getElementById('forum_select'); 
		if (forumSelector != null) {
			forumSelector.setAttribute('name', 'forum_select[]');
		}
	}

	if (document.getElementById('announcementType') != null) {
		document.getElementById('announcementType').onchange = function() {deleteRandomElements()}
		deleteRandomElements();
	}

		function deleteRandomElements() {
			var sel = document.getElementById('announcementType');
			
			var displayLocationSel = document.getElementById('location');
			var forumClass = document.getElementById('forum_select');

			
			if (sel.tagName != 'SELECT') {
				return; 
			}
			
			var value = sel.options[sel.selectedIndex].value;
			var text = sel.options[sel.selectedIndex].text;

			var displayVar = '';
			if ((text == '" . $lang->bam_random_select . "') || value == 'random') {
				var displayVar = 'none';
			}

			forumClass.parentNode.parentNode.style.display = displayVar; 
			displayLocationSel.parentNode.parentNode.parentNode.style.display = displayVar;

			var pinned = document.querySelectorAll(\"input[name='pinned']\");
			var i;
			for (i = 0; i < pinned.length; i++) {
				pinned[i].parentNode.parentNode.parentNode.style.display = displayVar;
			}

			var global = document.querySelectorAll('input[name=\"global\"]');
			var i;
			for (i = 0; i < global.length; i++) {
				global[i].parentNode.parentNode.parentNode.style.display = displayVar;
			}
			
			var specialPages = document.getElementById('additional_pages');
			if (specialPages != null) {
				specialPages.parentNode.parentNode.style.display = displayVar;
			}
			
		}

		function manageDisplayModes (changed=null) {
			var displaySel = document.getElementById('location');
			var value = displaySel.options[displaySel.selectedIndex].value;
			var text = displaySel.options[displaySel.selectedIndex].text;

			// Determine whether which fields should be displayed. 

			var displayVarAdditional = 'None';
			var displayVarForums = 'None';
			correctForumSelector();

			if ((text == '" . $lang->bam_list_display_special . "') || value == 'special') {
				
				var displayVarAdditional = '';
				var displayVarForums = 'None';
			}
			else if ((text == '" . $lang->bam_list_display_forums . "') || value == 'forums') {
				var displayVarAdditional = 'None';
				var displayVarForums = '';
			}
			else {
				var displayVarAdditional = 'None';
				var displayVarForums = 'None';
			}
 
			var forumClass = document.getElementById('forum_select');
			var specialSelect = document.getElementById('additional_pages');
			
			// old forumselect. remove a parent node.
			forumClass.parentNode.parentNode.parentNode.style.display = displayVarForums; 
			forumClass.parentNode.parentNode.style.display = displayVarForums; 
			specialSelect.parentNode.parentNode.parentNode.style.display = displayVarAdditional;

			if (!isEmpty(specialSelect.value)) {
				if (changed) {
					var specialClassContainer = specialSelect.parentNode.parentNode.getElementsByClassName('description')[0];
					specialClassContainer.innerHTML = '<span class=\"description\"></span>" . $lang->bam_remove_additional_page . "';
					specialSelect.parentNode.parentNode.parentNode.style.display = '';
					displaySel.value = 'special';
					forumClass.parentNode.parentNode.style.display = 'None';
					forumClass.parentNode.parentNode.parentNode.display = 'None';
					correctForumSelector(); // correct a MyBB bug. 
				}
			} else {
				specialSelect.parentNode.parentNode.style.display = displayVarAdditional;
			} 
		}

		// 
		function setCustomClass() {
			var classSel = document.getElementById('style');
			var value = classSel.options[classSel.selectedIndex].value;
			var text = classSel.options[classSel.selectedIndex].text;

			// Determine whether the custom class field should be displayed.

			var displayVar = 'none';
			if ((text == '" . $lang->bam_custom . "') || value == 'bam_custom') {
				var displayVar = '';
			}
			
			var customClass = document.getElementById('custom_class');
			// 
			if (!isEmpty(customClass.value)) {
				var customClassContainer = customClass.parentNode.parentNode.getElementsByClassName('description')[0];
				customClassContainer.innerHTML = '" . $lang->bam_remove_custom_class . "';
				customClass.parentNode.parentNode.style.display = '';
				classSel.value = 'bam_custom';
			} else {
				customClass.parentNode.parentNode.style.display = displayVar;
			}
		}

		// See lang file for the full alert text. 

		function showAnnouncementTags() {
			var announcement = document.getElementById('announcement_text');
			var announcementContainer = announcement.parentNode.parentNode.getElementsByClassName('description')[0];
			announcementContainer.innerHTML = '".$lang->bam_announcement_tags_alert."';
			// announcementContainer.parentNode.parentNode.style.display = '';
		}

 </script>";
echo $form_javascript;
$page->output_footer($lang->bam_title_acronym); 

