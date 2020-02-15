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

$l['bam_title'] = "BAM+ Announcements Manager";
$l['bam_title_acronym'] = "BAM+ Announcements Manager (Updated and Improved!)";
$l['bam_desc'] = "Allows you to create and manage announcements in your forum header.";
$l['bam_announcements_menu'] = "BAM+ Announcements";
$l['bam_enable'] = "Activate BAM Announcements Manager?";
$l['bam_enable_desc'] = "Enable or disable BAM Announcements Manager without the need to uninstall this plugin.";
$l['bam_random_enable'] = "Enable/Disable Random Mode";
$l['bam_random_desc'] = "Enables the <i>random mode</i> tab, along with automatically refreshing announcements. This feature does not affect your standard announcements!";
$l['bam_random_max'] = "Random Results Generated";
$l['bam_random_max_desc'] = "Maximum number of random results generated if random mode is enabled. This setting does nothing if random mode is disabled. ";
$l['bam_random_group'] = "Random Mode Permissions";
$l['bam_random_group_desc'] = "Configure random mode visibility by usergroup. Usergroup visibility can alternatively be configured by announcement, if preferred. ";
$l['bam_global'] = 'Display Announcements Globally? ';
$l['bam_global_desc'] = "This setting allows you to control the visibility of global announcements. By default, announcements are displayed only on the index page.";
$l['bam_global_disable'] = "Disable";
$l['bam_global_pinned'] = "Pinned Announcements Only";
$l['bam_global_all'] = "All Announcements";
$l['bam_index_page'] = "Index Page (Advanced Users)";
$l['bam_index_page_desc'] = "Identify the page that will be considered the \"index page\" for non global announcements. By default, this should be set to index.php. Unless you have renamed index.php, you should not need to edit this value. <b>New in 2.0: This field now can take multiple values, separated by a comma. </b> Use multiple values if you want non-global announcements to display on several pages, such as portal.php and index.php. ";
$l['bam_custom_css'] = "Custom CSS";
$l['bam_custom_css_desc'] = "Add any custom CSS classes here. ";
$l['bam_welcome'] = "[b]BAM+ 2.0 has successfully been installed![/b] You may now manage your announcements via the ACP. ";
$l['bam_date_enable'] = "Display Announcement Date?";
$l['bam_date_desc'] = "If enabled, BAM will display the date posted for announcements. ";

// 2.0 

$l['bam_enable_dismissal'] = "Announcement Dismissals: ";
$l['bam_enable_dismissal_desc'] = "Set how BAM should handle announcement dismissals.  
If set to \"Close Only,\" announcements will return when the user revisits. \"Close and dismiss\" prevents the announcement from being displayed again. ";
$l['bam_dismissal_days'] = "Dismissed Announcements Expiration: ";
$l['bam_dismissal_days_desc'] = "Defines how many days a BAM announcement dismissal should last. Default value is 30.";
$l['bam_guest_dismissal_enable'] = "Enable Guests to Dismiss Announcements?";
$l['bam_guest_dismissal_enable_desc'] = "By default, guests cannot dismiss announcements, even if the announcement's settings allow it. Enable this setting if you want to allow guests to close announcements anyway. ";
$l['bam_random_dismissal'] = "Enable Random Mode announcements to be dismissed?";
$l['bam_random_dismissal_desc'] =  "Random mode handles announcement dismissals differently than standard mode. When an announcement is dismissed, it is simply removed from the page, and a new announcement will be loaded on the next page. This setting defines whether this behavior should be enabled, or whether dismissals should be completely disabled. <b> This setting only affects announcements that are created under random mode. Standard announcements follow normal settings. </b>";
$l['bam_manage_popupmenu'] = "Manage";
$l['bam_cookie_id_prefix'] = "Announcement ID Prefix for Cookies/Dismissals: ";
$l['bam_cookie_id_prefix_desc'] = "Change this value (to any numeric value) if you need to clear/reset all announcement dismissals. This value is created automatically when BAM is installed.";
$l['bam_dismissal_disable'] = "Disable all dismissals";
$l['bam_dismissal_closeonly'] = "Close announcements only ";
$l['bam_dismissal_savecookie'] = "Close and dismiss from displaying again ";
$l['bam_dismissal_savecookie_useronly'] = "Close and dismiss, but only if the user is registered ";

