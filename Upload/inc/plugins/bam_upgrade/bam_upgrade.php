<?php
    /* HOW THIS WORKS: 
        *
        * BAM's upgrader is fully in-place, meaning it runs while the plugin is activated. 
        * This was required because the upgrade link is integrated directly within the ACP. Hooks that process
        * These links cannot run unless BAM is activated, so the upgrade had to be designed to be in-place and live. 
        * This was significantly simpler (hehe, hehe) than the alternatives, such as putting the upgrader 
        * in the activate function, or using standalone scripts. (I lied. )
        * It also has the added bonus of allowing a BAM upgrade to run completely interuption-free. 
        *
        * BAM first checks in the bam_info() function on whether the database has been upgraded.   
        * If not, it replaces the plugin's description with an upgrade link to notify the administrator.  
        * Once the upgrade link is launched, /admin/modules/config/bam.php checks for this link and verifies the POST CODE. 
        * If it matches, it includes this file, and calls the bam_upgrade() function below. 
        * 
        * bam_upgrade() removes old templates and settings, but leaves the database intact. 
        * It adds the necessary columns for BAM 2's schema, and then runs a normal install. 
        * Since we didn't delete the table, bam_install() will leave the database intact and only create settings and templates.
        *
        * Within /inc/plugins/bam.php, some legacy announcement rendering code from BAM 1 was included. This code is used
        * if BAM 2's files have been uploaded to the server without running the upgrade script. 
        * This allows the BAM 2 code base to operate correctly on the front end of the forum, 
        * even if the database and templates have not been updated yet. Announcements will still display with no interruptions.  
        *
        * This sounds complicated, but it works seamlessly! From the administrator's perspective, 
        * they simply upload, click the upgrade link, and they are done! 
        *
        */ 
    
    function bam_upgrade () {
        global $db, $lang, $mybb;

        require_once("../inc/plugins/bam.php");
        $lang->load('bam');	

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

    
        $templates = array('bam_announcement', 'bam_announcement_container'); // remove old templates. 
        foreach($templates as $template) {
            $db->delete_query('templates', "title = '{$template}'");
        }
        
        $query = $db->simple_select('settinggroups', 'gid', 'name = "bam"'); // remove old settings
        $groupid = $db->fetch_field($query, 'gid');
        $db->delete_query('settings','gid = "'.$groupid.'"');
        $db->delete_query('settinggroups','gid = "'.$groupid.'"');
        rebuild_settings();	

        // Add new templates, create new settings. Run the standard install script. 

        bam_install();
        rebuild_settings();
    }
