<?php
$prefix = 'MODULE_PAYMENT_HPMK_';

define($prefix.'TEXT_TITLE', 'MangirKart');
define($prefix.'TEXT_DESC', 'MangirKart over Heidelberger Payment GmbH');

define($prefix.'SECURITY_SENDER_TITLE', 'Sender ID');
define($prefix.'SECURITY_SENDER_DESC', 'Your Heidelpay Sender ID');

define($prefix.'USER_LOGIN_TITLE', 'User Login');
define($prefix.'USER_LOGIN_DESC', 'Your Heidelpay User Login');

define($prefix.'USER_PWD_TITLE', 'User Password');
define($prefix.'USER_PWD_DESC', 'Your Heidelpay User Password');

define($prefix.'TRANSACTION_CHANNEL_TITLE', 'Channel ID');
define($prefix.'TRANSACTION_CHANNEL_DESC', 'Your Heidelpay Channel ID');

define($prefix.'TRANSACTION_MODE_TITLE', 'Transaction Mode');
define($prefix.'TRANSACTION_MODE_DESC', 'Please choose your transaction mode.');

define($prefix.'MIN_AMOUNT_TITLE', 'Minimum Amount');
define($prefix.'MIN_AMOUNT_DESC', 'Please choose the minimum amount <br>(in EURO-CENT e.g. 5 EUR = 500 Cent).');

define($prefix.'MAX_AMOUNT_TITLE', 'Maximum Amount');
define($prefix.'MAX_AMOUNT_DESC', 'Please choose the maximum amount <br>(in EURO-CENT e.g. 5 EUR = 500 Cent).');

define($prefix.'MODULE_MODE_TITLE', 'Module Mode');
define($prefix.'MODULE_MODE_DESC', 'DIRECT: Paymentinformations will be entered on payment selection with REGISTER function (plus Registerfee). <br>AFTER: Paymentinformations will be entered after process with DEBIT function.');

define($prefix.'PAY_MODE_TITLE', 'Payment Mode');
define($prefix.'PAY_MODE_DESC', 'Select between Debit (DB) and Preauthorisation (PA).');

define($prefix.'TEST_ACCOUNT_TITLE', 'Test Account');
define($prefix.'TEST_ACCOUNT_DESC', 'If Transaction Mode is not LIVE, the following Accounts (EMail) can test the payment. (Comma separated)');

define($prefix.'PROCESSED_STATUS_ID_TITLE', 'Orderstatus - Success');
define($prefix.'PROCESSED_STATUS_ID_DESC', 'Order Status which will be set in case of successfully payment');

define($prefix.'PENDING_STATUS_ID_TITLE', 'Bestellstatus - Waiting');
define($prefix.'PENDING_STATUS_ID_DESC', 'Order Status which will be set when the customer is on foreign system');

define($prefix.'CANCELED_STATUS_ID_TITLE', 'Orderstatus - Cancel');
define($prefix.'CANCELED_STATUS_ID_DESC', 'Order Status which will be set in case of cancel payment');

define($prefix.'NEWORDER_STATUS_ID_TITLE', 'Orderstatus - New Order');
define($prefix.'NEWORDER_STATUS_ID_DESC', 'Order Status which will be set in case of beginning payment');

define($prefix.'STATUS_TITLE', 'Activate Module');
define($prefix.'STATUS_DESC', 'Do you want to activate the module?');

define($prefix.'SORT_ORDER_TITLE', 'Sort Order');
define($prefix.'SORT_ORDER_DESC', 'Sort order for display. Lowest will be shown first.');

define($prefix.'ZONE_TITLE', 'Paymentzone');
define($prefix.'ZONE_DESC', 'If a zone is selected, only enable this payment method for that zone.');

define($prefix.'ALLOWED_TITLE', 'Allowed Zones');
define($prefix.'ALLOWED_DESC', 'Please enter the zones <b>separately</b> which should be allowed to use this modul (e. g. AT,DE (leave empty if you want to allow all zones))');

define($prefix.'DEBUG_TITLE', 'Debug Mode');
define($prefix.'DEBUG_DESC', 'Please activate only if heidelpay told this to you. Otherwise the checkout will not work in your shop correctly.');

define($prefix.'TEXT_INFO', '');
define($prefix.'DEBUGTEXT', 'The payment is temporary not available. Please use another one or try again later.');

define($prefix.'FRONTEND_BUTTON_CONTINUE', 'Continue');
define($prefix.'FRONTEND_BUTTON_CANCEL', 'Previous Page');