$l['bam_add_announcement'] = 'Add Announcement';
$l['bam_edit_announcement'] = "Edit Announcement";
$l['bam_manage_announcements'] = "Manage Announcements";


$l['bam_manage'] = "Manage";
$l['bam_manage_random'] = "Random Mode";
$l['bam_manage_random_desc'] = "<b>BAM will randomly select one announcement from this list to display on the forum index.</b> These can be managed here! <br /><br />";
$l['bam_manage_random_desc'] .= " - To add announcements to this list, select \"Random Announcement\" when adding a new announcement. <br />";
$l['bam_manage_random_desc'] .= " - BAM has some special configuration settings for these. See BAM's plugin settings for more options. <br />";
$l['bam_manage_random_desc'] .= " - These announcements will be displayed below standard announcements. <br /> ";
$l['bam_manage_random_desc'] .= " - Use this feature if you want announcements to automatically refresh on each page visit.";
$l['bam_manage_random_desc'] .= "<br /><br /><i>Note that BAM only displays these on the forum index.</i>";

$l['bam_manage_desc_norandom'] = "This page allows you to manage, edit, delete, and re-order your announcements. This page only manages standard announcements. If you need announcements to be randomly selected on your forum index, enable <i>random mode</i> in BAM's plugin settings.";
$l['bam_manage_desc'] = "This page allows you to manage, edit, delete, and re-order your announcements. These announcements are standard, static announcements. They always display. See <i>random mode</i> for automatically refreshing announcements.";
$l['bam_edit'] = 'Edit';
$l['bam_edit_desc'] = "This page allows you to edit an existing announcement. ";
$l['bam_add_announcement_desc'] = "Add new announcements here. You may add HTML in your announcements. ";
$l['bam_add_announcement_noadvance_desc'] = "Add new announcements here. You may use BBcode in your announcements. If you need to use full HTML, you can enable this in BAM's settings. ";
$l['bam_order_success'] = "Announcement orders updated successfully. ";
$l['bam_no_announcement'] = "Error: no announcement to update. ";
$l['bam_pin_success'] = "Successfully set announcement as sticky. It is now undismissable. ";
$l['bam_unpin_success'] = 'Successfully set this announcement as unsticky. It can now be dismissed by users. ';
$l['bam_error'] = "Error. ";
$l['bam_form_announcement'] = 'Announcement: ';
$l['bam_form_announcement_desc'] = 'You may use BBcode in your announcements. Up to 1024 characters are allowed.';
$l['bam_form_announcement_advanced_desc'] = "You may use BBcode and HTML in your announcements. Up to 1024 characters are allowed. ";
$l['bam_form_tags_link'] = "Show Available Tags.";
$l['bam_make_standard'] = "Make Static";
$l['bam_make_standard_confirm'] = "Set your announcement as a standard announcement?";
$l['bam_make_random_confirm'] = "Set your announcement as a random mode announcement?";
$l['bam_make_random'] = "Make Announcement Random";
$l['bam_make_random_success'] = "Successfully made your announcement a <i>random mode</i> announcement. Please make sure all settings look correct. ";
$l['bam_make_standard_success'] = "Successfully reset your announcement to standard mode. Please edit any additional settings and make sure they look correct!";
$l['bam_make_standard_header'] = "Manage Type";
$l['bam_add_new_random'] = "Create Random Announcement";

