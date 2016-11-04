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
 * Plugin init.
 *
 * @param ConfigManager $conf instance.
 */
function launch_plugin_init($conf)
{
    if (! $conf->exists('plugins.LAUNCH_CUSTOM_MENU')
        && file_exists(PluginManager::$PLUGINS_PATH . '/launch_plugin/menu.json')
    ) {
        $menu = json_decode(file_get_contents(PluginManager::$PLUGINS_PATH . '/launch_plugin/menu.json'));
        if (is_array($menu)) {
            $conf->set('plugins.LAUNCH_CUSTOM_MENU', $menu);
            $conf->write(true);
        }
    }
}

/**
 * Includes hook: process theme settings form data.
 *
 * @param array         $data data passed to the plugin.
 * @param ConfigManager $conf instance.
 *
 * @return array updated data.
 *
 * @throws Exception Couldn't write config file.
 */
function hook_launch_plugin_render_includes($data, $conf)
{
    if (isset($_POST['launch'])) {
        if (isset($_POST['reload']) && file_exists(PluginManager::$PLUGINS_PATH . '/launch_plugin/menu.json')) {
            $menu = json_decode(file_get_contents(PluginManager::$PLUGINS_PATH . '/launch_plugin/menu.json'));
            if (is_array($menu)) {
                $conf->set('plugins.LAUNCH_CUSTOM_MENU', $menu);
            }
        } else {
            $conf->set('plugins.LAUNCH_SUBTITLE', !empty($_POST['subtitle']) ? escape($_POST['subtitle']) : '');
            $conf->set('plugins.LAUNCH_VERTICAL_MENU', !empty($_POST['vertical_menu']));
            $conf->set('plugins.LAUNCH_HORIZONTAL_MENU', !empty($_POST['horizontal_menu']));
            $conf->set('plugins.LAUNCH_OVERRIDE_VERTICAL', !empty($_POST['override_vertical']));
        }
        $conf->write(true);
    }

    return $data;
}

/**
 * Adds a subtitle.
 *
 * @param array         $data data passed to the plugin.
 * @param ConfigManager $conf instance.
 *
 * @return array updated data.
 */
function hook_launch_plugin_render_header($data, $conf)
{
    $data['launch_subtitle'] = $conf->get('plugins.LAUNCH_SUBTITLE', '');
    $data['launch_horizontal'] = $conf->get('plugins.LAUNCH_HORIZONTAL_MENU', false);

    $menu = $conf->get('plugins.LAUNCH_CUSTOM_MENU');
    if ($conf->get('plugins.LAUNCH_OVERRIDE_VERTICAL', false) && !empty($menu)) {
        $data['launch_vertical'] = $menu;
    }

    return $data;
}

/**
 * Add plugin settings to the tool page.
 *
 * @param array         $data data passed to the plugin.
 * @param ConfigManager $conf instance.
 *
 * @return array updated data.
 */
function hook_launch_plugin_render_tools($data, $conf)
{
    $menu = $conf->get('plugins.LAUNCH_CUSTOM_MENU');
    $tplVar = array(
        'SUBTITLE' => $conf->get('plugins.LAUNCH_SUBTITLE', ''),
        'HORIZONTAL_CHECKED' => $conf->get('plugins.LAUNCH_HORIZONTAL_MENU', false) ? 'checked="checked"' : '',
        'OVERRIDE_VERTICAL' => $conf->get('plugins.LAUNCH_OVERRIDE_VERTICAL', false) ? 'checked="checked"' : '',
        'CUSTOM_MENU' => !empty($menu) ? json_encode($menu, JSON_PRETTY_PRINT) : '',
    );
    $tpl = file_get_contents(PluginManager::$PLUGINS_PATH . '/launch_plugin/tools.html');
    $tpl = replace_tpl_var($tpl, $tplVar);
    $data['tools_plugin'][] = $tpl;
    return $data;
}

/**
 * Mini template engine: replace variable in html content.
 *
 * @param string $tpl          HTML content.
 * @param array  $var          Variable keys to replace.
 * @param string $varSeparator Variables start and end with this separator (default: '~').
 *
 * @return string tpl with variables content.
 */
function replace_tpl_var($tpl, $var, $varSeparator = '~')
{
    foreach ($var as $key => $value) {
        $tpl = preg_replace(
            '/'. $varSeparator . $key . $varSeparator . '/s',
            $value,
            $tpl
        );
    }

    return $tpl;
}
