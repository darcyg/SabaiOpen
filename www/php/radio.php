<?php
// Sabai Technology - Apache v2 licence
// copyright 2014 Sabai Technology
$UCI_PATH="-c /configs";
// Bring over variables from the WAN page
$proto=trim($_POST['wan_proto']);
$ip=trim($_POST['wan_ip']);
$mask=trim($_POST['wan_mask']);
$gateway=trim($_POST['wan_gateway']);
$mac=trim($_POST['wan_mac']);
$mtu=trim($_POST['wan_mtu']);
$dns=implode(" ", $_POST["dns_servers"]);
$command="sh /www/bin/wan.sh " . $proto;
// Set the Sabai config to reflect latest settings
exec("uci $UCI_PATH set sabai.wan.proto=\"" . $proto . "\"");
exec("uci $UCI_PATH set sabai.wan.ipaddr=\"" . $ip . "\"");
exec("uci $UCI_PATH set sabai.wan.netmask=\"" . $mask . "\"");
exec("uci $UCI_PATH set sabai.wan.gateway=\"" . $gateway . "\"");
exec("uci $UCI_PATH set sabai.wan.mac=\"" . $mac . "\"");
exec("uci $UCI_PATH set sabai.wan.mtu=\"" . $mtu . "\"");
exec("uci $UCI_PATH set sabai.wan.dns=\"" . $dns . "\"");
exec("uci $UCI_PATH commit sabai");
exec($command);

// Send completion message back to UI
echo "res={ sabai: true, msg: 'WAN settings applied' }";

?>
