BAM Announcements Manager — Version 2.0 - BETA VERSION: 

VERY EARLY BETA VERSION OF THIS PLUGIN. USE AT YOUR OWN RISK. 

   — New and updated! Please read this document fully, as some features have changed. Please be aware that this is a beta version. There is no warranty with this version, and there WILL be various issues that pop up. If you would like to contribute, please report these issues as you discover them, and I will make sure to fix them before the plugin’s release. Otherwise, hang tight. The official release will be out shortly!

————————————————————————————————
BAM Announcements Manager is a plugin that allows you to manage announcements in the header area of your forum without any need for modifications to the templates. BAM+ adds several new features, and creates an improved announcement interface and manager for your forum. 

NEW IN BAM 2.0
   - Dismissible announcements (finally)! 
   - Random mode is completely redesigned, and is now much easier to use.  
   - The "index page" setting now takes multiple pages. Non global announcements can be displayed on multiple pages without being displayed forum-wide.  
   - Global announcements are now handled on a per-announcement basis. Pinned announcements deprecated (and are no longer the method of making announcements global). This gives much more granularity for where announcements are displayed.
   - New Advanced Mode. Please read the remainder of this documentation for more details.
   - HTML, as well as the image tag are now enabled when using advanced mode. 
   - New ability to set announcements to display only on specific pages, on a per-announcement basis (see advanced mode section). 
   - “Unofficial” feature to display announcements only on specific themes and languages (see the section on theme tags). 
   - New variables for announcements

STANDARD FEATURES (BAM 1.0): 
   - Support for an unlimited number of header announcements, with the ability to order and reorder announcements. 
   - Four included color classes, with the ability to define additional custom classes by announcement. 
   - Usergroup permissions for announcements
   - Support for BBcode and emoticons (and now HTML — advanced mode) in announcements. 
   - Ability to optionally display the date posted on announcements.
   - Ability to display announcements globally. 
   - Ability to parse the username if {$username} is placed in an announcement. (Parses to "Guest" if the user is not logged in. )
   - Optional "random mode" functionality. 
   - Ability to turn an announcement into a link without the use of BBcode. 

ADVANCED MODE:
    - This feature was heavily requested by members of the MyBB community. I read your requests and your suggestions. I’ve decided to implement it to answer the requests from the community!
   - The purpose of this mode is to add features that are a little more technical in nature. Some of these features may not necessarily make sense to users who have not read the documentation, so they are disabled by default. However, in the interest of providing as much functionality as possible, these           features are enabled by enabling the “Advanced Mode” setting, for users who are comfortable. 
   - I anticipate that most forum owners who use this plugin heavily will enable this mode. Let me know if there  are any features you would like added, as this mod is still in beta status!

Features enabled by advanced mode: 
    - Ability to determine specific pages for announcements to display. 
    - Ability to use full HTML in announcements. 
    - A new theme tag to make announcements visible only on specific themes. 
    - New language tag to make announcements visible only on specific languages
    - Brings back the deprecated “announcement link” feature

-----------------------------------------



MAKING ANNOUNCEMENTS DISPLAY ON SPECIFIC PAGES: 

   - This is a brand new feature in BAM 2.0! This gives quite a bit of granularity on where announcements will be posted. You can make them display only on a specific user’s profile, or on a specific forum board, or on a specific thread, for example. 
   - When this setting is used, your announcement will ONLY display on these pages. Global announcement settings are ignored. 
   - The vast majority of pages on your forum will work simply by pasting the URL into the "Additional display pages" setting. Although this is a beta feature, it works very well for most common pages on the forum, and has been tested extensively. 
   - There are certain pages where announcements won't display, or where the URL won't parse quite like you might expect. Many very obscure pages (such as the login redirect page) will not display announcements, for example. Most of these situations are documented below. If you have any issues with this setting, please refer to the steps below. 

TECHNICAL DETAILS ON THE ADVANCED MODE PARSER (READ IF YOU ENCOUNTER ANY ISSUES): 

The URL parser works differently than it might seem at first glance. MyBB does not necessarily "see" your page's URL when you visit a page. Instead, it sees building blocks that tell it what to do and what to display. Some of these building blocks are very important. Others are much more minor or extraneous altogether. In any case, MyBB creates your page by displaying and executing the correct code, depending on which parameters (or building blocks) are present in your URL. 

BAM+ does not look at the entire URL as a single link to check against the user's current page. Instead, it looks for these building blocks in the URL, and tries to determine whether it should display the page based on the parameters it is given. This was necessary for a number of reasons. BAM+ would act very strangely otherwise! 

In general, this improves functionality rather than introducing bugs. If, for example, you give a URL of http://makestation.net/member.php?action=profile&uid=3, BAM will know that you need the announcement to display specifically, and exclusively, on a single user's profile. However, if you give BAM the URL of http://makestation.net/member.php?action=profile, BAM will see that a user ID is not in the URL, and then display the announcement on all user profiles. 

This functionality works in exactly the same manner on almost every section of your forum. For example, if you want announcements to display only on one specific board/forum on your community, you would copy/paste a URL that looks like this: http://makestation.net/forumdisplay.php?fid=18 .  BAM would see that forumdisplay.php has an additional 'fid=18' parameter, and correctly assume that announcements should only be displayed on board ID 18. 

In general, this works exactly like you would expect. Simply paste the link to the page that you need the announcement to display on, and BAM will do the rest. 

WHEN THE LINK FUNCTIONALITY WORKS DIFFERENTLY: 

