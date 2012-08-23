<?

function cache_init() {
    global $memcache, $cache_srv;
    if (class_exists('Memcache')) {
        $memcache = new Memcache;
        $memcache->connect($cache_srv['memcache_host'], $cache_srv['memcache_port']) or die("Could not connect to Memcache");
    } else {
        die("Memcache is not Installed");
    }
}

function cache_set($name, $data) {
    global $memcache, $cache_srv;

    return $memcache->set($cache_srv['prefix'] . $name, $data, MEMCACHE_COMPRESSED, $cache_srv['expire']);
}

function cache_get($name) {
    global $memcache, $cache_srv;

    return $memcache->get($cache_srv['prefix'] . $name);
}

function cache_del($name) {
    global $memcache, $cache_srv;
    return $memcache->delete($cache_srv['prefix'] . $name);
}