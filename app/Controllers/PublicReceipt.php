<?php

namespace App\Controllers;

use App\Libraries\Barcode_lib;
use App\Libraries\Sale_lib;
use App\Libraries\Tax_lib;
use App\Libraries\Token_lib;
use App\Models\Customer;
use App\Models\Customer_rewards;
use App\Models\Dinner_table;
use App\Models\Employee;
use App\Models\Item;
use App\Models\Item_kit;
use App\Models\Sale;
use App\Models\Stock_location;
use Config\OSPOS;
use Config\Encryption;
use stdClass;

/**
 * PublicReceipt Controller
 *
 * Provides a secure, unauthenticated way for customers to view their
 * receipts online by visiting a signature-signed URL.
 */
class PublicReceipt extends BaseController
{
    private Barcode_lib $barcode_lib;
    private Sale_lib $sale_lib;
    private Tax_lib $tax_lib;
    private Token_lib $token_lib;
    private Customer $customer;
    private Customer_rewards $customer_rewards;
    private Dinner_table $dinner_table;
    private Employee $employee;
    private Item $item;
    private Item_kit $item_kit;
    private Sale $sale;
    private Stock_location $stock_location;
    private array $config;
    private $session;

    public function __construct()
    {
        $this->session = session();
        $this->barcode_lib = new Barcode_lib();
        $this->sale_lib = new Sale_lib();
        $this->tax_lib = new Tax_lib();
        $this->token_lib = new Token_lib();
        $this->config = config(OSPOS::class)->settings;

        $this->customer = model(Customer::class);
        $this->sale = model(Sale::class);
        $this->item = model(Item::class);
        $this->item_kit = model(Item_kit::class);
        $this->stock_location = model(Stock_location::class);
        $this->customer_rewards = model(Customer_rewards::class);
        $this->dinner_table = model(Dinner_table::class);
        $this->employee = model(Employee::class);
    }