$l['bam_announcement_is_global'] = "This announcement is global (displayed on all pages).";
$l['bam_announcement_is_index'] = "This announcement is displayed on the default homepage.";
$l['bam_announcement_is_sticky'] = "This announcement is sticky (cannot be dismissed).";
$l['bam_announcement_is_forums'] = "This announcement is displayed on specific forums.";
$l['bam_announcement_is_random'] = "This is a random mode announcement.";
$l['bam_announcement_has_directives'] = "This announcement has special directives. ";

$l['bam_green'] = "Green/Success";
$l['bam_blue'] = "Blue/Notice";
$l['bam_yellow'] = 'Yellow/Alert';
$l['bam_red'] = "Red/Alert";
$l['bam_silver'] = 'Silver/Info';
$l['bam_magenta'] = "Magenta/Info";
$l['guest_notice'] = "Guest Notice"; // Not implemented. 
$l['bam_custom'] = "Custom (Define Below)";

$l['bam_form_style'] = "Style/Color Class: ";
$l['bam_form_style_desc'] = "Select the color and style for your announcement. ";
$l['bam_form_class_custom'] = "Custom CSS Classes: ";
$l['bam_form_class_custom_desc'] = "Enter custom CSS classes to be used instead of BAM's built in styles. You can enter multiple values separated by a space. ";

// This is entered into javascript, so make sure there are no new lines in the string that is generated if you translate this. 
$l['bam_remove_custom_class'] = "<i><font color=\'red\'>You must remove custom classes before setting a predefined color style. ";
$l['bam_remove_custom_class'] .= "If you need to use a predefined color value in addition to a custom class, you can add both into the custom class setting below. </font>";
$l['bam_remove_custom_class'] .= "See documentation for instructions on how to add custom classes. </i>";
$l['bam_remove_custom_class'] .= "<br /><br />Example: \"blue my_custom_class\" &nbsp;&nbsp; (separate multiple classes by spaces)<br />";
$l['bam_remove_custom_class'] .= "Built in classes: red, blue, yellow, green, magenta, silver <br/><br />";

$l['bam_remove_additional_page'] = "<i><font color = \'red\'>You must remove additional pages before using BAM\'s normal location settings. ";
$l['bam_remove_additional_page'] .= "</font>If you need your announcement to display on the index.php page or on specific boards, you can add these fields along with any additional pages below.</i><br /><br />";
$l['bam_remove_additional_page'] .= "<b>Example: index.php, forumdisplay.php?fid=2, forumdisplay.php?fid=3</b> &nbsp;&nbsp; (Display on index, board ID 2, board ID 3)<br /><br />";

$l['bam_form_order'] = "Display Order: ";
$l['bam_form_order_desc'] = "Select the display order for this announcement. Leave this field blank if you are unsure. ";
$l['bam_form_groups'] = "Group Permissions: ";
$l['bam_form_groups_desc'] = "Select which groups will be allowed to view this announcement. Hold CTRL to select multiple groups. ";
$l['bam_form_url'] = "Announcement URL (Optional): ";
$l['bam_form_url_desc'] = "If you define a URL for this announcement, the announcement will be turned into a link. This setting can be left blank.";
$l['bam_form_pinned'] = "Make This Announcement Sticky?";
$l['bam_form_pinned_desc'] = "Sticky announcements cannot be dismissed, and display above other announcements.";
$l['bam_form_add_submit'] = "Add New Announcement";
$l['bam_form_add'] = "Add New Announcement";
$l['bam_form_edit_submit'] = "Edit Announcement";

