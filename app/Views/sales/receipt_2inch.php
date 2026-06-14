<?php
/**
 * @var string $transaction_time
 * @var int $sale_id
 * @var string $employee
 * @var float $discount
 * @var float $subtotal
 * @var array $taxes
 * @var float $total
 * @var array $payments
 * @var float $amount_change
 * @var string $barcode
 * @var array $config
 */
?>

<style type="text/css">
    @page {
        size: 58mm auto;
        margin: 1.5mm;
    }

    html.receipt-template-2inch,
    body.receipt-template-2inch {
        width: 58mm;
        min-width: 58mm;
        margin: 0;
        padding: 0;
        background: #fff;
    }

    body.receipt-template-2inch .wrapper,
    body.receipt-template-2inch .container,
    body.receipt-template-2inch .row {
        width: auto;
        max-width: none;
        min-width: 0;
        margin: 0;
        padding: 0;
    }

    body.receipt-template-2inch .container {
        margin-left: 0;
        margin-right: 0;
    }

    body.receipt-template-2inch #footer,
    body.receipt-template-2inch .topbar,
    body.receipt-template-2inch .navbar {
        display: none;
    }

    body.receipt-template-2inch #control_buttons {
        width: 58mm;
        margin: 0 auto 6px;
    }

    #receipt_wrapper.receipt-2inch {
        width: 58mm;
        max-width: 58mm;
        margin: 0 auto;
        padding: 0;
        line-height: 1.2;
    }

    #receipt_wrapper.receipt-2inch,
    #receipt_wrapper.receipt-2inch table,
    #receipt_wrapper.receipt-2inch td,
    #receipt_wrapper.receipt-2inch th {
        color: #000;
    }

    #receipt_wrapper.receipt-2inch #receipt_header,
    #receipt_wrapper.receipt-2inch #receipt_general_info,
    #receipt_wrapper.receipt-2inch #sale_return_policy,
    #receipt_wrapper.receipt-2inch #barcode {
        text-align: center;
        margin: 0 0 4px;
    }

    #receipt_wrapper.receipt-2inch #company_name,
    #receipt_wrapper.receipt-2inch #sale_receipt {
        font-weight: 600;
    }

    #receipt_wrapper.receipt-2inch #image {
        max-width: 42mm;
        max-height: 16mm;
        margin-bottom: 2px;
    }

    #receipt_wrapper.receipt-2inch #receipt_general_info > div,
    #receipt_wrapper.receipt-2inch #receipt_header > div,
    #receipt_wrapper.receipt-2inch #sale_return_policy {
        margin: 0;
        padding: 0;
    }

    #receipt_wrapper.receipt-2inch #receipt_items {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    #receipt_wrapper.receipt-2inch #receipt_items th,
    #receipt_wrapper.receipt-2inch #receipt_items td {
        padding: 1px 0;
        vertical-align: top;
    }

    #receipt_wrapper.receipt-2inch #receipt_items thead th {
        border-bottom: 1px solid #000;
        font-weight: 600;
    }

    #receipt_wrapper.receipt-2inch .item-name,
    #receipt_wrapper.receipt-2inch .detail-row td:first-child {
        width: 62%;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    #receipt_wrapper.receipt-2inch .item-qty {
        width: 14%;
        text-align: center;
        white-space: nowrap;
    }

    #receipt_wrapper.receipt-2inch .item-total,
    #receipt_wrapper.receipt-2inch .total-value {
        width: 24%;
        text-align: right;
        white-space: nowrap;
    }

    #receipt_wrapper.receipt-2inch .detail-row td,
    #receipt_wrapper.receipt-2inch .discount-row td {
        font-size: 0.95em;
    }

    #receipt_wrapper.receipt-2inch .summary-start td {
        border-top: 1px solid #000;
        padding-top: 2px;
    }

    #receipt_wrapper.receipt-2inch .summary-label {
        text-align: right;
        padding-right: 4px;
    }

    #receipt_wrapper.receipt-2inch .grand-total td {
        font-weight: 700;
    }

    #receipt_wrapper.receipt-2inch #barcode {
        margin-bottom: 0;
    }

    #receipt_wrapper.receipt-2inch #barcode br {
        display: none;
    }

    @media print {
        html.receipt-template-2inch,
        body.receipt-template-2inch {
            width: 58mm;
            min-width: 58mm;
        }

        body.receipt-template-2inch .wrapper,
        body.receipt-template-2inch .container,
        body.receipt-template-2inch .row {
            margin: 0 !important;
            padding: 0 !important;
        }

        body.receipt-template-2inch #control_buttons {
            display: none !important;
        }

        body.receipt-template-2inch #receipt_wrapper.receipt-2inch {
            margin: 0;
        }
    }
