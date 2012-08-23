<?
if(!defined('IS_ADMIN')){die('No Access');} 

//--------------- Load Admin Plugins --------------------------
$pls = load_plugins("admin.php");
  if(is_array($pls)){foreach($pls as $pl){include($pl);}}                    
//--------------------------------------------------

?>
</td></tr></table>
</body>
</html>