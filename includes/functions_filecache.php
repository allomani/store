<?
function cache_init(){
    global $cache_srv,$cache_dir;
$cache_dir = CWD . "/".$cache_srv['filecache_dir'];
 if(!is_writable($cache_dir."/")){
 die("Filecache Dir is not writable");    
 }
}

function cache_set($name,$data){
       global $cache_srv,$cache_dir;   
       return file_put_contents($cache_dir."/".md5($cache_srv['prefix'].$name),serialize($data));
}

function cache_get($name){
      global $cache_srv,$cache_dir; 
      $filename = $cache_dir."/".md5($cache_srv['prefix'].$name);
      
    if(file_exists($filename)){  
        
$c_time = time() - filemtime($filename); 
         
    if($c_time > $cache_srv['expire']){
    return false;    
     }else{   
     return unserialize(file_get_contents($filename)); 
     }
    }else{
        return false;
    }       
}

function cache_del($name){
     global $cache_srv,$cache_dir;   
      @unlink($cache_dir."/".md5($cache_srv['prefix'].$name));
      return true;
}