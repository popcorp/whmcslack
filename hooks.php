<?php

function whmcslack_getconfig()
{
    $q = select_query('tbladdonmodules', 'setting, value', array('module' => 'whmcslack'));
    if (mysql_num_rows($q) == 0)
        return null;
    $r = [];
    while ($d = mysql_fetch_array($q)) {
        $r[$d['setting']] = $d['value'];
    }
    return $r;
}

function whmcslack_call($webhookUrl, $data)
{
    $payload = ['payload' => json_encode($data)];
    $response = curlCall($webhookUrl, $payload);
}

function whmcslack_ClientAdd($vars)
{
    global $customadminpath, $CONFIG;
    $conf = whmcslack_getconfig();
    if ($conf == null || empty($conf['webhook']))
        return;
    if (!$conf['new_client'])
        return;
    $url = $CONFIG['SystemURL'] . '/' . $customadminpath . '/clientssummary.php?userid=' . $vars['userid'];
    $data = [];
    $data['text'] = 'A new client has signed up! ' .
        '<' . $url . '|Click here> for details!';
    whmcslack_call($conf['webhook'], $data);
}

function whmcslack_InvoicePaid($vars)
{
    global $customadminpath, $CONFIG;
    $conf = whmcslack_getconfig();
    if ($conf == null || empty($conf['webhook']))
        return;
    if (!$conf['new_invoice'])
        return;
    $url = $CONFIG['SystemURL'] . '/' . $customadminpath . '/invoices.php?action=edit&id=' . $vars['invoiceid'];
    $data = [];
    $data['text'] = 'Invoice ' . $vars['invoiceid'] . ' has just been paid! ' .
        '<' . $url . '|Click here> for details!';
    whmcslack_call($conf['webhook'], $data);
}

function whmcslack_TicketOpen($vars)
{
    whmcslack_TicketChange('create', $vars);
}

function whmcslack_TicketUserReply($vars)
{
    whmcslack_TicketChange('user_reply', $vars);
}

function whmcslack_TicketAdminReply($vars)
{
    whmcslack_TicketChange('admin_reply', $vars);
}

function whmcslack_GetUserDisplayName($userid)
{
    $result = select_query('tblclients', 'firstname, lastname, email', ['id' => $userid]);
    if (mysql_num_rows($result) == 0)
        return '?';
    $data = mysql_fetch_array($result);
    $firstname = $data['firstname'];
    $lastname = $data['lastname'];
    $email = $data['email'];
    return $firstname . ' ' . $lastname . ' (' . $email . ')';
}

function whmcslack_TicketChange($status, $vars)
{
    global $customadminpath, $CONFIG;
    $conf = whmcslack_getconfig();
    if ($conf == null || empty($conf['webhook']))
        return;
    $url = $CONFIG['SystemURL'] . '/' . $customadminpath . '/supporttickets.php?action=viewticket&id=' . $vars['ticketid'];
    $data = [];
    $attachement = [];
    $attachement['title'] = 'Ticket #' . $vars['ticketid'] . ': ' . $vars['subject'];
    $attachement['title_link'] = $url;
    $attachement['text'] = substr($vars['message'], 0, 50) . '…';
    $attachement['color'] = "#7CD197";
    $attachement['fields'] = [];
    $attachement['fields'][] = ['title' => 'Priority', 'value' => $vars['priority'], 'short' => false];
    $attachement['fields'][] = ['title' => 'Department', 'value' => $vars['deptname'], 'short' => false];

    switch ($status) {
        case 'user_reply':
            if (!$conf['new_update'])
                return;
            $attachement['pretext'] = 'New ticket reply from ' . whmcslack_GetUserDisplayName($vars['userid']);
            break;
        case 'admin_reply':
            if (!$conf['new_update_admin'])
                return;
            $attachement['pretext'] = 'New ticket reply from ' . $vars['admin'];
            break;
        case 'create':
            if (!$conf['new_ticket'])
                return;
            $attachement['pretext'] = 'New ticket from ' . whmcslack_GetUserDisplayName($vars['userid']);
            break;
        default:
            return;
    }

    $data['attachments'] = [$attachement];
    whmcslack_call($conf['webhook'], $data);
}


add_hook("ClientAdd", 1, "whmcslack_ClientAdd");
add_hook("InvoicePaid", 1, "whmcslack_InvoicePaid");
add_hook("TicketOpen", 1, "whmcslack_TicketOpen");
add_hook("TicketUserReply", 1, "whmcslack_TicketUserReply");
add_hook("TicketAdminReply", 1, "whmcslack_TicketAdminReply");
