<?php
/**
 * Launch Plugin.
 *
 * This plugin enhance the Launch theme:
 *   - add a subtitle.
 *   - choose your menu style.
 */

$configFilename = PluginManager::$PLUGINS_PATH . '/launch_plugin/config.php';
$defaultConfigFilename = PluginManager::$PLUGINS_PATH . '/launch_plugin/config.php.dist';
if (! is_file($configFilename)
    && is_file($defaultConfigFilename)) {
    copy($defaultConfigFilename, $configFilename);
}

if (is_file($configFilename)) {
    require_once $configFilename;
}

/**
 * Adds a subtitle.
 * FIXME! It's a bit hacky. Maybe the plugin system needs a better way to do that.
 */
function hook_launch_plugin_render_header($data)
{
    if (! empty($GLOBALS['plugins']['LAUNCH_SUBTITLE'])) {
        $data['launch_subtitle'] = $GLOBALS['plugins']['LAUNCH_SUBTITLE'];
    }

    if (! empty($GLOBALS['plugins']['LAUNCH_HORIZONTAL_MENU'])
        && ($GLOBALS['plugins']['LAUNCH_HORIZONTAL_MENU'] == 1
        || $GLOBALS['plugins']['LAUNCH_HORIZONTAL_MENU'] === true)
    ) {
        $data['launch_horizontal'] = true;
    }
    else {
        $data['launch_horizontal'] = false;
    }

    return $data;
}