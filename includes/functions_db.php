<?

$queries = 0;
$last_sql = '';

//------------ db escape -------------//
function db_escape($str, $specialchars = true) {
    return db::instance()->escape($str,$specialchars);
}

//----------- Connect ----------
function db_connect($host, $user, $pass, $dbname, $dbcharset = "") {
    return db::instance()->connect($host, $user, $pass, $dbname, $dbcharset = "");
}

//--------- select db ------------
function db_select($db_name, $db_charset = "") {
   return db::instance()->select($db_name, $db_charset = "");
}

//----------- query ------------------
function db_query($sql) {
return db::instance()->query($sql);
}

//---------------- fetch -------------------
function db_fetch($r) {
return db::instance()->fetch($r);
}

// ------------------------ num -----------------------
function db_num($qr) {
  return db::instance()->num($qr);
}

//------------------ Query + fetch ----------------------
function db_qr_fetch($sql) {
 return db::instance()->qr_fetch($sql);
}

//--------------- Fetch First ---------------
function db_fetch_first($r) {
return db::instance()->fetch_first($r);
}

// ------------------- query + num --------------------
function db_qr_num($sql) {
return db::instance()->qr_num($sql);
}

//------------- query and return array ----------------
function db_fetch_all($r) {
return db::instance()->fetch_all($r);
}

/*
 * return last inserted ID
 * 
 */

function db_inserted_id() {
 return db::instance()->inserted_id();
}

function db_server_info() {
   return db::instance()->server_info();
}

function db_client_info() {
   return db::instance()->client_info();
}