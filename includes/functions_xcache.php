<?

function cache_init() {
    global $cache_srv;
    if (!function_exists('xcache_get')) {
        die("Xcache is not Installed");
    }
}

function cache_set($name, $data) {
    global $cache_srv;
    return xcache_set($cache_srv['prefix'] . $name, $data, $cache_srv['expire']);
}

function cache_get($name) {
    global $cache_srv;

    $data = xcache_get($cache_srv['prefix'] . $name);
    if ($data == NULL) {
        return false;
    } else {
        return $data;
    }
}

function cache_del($name) {
    global $cache_srv;
    return xcache_unset($cache_srv['prefix'] . $name);
}