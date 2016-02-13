# Launch Plugin

Launch Plugin is a plugin designed to enhance and customize 
[Launch Theme](https://github.com/ArthurHoaro/shaarli-launch) for [Shaarli](https://github.com/shaarli/Shaarli).

Customize Launch in Shaarli `Tools` administration page.

## Features

### Subtitle

![screenshot-subtitle](http://i.imgur.com/C1e048X.png)

### Horizontal Menu

Display Shaarli horizontally on header and footer.

![screenshot-horizontal-menu](http://i.imgur.com/GW2E8SJ.png)

### Customize your vertical menu

![screenshot-vertical-menu](http://i.imgur.com/3bammWw.png)

## Installation

  1. Download the latest [release](https://github.com/ArthurHoaro/launch-plugin/releases) of this plugin.
  2. Put the `launch-plugin` folder in your Shaarli installation, in `plugins/` (don't change the name).
  3. Enable Launch plugin in Shaarli's plugin administration page. 

##### Plugin order

For a better display of the Tools page, put this after any plugin altering the Tools template.

## Custom menu

Enable vertical menu overriding in Shaarli `Tools` administration page. 

To customize the vertical menu, in `plugins/launch-plugin/config.php`, 
edit `$GLOBALS['plugins']['LAUNCH_CUSTOM_MENU']`.
You can edit existing item, and add new ones.

Available icons:

  * `home`
  * `blog`
  * `rss`
  * `contact`
  * `link`
  * `cv`
  * `projects`
  * `wiki`
  * `cloud`