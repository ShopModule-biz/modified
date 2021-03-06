<?php
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

define('MODULE_HPPP_CHECK_TEXT_DESCRIPTION', 'PrÜfen Sie die Zahlungseingänge über Heidelpay Vorkasse');
define('MODULE_HPPP_CHECK_TEXT_TITLE', 'Heidelpay Vorkasse Prüfung');
define('MODULE_HPPP_CHECK_STATUS_DESC', 'Modulstatus');
define('MODULE_HPPP_CHECK_STATUS_TITLE', 'Status');
define('HPPP_IMAGE_EXPORT', 'Dr&uuml;cken Sie Ok um die offenen Vorkassezahlungen mit Heidelpay abzugleichen!');
define('HPPP_IMAGE_EXPORT_TYPE', '<b>Abgleich der Vorkassezahlungen:</b>');

if (file_exists(DIR_WS_CLASSES . 'class.heidelpay.php')) {
    include_once(DIR_WS_CLASSES . 'class.heidelpay.php');
} else {
    require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.heidelpay.php');
}

class hppp_check
{
    public $code;
    public $title;
    public $description;
    public $enabled;
    public $order;

    public function __construct()
    {
        global $order;
        $this->order = $order;

        $this->code = 'hppp_check';
        $this->title = MODULE_HPPP_CHECK_TEXT_TITLE;
        $this->description = MODULE_HPPP_CHECK_TEXT_DESCRIPTION;
        $this->sort_order = MODULE_HPPP_CHECK_SORT_ORDER;
        $this->enabled = ((MODULE_HPPP_CHECK_STATUS == 'True') ? true : false);
        $this->hp = new heidelpay();
    }

    public function process()
    {
        $payment_class = 'hppp';
        $paystatus = MODULE_PAYMENT_HPPP_PROCESSED_STATUS_ID;
        $finishstatus = MODULE_PAYMENT_HPPP_FINISHED_STATUS_ID;
        $payName = $this->hp->getOrderStatusName($paystatus);
        $finishName = $this->hp->getOrderStatusName($finishstatus);


        if (!empty($_POST['finalizeOrders'])) {
            $dat = $_POST['book'];
            foreach ($_POST['bookIds'] as $k => $v) {
                $orderId = $dat[$k]['orderId'];
                $shortId = $dat[$k]['shortId'];
                $uniqueId = $dat[$k]['uniqueId'];
                $status = $dat[$k]['status'];
                $amount = $dat[$k]['amount'];
                $currency = $dat[$k]['currency'];
                $this->finalizeOrder($orderId, $shortId, $uniqueId, $status, $amount, $currency);
            }
        }

        $ordersDate = $this->hp->getOpenOrdersDate($payment_class, $paystatus);
        // Wenn kein Datum, dann von Heute
        if (empty($ordersDate['min'])) {
            $ordersDate['min'] = date('Y-m-d');
        }
        if (empty($ordersDate['max'])) {
            $ordersDate['max'] = date('Y-m-d');
        }
        // Zeitraum auf max. 34 Tage begrenzen
        $treeMonth = 34 * 24 * 60 * 60;
        if (strtotime($ordersDate['max']) - strtotime($ordersDate['min']) > $treeMonth) {
            $ordersDate['min'] = date('Y-m-d', strtotime($ordersDate['max']) - $treeMonth);
        }

        @xtc_set_time_limit(0);
        $out = '<center><div id="hpBox"><div style="background-color: #888; position:fixed; display:block; margin:0;'
            . ' padding:0; top:0; left:0; opacity: 0.9; -moz-opacity: 0.9; -khtml-opacity: 0.9;'
            . ' filter:alpha(opacity=90); z-index: 1000; width: 100%; height: 100%;"></div>';
        $out .= '<div style="z-index: 1001; position: absolute; width: 900px; top: 50%; left: 50%;'
            . ' margin-top: -325px; margin-left: -450px; background-color: #fff;">';

        $opts = array();
        $xml = $this->hp->getQueryXML($ordersDate['min'], $ordersDate['max'], array('RC'), $opts, array('PP'));

        $crlf = "\r\n";
        $res = $this->hp->doRequest(array(), $xml);
        if (!empty($res)) {
            $xmlObject = new SimpleXMLElement($res);
            if (count($xmlObject->Result->Transaction) > 0) {
                $out .= '</form><form method="post"'
                .' action="module_export.php?set=&action=edit&dowork=1&module=hppp_check">'
                    . $crlf;
                $out .= '<table style="font-size: 12px" width="98%">' . $crlf;
                $out .= '<tr>' . $crlf;
                $out .= '<td>&nbsp;</td>' . $crlf;
                $out .= '<td>BestellNr.</td>' . $crlf;
                $out .= '<td>Kundenname</td>' . $crlf;
                $out .= '<td>Bestelldatum.</td>' . $crlf;
                $out .= '<td>Bezahldatum.</td>' . $crlf;
                $out .= '<td>ShortId</td>' . $crlf;
                $out .= '<td align="right">Bestellwert</td>' . $crlf;
                $out .= '<td align="right">Zahlungseingang</td>' . $crlf;
                $out .= '</tr>' . $crlf;
                $i = 0;

                foreach ($xmlObject->Result->Transaction as $k => $v) {
                    if ($v->Processing->Result != 'ACK') {
                        continue;
                    } // Nur erfolgreiche Zahlungen

                    $uniqueId = (string)$v->Identification->ReferenceID;
                    $order = $this->hp->getOpenOrderByUniqueId($uniqueId, $payment_class);

                    if (empty($order)) {
                        continue;
                    } // Wenn keine Bestellung gefunden
                    if ($order['orders_status'] == $finishstatus) {
                        continue;
                    } // Abgeschlossene Bestellungen ausblenden.
                    if ($this->hp->checkOrderStatusHistory($order['orders_id'], $v->Identification->ShortID)) {
                        continue;
                    } // Schon verbuchte Zahlungen ausblenden

                    $i++;

                    $color = 'd00';
                    $checked = '';
                    $nochecked = 'checked';
                    if ($order['value'] == (string)$v->Payment->Clearing->Amount) {
                        $color = '0d0';
                        $checked = 'checked';
                        $nochecked = '';
                    }

                    $bgcol = $i % 2 == 0 ? 'fff' : 'eee';
                    $out .= '<tr style="background-color: #' . $bgcol . '">' . $crlf;
                    $out .= '<input type="hidden" name="book[' . $i . '][orderId]" value="'
                        . $order['orders_id'] . '">' . $crlf;
                    $out .= '<input type="hidden" name="book[' . $i . '][shortId]" value="'
                        . $v->Identification->ShortID . '">' . $crlf;
                    $out .= '<input type="hidden" name="book[' . $i . '][uniqueId]" value="' . $uniqueId . '">' . $crlf;
                    $out .= '<input type="hidden" name="book[' . $i . '][amount]" value="'
                        . (string)$v->Payment->Clearing->Amount . '">' . $crlf;
                    $out .= '<input type="hidden" name="book[' . $i . '][currency]" value="'
                        . (string)$v->Payment->Clearing->Currency . '">' . $crlf;
                    $out .= '<td><input type="checkbox" name="bookIds[' . $i . ']" value="'
                        . $order['orders_id'] . '" ' . $checked . '></td>' . $crlf;
                    $out .= '<td><a href="orders.php?oID=' . $order['orders_id'] . '&action=edit" target="_blank">'
                        . $order['orders_id'] . '</a></td>' . $crlf;
                    $out .= '<td><a href="customers.php?page=1&cID='
                        . $order['customers_id'] . '&action=edit" target="_blank">'
                        . $order['customers_name'] . '</a></td>' . $crlf;
                    $out .= '<td>' . $order['date_purchased'] . '</td>' . $crlf;
                    $out .= '<td>' . (string)$v->Payment->Clearing->FxDate . '</td>' . $crlf;
                    $out .= '<td>' . $v->Identification->ShortID . '</td>' . $crlf;
                    $out .= '<td align="right">' . $order['text'] . '</td>' . $crlf;
                    $out .= '<td align="right" style="color: #'
                        . $color . '">' . (string)$v->Payment->Clearing->Amount . ' '
                        . (string)$v->Payment->Clearing->Currency . '</td>' . $crlf;
                    $out .= '<td>' . $crlf;
                    $out .= '<input type="radio" name="book[' . $i . '][status]" value="' . $finishstatus . '" '
                        . $checked . '> ' . $finishName . '<br>' . $crlf;
                    $out .= '<input type="radio" name="book[' . $i . '][status]" value="' . $paystatus . '" '
                        . $nochecked . '> ' . $payName . $crlf;
                    $out .= '</td>' . $crlf;
                    $out .= '</tr>' . $crlf;
                }
                $out .= '</table>' . $crlf;
                if ($i == 0) {
                    $out .= '<br><br>Es liegen keine weiteren Zahlungseing�nge vor.<br><br>' . $crlf;
                } else {
                    $out .= '<input type="submit" name="finalizeOrders" value="Zahlungseing�nge verbuchen">' . $crlf;
                }
                $out .= '</form>' . $crlf;
            } else {
                $out .= '<div class="messageStackWarning">';
                $out .= 'Es wurden keine Transaktionen gefunden.';
                $out .= '</div>';
            }
        }


        $out .= '<br>';
        $out .= '<a href="" onClick="document.getElementById(\'hpBox\').style.display=\'none\'; return false;">'
            .'close</a></div></div></center>';

        return $out;
    }

