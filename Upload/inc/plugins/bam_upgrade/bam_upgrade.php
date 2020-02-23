<?php

if(!defined("IN_MYBB")) {
	die("Standalone upgrader not supported. Please run through your ACP. "); // direct access to this file not allowed. 
}
    /* HOW THIS WORKS: 
        *
        * BAM's upgrader is fully in-place, meaning it runs while the plugin is activated. 
        * This was required because the upgrade link is integrated directly within the ACP. Hooks that process
        * These links cannot run unless BAM is activated, so the upgrader is designed to be in-place and live. 
        * This was significantly simpler (hehe, hehe) than the alternatives, such as putting the upgrader 
        * in the activate function, or using standalone scripts. (I lied. But it works.)
        * It also has the added bonus of allowing a BAM upgrade to run completely interuption-free. 
        *
        * If BAM 2 is uploaded, it first checks in bam_info() if the database has been updated  
        * If not, it replaces the plugin's description with the upgrade script link to notify the administrator.  
        * Once the upgrade link is launched, /admin/modules/config/bam.php checks for this link, 
        * includes this file if it matches, and then lanches the upgrader. 
        * 
        * bam_upgrade() then removes old templates and settings, but leaves the database intact. 
        * It adds the necessary database columns for BAM 2, and then runs a normal install.  
        * bam_install() will then create the settings and templates. 
        *
        * Within /inc/plugins/bam.php, some legacy announcement rendering code from BAM 1 was included. This code is used
        * if BAM 2's files have been uploaded to the server without yet running the upgrade script. 
        * This allows the BAM 2 code base to operate correctly on the front end of the forum, 
        * even if the database and templates have not been updated. Announcements will still display with no interruptions.  
        *
        * This sounds complicated, but it works seamlessly! From the administrator's perspective, 
        * they simply upload, click the upgrade link, and they are done! 
        *
        */ 
    
    function bam_upgrade () {
        global $db, $lang, $mybb;

        // Include the plugin itself... 
        require_once("../inc/plugins/bam.php");
        $lang->load('bam');	

        // Save a couple of old settings so that we can properly update these in the database later. 
        $globalSettings = $mybb->settings['bam_global'];
        $oldCSS = $mybb->settings['bam_custom_css'];

        // Add new columns to database. 
        $db->query("ALTER TABLE ".TABLE_PREFIX."bam
                ADD `global` INT UNSIGNED DEFAULT 0 AFTER pinned;");

        $db->query("ALTER TABLE ".TABLE_PREFIX."bam
                ADD `random` INT UNSIGNED DEFAULT 0 AFTER global;");

        $db->query("ALTER TABLE ".TABLE_PREFIX."bam
                ADD `additional_display_pages` VARCHAR(512) DEFAULT NULL AFTER random;");

        $db->query("ALTER TABLE ".TABLE_PREFIX."bam
                ADD `forums` VARCHAR(256) DEFAULT NULL AFTER additional_display_pages;");

        $db->query("ALTER TABLE ".TABLE_PREFIX."bam MODIFY announcement VARCHAR(1024);");
        
        // Unpinned announcements used to be random mode announcements. Convert these. 
        if ($mybb->settings['bam_random'] == 1 || $mybb->settings['bam_random'] == '1') {
            $db->query("UPDATE ".TABLE_PREFIX."bam SET `random` = 1 WHERE pinned = 0;");
        }

        // Properly set global announcements based on the previous settings for BAM 1.0 
        if ($globalSettings == 'global_pinned') {
            $db->query("UPDATE ".TABLE_PREFIX."bam SET `global` = 1 WHERE `pinned` = 1;");
        } 

        // User previously displayed ALL announcements globally. Set this accordingly. 
        else if ($globalSettings == 'global_all') {
            $db->query("UPDATE ".TABLE_PREFIX."bam SET `global` = 1;");
        }

        // BAM 1.0 had a field for active/inactive announcements, but it was unused. 
        // Set all announcements to be activated for the new BAM 2.0.
        $db->query("UPDATE ".TABLE_PREFIX."bam SET `active` = 1;");
    
        // remove old templates. 
        $templates = array('bam_announcement', 'bam_announcement_container');
        foreach($templates as $template) {
            $db->delete_query('templates', "title = '{$template}'");
        }
        
        // Delete old settings from the database. 
        $query = $db->simple_select('settinggroups', 'gid', 'name = "bam"'); 
        $groupid = $db->fetch_field($query, 'gid');

        // Get rid of the setting group too. We'll replace this. 
        $db->delete_query('settings','gid = "'.$groupid.'"');
        $db->delete_query('settinggroups','gid = "'.$groupid.'"');
        rebuild_settings();	

        // Add new templates, create new settings. Run the standard install script. 
        bam_install();
        rebuild_settings();

        // We will be nice and restore the user's old custom CSS setting.  
        $db->update_query("settings", array('value' => $db->escape_string($oldCSS)), "name='bam_custom_css'");
        rebuild_settings();
    }
