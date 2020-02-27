NOTICE: You are viewing the final release candidate for BAM 2.0. This is nearly release-ready, tested, and safe to use on a live forum. It will be released by March 1st. If you discover any additional bugs before the final release date, please let me know as soon as possible! Any additional fixes will be implemented before the release. 

Thank you! 
-Darth Apple

---- ---- ---- ---- 

BAM Announcements Manager has been updated, and is better than ever before! This plugin allows you to manage announcements on your forum’s header area, specific boards, or on any page of your community. These are styled based on the announcements used on the MyBB Support Forum, so if you’ve been looking for a plugin to make similar announcements, this plugin is the answer to your requests! 


-------------------------------------------------------
(MAJOR UPDATE) FEATURES IN BAM 2.0: 
-------------------------------------------------------

 - Manage unlimited announcements on ANY page of your forum. 
 - Seven included colors and styles for announcements. You can create additional styles if you desire. 
 - Announcements can be now be dismissed by users! Configurable on a per-announcement basis.
 - Display announcements globally, on the forum index, on specific boards, or anywhere else you desire.  
 - New: Alternatively, paste a link to any specific page (thread, profile, or otherwise) to display your announcement on. 
 - Manage which usergroups can view announcements (on a per-announcement basis)
 - Display announcements only on specific themes or languages
 - Specify alternative templates to display announcements with (useful if you need javascript announcements) 
 - Full support for BBCode, HTML, {username} tags, and several other new variables
 - Optional Random Mode: Make a new announcement appear on each page refresh.
 - Sticky announcements: Select which announcements are undismissable by users. 
 - Announcements can be activated and deactivated (new!)


This plugin is supported on all MyBB 1.8.X forums, and has been tested extensively. If you used BAM 1.0 before, it is strongly recommended to update to the new version of this plugin, as it is significantly more advanced and more powerful than the initial version. Many thanks to @Guardian (Sawedoff), @VintageDaddyo and @Eldenroot for many ideas, contributions, testing, and for feedback on the new version of this mod! You guys have made BAM 2.0 possible. 
 

-------------------------------------------------------
CUSTOM ANNOUNCEMENT PAGES (ADVANCED):  
-------------------------------------------------------


This is a new feature in BAM 2.0 that allows you to paste a link to a specific page to display your announcement. This setting can also take multiple values, separated by commas. Some examples of possible inputs are listed below. 

THINGS BAM EXPLICITLY PARSES: 

 - Any page's .php filename. If the ONLY thing in this field is this filename (such as portal.php, forumdisplay.php, etc.), BAM will display your announcement on EVERY page that includes this filename. This, for example, is how you can display announcements only on the portal, or on all boards. 
 - any fid (forum/board ID), tid (thread ID), uid (user ID), gid (usergroup ID), or action (usually a page). 

EXAMPLES: 

- forumdisplay.php?fid=2                  Displays only if the user is visiting a specific board (in this case, fid 2)
- forumdisplay.php 			  Displays on all boards. 
- member.php?action=profile&uid=15        Displays on a specific user’s profile (UID 15). (Disclaimer: Not responsible for any forum drama. )
- member.php?action=profile               Displays on ALL user profiles
- usercp.php                              Displays on the user control panel
- member.php?action=register              Displays on the registration page
- memberlist.php                          Displays on the Memberlist    
- showthread.php?tid=947.                 Display only on a specific thread (in this case, thread 947)

This is an advanced feature that is very powerful, but that has specific parameters that must be met in order to work properly. 

URL REWRITES: This plugin supports search engine friendly URLs as defined by MyBB's built-in htaccess file. You may paste these in exactly the same manner as native URLs, and BAM will convert these internally. 

THIRD PARTY SEO PLUGINS: BAM does not have support for third party SEO plugins, such as Google SEO. You must paste native URLs into this field for them to be properly parsed if you are using such plugins. If you would like to help extend BAM, please reach out, and I will be happy to collaborate with you to help bring third-party SEO plugin support to BAM. 


--------------------------------------------------
FREQUENTLY ASKED QUESTIONS:  
--------------------------------------------------


WHAT IS RANDOM MODE? 

 - Random mode allows announcements to be refreshed on each page visit. BAM will select an announcement randomly from the random mode tab, and will display a different announcement each time your forum's index is visited. 
 - Due to the complexity of how BAM manages different types of announcements, you can’t put random mode announcements on other pages in this version. (Maybe a future release will expand upon this). They display on the forum’s index, below standard announcements. There are certain settings that work differently in random mode, so make sure to double check your announcement’s settings if you convert your announcement from standard to random mode. 


