<?php

if (!defined('WHMCS'))
    die('This file cannot be accessed directly');

function whmcslack_config()
{
    $configarray = [
        'name' => 'WHMCSlack',
        'description' => 'Receive a Slack notification for major event',
        'version' => '1.1',
        'author' => 'PunKeel',
        'language' => 'english',
        'fields' => [
            'webhook' => ['FriendlyName' => 'Slack Webhook', 'Type' => 'text', 'Size' => '80', 'Description' => '', 'Default' => 'https://hooks.slack.com/services/T012345AC/B0AT00A0A/...'],
            'new_client' => ['FriendlyName' => 'Alert New Client', 'Type' => 'yesno', 'Size' => '25',
                'Description' => ''],
            'new_invoice' => ['FriendlyName' => 'Alert Invoice Paid', 'Type' => 'yesno', 'Size' => '25',
                'Description' => ''],
            'new_ticket' => ['FriendlyName' => 'Alert New Ticket', 'Type' => 'yesno', 'Size' => '25',
                'Description' => ''],
            'new_update' => ['FriendlyName' => 'Alert Ticket Update', 'Type' => 'yesno', 'Size' => '25',
                'Description' => ''],
            'new_update_admin' => ['FriendlyName' => 'Alert Ticket Admin Reply', 'Type' => 'yesno', 'Size' => '25',
                'Description' => ''],
        ]
    ];
    return $configarray;
}

function whmcslack_activate()
{
    return array('status' => 'info', 'description' => 'WHMCSlack is now enabled!');
}

function whmcslack_deactivate()
{
    return array('status' => 'info', 'description' => 'WHMCSlack is now enabled!');
}