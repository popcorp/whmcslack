<?php

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

function noti_config() {
    $configarray = array(
    "name" => "Desktop Notifications with Noti",
    "description" => "",
    "version" => "1.0",
    "author" => "aTech Media",
    "language" => "english",
    "fields" => array(
        "key" => array ("FriendlyName" => "Noti API Key", "Type" => "text", "Size" => "50", "Description" => "", "Default" => "", ),
    ));
    return $configarray;
}

function noti_activate() {
  $query = "CREATE TABLE IF NOT EXISTS `tblnoti` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `adminid` int(11) NOT NULL,
    `access_token` varchar(255) NOT NULL,
    `permissions` text NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;";
	$result = mysql_query($query);
}

function noti_deactivate() {
  $query = "DROP TABLE `tblnoti`";
	$result = mysql_query($query);
}

function noti_output($vars) {
  global $customadminpath, $CONFIG;
  
  $access_token = select_query('tblnoti', '', array('adminid' => $_SESSION['adminid']));
 
  if ( $_GET['return'] == '1' && $_SESSION['request_token'] ) {    
    $response = curlCall("http://notiapp.com/api/v1/get_access_token",array('app' => $vars['key'], 'request_token' => $_SESSION['request_token']));
    $result = json_decode($response, true);    
    insert_query("tblnoti", array("adminid" => $_SESSION['adminid'], "access_token" => $result['access_token']) );
    $_SESSION['request_token'] = "";
    curlCall("http://notiapp.com/api/v1/add",array('app' => $vars['key'], 'user' => $result['access_token'], "notification[title]" => "WHMCS is ready to go!", "notification[text]" => "You will now receive WHMCS notifications directly to your desktop", "notification[sound]" => "alert1"));
    header("Location: addonmodules.php?module=noti");
  } elseif($_GET['setup'] == '1' && !mysql_num_rows($access_token)) {
    $response = curlCall("http://notiapp.com/api/v1/request_access",array('app' => $vars['key'], 'redirect_url' => $CONFIG['SystemURL']."/".$customadminpath."/addonmodules.php?module=noti&return=1"));
    $result = json_decode($response, true);        
    if ($result['request_token'] && $result['redirect_url']) {
      $_SESSION['request_token'] = $result['request_token'];
      header("Location: ".$result['redirect_url']);
    } else {
      echo "<div class='errorbox'><strong>Incorrect API Key</strong></br>Incorrect Noti API Key specified.</div>";
    }
  } elseif( $_GET['disable'] == '1' && mysql_num_rows($access_token) ) {
    full_query("DELETE FROM `tblnoti` WHERE `adminid` = '".$_SESSION['adminid']."'");
    echo "<div class='infobox'><strong>Successfully Disabled Noti</strong></br>You have successfully disabled Noti.</div>";
  } elseif( mysql_num_rows($access_token) && $_POST ){
    update_query('tblnoti',array('permissions' => serialize($_POST['notification'])), array('adminid' => $_SESSION['adminid']));
    echo "<div class='infobox'><strong>Updated Notifications</strong></br>You have successfully updated your notification preferences.</div>";    
  }
  
  $access_token = select_query('tblnoti', '', array('adminid' => $_SESSION['adminid']));
  $result = mysql_fetch_array($access_token, MYSQL_ASSOC);
  $permissions = unserialize($result['permissions']);   

  if ( !mysql_num_rows($access_token)) {
    echo "<p><a href='addonmodules.php?module=noti&setup=1'>Setup Noti</a></p>";
  } else {
    echo "<p><a href='addonmodules.php?module=noti&disable=1'>Disable Noti</a></p>";
    
    echo '<form method="POST"><table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
    <tr>
      <td class="fieldlabel" width="200px">Notifications</td>
      <td class="fieldarea">
      <table width="100%">
        <tr>
           <td valign="top">
             <input type="checkbox" name="notification[new_client]" value="1" id="notifications_new_client" '.($permissions['new_client'] == "1" ? "checked" : "").'> <label for="notifications_new_client">New Clients</label><br>
             <input type="checkbox" name="notification[new_invoice]" value="1" id="notifications_new_invoice" '.($permissions['new_invoice'] == "1" ? "checked" : "").'> <label for="notifications_new_invoice">Paid Invoices</label><br>
             <input type="checkbox" name="notification[new_ticket]" value="1" id="notifications_new_ticket" '.($permissions['new_ticket'] == "1" ? "checked" : "").'> <label for="notifications_new_ticket">New Support Ticket</label><br>
           </td>
         </tr>
         
    </table>
  </table>
  
  <p align="center"><input type="submit" value="Save Changes" class="button"></p></form>
  ';
  }
  
  

}