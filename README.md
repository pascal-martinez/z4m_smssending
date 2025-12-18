# ZnetDK 4 Mobile module: SMS Sending (z4m_smssending)
![Screenshot of the SMS sent view provided by the ZnetDK 4 Mobile 'z4m_smssending' module](https://mobile.znetdk.fr/applications/default/public/images/modules/z4m_smssending/screenshot3.png)

The **z4m_smssending** module extends the capabilities of the [ZnetDK 4 Mobile](/../../../znetdk4mobile) starter application by adding SMS sending functionality.

## LICENCE ##
This module is published under the version 3 of GPL General Public Licence.

## FEATURES ##
The **z4m_smssending** module allows you:
- To configure SMS sending (sender, token, alert threshold...) through the [`z4m_sms_sending_config`](mod/view/z4m_sms_sending_config.php) view.
- To send an SMS through a form for simulation purpose,
- To send a transactional SMS via module API,
- To display the **history of sent SMS** through the [`z4m_sms_sending_history`](mod/view/z4m_sms_sending_history.php) view, with the ability to filter and purge content by period and sending status.
- To monitor remaining SMS credits via the [`homemenu_credit_balance.php`](mod/view/fragment/homemenu_credit_balance.php) monitoring box displayed on the home page.
- To alert users on the home page via the [`homemenu_credit_alert.php`](mod/view/fragment/homemenu_credit_alert.php) script when the configured remaining SMS credit threshold has been reached.

## REQUIREMENTS ##
- An **API token** obtained after opening a user account on the [SMSFactor website](https://www.smsfactor.com/),
- [ZnetDK 4 Mobile](/../../../znetdk4mobile) version 2.9 or higher,
- A **MySQL** database [is configured](https://mobile.znetdk.fr/getting-started#z4m-gs-connect-config) to store the application data,
- **PHP version 7.4** or higher,
- Authentication is enabled
([`CFG_AUTHENT_REQUIRED`](https://mobile.znetdk.fr/settings#z4m-settings-auth-required)
is `TRUE` in the App's
[`config.php`](/../../../znetdk4mobile/blob/master/applications/default/app/config.php)).

## INSTALLATION ##
1. Add a new subdirectory named `z4m_smssending` within the
[`./engine/modules/`](/../../../znetdk4mobile/tree/master/engine/modules/) subdirectory of your
ZnetDK 4 Mobile starter App,
2. Copy module's code in the new `./engine/modules/z4m_smssending/` subdirectory,
or from your IDE, pull the code from this module's GitHub repository,
3. Edit the App's [`menu.php`](/../../../znetdk4mobile/blob/master/applications/default/app/menu.php)
located in the [`./applications/default/app/`](/../../../znetdk4mobile/tree/master/applications/default/app/)
subfolder and include the [`menu.inc`](mod/menu.inc) script to add menu item definition for the
`z4m_sms_sending_config` and `z4m_sms_sending_history` views.
```php
require ZNETDK_MOD_ROOT . '/z4m_smssending/mod/menu.inc';
```
4. Go to the **SMS sending config.** menu and configure SMS sending. 

## USERS GRANTED TO MODULE FEATURES ##
Once the **SMS sending** menu item is added to the application, you can restrict 
its access via a [user profile](https://mobile.znetdk.fr/settings#z4m-settings-user-rights).  
For example:
1. Create a user profile named `Admin` from the **Authorizations | Profiles** menu,
2. Select for this new profile, the **SMS sent** and **SMS sending config.** menu items,
3. Finally for each allowed user, add them the `Admin` profile from the **Authorizations | Users** menu. 

## CONFIGURING SMS SENDING ##
![Screenshot of the SMS Sending config. view provided by the ZnetDK 4 Mobile 'z4m_smssending' module](https://mobile.znetdk.fr/applications/default/public/images/modules/z4m_smssending/screenshot2.png)

See below informations to enter from the **SMS Sending config.** view to configure SMS sending.
- **Sender name** (mandatory): sender name displayed on the recipient's smartphone when they receive the SMS (between 3 and 11 alphanumeric characters, no space allowed).
- **Authentication API** (mandatory): API key generated from the [SMSFactor](https://www.smsfactor.com/) account that is needed to authenticate each SMS sending.
- **Alert threshold credits almost exhausted** (optional): number of remaining SMS from which an alert is displayed on the home page.
- **SMS sending enabled** (check box): when this option is checked, SMS are sent.
- **History of sent SMS enabled** (check box): when this option is checked, sent SMS are recorded by the Application and displayed from the **SMS sent** view.

## TESTING SMS SENDING ##
![Screenshot of the Send a simulation SMS... form provided by the ZnetDK 4 Mobile 'z4m_smssending' module](https://mobile.znetdk.fr/applications/default/public/images/modules/z4m_smssending/screenshot1.png)

Once SMS sending is configured, a test SMS can be sent from the form shown when clicking the **Send a simulation SMS...** button from the **SMS Sending config.** view.  
The SMS sending form data is:
- **Is a simulation** (check box): checked by default, means the SMS is not sent to the recipient (for testing purpose).
- **Text of the message to send** (mandatory): text of the message to send.
- **Recipient's phone number** (mandatory): the recipient's phone number.
- **Recipient's name** (optional): recipient's name displayed in the **SMS sent** view.
- **Business reference** (optional): a business reference displayed in the **SMS sent** view.
Click the **Send the SMS** button to send the SMS.

## SENDING A TRANSACTIONAL SMS VIA MODULE API ##
A transactional SMS is sent from your Application via the [`SMSToSend`](mod/SMSToSend.php) PHP class.  
Here is below an example of PHP code to send a transactional SMS:
```php
$pushType = 'alert'; // 'alert' or 'marketing'.
$isSimulation = TRUE; // This is a simulation.
$sms = new SMSToSend($pushType, $isSimulation);
try {
    $sms->send('This is the text of my message', '33600000001');
} catch (\Exception $ex) {
    $response->setFailedMessage(NULL, MOD_Z4M_SMSSENDING_FAILED . $ex->getMessage());
}
```
See PHP doc of the [`SMSToSend`](mod/SMSToSend.php) PHP class to get more information.

## DISPLAYING THE SMS CREDIT BALANCE MONITORING BOX ##
![Screenshot of the SMS credit balance monitoring box provided by the ZnetDK 4 Mobile 'z4m_smssending' module](https://mobile.znetdk.fr/applications/default/public/images/modules/z4m_smssending/screenshot4.png)

To display on the home view the SMS credit balance, just include the [`homemenu_credit_balance.php`](mod/view/fragment/homemenu_credit_balance.php) script as illustrated below.
```php
<?php
require 'z4m_smssending/mod/view/fragment/homemenu_credit_balance.php';
?>
```
To integrate this monitoring box to the [`z4m_homemenu`](/../../../z4m_homemenu) module, edit the App's [`config.php`](/../../../znetdk4mobile/blob/master/applications/default/app/config.php) file and configure the `MOD_Z4M_HOMEMENU_MONITORING_BOXES` constant as shown below.
```php
define('MOD_Z4M_HOMEMENU_MONITORING_BOXES', [
    ['boxPath' => 'z4m_smssending/mod/view/fragment/homemenu_credit_balance.php']
]);
```

## DISPLAYING AN ALERT WHEN SMS CREDITS ARE ALMOST EXHAUSTED ##
![Screenshot of the alert SMS credit almost exhausted displayed by the ZnetDK 4 Mobile 'z4m_smssending' module](https://mobile.znetdk.fr/applications/default/public/images/modules/z4m_smssending/screenshot5.png)

To display an alert on the home view when SMS credits are almost exhausted (see configured value for the **Alert threshold credits almost exhausted** entry field), just include the [`homemenu_credit_alert.php`](mod/view/fragment/homemenu_credit_alert.php) script in your home view as illustrated below.
```php
<?php
require 'z4m_smssending/mod/view/fragment/homemenu_credit_alert.php';
?>
```
To integrate this alert to the [`z4m_homemenu`](/../../../z4m_homemenu) module:
1. add a new home view (for example `my_home_view.php`) in the [`applications/default/app/view/`](/../../../znetdk4mobile/blob/master/applications/default/app/view) directory with the following code:
```php
<?php
require 'z4m_smssending/mod/view/fragment/homemenu_credit_alert.php';
require 'z4m_homemenu/mod/view/z4m_homemenu.php';
```
2. Declare your new home view in the App's [`menu.php`](/../../../znetdk4mobile/blob/master/applications/default/app/menu.php) script.
3. Edit the App's [`config.php`](/../../../znetdk4mobile/blob/master/applications/default/app/config.php) script and declare your new home view via the constant as shown below:
```php
define('MOD_Z4M_HOMEMENU_EXCLUDED_VIEW', 'my_home_view');
```

## TRANSLATIONS ##
This module is translated in **French**, **English** and **Spanish** languages.  
To translate this module in another language or change the standard translations:
1. Copy in the clipboard the PHP constants declared within the 
[`locale_en.php`](mod/lang/locale_en.php) script of the module,
2. Paste them from the clipboard within the
[`locale.php`](/../../../znetdk4mobile/blob/master/applications/default/app/lang/locale.php) script of your application,   
3. Finally, translate each text associated with these PHP constants into your own
language.

## INSTALLATION ISSUES ##
The `z4m_sms_sending_config` and `z4m_sms_sending_history` SQL tables
are created automatically by the module when one of the module views is displayed
for the first time.  
If the MySQL user declared through the
[`CFG_SQL_APPL_USR`](https://mobile.znetdk.fr/settings#z4m-settings-db-user)
PHP constant does not have `CREATE` privilege, the module can't create the
required SQL tables.   
In this case, you can create the module's SQL tables by importing in MySQL or
phpMyAdmin the script [`z4m_smssending.sql`](mod/sql/z4m_emailsending.sql)
provided by the module.

## CHANGE LOG
See [CHANGELOG.md](CHANGELOG.md) file.

## CONTRIBUTING
Your contribution to the **ZnetDK 4 Mobile** project is welcome. Please refer to the [CONTRIBUTING.md](https://github.com/pascal-martinez/znetdk4mobile/blob/master/CONTRIBUTING.md) file.
