<?php 
  
$json = json_decode($_POST['pftable'], true);
$file = '/tmp/table1';  
unset ($json[0]);
$aaData=json_encode($json);
file_put_contents($file, $aaData);
$command="sh /www/bin/pftable.sh";
exec($command);
 
// Send completion message back to UI
$res = array('sabai' => true, 'rMessage' => 'Port Forwarding in development');
echo json_encode($res);

?>  
