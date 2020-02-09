BAM Announcements Manager is a plugin that allows you to manage announcements in the header area of your forum without any need for modifications to the templates. You are reading the documentation for BAM 2.0, which has refreshed many of the features in BAM and has added a few new features that were requested from the community. If you previously used BAM before, please re-read this documentation for the new version, as a number of things have changed. 

New in 2.0: 
 - Dismissible announcements (finally added)! 
 - Random mode is refactored, and much easier to use. 
 - The "index page" setting now takes multiple pages. Non global announcements can now be displayed on multiple pages without being displayed forum-wide.  
 - Global announcements are now handled on a per-announcement basis. Pinned announcements are no longer global by default. This gives much more granularity for what announcements are shown globally. 

- Advanced mode: New in 2.0: 
   - Now supports full HTML in announcements (javascript works as well). 
   - Supports displaying announcement on specific pages of the website (see "specific announcement pages" section)
   - Announcement links are supported. This feature is now deprecated. BBCode can replace its functionality with better granularity. 

Standard Features (BAM 1.0): 
 - Support for an unlimited number of header announcements, with the ability to order and reorder announcements. 
 - Four included color classes, with the ability to define additional custom classes by announcement. 
 - Usergroup permissions for announcements
 - Support for BBcode and emoticons in announcements. 
 - Ability to optionally display the date posted on announcements.
 - Ability to optionally display announcements globally. 
 - Ability to parse the username if {$username} is placed in an announcement. (Parses to "Guest" if the user is not logged in. )
 - Optional "random mode" functionality. 
 - Ability to turn an announcement into a link without the use of BBcode. 
	
Random Mode: 

  - BAM announcements manager includes random mode, which is disabled by default. When enabled, BAM handles two categories of announcements. Standard announcements are persistent and always displayed. Random mode announcements are displayed below standard announcements, and are selected randomly. 
     BAM 2.0 handles random mode announcements differently than BAM 1.0. When random mode is enabled, a new tab will be displayed on the announcements management panel, and this will allow random mode announcements to be managed. This tab is only visible if random mode is enabled, and will be hidden if random mode is disabled. Pinned announcements no longer affect random mode functionality, and are no longer used to determine which announcements are displayed randomly. 
     
Some Random Mode Gotchas: 
  - 


Global Display of Announcements: 

  - By default, announcements only display on the forum index. You can choose to display them globally instead in your plugin settings, or alternatively configure BAM to display only pinned announcements globally. The index page setting defines the page that will be considered the "index page" by the plugin. Unless you have renamed index.php or are using the portal as your home page, it is highly recommended that this setting be left at its default. 
  
Ordering Announcements: 

  - Announcements are assigned a display order by default when adding a new announcement. You can edit these display orders on the announcements management index. Note that pinned announcements are always displayed first, regardless of their display order. If you have multiple pinned announcements, pinned announcements will be displayed first by their display orders, and non pinned announcements will then be displayed by their display orders. 

Adding Custom Styles: 

  - By default, BAM comes with four color styles. You can add additional styles easily by defining the background color and border color in a new class. Other properties of the announcement layout (padding, etc.) are defined by a global class used by all announcements. You can place your custom style classes in the Custom CSS field in your plugin settings. Once you have saved your custom CSS classes, simply define the "custom class" field when creating announcements to use your new custom color style!

  Example: 
    
    .bam_announcement.aqua {
       border: 1px solid #0399C9;
       background-color: #BFFAFF;
    }

  You will now be able to use "aqua" in the custom class field. 

UPGRADING FROM BAM 1.0: IMPORTANT 

  - Bam 2.0 introduces a number of database and template changes. Because of this, BAM 2.0 will not work unless the database tables are updated to the new schema. This was something that I attempted to avoid during the update, but as development went forward, it became clear that in order to expand BAM's functionality and to bring many of the requested features to the plugin, deeper changes would be required. 

To upgrade, it is very important that the steps below be followed during an installation. Please make note and save any announcements that you will need to add after this has been completed. These announcements will be removed during the upgrade. 

  - Go to ACP -> plugins and uninstall the old version of BAM. 
  - Upload BAM 2.0 to the server, overwriting any existing files if prompted. 
  - Go to ACP -> plugins, and install/activate BAM. 
  - You're now upgraded! 

Do not upload the new version of BAM announcements manager before uninstalling the old version. You do not need to remove the old files. It is only necessary to uninstall it from the ACP -> plugins panel. 


Installation: 

  - To install, simply upload the contents of "Upload" folder on this zip file to your MyBB root directory, and enable "Board Announcements Manager" from your admin control panel. A new "Announcements" link will be added to the sidebar under the configuration tab, allowing you to manage your announcements. 
 
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
