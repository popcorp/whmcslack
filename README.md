# Slack - WHMCS Integration
A fork of [atech/noti-whmcs](https://github.com/atech/noti-whmcs)

## DISCLAIMER
I have not tested the code, as I don't own WHMCS myself. Feel free to open issues if you find bugs.


This module will notify you each time a client signs up, pays an invoice or opens a support ticket in WHMCS.

## Installation

To install you need to download the files within the repo and upload them to;

```
WHMCS_ROOT/modules/addons/whmcslack
```

Once the files have successfully uploaded navigate to the WHMCS administration area and click (Setup > Addon Modules)

Here you will see a list of modules that have been uploaded, within this list you will see WHMCSlack. Click the 'Activate' link to begin setting up the module.

`WebHook URL` can be obtained through your Slack integrations page, `https://<yourname>.slack.com/services/new/incoming-webhook` and looks like `https://hooks.slack.com/services/T012345AC/B0AT00A0A/...`.