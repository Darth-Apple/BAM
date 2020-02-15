Notice: This is a beta version. If you would like to test on a live board, please report any bugs that you discover! Full release is coming soon! 

BAM Announcements Manager has been updated, and is better than ever before! This plugin allows you to manage announcements on your forum’s header area, specific boards, or on any page of your community. These are styled based on the announcements used on the MyBB Support Forum, so if you’ve been looking for a plugin to make similar announcements, this plugin is the answer to your requests! 

(Major Update) New Version Features: 

 - Manage unlimited announcements on ANY page of your forum. 
 - Six included colors and styles for announcements. You can create additional styles if you desire. 
 - Announcements can be now be dismissed by users! Configurable on a per-announcement basis.
 - Display announcements globally, on the forum index, on specific boards, or anywhere else you desire.  
 - New: Alternatively, paste a link to any specific page (thread, profile, or otherwise) to display your announcement on. 
 - Manage which usergroups can view announcements (on a per-announcement basis)
 - Display announcements only on specific themes or languages
 - Specify alternative templates to display announcements with (useful if you need javascript announcements) 
 - Full support for BBCode, HTML, {username} tags, and several other new variables
 - Optional Random Mode: Make a new announcement appear on each page refresh.
 - Sticky announcements: Select which announcements are undismissable by users.  

 EASTER EGGS: 

 - Can create a banner with the CORRECT count on your counting thread (forum games), even if the count in your thread is offset from the actual reply count. We use it over at Makestation. (Try posting the wrong count in your thread. The counter retains the correct count even if the wrong count is posted by a user!)
 - {newmember} and {newmember_link} parse to the username and the profile link of the newest registered member, respectively. 
 - Updated ACP interface that displays more information about where announcements will be posted. 
 - Turn announcement into a link without the use of BBcode. Simply paste a link into the URL field. 

This plugin is supported on all MyBB 1.8.X forums, and has been tested extensively. If you used BAM 1.0 before, it is strongly recommended to update to the new version of this plugin, as it is significantly more advanced and more powerful than the initial version. Many thanks to @Guardian and @Eldenroot for many ideas and for feedback on the new version of this mod! 




CUSTOM ANNOUNCEMENT PAGES: 

This is a new feature in BAM 2.0 that allows you to paste a link to a specific page to display your announcement. This setting can also take multiple values, separated by commas. Some examples of possible inputs are listed below. 

THINGS BAM EXPLICITLY PARSES: 

 - Any page's .php filename. If the ONLY thing in this field is this filename (such as portal.php, forumdisplay.php, etc.), BAM will display your announcement on EVERY page that includes this filename. This, for example, is how you can display announcements only on the portal, or on all boards. 
 - any fid (forum/board ID), tid (thread ID), uid (user ID) or action (usually a page). 

EXAMPLES: 

- forumdisplay.php?fid=2                  Displays only if the user is visiting a specific board (in this case, fid 2)
- forumdisplay.php 			  Displays on all boards. 
- member.php?action=profile&uid=15        Displays on a specific user’s profile (UID 15). (Disclaimer: Not responsible for any forum drama. )
- member.php?action=profile               Displays on ALL user profiles
- usercp.php                              Displays on the user control panel
- member.php?action=register              Displays on the registration page
- memberlist.php                          Displays on the Memberlist    
- showthread.php?tid=947.                 Display only on a specific thread (in this case, thread 947)

More documentation is available in advanced_documentation.txt. This is an advanced feature that is very powerful, but that has specific parameters that must be met in order to work properly. 



FAQ: 


CAN I DISABLE AN ANNOUNCEMENT WITHOUT DELETING IT? 

    - There are two ways you can do this. 1) Disable all usergroups for the announcement. 2) Put the [@disabled] tag in the announcement. Both will disable the announcement without deleting it! 

HOW DOES THE FORUM GAMES COUNTING THREAD VARIABLE WORK? 
 
    - It works by looking for a group of posts with consecutive numbers. It determines that a relatively large group of consecutive posts must have the correct count, and therefore corrects itself if a user posts the wrong count. It will reset itself if you post a new count for 3-4 posts in a row. 
    - {threadreplies} does the same thing, but bases itself on the reply count. If your forum game is not offset from your reply count, use this variable instead. 