    public function finalizeOrder($orderId, $shortId, $uniqueId, $status, $amount, $currency)
    {
        $payment_class = 'hppp';
        $comment = 'ShortID: ' . $shortId . ' ' . $amount . ' ' . $currency;
        $this->hp->saveIds($uniqueId, $orderId, $payment_class, $shortId);
        $this->hp->addHistoryComment($orderId, $comment, $status);
        $this->hp->setOrderStatus($orderId, $status);
    }

    public function display()
    {
        $out = array(
            'text' => HPPP_IMAGE_EXPORT_TYPE . '<br>' .
                HPPP_IMAGE_EXPORT . '<br>'
                . xtc_button_link(
                    BUTTON_REVIEW_APPROVE,
                    xtc_href_link(
                        FILENAME_MODULE_EXPORT, 'set=' . $_GET['set']
                        . '&action=edit&dowork=1&module=' . $this->code
                    )
                )
                . xtc_button_link(
                    BUTTON_CANCEL,
                    xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=' . $this->code)
                )
        );
        if ($_GET['dowork'] == 1) {
            $out['text'] .= $this->process();
        }
        return $out;
    }

    public function check()
    {
        if (!isset($this->_check)) {
            $check_query = xtc_db_query(
                "select configuration_value from " . TABLE_CONFIGURATION
                . " where configuration_key = 'MODULE_HPPP_CHECK_STATUS'"
            );
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    }

    public function install()
    {
        xtc_db_query(
            "insert into " . TABLE_CONFIGURATION
            . " (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added)"
            . " values ('MODULE_HPPP_CHECK_STATUS', 'True',  '6', '1',"
            . " 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())"
        );
    }

    public function remove()
    {
        xtc_db_query(
            "delete from " . TABLE_CONFIGURATION
            . " where configuration_key in ('" . implode("', '", $this->keys()) . "')"
        );
    }

    public function keys()
    {
        return array('MODULE_HPPP_CHECK_STATUS');
    }
}
