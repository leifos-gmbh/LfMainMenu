# LfMainMenu

Fully configurable ILIAS main menu.

**Requirements**

- ILIAS: 5.0.3 -5.4.x
- PHP: 7.0-7.3 (older versions may still work, but are not tested anymore)

If you use ILIAS 5.4.x the default "Personal Desktop" submenu will no be rendered anymore. You need to setup a custom menu and add the subfeatures one by one to get the same result.

Why ILIAS 5.4 support? ILIAS 5.4 includes a configurable main menu now, but if you used this plugin before you may miss some of its features, e.g. submenu support.

**Features**
- Custom menus and submenus(!)
- Standard menus "Personal Desktop", "Repository" and "Administration"
- Separators
- All features of the Personal Desktop (Bookmarks, Calendar, Mail, ...) can be separately embedded (automatic feature activation check, no need to use complicated GUI class paths)
- All repository nodes (by using their Ref IDs) embeddable
- "Last Visited" integration
- Multilinguality
- Custom URLs and permission checks
 
**Installation**
- Download the zip file
- Extract content into a subdirectory Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/LfMainMenu within your main ILIAS directory
- Enter ILIAS Administration > Plugins and activate/configure the plugin
 
**Screenshot**

<img src="http://www.ilias.de/docu/data/docu/mobs/mm_45170/Bildschirmfoto_2015-07-17_um_14.29.38.png" />