// 2.0
$l['bam_announcement_type'] = "Announcement Type: ";
$l['bam_announcement_type_desc'] = "Select whether this announcement will be added under random mode or standard mode. Note that BAM handles Random Mode announcements differently than standard mode announcements. Please see documentation for details. ";
$l['bam_display_mode'] = "Pages to Display Announcement"; // no longer used
$l['bam_display_mode_desc'] = "Select where this announcement should be displayed. By default, it is displayed on the index page only.";
$l['bam_list_display_global'] = "Global (display on all pages)";
$l['bam_list_display_index'] = "Forum index only";
$l['bam_list_display_forums'] = "Display on specific forums/boards";
$l['bam_list_display_special'] = "Other (advanced - define below)";
$l['bam_make_global'] = "Make This Announcement Global?";
$l['bam_make_global_desc'] = "Select whether this announcement will be made global. Global announcements are displayed on all pages, regardless of any other settings or constraints. ";
$l['bam_additional_pages'] = "Define Custom Pages and Parameters (Advanced Users): ";
$l['bam_has_additional_pages'] = "This announcement has custom display settings.";
$l['bam_additional_pages_desc'] = "<b>Set custom pages to display your announcement on. </b>
 Note that this setting overrides all other announcement settings regarding where this announcement is posted.
<i>This announcement will only display on pages that are explicitely defined here. You can also copy/paste full links, if desired. </i><br /><br />

<b>Examples:</b> <br />
  - \"forumdisplay.php, index.php\" &nbsp;&nbsp;&nbsp;&nbsp; -- <i>Display on index.php and forumdisplay.php only</i><br />
  - \"forumdisplay.php?fid=2\" &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -- <i>Display this announcement on forumdisplay.php, but only if the fid is 2.</i><br /><br />
  <b>Supported pages: </b> Any .php page within your forum's directory. <br />
  <b>URL parameters supported: </b> action, uid, tid, fid. All other URL parameters will be ignored. See documentation for details. <br />";

$l['bam_random_select'] = "Random Mode Announcement";
$l['bam_standard_select'] = "Static Announcement (always display)";
$l['bam_advanced_mode'] = "Allow HTML in announcements?";
$l['bam_advanced_mode_desc'] = "By default, BAM parses BBcode in announcements. If you need full HTML, enable this setting.";
$l['bam_manage_random_form_container'] = "Manage Random Mode Announcements";
$l['bam_forum_select'] = "Boards to Display Announcement On: ";
$l['bam_forum_select_desc'] = "Select which boards/forums BAM should display this announcement on. Hold CTRL to select multiple forums.";

$l['bam_undefined'] = "Undefined";
$l['bam_edit_success'] = "Successfully Edited Announcement. ";
$l['bam_add_success'] = "Successfully Added Announcement. ";
$l['bam_delete_success'] = "Successfully Deleted Announcement. ";
$l['bam_delete_error'] = "Error deleting announcement: announcement not found. ";

$l['bam_manage_announcement'] = "Announcement";
$l['bam_manage_class'] = "Style/Class";
$l['bam_manage_order'] = "Display Order";
$l['bam_manage_actions'] = "Actions";
$l['bam_manage_edit'] = "Edit";
$l['bam_manage_delete'] = "Delete";
$l['bam_manage_pin'] = "Sticky";
$l['bam_manage_unpin'] = "Unsticky";
$l['bam_manage_delete_confirm'] = "Are you sure you want to delete this announcement?";
$l['bam_manage_actions'] = "Actions";
$l['bam_manage_null'] = "No announcements found. ";
$l['bam_manage_order_submit'] = "Update Display Orders";
$l['bam_invalid_post_code'] = "Invalid post code detected. Please try again. ";

$l['bam_cookie_notice'] = "This forum uses cookies. By browsing this forum, you are agreeing and consenting to the use of cookies. ";