WHAT IS RANDOM MODE? 

    - Random mode allows announcements to be refreshed on each page visit. BAM will select an announcement randomly from the random mode tab, and will display a different announcement each time your forum's index is visited. 
    - Due to the complexity of how BAM manages different types of announcements, you can’t put random mode announcements on other pages (or at least, not yet. Maybe a future release will expand upon this). They display on the forum’s index, below standard announcements. There are certain settings that work differently in random mode, so make sure to double check your annoncement’s settings if you convert your announcement from standard to random mode. 

CAN I USE HTML IN ANNOUNCEMENTS? 

 - Yes. Enable the HTML setting in BAM's plugin settings to allow HTML. This does not work for javascript. If you need javascript, see below. 

CAN I PUT JAVASCRIPT IN AN ANNOUNCEMENT? 

    - Technically, yes. Javascript is not, by default, enabled. However, you can make BAM load a different template for a specific announcement. If you have a global template named my_announcement_template, put [@template:my_announcement_template] anywhere in your announcement’s text. This tag tells BAM to load a different template, which can contain javascript or anything else that you need. 


CAN I MAKE BAM DISPLAY ON MULTIPLE THEMES WITHOUT DISPLAY ON EVERY THEME? 

    - Yes. Put [@themes:1,2,3] anywhere in your announcement’s text. This works for languages too! 


CAN I MAKE ANNOUNCEMENTS UNDISMISSABLE FOR GUESTS? 

    - Yes. There is a setting in the general BAM settings for this. If guests are disabled for dismissals, they will close, but not be dismissed permanently. 


CAN I RESET ALL DISMISSALS ON THE FORUM? 

    - Yes. There is a cookie prefix in BAM’s general plugin settings. Change this to any numeric value to reset all dismissals. 
    - You can also configure how long the cookie lasts (and how long dismissed announcements remain dismissed). 




ADDING CUSTOM STYLES/COLORS: 

  - By default, BAM comes with six color styles, and two of these are new styles that come with BAM+. You can add additional styles easily by defining the background color and border color in a new class. Other properties of the announcement layout (padding, etc.) are defined by a global class used by all announcements. You can place your custom style classes in the Custom CSS field in your plugin settings. Once you have saved your custom CSS classes, simply define the "custom class" field when creating announcements to use your new custom color style!

  Example: 
    
    .bam_announcement.aqua {
       border: 1px solid #0399C9;
       background-color: #BFFAFF;
    }

  You will now be able to use "aqua" in the custom class field. 



UPGRADING FROM BAM 1.0: IMPORTANT 

RECOMMENDED: Uninstall the old version of BAM. Upload the new version, and install as usual. 

 - This is the more stable way to perform an upgrade. If you don't have many announcements currently, it is recommended to perform an upgrade with a full uninstall/reinstall. 


ALTERNATIVE: Use the built in upgrader (less stable). Upload the new version of BAM. Make sure to leave BAM activated, and navigate to your plugins page. Click the upgrade link that appears in BAM's plugin description. This will make all of the necessary changes to upgrade you to BAM 2.0! 

 - The upgrade script REQUIRES bam to be fully activated first. This will not break your forum, as the new BAM is able to display forum announcements on the old templates and database temporarily. 

 - This is because BAM's upgrade script runs from straight within the BAM ACP module. Due to the way that MyBB plugins work, BAM is not able to load its own upgrade script unless it is activated. 

 - This script doesn't migrate general plugin settings. These have changed heavily from BAM 1 to BAM 2. Make sure to reconfigure BAM once the upgrade has completed! 




INSTALLATION: 

  - To install, simply upload the contents of "Upload" folder on this zip file to your MyBB root directory, and enable "BAM Announcements Manager 2.0” from your admin control panel. A new "Announcements" link will be added to the sidebar under the configuration tab, allowing you to manage your announcements. 
 
  - This plugin adds a variable in your header template after the {$awaitingusers} variable on installation. On the default theme, this causes announcements to display before the page breadcrumbs. While this method seems to ensure the best compatibility of this plugin with various themes, some users may wish you have the announcements display after the breadcrumbs. To do this, simply find "<!-- BAM -->{$bam_announcements}<!-- /BAM -->" in your templates, and copy/paste this text to a new line after "<navigation>" in your header template. 

Known issues: HTML does not work in announcements. Will be fixed for final release. 


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



