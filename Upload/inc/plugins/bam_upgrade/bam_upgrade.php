<?php

    // 
    function bam_upgrade () {
        global $db, $lang;

        require_once("../inc/plugins/bam.php");
        $lang->load('bam');	
        // if(!$db->table_exists($prefix.'bam')) {
    
            /*
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
                    forums VARCHAR(256) DEFAULT NULL,
                      PRIMARY KEY (PID)
                    ) ENGINE=MyISAM
                    ".$db->build_create_table_collation().";"; */


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

        if ($mybb->settings['bam_random'] == 1) {
            $db->query("UPDATE ".TABLE_PREFIX."bam SET `random` = 1 WHERE pinned = 0");
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
    }