$l['bam_announcement_tags_alert'] = "<b>BAM supports additional tags and directives within announcements. </b> These will be parsed when your announcement is displayed. </b><br /><br />";
$l['bam_announcement_tags_alert'] .= "<b>Variables: </b><br />";
$l['bam_announcement_tags_alert'] .= "&nbsp;&nbsp;&nbsp;&nbsp; {username} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i>Parses to user\'s username (or Guest if they are not logged in).</i>";
$l['bam_announcement_tags_alert'] .= "<br />&nbsp;&nbsp;&nbsp;&nbsp; {newestmember} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i>Parses to the username of the newest registered member.</i>";
$l['bam_announcement_tags_alert'] .= "<br />&nbsp;&nbsp;&nbsp;&nbsp; {newestmember_link} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i>Creates a link to the profile of the newest registered member.</i>";
$l['bam_announcement_tags_alert'] .= "<br />&nbsp;&nbsp;&nbsp;&nbsp; {threadreplies} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i>Show thread only. Parses to the number of replies in the current thread. </i>";
$l['bam_announcement_tags_alert'] .= "<br />&nbsp;&nbsp;&nbsp;&nbsp; {countingthread} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i>Experimental. Parses to current count in forum games/counting threads. Attempts to resolve and determine actual count if a user posts the wrong count. </i>";

$l['bam_announcement_tags_alert'] .= "<br /><br /><b>Announcement Directives: </b><br />";
$l['bam_announcement_tags_alert'] .= "&nbsp;&nbsp;&nbsp;&nbsp;These directives give BAM additional information on where to display announcements. These are experimental. <br />";
$l['bam_announcement_tags_alert'] .= "<br />&nbsp;&nbsp;&nbsp;&nbsp; [@themes:1,2] &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i>Display announcement only on theme IDs 1 and 2. </i>";
$l['bam_announcement_tags_alert'] .= "<br />&nbsp;&nbsp;&nbsp;&nbsp; [@languages:espanol] &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i>Show announcement only in Spanish (or any language you choose). </i>";
$l['bam_announcement_tags_alert'] .= "<br />&nbsp;&nbsp;&nbsp;&nbsp; [@template:custom] &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i>Use an alternative global template for this announcement (Advanced - use if you need javascript).</i>";
$l['bam_announcement_tags_alert'] .= "<br />&nbsp;&nbsp;&nbsp;&nbsp; [@disabled] &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i>Ghost mode (disables announcement).</i>";

$l['bam_announcement_tags_alert'] .= "<br /><br />These directives can be placed anywhere in your announcement\'s text, and will be removed before your announcement is displayed on your forum. These features are considered experimental. Please request support or visit the documentation with any questions!<br /><br />";

$l['bam_announcement_too_long'] = "Error. Your announcement cannot be more than 1023 characters. ";
$l['bam_announcement_link_too_long'] = "Error. Your announcement's URL cannot be more than 159 characters. ";
$l['bam_class_too_long'] = "Error. Your custom classes cannot be more than 39 characters. ";
$l['bam_additional_pages_too_long'] = "Error. Additional pages field cannot be more than 511 characters. ";

$l['bam_upgrade_required'] = "<b> You have successfully uploaded BAM 2.0 to the server.</b> In order to use BAM 2.0, you must run BAM's upgrade script to refresh database fields and templates. Please note that the upgrade script does not migrate settings, and it can only migrate announcements. Make sure to visit BAM's settings after running your upgrade and verify that everything still looks correct. ";
$l['bam_upgrade_link_text'] = "Click here to run the upgrade!";
$l['bam_upgrade_link_text_plugins_panel'] = "BAM is ready to update. Click here to finish upgrading to BAM 2.0!<br />";
$l['bam_upgrade_success'] = "You have successfully updated BAM to BAM+ 2.0! Verify any settings are correct, as these have been completely refreshed and restored to defaults from the upgrade. If you have any additional issues, uninstall BAM and reinstall from the plugins page. This will resolve any remaining issues.";
$l['bam_info_upgrade'] = "<font color='red'>You have uploaded BAM 2.0 to the server, but an upgrade script is required. <b> Important: Make sure that BAM is <u>installed AND activated BEFORE running the upgrade. </u></b></font> Once BAM is activated, an upgrade link will appear below to enable the ACP panel. <br /><br /><i>(Activation is required first because BAM cannot load its own upgrade script otherwise.) </i> ";