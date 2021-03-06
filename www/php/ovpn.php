<?php
header('Content-Type: application/javascript');
// Sabai Technology - Apache v2 licence
// copyright 2014 Sabai Technology
$UCI_PATH="-c /configs";

// Bring over variables from the openVPN

$act=trim($_POST['act']);
$VPNname=trim($_POST['VPNname']);
$VPNpassword=trim($_POST['VPNpassword']);
$conf=trim($_POST['conf']);

exec("uci $UCI_PATH delete sabai.ovpn");
exec("uci $UCI_PATH add sabai.ovpn=interface");
exec("uci $UCI_PATH set sabai.ovpn.VPNname=\"" . $VPNname . "\"");
exec("uci $UCI_PATH set sabai.ovpn.VPNpassword=\"" . $VPNpassword . "\"");
exec("uci $UCI_PATH set sabai.ovpn.conf=\"" . $conf . "\"");
exec("uci $UCI_PATH commit sabai");

function newfile(){
 $file = ( array_key_exists('file',$_FILES) && array_key_exists('name',$_FILES['file']) ? $_FILES['file']['name'] : "" );
  exec("uci set openvpn.sabai.filename=$file");
  file_put_contents('/etc/sabai/openvpn/ovpn.filename', $file);
 $contents = ( array_key_exists('file',$_FILES) && array_key_exists('tmp_name',$_FILES['file']) ? file_get_contents($_FILES['file']['tmp_name']) : "" );
    $filelocation='/etc/sabai/openvpn/ovpn.current';
   $contents = preg_replace(array("/^script-security.*/m","/^route-up .*/m","/^up .*/m","/^down .*/m"),"",$contents);
  file_put_contents($filelocation, $contents);
 $type = strrchr($file,".");
 file_put_contents('/etc/sabai/openvpn/auth-pass', '');
  exec("uci set openvpn.sabai.filetype=$type");
  exec("uci commit");

 switch($type){
  case ".sh":
   $contents = stristr(stristr($contents,"nvram set ovpn_cfg='"),"'");
     file_put_contents("/tmp/contents1", $contents);
   $contents = trim( substr( $contents, 0, stripos($contents,"nvram set ovpn") ), "\n'");
   $contents = preg_replace(array("/^script-security.*/m","/^route-up .*/m","/^up .*/m","/^down .*/m"),"",$contents);
  case ".conf":
  case ".ovpn":
   file_put_contents($filelocation,$contents);
   file_put_contents("/etc/sabai/openvpn/ovpn", "{ file: '$file', res: { sabai: true, msg: 'OpenVPN $type file loaded.' } }");
  break;
  default:{
   file_put_contents("/etc/sabai/usr/ovpn","{ file: '', res: { sabai: false, msg: 'OpenVPN file failed.' } }");
  }
 }
 header("Location: /?panel=vpn&section=openvpnclient");
}

function savefile(){
$name=$_REQUEST['VPNname'];
$password=$_REQUEST['VPNpassword'];
 if(array_key_exists('conf',$_REQUEST)){
  file_put_contents("/etc/sabai/openvpn/ovpn.current",$_REQUEST['conf']);
  file_put_contents("/etc/sabai/openvpn/auth-pass",$name ."\n");
  file_put_contents("/etc/sabai/openvpn/auth-pass",$password, FILE_APPEND);
  exec("sed -ir 's\auth-user-pass.*$\auth-user-pass /etc/sabai/openvpn/auth-pass\g' /etc/sabai/usr/ovpn.current");
  echo "res={ sabai: true, msg: 'OpenVPN configuration saved.', reload: true };";
 }else{
  echo "res={ sabai: false, msg: 'Invalid configuration.' };";
 }
}


$act=$_REQUEST['act'];
switch ($act){
  case "start":
    if(!file_exists("/etc/sabai/openvpn/ovpn.current")){ echo "res={ sabai: false, msg: 'OpenVPN file missing.' };"; break; }
  case "stop":
    $line=exec("sh /www/bin/ovpn.sh $act 2>&1",$out);
    $i=count($out)-1;
    while( substr($line,0,3)!="res" && $i>=0 ){ $line=$out[$i--]; }
    file_put_contents("/etc/sabai/stat/php.ovpn.log", implode("\n",$out) );
    echo $line;
  break;
  case "clear":
    exec("sh /www/bin/ovpn.sh clear 2>&1");
    echo "res={ sabai: true, msg: 'OpenVPN file removed.', reload: true };";
  break;
  case "newfile": newfile(); break;
  case "save": savefile(); break;
  case "log": echo (file_exists("/etc/sabai/openvpn/ovpn.log") ? str_replace(array("\"","\r"),array("'","\n"),file_get_contents("/etc/sabai/openvpn/ovpn.log")) : "No log."); break;
}

?>
