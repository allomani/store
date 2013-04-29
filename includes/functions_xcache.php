<?

function cache_init() {
    global $config;
    if (!function_exists('xcache_get')) {
        die("Xcache is not Installed");
    }
}

function cache_set($name, $data) {
    global $config;
    return xcache_set($config['cache']['prefix'] . $name, $data, $config['cache']['expire']);
}

function cache_get($name) {
    global $config;

    $data = xcache_get($config['cache']['prefix'] . $name);
    if ($data == NULL) {
        return false;
    } else {
        return $data;
    }
}

function cache_del($name) {
    global $config;
    return xcache_unset($config['cache']['prefix'] . $name);
}