CAN I USE HTML IN ANNOUNCEMENTS? 

 - Yes. Ordinarily, only MyCode is supported. However, you may enable the HTML setting in BAM's plugin settings to allow HTML. Please note that, by design, this does not work for javascript, which is purposefully removed before rendering announcements. If you need javascript, see below. 


CAN I PUT JAVASCRIPT IN AN ANNOUNCEMENT? 

    - Javascript is not, by default, enabled. However, you can make BAM load a different template for a specific announcement. If you have a global template named my_announcement_template, put [@template:my_announcement_template] anywhere in your announcement’s text. This tag tells BAM to load a different template, which can contain javascript or anything else that you need. 


CAN I MAKE BAM DISPLAY ON MULTIPLE THEMES WITHOUT DISPLAYING ON EVERY THEME? 

    - Yes. Put [@themes:1,2,3] anywhere in your announcement’s text. This works for languages too! 


HOW TO I GET MY LANGUAGE AND THEME IDS FOR ANNOUNCEMENT TAGS? 

    - Language IDs are based on the folder name of the language pack on your forum. Navigate to /inc/languages to see the language packs. The name of your language's folder (english, espanol, etc.) is the value you should use for the language tag. 

    - Theme IDs can be seen from the URL when you edit a specific theme in the ACP. Visit your theme. Edit it, and look for (tid=some_number) in your URL. This is your theme ID!


CAN I MAKE ANNOUNCEMENTS UNDISMISSABLE FOR GUESTS? 

    - Yes. There is a setting in the general BAM settings for this. If guests are disabled for dismissals, they will close, but not be dismissed permanently. 


CAN I RESET ALL DISMISSALS ON THE FORUM? 

    - Yes. There is a cookie prefix in BAM’s general plugin settings. Change this to any numeric value to reset all dismissals. 
    - You can also configure how long the cookie lasts (and how long dismissed announcements remain dismissed). 


CAN I DISPLAY ANNOUNCEMENTS GLOBALLY? 

    - Yes! Set your announcement to "global" in the drop down menu when creating a new announcement. 


CAN I DISPLAY ANNOUNCEMENTS ON THE PORTAL AND ON THE INDEX? 

    - Yes! Go to BAM's plugin settings. In the "index page" setting, set it to "portal.php, index.php" (without the quotes), and save. BAM will now consider both of these pages to be your forum's "index page." Any announcement that is set to display on the index will now display on both. 


CAN I DISPLAY ANNOUNCEMENTS ON SPECIFIC PAGES OF MY FORUM? 

    - Yes! When creating an announcement, select (Other - Advanced) in the drop down menu. Paste a link to specific pages to display your announcement on. This field can take multiple values, separated by a comma. 

    - Note that while this works for most pages, there are certain pages that cannot display announcements. See the the "custom pages" section of this readme (above) for more information. 


CAN I ADD MODERATOR CP ACCESS? 

 - BAM does not have moderator control panel access at this time. Adding this would require a ground-up rewrite of the ACP module that BAM currently implements. However, administrator permissions are implemented in BAM 2.0. You may create customized administrator groups that only have access to BAM's announcement management panel. 


HOW DOES THE FORUM GAMES COUNTING THREAD VARIABLE WORK? 
 
    - It works by looking for a group of posts with consecutive numbers. It determines that a relatively large group of consecutive posts must have the correct count, and therefore corrects itself if a user posts the wrong count. It will reset itself if you post a new count for 3-4 posts in a row. 
    - {threadreplies} does the same thing, but bases itself on the reply count. If your forum game is not offset from your reply count, use this variable instead. 


I INSTALLED BAM, BUT THE ANNOUNCEMENTS DON'T DISPLAY. 

    - BAM, (must like most other MyBB plugins), makes template modifications in order to display on your forum. The vast majority of themes can be modified by BAM's installer without issues. However, if your announcements don't display, see the installation instructions (below) for instructions on how to manually modify your template!