- Because of the way that BAM parses the links that it is given, it may see certain links differently than you, as a user, would. A perfect example of this would be displaying an announcement on "forumdisplay.php." If you were to visit this page on your forum without any additional parameters, you would get an error. MyBB refuses to display a forum/board if it doesn't know what board to display. 

- BAM sees this page differently. It will see that there are no parameters, and will conclude that it should display the announcement on ALL forums/boards. This might seem like a bug, but this is explicitly not a bug, and is exactly how BAM was designed to see these URLs. If BAM saw URLs "literally" rather than by parsing them as it does, there would be a lot of functionality that would be lost. It would, for example, be impossible to display announcements on all boards. 

THINGS BAM EXPLICITLY IGNORES: 

 - There are specific parameters that BAM explicitly does not parse. These omissions are not bugs, and they serve to make BAM behave in a way that is more consistent with how it would be expected to operate. Some of these fields include: 

 - Page numbers. You can display an announcement on a specific thread, but you cannot make an announcement display only on a certain page of that thread. (If this functionality was enabled, users would, by accident, copy/paste a thread's URL that included a page number, and mistakenly disable announcements for every page except this particular page number.)
 - Login and redirect pages. (Not supported at all)
 - Specific search queries. (You can put announcements on the search page, but they will display for all search queries.) 

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

In general, you can simply copy/paste a link to the page, and BAM will parse it correctly. These are just a few examples of some that are tested, and many others will work! If you have any issues with this setting, please refer to the documentation above, and report the issue if you feel that there is a bug! 

DISPLAYING ON MULTIPLE PAGES: 

If you need to define more than one page, you can do so by separating URLs with a comma. The URL parser will display your announcement on all pages that are valid. 


-----------------------------


VARIABLES AND DIRECTIVES: 
Place any of the following in your announcement to have BAM replace it with its value. 

	{username}     		Parses to the current user's username (or Guest, if not registered)
	{newestmember}     	Parses to the username of the most recently registered member. 
	{newestmember_uid}	Parses to the UID of the most recent member. 
	{newestmember_link}	Creates a link to the newest registered member. 
	{threadreplies}		Only on showthread.php. Shows how many replies the current thread has. 
	{countingthread}	For your forum games/counting thread. Parses to the CORRECT count based on the last 10 posts, regardless of whether your users get off track by accident (highly experimental feature. Only works on threads where a consecutive number is generally posted on every reply).

 
DISPLAYING ANNOUNCEMENTS ON SPECIFIC THEMES:

    - This feature was implemented by request in BAM 2.0 as an “unofficial feature” to avoid cluttering the announcement settings. To use this feature, you must put a tag in your announcement with the theme IDs to display your announcement on. 

Theme tag: “[@themes: 1, 2, 3]”

If this tag  (without the quotes) is present anywhere in your announcement’s text, it will ONLY be displayed on themes 1, 2, 3, for example. To get your theme’s ID,  you will need to visit the theme within the Admin Control Panel, and look for “tid=“ in the URL. This will show the theme ID for any specific theme. 

DISPLAYING ANNOUNCEMENTS ON SPECIFIC LANGUAGES: 

   - Similarly to the theme tag feature, BAM+ 2.0 now supports language tags. If this tag is put anywhere in your announcement’s text, the announcement will be displayed only on languages 1 and 2. 
 
[@languages: 1, 2] 

Both theme tags and language tags are internal directives for BAM, and are removed from announcements before being displayed. Although they are displayed on the management panel, users will not be able to see these tags on your forum! 

OTHER DIRECTIVES:

[@disabled]      		 -- Disables an announcement without deleting it. 
[@template:custom_template]      -- Selects a different template for this announcement. 

If you use custom templates, you must define this template in your forum's global templates. Please note that using custom templates may break features such as announcement dismissals and date functionality. 

---------------------------------

RANDOM MODE:

     - BAM announcements manager includes random mode, which is disabled by default. When enabled, BAM handles two categories of announcements. Standard announcements are persistent and always displayed. Random mode announcements are displayed below standard announcements, and are selected randomly. 
    - Random mode announcements handle dismissals differently. They can be closed, but they cannot be dismissed. If the page is refreshed, another random announcement will display. There is a setting available to disable random mode dismissals entirely, if desired. 
    - If you previously used BAM 1.0 to handle random announcements, BAM 2.0 now has an updated and improved ACP interface for managing them. Random announcements now exist on their own page, and are now completely separate from standard announcements. You no longer need to pin announcements to define them as standard announcements. The new functionality should be significantly less confusing and much easier to use. 

GLOBAL ANNOUNCEMENTS:

   - Global announcements are now handled on a per-announcement basis. 
   - Pinned announcements (BAM 1.0) are deprecated, and thus no longer global by default. Simply set each individual announcement as global to make them display on all pages of your forum. 
    - Global announcements do not affect random mode, nor do they affect announcements that are configured to display on specific pages (advanced mode). Only standard announcements will be displayed globally if configured to do so. (This is an improvement over BAM 1.0, where it was previously impossible to manage both random and global announcements in this manner.)
  
ORDERING ANNOUNCEMENTS: 

   - Announcements are assigned a display order by default when adding a new announcement. You can edit these display orders on the announcements management index. Note that Sticky (non-dismissible) announcements are always displayed first, regardless of their display order. If you have multiple stickied announcements, sticky announcements will be displayed first by their display orders, and non-sticky announcements will then be displayed by their display orders. 

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

Upgrading: 
  - See the upgrading instructions listed above. 

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
