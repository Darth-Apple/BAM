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
	if(!defined("IN_MYBB")) {
		die("Hacking attempt detected. Server responded with 403. "); // direct access to this file not allowed. 
	}

	$lang->load('bam');
	
	$class_select = array(
		"green" => $lang->bam_green,
		"yellow" => $lang->bam_yellow,
		"red" => $lang->bam_red,
		"blue" => $lang->bam_blue,
		"bam_custom" => $lang->bam_custom
	); // list of programmed classes for BAM announcements. This list may expand in future versions of this plugin. 
	
	global $class_select;

	/***** Add breadcrumbs and tabs *****/
	
	if($mybb->input['action'] == "edit") {
		$page->add_breadcrumb_item($lang->edit_announcement);
	}
	else if ($mybb->input['action'] == "add") {
		$page->add_breadcrumb_item($lang->add_announcement);
	}
	else {
		$page->add_breadcrumb_item($lang->manage_announcements);
	}

	$sub_tabs['bam_manage'] = array(
		'title' => $lang->bam_manage,
		'link' => "index.php?module=config-bam",
		'description' => $lang->bam_manage_desc
	);

	$sub_tabs['bam_add'] = array(
		'title' => $lang->add_announcement,
		'link' => "index.php?module=config-bam&action=add",
		'description' => $lang->add_announcement_desc
	);

	$page->output_header($lang->bam_title);

	// create tabs, select active tab correctly
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
	else {
		$page->output_nav_tabs($sub_tabs, 'bam_manage');
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
		admin_redirect("index.php?module=config-bam");
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
		$page->add_breadcrumb_item($lang->edit_announcement);

		while($querydata = $db->fetch_array($query)) {	
			$data['PID'] = $querydata['PID'];
			$data['announcement'] = $querydata['announcement'];
			$data['class'] = $querydata['class'];
			$data['pinned'] = $querydata['pinned'];
			$data['disporder'] = $querydata['disporder'];
			$data['link'] = $querydata['link'];
			$data['usergroup'] = $querydata['groups'];
			$data['usergroup'] = explode(',', $querydata['groups']);
		}

		$form = new Form("index.php?module=config-bam", "post");
		$form_container = new FormContainer($lang->edit_announcement);
		$form_container->output_row($lang->bam_form_announcement, $lang->bam_form_announcement_desc, $form->generate_text_area("announcement", html_entity_decode($data['announcement']), array("class" => "text_input align_left", "style" => "width: 50%;")), 'announcement');
		
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
		$form_container->output_row($lang->bam_form_class_custom, $lang->bam_form_class_custom_desc, $form->generate_text_box("custom_class", html_entity_decode($custom_class), array("class" => "text_input", "style" => "width: 25%;", 'value' => $data['class'])), 'custom_class');	
		$form_container->output_row($lang->bam_form_order, $lang->bam_form_order_desc, $form->generate_text_box("disporder", $data['disporder'], array("class" => "text_input align_right", "style" => "width: 25%;")), 'disporder');
		
		$options = array();
		$query = $db->simple_select("usergroups", "gid, title", null, array('order_by' => 'title'));
		
		while($usergroup = $db->fetch_array($query))
		{
			$options[(int)$usergroup['gid']] = $usergroup['title'];
		}

		$form_container->output_row($lang->bam_form_groups, $lang->bam_form_groups_desc, $form->generate_select_box('usergroup[]', $options, $data['usergroup'], array('id' => 'usergroup', 'multiple' => true, 'size' => 5)), 'usergroup');
		$form_container->output_row($lang->bam_form_url, $lang->bam_form_url_desc, $form->generate_text_box("url", html_entity_decode($data['link']), array("class" => "text_input align_right", "style" => "width: 25%;")), 'url');
		$form_container->output_row($lang->bam_form_pinned, $lang->bam_form_pinned_desc, $form->generate_yes_no_radio('pinned', (int)$data['pinned']));
	
		$buttons[] = $form->generate_submit_button($lang->bam_form_edit_submit);
		$form_container->end();
		$form->output_submit_wrapper($buttons);
		$form->end();
		echo "<br />";
		$page->output_footer($lang->bam_title_acronym);
	}



	if($mybb->input['action'] == 'submit_edit' && $mybb->request_method=="post") {
		// process edit announcement form
		verify_post_check($mybb->input['my_post_key']); 

		$id = (int)$mybb->input['id'];
		$url = null;
		$disporder = 1;
		$pinned = 0;
		if (($mybb->input['custom_class'] != null)) {
			$class = $db->escape_string(htmlspecialchars($mybb->input['custom_class'], ENT_QUOTES));
		}
		else {
			$class = $db->escape_string(htmlspecialchars($mybb->input['class'], ENT_QUOTES));
		}
		
		if ($mybb->input['url'] != null) {
			$url = $db->escape_string(htmlspecialchars($mybb->input['url'], ENT_QUOTES));
		}
		
		if ($mybb->input['pinned'] != 0) {
			$pinned = 1;
		}

		if((!isset($mybb->input['usergroup'])) || (empty($mybb->input['usergroup'])) || (in_array('*', $mybb->input['usergroup'])))
		{
			$mybb->input['usergroup'] = '*';
		}
		else
		{
			$mybb->input['usergroup'] = implode(',', array_map('intval', $mybb->input['usergroup']));
		}
		$usergroups = $db->escape_string($mybb->input['usergroup']);
		$disporder = (int)$mybb->input['disporder'];
		$announcement = $db->escape_string(htmlspecialchars($mybb->input['announcement'], ENT_QUOTES));

		$db->update_query("bam", array('pinned' => $pinned, 'disporder' => $disporder, 'announcement' => $announcement, 'groups' => $usergroups, 'link' => $url, 'class' => $class), "PID='$id'");

		flash_message($lang->bam_edit_success, 'success');
		admin_redirect('index.php?module=config-bam');
	}

	if($mybb->input['action'] == 'submit_add' && $mybb->request_method=="post") {
		// process new announcement form		
		verify_post_check($mybb->input['my_post_key']); 

		$url = null;
		$disporder = 1;
		$pinned = 0;
		if (($mybb->input['custom_class'] != null)) {
			$class = $db->escape_string(htmlspecialchars($mybb->input['custom_class'], ENT_QUOTES));
		}
		else {
			$class = $db->escape_string(htmlspecialchars($mybb->input['class'], ENT_QUOTES));
		}

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


		$inserts = array(
			'announcement' => $db->escape_string(htmlentities($mybb->input['announcement'])),
			'class' => $class,
			'link' => $url,
			'pinned' => $pinned,
			'date' => time(),
			'disporder' => (int)$mybb->input['disporder'],
			'groups' => $db->escape_string($mybb->input['usergroup'])
			);
		$db->insert_query('bam', $inserts);
		flash_message($lang->bam_add_success, 'success');
		admin_redirect('index.php?module=config-bam');
	}


	
	if($mybb->input['action'] == 'delete' && $mybb->request_method=="get") {
		// process delete announcement
		$key = verify_post_check($mybb->input['my_post_key'], true); 
		if ($key == false) {
			flash_message($lang->bam_invalid_post_code, 'error');
			admin_redirect("index.php?module=config-bam");
		}		

		$PID = (int) $mybb->input['id'];
		if($PID == null) {
			// no announcement was defined. 
			flash_message($lang->bam_delete_error, 'error');
			admin_redirect('index.php?module=config-bam');
		}
	
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

		
	if ($mybb->input['action'] == "add") {
		// generate add announcement form. 

		$form = new Form("index.php?module=config-bam", "post");
		$form_container = new FormContainer($lang->bam_form_add);
		
		echo $form->generate_hidden_field("action", "submit_add");
		
		$form_container->output_row($lang->bam_form_announcement, $lang->bam_form_announcement_desc, $form->generate_text_area("announcement", '', array("class" => "text_input align_left", "style" => "width: 50%;")), 'announcement');	
		$form_container->output_row($lang->bam_form_style, $lang->bam_form_style_desc, $form->generate_select_box('class', $class_select, $mybb->input['fieldtype'], array('id' => 'style')), 'class');
		$form_container->output_row($lang->bam_form_class_custom, $lang->bam_form_class_custom_desc, $form->generate_text_box("custom_class", $mybb->input['custom_class'], array("class" => "text_input", "style" => "width: 25%;")), 'custom_class');	

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
		
		$form_container->output_row($lang->bam_form_order, $lang->bam_form_order_desc, $form->generate_text_box("disporder", ((int) $last['disporder'] + 1), array("class" => "text_input align_right", "style" => "width: 25%;")), 'disporder');
		$form_container->output_row($lang->bam_form_url, $lang->bam_form_url_desc, $form->generate_text_box("url", $mybb->input['url'], array("class" => "text_input align_right", "style" => "width: 25%;")), 'url');
		$form_container->output_row($lang->bam_form_pinned,  $lang->bam_form_pinned_desc, $form->generate_yes_no_radio('pinned', 0));
		
		$buttons[] = $form->generate_submit_button($lang->bam_form_add_submit);
		$form_container->end();
		$form->output_submit_wrapper($buttons);
		$form->end();
		echo "<br />";
	}


	
	if (empty($mybb->input['action'])) {
		// list announcements

		require_once MYBB_ROOT."/inc/class_parser.php";
		$parser = new postParser(); 
		$parser_options = array(
    			'allow_html' => 'no',
    			'allow_mycode' => 'yes',
    			'allow_smilies' => 'yes',
    			'allow_imgcode' => 'yes',
    			'filter_badwords' => 'yes',
    			'nl2br' => 'yes'
		);

		$form_t = new Form("index.php?module=config-bam", "post");
		$table = new FormContainer($lang->bam_manage);
		
		echo $form_t->generate_hidden_field("action", "order");
		
		$table->output_row_header($lang->bam_manage_announcement, array('width' => '62%'));
		$table->output_row_header($lang->bam_manage_class, array('width' => '12%'));
		$table->output_row_header($lang->bam_manage_order, array('width' => '12%'));
		$table->output_row_header($lang->bam_manage_actions, array('width' => '14%', 'colspan' => 3));
		
		$query = $db->query("
			SELECT *
			FROM ".TABLE_PREFIX."bam
			ORDER BY pinned DESC, disporder ASC, PID ASC
		");
	
		$data = array();
		$count = 0;
		while($querydata = $db->fetch_array($query))
		{	
			if ($querydata['link'] != null) {
				$data[$count]['announcement'] = $parser->parse_message("[url=".$querydata['link']."]".html_entity_decode($querydata['announcement'])."[/url]", $parser_options);
			}
			else {
				$data[$count]['announcement'] = $parser->parse_message(html_entity_decode($querydata['announcement']), $parser_options); // parse bbcode
			}
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
					$table->output_cell("<center><input type='text' name=\"disporder[".$data[$i]['PID']."]\" value='".$data[$i]['disporder']."' /></center>");
					$table->output_cell("<center><a href='index.php?module=config-bam&action=edit&id=".$data[$i]['PID']."'>".$lang->bam_manage_edit."</a></center>");	
					$table->output_cell("<center><a href='index.php?module=config-bam&action=delete&id=".$data[$i]['PID']."&my_post_key=".$mybb->post_code."' onclick=' return confirm(\"".$lang->bam_manage_delete_confirm."\");'>".$lang->bam_manage_delete."</a></center>");
					
					if ($data[$i]['pinned'] == 1) {
						$table->output_cell("<center><a href='index.php?module=config-bam&action=unpin&id=".$data[$i]['PID']."&my_post_key=".$mybb->post_code."'>".$lang->bam_manage_unpin."</a></center>");	
					}
					else {
						$table->output_cell("<center><a href='index.php?module=config-bam&action=pin&id=".$data[$i]['PID']."&my_post_key=".$mybb->post_code."'>".$lang->bam_manage_pin."</a></center>");	
					}
			
					$table->construct_row();
				}	
				$i++;		
			}
		
		}
	
		$table->end();
		$buttons = array();
		$buttons[] = $form_t->generate_submit_button($lang->bam_manage_order_submit);	
		$form_t->output_submit_wrapper($buttons);
		$page->output_footer($lang->bam_title_acronym);
	}
