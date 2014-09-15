Board Announcements Manager is a plugin that allows you to manage announcements in the header area of your forum without any need for modifications to the templates. 

Features: 
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

  - Board announcements manager includes random mode, which is disabled by default. When enabled, BAM will select announcements randomly from the announcements list rather than displaying all available announcements. Under random mode, you will be able to configure how many announcements are randomly selected. Pinned announcements will always display, regardless of how many random announcements are set to display in the settings. You may also configure which groups are allowed to see randomly selected announcements. 

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

Installation: 

  - To install, simply upload the contents of "Upload" folder on this zip file to your MyBB root directory, and enable "Board Announcements Manager" from your admin control panel. A new "Announcements" link will be added to the sidebar under the configuration tab, allowing you to manage your announcements. 
 
  - This plugin adds a variable in your header template after the {$awaitingusers} variable on installation. On the default theme, this causes announcements to display before the page breadcrumbs. While this method seems to ensure the best compatibility of this plugin with various themes, some users may wish you have the announcements display after the breadcrumbs. To do this, simply find "<!-- BAM -->{$bam_announcements}<!-- /BAM -->" in your templates, and copy/paste this text to a new line after "<navigation>" in your header template. 

Upgrading: 
  - Follow the upgrading instructions that will be included in future versions of this plugin. 

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
