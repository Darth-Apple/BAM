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


FAQ: 

Can I set a start or end date for announcements? 
    - I did not have time to implement this in the new version. OUGC’s plugin has this feature if you desire. You may use both plugins concurrently. 

Can I disable an announcement in this version? 
    - There are two ways you can do this. 1) Disable all usergroups for the announcement. 2) Put the [@disabled] tag in the announcement. Both will disable the announcement without deleting it! 

How does the forum games feature work? 
    - I left some mystery to it for the users on my forum, but you can post the wrong count without throwing the announcement off track, and it will display the correct count EVEN if this count is offset from the reply count (due to deleted spam posts, etc.)
    - It works by looking for a group of posts with consecutive numbers. It determines that a relatively large group of consecutive posts must have the correct count, and therefore corrects itself if a user posts the wrong count. It will reset itself if you post a new count for 3-4 posts in a row. 
    - {threadreplies} does the same thing, but bases itself on the reply count. If your forum game is not offset from your reply count, use this variable instead. 


What exactly is Random Mode? 

    - This was the very first feature in BAM, before I even called it BAM. It was initially a plugin I did by request for a community. They needed randomly refreshing announcements from a list of announcements, so that is what we did. Everything else that has been added to BAM since has been an extension of the original functionality. 
    - It still exists, in full form! If you have several announcements and you need one to display randomly on each page visit, create these announcements under random mode. If you don’t see the random mode tab, make sure random mode is enabled in the plugin’s settings first. These don’t affect standard mode announcements, which will still display like normal! 
    - Due to the complexity of how BAM manages different types of announcements, you can’t put random mode announcements on other pages. They display on the forum’s index, below standard announcements. There are certain settings that work differently in random mode, so make sure to double check your annoncement’s settings if you convert your announcement from standard to random mode. 


Can I put javascript in an announcement? 

    - Technically, yes. Javascript is not, by default, enabled. However, you can make BAM load a different template for a specific announcement. If you have a global template named my_announcement_template, put [@template:my_announcement_template] anywhere in your announcement’s text. This tag tells BAM to load a different template, which can contain javascript or anything else that you need. 


Can I make BAM display on multiple themes, but not every theme? 

    - Yes. Put [@themes:1,2,3] anywhere in your announcement’s text. This works for languages too! 


Why do these tags display on the admin CP, but not on the announcement itself? 

    - Hiding them on the admin CP would make it harder to see the tags from the management page. They should only be hidden on the forum itself, not on the admin CP! :P


Can I make announcements close without permanently dismissing them? 

    - Yes. There is a setting in the general BAM settings for this. You can also disable dismissals for guests. If guests are disabled for dismissals, they will close, but not be dismissed permanently. 


Can I clear all dismissals and reset them for every user? 

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

  - This is a major upgrade, as BAM 2.0 introduces a number of database and template changes. BAM will not work unless the database tables are updated with the new version. To upgrade, it is very important that the steps below be followed during an installation. Your announcements will need to be re-added after the upgrade, so copy/paste any announcements if you need them saved.  

  - Go to ACP -> plugins. Uninstall the old version of BAM. 
  - Upload BAM 2.0 to the server, overwriting any existing files if prompted. 
  - Go to ACP -> plugins. Install/activate the new version of BAM. 
  - You're now upgraded! 

Do not upload the new version of BAM announcements manager before uninstalling the old version. You DO NOT need to remove the old files before doing this! It is ONLY necessary to uninstall it from the ACP -> plugins panel. 


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



