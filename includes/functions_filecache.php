<?
function cache_init(){
    global $config,$cache_dir;
$cache_dir = CWD . "/".$config['cache']['filecache_dir'];
 if(!is_writable($cache_dir."/")){
 die("Filecache Dir is not writable");    
 }
}

function cache_set($name,$data){
       global $config,$cache_dir;   
       return file_put_contents($cache_dir."/".md5($config['cache']['prefix'].$name),serialize($data));
}

function cache_get($name){
      global $config,$cache_dir; 
      $filename = $cache_dir."/".md5($config['cache']['prefix'].$name);
      
    if(file_exists($filename)){  
        
$c_time = time() - filemtime($filename); 
         
    if($c_time > $config['cache']['expire']){
    return false;    
     }else{   
     return unserialize(file_get_contents($filename)); 
     }
    }else{
        return false;
    }       
}

function cache_del($name){
     global $config,$cache_dir;   
      @unlink($cache_dir."/".md5($config['cache']['prefix'].$name));
      return true;
}