----------------------------------------------------
ADDING CUSTOM STYLES/COLORS:
----------------------------------------------------

  - By default, BAM comes with seven color styles, and three of these are new styles that come with BAM+. You can add additional styles easily by defining the background color and border color in a new class. Other properties of the announcement layout (padding, etc.) are defined by a global class used by all announcements. You can place your custom style classes in the Custom CSS field in your plugin settings. Once you have saved your custom CSS classes, simply define the "custom class" field when creating announcements to use your new custom color style!

  Example: 
    
    .bam_announcement.aqua {
       border: 1px solid #0399C9;
       background-color: #BFFAFF;
    }

  You will now be able to use "aqua" in the custom class field. 

  Why use classes instead of a "custom CSS" field for every announcement? Because classes are re-usable! Once you design a style that you like, you can use it in as many announcements as you desire. 


-----------------------------------------------------
UPGRADING FROM BAM 1.0: IMPORTANT 
-----------------------------------------------------

1) RECOMMENDED METHOD: Uninstall the old version of BAM. Upload the new version, and install as usual. 

 - This is the more stable way to perform an upgrade. If you don't have many announcements currently, it is recommended to perform an upgrade with a full uninstall/reinstall. Note that this will delete all current announcements! 

----------------------

2) ALTERNATIVE METHOD: Use the built in upgrader to keep existing announcements. Upload the new version of BAM. Make sure to leave BAM activated, and navigate to your plugins page. Click the upgrade link that appears in BAM's plugin description. This will make all of the necessary changes to upgrade you to BAM 2.0! 

  1. Note that the upgrade script REQUIRES bam to be fully activated first. This is because it runs in-place straight from within the BAM ACP module. Due to the way that MyBB plugins work, BAM is not able to load its own upgrade script unless it is activated. 

  2. This will not break your forum or interrupt announcements! BAM 2.0 has been specifically designed to enable the new version of BAM to display using the old version's templates and database temporarily. Your forum announcements will remain uninterrupted.  

  3. This script doesn't migrate general plugin settings. These have changed heavily from BAM 1 to BAM 2. Make sure to reconfigure BAM's general plugin settings once the upgrade has completed! 


-----------------------------------------------------
INSTALLATION: 
-----------------------------------------------------

  - To install, simply upload the contents of "Upload" folder on this zip file to your MyBB root directory, and enable "BAM Announcements Manager 2.0” from your admin control panel. A new "Announcements" link will be added to the sidebar under the configuration tab, allowing you to manage your announcements. 
 
  - This plugin adds a variable in your header template after the {$awaitingusers} variable on installation. On the default theme, this causes announcements to display before the page breadcrumbs. While this method seems to ensure the best compatibility of this plugin with various themes, some users may wish you have the announcements display after the breadcrumbs. To do this, simply find "<!-- BAM -->{$bam_announcements}<!-- /BAM -->" in your templates, and copy/paste this text to a new line after "<navigation>" in your header template. 

-----------------------------------------------------
COMPATIBILITY MODE (ADVANCED):  
-----------------------------------------------------
Leave this setting off if you are unsure! 

BAM's default variables, template modifications, and hooks (how BAM interacts with MyBB) have been designed to ensure the best possible compatibility with a wide variety of MyBB forums. Occasionally, these default values may cause compatibility issues with other plugins, themes, or specific versions of PHP. 

This setting forces BAM to generate and render announcements at the end of page generation, immediately before sending to the browser. BAM will attempt to replace the standard BAM variable with your parsed announcements. If this variable cannot be found (usually because of heavily modified themes), it will force the announcements above the navigation bar, even without the template variable. 

This method can improve compatibility with heavily modified themes where BAM cannot appropriately modify the templates during activation. Additionally, it can occasionally resolve certain compatibility issues between some untested plugins that might conflict with BAM, and can resolve unexpected issues and bugs related to posting issues on your forum. 

This setting is experimental. It may not work in all cases, and it is recommended to leave this setting off unless you have issues related to themes, plugins, or otherwise. In the vast majority of cases, standard mode can render announcements properly, and will generally be more stable than compatibility mode. 

-------------------------------------------------------
EASTER EGGS AND EXTRAS: 
-------------------------------------------------------

 - Can create a banner with the CORRECT count on your counting thread (forum games), even if the count in your thread is offset from the actual reply count. Use the {countingthread} variable to enable this functionality. To demonstrate, try posting the wrong count in your counting thread. To use this feature, you will need to paste the link to your counting thread in the "Custom Pages and Parameters" field. 

 - {newmember} and {newmember_link} parse to the username and the profile link of the newest registered member, respectively. 

 - Updated ACP interface that displays more information (with new icons) about where announcements will be posted. 

 - Both BAM 1.0 and 2.0 allow you to turn your announcement into a link without the use of MyCode. Simply paste a link into the URL field!

Copyright: 

   This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.



