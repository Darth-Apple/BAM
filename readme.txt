BAM Announcements Manager — Version 2.0 - BETA VERSION: 

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

MAKING ANNOUNCEMENTS DISPLAY ON SPECIFIC PAGES: 
   - This is a brand new feature in BAM 2.0! This gives quite a bit of granularity on where announcements will be posted. You can make them display only on a specific user’s profile, or on a specific forum board, or on a specific thread, for example. 
   - This setting allows you to paste links to specific pages that your announcement should be displayed on. When this setting is used, your announcement will ONLY display on these pages. 
   - For stability and security reasons, there are specific constraints that must be met for the pages that are enabled by this setting. While most links will work, there are very specific fields that are parsed by this plugin. Many very obscure pages (such as the login redirect page) will not display announcements. 

TECHNICAL DETAILS ON THE ADVANCED MODE PARSER (READ IF YOU ENCOUNTER ANY ISSUES): 

Please note that you are using a beta version of this plugin. This feature will require rigorous testing before it can be considered stable. If the announcement does not display on the correct page, please read this documentation and make sure that all of the required steps are followed. Otherwise, the parser may look at the URL slightly different than a user would, and may determine that the page doesn’t match what it is looking for. 

   - For security and stability reasons, only specific URL constraints are parsed. On redirect pages or on pages with unusual parameters, this won’t work.
   - Any PHP file for your forum (such as index.php, portal.php, forumdisplay.php, member.php, etc.) will be capable of displaying announcements. 
   - BAM does not check additional fields unless they exist in the announcement’s settings. If, for example, you use “forumdisplay.php,” BAM will completely ignore any additional URL fields as a user browses your forum. Any page that is served by forumdisplay.php will serve your announcement, regardless of anything that follows in the URL. (This is intended functionality. BAM would act very strangely otherwise. ) 
   -  However, if you set an announcement with any additional fields outside of the .PHP file (e.g. “forumdisplay.php?fid=2”), BAM will make sure that ALL whitelisted fields match before displaying an announcement. 
   - Before pasting your link, check if there are extra fields in the URL first. Try removing these fields, and make sure you still end up on the same page. If so, the URL is now safe to use for BAM announcements. 

Again, this is an advanced feature because of its technical nature. It was implemented by request and is very powerful, but it may not work for certain pages. However, examples of pages it will work perfectly for include: 

- forumdisplay.php?fid=2                                   Displays only if the user is visiting a specific board (in this case, fid 2)
- member.php?action=profile&uid=15               Displays on a specific user’s profile (UID 15). (Disclaimer: Not responsible for any forum drama. )
- member.php?action=profile                             Displays on ALL user profiles
- usercp.php                                                        Displays on the user control panel
- member.php?action=register                           Displays on the registration page
- memberlist.php                                                 Displays on the Memberlist    
- showthread.php?tid=947.                                Display only on a specific thread (in this case, thread 947)

Many other pages will work perfectly. These are just a few examples of some that are tested. If you need to define more than one page, you can do so by separating URLs with a comma. The URL parser will display your announcement on all pages that are valid. 

List of whitelisted URL fields: fid, tid, uid, action
  — (All other fields are ignored)


DISPLAYING ANNOUNCEMENTS ON SPECIFIC THEMES:

    - This feature was implemented by request in BAM 2.0 as an “unofficial feature” to avoid cluttering the announcement settings. To use this feature, you must put a tag in your announcement with the theme IDs to display your announcement on. 

Theme tag: “[@themes: 1, 2, 3]”

If this tag  (without the quotes) is present anywhere in your announcement’s text, it will ONLY be displayed on themes 1, 2, 3, for example. To get your theme’s ID,  you will need to visit the theme within the Admin Control Panel, and look for “tid=“ in the URL. This will show the theme ID for any specific theme. 

DISPLAYING ANNOUNCEMENTS ON SPECIFIC LANGUAGES: 

   - Similarly to the theme tag feature, BAM+ 2.0 now supports language tags. If this tag is put anywhere in your announcement’s text, the announcement will be displayed only on languages 1 and 2. 
 
[@languages: 1, 2] 

Both theme tags and language tags are internal directives for BAM, and are removed from announcements before being displayed. Although they are displayed on the management panel, users will not be able to see these tags on your forum! 

RANDOM MODE:

     - BAM announcements manager includes random mode, which is disabled by default. When enabled, BAM handles two categories of announcements. Standard announcements are persistent and always displayed. Random mode announcements are displayed below standard announcements, and are selected randomly. 
    - Random mode announcements handle dismissals differently. They can be closed, but they cannot be dismissed. If the page is refreshed, another random announcement will display. There is a setting available to disable random mode dismissals entirely, if desired. 
    - If you previously used BAM 1.0 to handle random announcements, BAM 2.0 now has an updated and improved ACP interface for managing them. Random announcements now exist on their own page, and are now completely separate from standard announcements. You no longer need to pin announcements to define them as standard announcements. The new functionality should be significantly less confusing and much easier to use. 

GLOBAL ANNOUNCEMENTS:

   - Global announcements are now handled on a per-announcement basis. 
   - Pinned announcements (BAM 1.0) are deprecated, and thus no longer global by default. Simply set each individual announcement as global to make them display on all pages of your forum. 
    - Global announcements do not affect random mode, nor do they affect announcements that are configured to display on specific pages (advanced mode). Only standard announcements will be displayed globally if configured to do so. (This is an improvement over BAM 1.0, where it was previously impossible to manage both random and global announcements in this manner.)
  
ORDERING ANNOUNCEMENTS: 

   - Announcements are assigned a display order by default when adding a new announcement. You can edit these display orders on the announcements management index. Note that pinned announcements are always displayed first, regardless of their display order. If you have multiple pinned announcements, pinned announcements will be displayed first by their display orders, and non pinned announcements will then be displayed by their display orders. 

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