</style>

<?php
$showCustomer = isset($customer) && $customer !== '';
$showInvoiceNumber = !empty($invoice_number);
$showReturnPolicy = trim((string)$config['return_policy']) !== '';
$showBarcode = !empty($barcode);
$showSubtotalSection = $config['receipt_show_taxes'] || ($config['receipt_show_total_discount'] && $discount > 0);
?>

<div id="receipt_wrapper" class="receipt-2inch" style="font-size: <?= esc($config['receipt_font_size']) ?>px;">
    <div id="receipt_header">
        <?php if ($config['company_logo'] != '') { ?>
            <div><img id="image" src="<?= base_url('uploads/' . esc($config['company_logo'], 'url')) ?>" alt="company_logo"></div>
        <?php } ?>
        <?php if ($config['receipt_show_company_name']) { ?>
            <div id="company_name"><?= esc($config['company']) ?></div>
        <?php } ?>
        <?php if ($config['address'] !== '') { ?>
            <div id="company_address"><?= nl2br(esc($config['address'])) ?></div>
        <?php } ?>
        <?php if ($config['phone'] !== '') { ?>
            <div id="company_phone"><?= esc($config['phone']) ?></div>
        <?php } ?>
        <div id="sale_receipt"><?= lang('Sales.receipt') ?></div>
        <div id="sale_time"><?= esc($transaction_time) ?></div>
    </div>

    <div id="receipt_general_info">
        <?php if ($showCustomer) { ?>
            <div id="customer"><?= lang('Customers.customer') . esc(": $customer") ?></div>
        <?php } ?>
        <div id="sale_id"><?= lang('Sales.id') . esc(": $sale_id") ?></div>
        <?php if ($showInvoiceNumber) { ?>
            <div id="invoice_number"><?= lang('Sales.invoice_number') . esc(": $invoice_number") ?></div>
        <?php } ?>
        <div id="employee"><?= lang('Employees.employee') . esc(": $employee") ?></div>
    </div>

    <table id="receipt_items">
        <thead>
            <tr>
                <th class="item-name"><?= lang('Sales.description_abbrv') ?></th>
                <th class="item-qty"><?= lang('Sales.quantity') ?></th>
                <th class="item-total"><?= lang('Sales.total') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cart as $item) { ?>
                <tr>
                    <td class="item-name"><?= esc(ucfirst(trim($item['name'] . ' ' . $item['attribute_values']))) ?></td>
                    <td class="item-qty"><?= to_quantity_decimals($item['quantity']) ?></td>
                    <td class="item-total"><?= to_currency($item[($config['receipt_show_total_discount'] ? 'total' : 'discounted_total')]) ?></td>
                </tr>
                <?php
                $details = [];
                if ($config['receipt_show_description'] && $item['description'] !== '') {
                    $details[] = $item['description'];
                }
                if ($config['receipt_show_serialnumber'] && $item['serialnumber'] !== '') {
                    $details[] = $item['serialnumber'];
                }
                ?>
                <?php if (!empty($details)) { ?>
                    <tr class="detail-row">
                        <td colspan="3"><?= esc(implode(' | ', $details)) ?></td>
                    </tr>
                <?php } ?>
                <?php if ($item['discount'] > 0) { ?>
                    <tr class="discount-row">
                        <?php if ($item['discount_type'] == FIXED) { ?>
                            <td colspan="2"><?= to_currency($item['discount']) . ' ' . lang('Sales.discount') ?></td>
                        <?php } else { ?>
                            <td colspan="2"><?= to_decimals($item['discount']) . ' ' . lang('Sales.discount_included') ?></td>
                        <?php } ?>
                        <td class="total-value"><?= to_currency($item['discounted_total']) ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>

            <?php if ($config['receipt_show_total_discount'] && $discount > 0) { ?>
                <tr class="<?= $showSubtotalSection ? 'summary-start' : '' ?>">
                    <td colspan="2" class="summary-label"><?= lang('Sales.sub_total') ?></td>
                    <td class="total-value"><?= to_currency($subtotal) ?></td>
                </tr>
                <tr>
                    <td colspan="2" class="summary-label"><?= lang('Sales.discount') ?></td>
                    <td class="total-value"><?= to_currency($discount * -1) ?></td>
                </tr>
            <?php } ?>

            <?php if ($config['receipt_show_taxes']) { ?>
                <tr class="<?= $config['receipt_show_total_discount'] && $discount > 0 ? '' : 'summary-start' ?>">
                    <td colspan="2" class="summary-label"><?= lang('Sales.sub_total') ?></td>
                    <td class="total-value"><?= to_currency($subtotal) ?></td>
                </tr>
                <?php foreach ($taxes as $tax) { ?>
                    <tr>
                        <td colspan="2" class="summary-label"><?= (float)$tax['tax_rate'] . '% ' . esc($tax['tax_group']) ?></td>
                        <td class="total-value"><?= to_currency_tax($tax['sale_tax_amount']) ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>

            <tr class="<?= $showSubtotalSection ? '' : 'summary-start' ?> grand-total">
                <td colspan="2" class="summary-label"><?= lang('Sales.total') ?></td>
                <td class="total-value"><?= to_currency($total) ?></td>
            </tr>

            <?php
            $onlySaleCheck = false;
            $showGiftcardRemainder = false;
            foreach ($payments as $payment) {
                $onlySaleCheck |= $payment['payment_type'] == lang('Sales.check');
                $splitpayment = explode(':', $payment['payment_type']);
                $showGiftcardRemainder |= $splitpayment[0] == lang('Sales.giftcard');
            ?>
                <tr>
                    <td colspan="2" class="summary-label"><?= esc($splitpayment[0]) ?></td>
                    <td class="total-value"><?= to_currency($payment['payment_amount'] * -1) ?></td>
                </tr>
            <?php } ?>

            <?php if (isset($cur_giftcard_value) && $showGiftcardRemainder) { ?>
                <tr>
                    <td colspan="2" class="summary-label"><?= lang('Sales.giftcard_balance') ?></td>
                    <td class="total-value"><?= to_currency($cur_giftcard_value) ?></td>
                </tr>
            <?php } ?>

            <tr>
                <td colspan="2" class="summary-label"><?= lang($amount_change >= 0 ? ($onlySaleCheck ? 'Sales.check_balance' : 'Sales.change_due') : 'Sales.amount_due') ?></td>
                <td class="total-value"><?= to_currency($amount_change) ?></td>
            </tr>
        </tbody>
    </table>

    <?php if ($showReturnPolicy) { ?>
        <div id="sale_return_policy"><?= nl2br(esc($config['return_policy'])) ?></div>
    <?php } ?>

    <?php if ($showBarcode) { ?>
        <div id="barcode">
            <div><?= $barcode ?></div>
            <div><?= $sale_id ?></div>
        </div>
    <?php } ?>
</div>
