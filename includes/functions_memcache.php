<?

function cache_init() {
    global $memcache, $config;
    if (class_exists('Memcache')) {
        $memcache = new Memcache;
        $memcache->connect($config['cache']['memcache_host'], $config['cache']['memcache_port']) or die("Could not connect to Memcache");
    } else {
        die("Memcache is not Installed");
    }
}

function cache_set($name, $data) {
    global $memcache, $config;

    return $memcache->set($config['cache']['prefix'] . $name, $data, MEMCACHE_COMPRESSED, $config['cache']['expire']);
}

function cache_get($name) {
    global $memcache, $config;

    return $memcache->get($config['cache']['prefix'] . $name);
}

function cache_del($name) {
    global $memcache, $config;
    return $memcache->delete($config['cache']['prefix'] . $name);
}