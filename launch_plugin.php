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
 * Includes hook: process theme settings form data.
 *
 * @param array $data data passed to the plugin.
 *
 * @return array updated data.
 *
 * @throws Exception Couldn't write config file.
 */
function hook_launch_plugin_render_includes($data)
{
    if (isset($_POST['launch'])) {
        $GLOBALS['plugins']['LAUNCH_SUBTITLE'] = !empty($_POST['subtitle']) ? escape($_POST['subtitle']) : '';
        $GLOBALS['plugins']['LAUNCH_VERTICAL_MENU'] = !empty($_POST['vertical_menu']);
        $GLOBALS['plugins']['LAUNCH_HORIZONTAL_MENU'] = !empty($_POST['horizontal_menu']);

        save_plugin_data();
    }

    return $data;
}

/**
 * Adds a subtitle.
 *
 * @param array $data data passed to the plugin.
 *
 * @return array updated data.
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

/**
 * Add plugin settings to the tool page.
 *
 * @param array $data data passed to the plugin.
 *
 * @return array updated data.
 */
function hook_launch_plugin_render_tools($data)
{
    $tplVar = array(
        'SUBTITLE' => !empty($GLOBALS['plugins']['LAUNCH_SUBTITLE']) ? $GLOBALS['plugins']['LAUNCH_SUBTITLE'] : '',
        'VERTICAL_CHECKED' => isset($GLOBALS['plugins']['LAUNCH_VERTICAL_MENU']) ? 'checked="checked"' : '',
        'HORIZONTAL_CHECKED' => isset($GLOBALS['plugins']['LAUNCH_HORIZONTAL_MENU']) ? 'checked="checked"' : '',
        'OVERRIDE_VERTICAL' => isset($GLOBALS['plugins']['OVERRIDE_VERTICAL']) ? 'checked="checked"' : '',
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

/**
 * Save new settings in plugin specific settings file (plugins/launch-plugin/config.php).
 *
 * @throws Exception Couldn't write config file.
 */
function save_plugin_data() {
    $dataKeys = array(
        'LAUNCH_SUBTITLE',
        'LAUNCH_VERTICAL_MENU',
        'LAUNCH_HORIZONTAL_MENU',
        'OVERRIDE_VERTICAL',
    );

    $configStr = '<?php '. PHP_EOL;
    foreach($dataKeys as $key) {
        if (isset($GLOBALS['plugins'][$key])) {
            $configStr .= '$GLOBALS[\'plugins\'][\''. $key .'\'] = '. var_export($GLOBALS['plugins'][$key], true) .';'. PHP_EOL;
        }
    }

    $settingsFile = PluginManager::$PLUGINS_PATH . '/launch_plugin/config.php';
    if (!file_put_contents($settingsFile, $configStr)
        || strcmp(file_get_contents($settingsFile), $configStr) != 0
    ) {
        throw new Exception(
            'Couldn\'t write Launch theme settings.
            Please make sure Shaarli has the right to write in the folder is it installed in.'
        );
    }
}