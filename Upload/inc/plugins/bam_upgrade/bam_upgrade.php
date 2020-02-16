<?php

    // 
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

    
        $templates = array('bam_announcement', 'bam_announcement_container'); // remove old templates. Templates
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