    /**
     * Publicly renders the receipt if the signature is valid.
     *
     * @param int $sale_id
     * @param string $hash
     * @return string
     */
    public function show(int $sale_id, string $hash): string
    {
        // 1. Verify URL signature to prevent unauthorized guessing
        $encryptionKey = config(Encryption::class)->key;
        $expectedHash = substr(hash_hmac('sha256', (string)$sale_id, $encryptionKey), 0, 16);
        if (!hash_equals($expectedHash, $hash)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // 2. Backup Cashier's Active Session Keys (prevent session collision)
        $sessionKeys = [
            'sales_cart', 'sales_payments', 'sales_customer', 'sales_employee', 
            'sales_comment', 'sales_invoice_number', 'sales_quote_number', 
            'sales_work_order_number', 'sales_dinner_table', 'sales_mode', 
            'sales_print_after_sale', 'sales_price_work_orders', 'sales_email_receipt',
            'sale_id', 'suspended_id', 'cash_mode', 'cash_rounding', 'cash_adjustment_amount'
        ];
        $backup = [];
        foreach ($sessionKeys as $key) {
            $backup[$key] = $this->session->get($key);
        }

        // 3. Load completed sale data
        $data = $this->_load_sale_data_public($sale_id);

        // 4. Restore Session
        foreach ($sessionKeys as $key) {
            if ($backup[$key] === null) {
                $this->session->remove($key);
            } else {
                $this->session->set($key, $backup[$key]);
            }
        }

        // Inject global configurations to view service (for header/footer)
        view('viewData', [
            'config'      => $this->config,
            'public_view' => true
        ]);

        // Hide control/action buttons on public view
        $data['public_view'] = true;
        $data['config'] = $this->config;

        return view('sales/receipt', $data);
    }

    /**
     * Standalone public loader for sale receipt data.
     */
    private function _load_sale_data_public(int $sale_id): array
    {
        $this->sale_lib->clear_all();
        $cash_rounding = $this->sale_lib->reset_cash_rounding();
        $data['cash_rounding'] = $cash_rounding;

        $sale_info = $this->sale->get_info($sale_id)->getRowArray();
        $this->sale_lib->copy_entire_sale($sale_id);
        $data = [];
        $data['cart'] = $this->sale_lib->get_cart();
        $data['payments'] = $this->sale_lib->get_payments();
        $data['selected_payment_type'] = $this->sale_lib->get_payment_type();

        $tax_details = $this->tax_lib->get_taxes($data['cart'], $sale_id);
        $data['taxes'] = $this->sale->get_sales_taxes($sale_id);
        $data['discount'] = $this->sale_lib->get_discount();
        $data['transaction_time'] = to_datetime(strtotime($sale_info['sale_time']));
        $data['transaction_date'] = to_date(strtotime($sale_info['sale_time']));
        $data['show_stock_locations'] = $this->stock_location->show_locations('sales');

        $data['include_hsn'] = (bool)$this->config['include_hsn'];

        $totals = $this->sale_lib->get_totals($tax_details[0]);
        $this->session->set('cash_adjustment_amount', $totals['cash_adjustment_amount']);
        $data['subtotal'] = $totals['subtotal'];
        $data['payments_total'] = $totals['payment_total'];
        $data['payments_cover_total'] = $totals['payments_cover_total'];
        $data['cash_mode'] = $this->session->get('cash_mode');
        $data['prediscount_subtotal'] = $totals['prediscount_subtotal'];
        $data['cash_total'] = $totals['cash_total'];
        $data['non_cash_total'] = $totals['total'];
        $data['cash_amount_due'] = $totals['cash_amount_due'];
        $data['non_cash_amount_due'] = $totals['amount_due'];

        if ($data['cash_mode'] && ($data['selected_payment_type'] === lang('Sales.cash') || $data['payments_total'] > 0)) {
            $data['total'] = $totals['cash_total'];
            $data['amount_due'] = $totals['cash_amount_due'];
        } else {
            $data['total'] = $totals['total'];
            $data['amount_due'] = $totals['amount_due'];
        }

        $data['amount_change'] = $data['amount_due'] * -1;

        $employee_info = $this->employee->get_info($this->sale_lib->get_employee());
        $data['employee'] = $employee_info->first_name . ' ' . mb_substr($employee_info->last_name, 0, 1);
        $this->_load_customer_data($this->sale_lib->get_customer(), $data);

        $data['sale_id_num'] = $sale_id;
        $data['sale_id'] = 'POS ' . $sale_id;
        $data['comments'] = $sale_info['comment'];
        $data['invoice_number'] = $sale_info['invoice_number'];
        $data['quote_number'] = $sale_info['quote_number'];
        $data['sale_status'] = $sale_info['sale_status'];

        $data['company_info'] = implode("\n", [$this->config['address'], $this->config['phone']]);

        if ($this->config['account_number']) {
            $data['company_info'] .= "\n" . lang('Sales.account_number') . ": " . $this->config['account_number'];
        }
        if ($this->config['tax_id'] != '') {
            $data['company_info'] .= "\n" . lang('Sales.tax_id') . ": " . $this->config['tax_id'];
        }

        $data['barcode'] = $this->barcode_lib->generate_receipt_barcode($data['sale_id']);
        $data['print_after_sale'] = false;
        $data['price_work_orders'] = false;

        if ($this->sale_lib->get_mode() == 'sale_invoice') {
            $data['mode_label'] = lang('Sales.invoice');
            $data['customer_required'] = lang('Sales.customer_required');
        } elseif ($this->sale_lib->get_mode() == 'sale_quote') {
            $data['mode_label'] = lang('Sales.quote');
            $data['customer_required'] = lang('Sales.customer_required');
        } elseif ($this->sale_lib->get_mode() == 'sale_work_order') {
            $data['mode_label'] = lang('Sales.work_order');
            $data['customer_required'] = lang('Sales.customer_required');
        } elseif ($this->sale_lib->get_mode() == 'return') {
            $data['mode_label'] = lang('Sales.return');
            $data['customer_required'] = lang('Sales.customer_optional');
        } else {
            $data['mode_label'] = lang('Sales.receipt');
            $data['customer_required'] = lang('Sales.customer_optional');
        }

        $invoice_type = $this->config['invoice_type'];
        if (!Sale_lib::isValidInvoiceType($invoice_type)) {
            $invoice_type = 'invoice';
        }
        $data['invoice_view'] = $invoice_type;

        $receipt_template = $this->config['receipt_template'] ?? '';
        if (!Sale_lib::isValidReceiptTemplate($receipt_template)) {
            $receipt_template = 'receipt_default';
        }
        $data['receipt_template_view'] = $receipt_template;

        return $data;
    }

    private function _load_customer_data(int $customer_id, array &$data): array|string|stdClass|null
    {
        $customer_info = '';

        if ($customer_id != NEW_ENTRY) {
            $customer_info = $this->customer->get_info($customer_id);
            $data['customer_id'] = $customer_id;

            if (!empty($customer_info->company_name)) {
                $data['customer'] = $customer_info->company_name;
            } else {
                $data['customer'] = $customer_info->first_name . ' ' . $customer_info->last_name;
            }

            $data['first_name'] = $customer_info->first_name;
            $data['last_name'] = $customer_info->last_name;
            $data['customer_email'] = $customer_info->email;
            $data['customer_phone'] = $customer_info->phone_number;
            $data['customer_address'] = $customer_info->address_1;

            if (!empty($customer_info->zip) || !empty($customer_info->city)) {
                $data['customer_location'] = $customer_info->zip . ' ' . $customer_info->city . "\n" . $customer_info->state;
            } else {
                $data['customer_location'] = '';
            }

            $data['customer_account_number'] = $customer_info->account_number;
            $data['customer_discount'] = $customer_info->discount;
            $data['customer_discount_type'] = $customer_info->discount_type;
            $package_id = $this->customer->get_info($customer_id)->package_id;

            if ($package_id != null) {
                $package_name = $this->customer_rewards->get_name($package_id);
                $points = $this->customer->get_info($customer_id)->points;
                $data['customer_rewards']['package_id'] = $package_id;
                $data['customer_rewards']['points'] = empty($points) ? 0 : $points;
                $data['customer_rewards']['package_name'] = $package_name;
            }

            $data['customer_info'] = implode("\n", [
                $data['customer'],
                $data['customer_address'],
                $data['customer_location']
            ]);

            if ($data['customer_account_number']) {
                $data['customer_info'] .= "\n" . lang('Sales.account_number') . ": " . $data['customer_account_number'];
            }

            if ($customer_info->tax_id != '') {
                $data['customer_info'] .= "\n" . lang('Sales.tax_id') . ": " . $customer_info->tax_id;
            }
            $data['tax_id'] = $customer_info->tax_id;
        }

        return $customer_info;
    }
}
