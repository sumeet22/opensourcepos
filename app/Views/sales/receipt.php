<?php
/**
 * @var int $sale_id_num
 * @var bool $print_after_sale
 * @var string $receipt_template_view
 * @var array $config
 * @var string|null $customer_phone
 */

use App\Models\Employee;

$template = $receipt_template_view ?? 'receipt_default';
$is_two_inch_template = $template === 'receipt_2inch';

?>

<?= view('partial/header') ?>

<?php if ($is_two_inch_template): ?>
    <script type="text/javascript">
        document.documentElement.classList.add('receipt-template-2inch');
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('receipt-template-2inch');
        });
    </script>
<?php endif; ?>

<?php
if (isset($error_message)) {
    echo '<div class="alert alert-dismissible alert-danger">' . $error_message . '</div>';
    exit;
}
?>

<?php if (!empty($customer_email)): ?>
    <script type="text/javascript">
        $(document).ready(function() {
            var send_email = function() {
                $.get('<?= site_url() . esc("/sales/sendPdf/$sale_id_num/receipt") ?>',
                    function(response) {
                        $.notify({
                            message: response.message
                        }, {
                            type: response.success ? 'success' : 'danger'
                        })
                    }, 'json'
                );
            };

            $("#show_email_button").click(send_email);

            <?php if (!empty($email_receipt)): ?>
                send_email();
            <?php endif; ?>
        });
    </script>
<?php endif; ?>

<script type="text/javascript">
    $(document).ready(function() {
        // ── WhatsApp click-to-chat ──────────────────────────────────────────
        var whatsappApiUrl = '<?= site_url() . esc("/sales/whatsappReceiptUrl/$sale_id_num") ?>';

        /**
         * Calls the server for the wa.me URL, then opens it in a new tab.
         * If the customer has no phone number on file, shows a modal so the
         * cashier can enter one manually.
         */
        function sendWhatsapp(phoneOverride) {
            var apiUrl = whatsappApiUrl;
            if (phoneOverride) {
                apiUrl += '?phone=' + encodeURIComponent(phoneOverride);
            }

            $.get(apiUrl, function(response) {
                if (response.success && response.url) {
                    window.open(response.url, '_blank', 'noopener,noreferrer');
                    $.notify({ message: response.message }, { type: 'success' });
                } else {
                    // No phone on record — ask the cashier
                    $('#whatsapp_phone_input').val('');
                    $('#whatsapp_phone_modal').modal('show');
                }
            }, 'json').fail(function() {
                $.notify({ message: '<?= lang('Sales.whatsapp_no_phone') ?>' }, { type: 'danger' });
            });
        }

        $('#show_whatsapp_button').on('click', function() {
            sendWhatsapp(null);
        });

        // Send button inside the manual phone-entry modal
        $('#whatsapp_phone_send').on('click', function() {
            var phone = $('#whatsapp_phone_input').val().trim();
            if (phone === '') {
                $.notify({ message: '<?= lang('Sales.whatsapp_phone_required') ?>' }, { type: 'warning' });
                return;
            }
            $('#whatsapp_phone_modal').modal('hide');
            sendWhatsapp(phone);
        });

        // Allow Enter key inside the phone input
        $('#whatsapp_phone_input').on('keydown', function(e) {
            if (e.key === 'Enter') {
                $('#whatsapp_phone_send').trigger('click');
            }
        });
    });
</script>

<!-- WhatsApp phone-entry modal (only shown when no phone is on record) -->
<div class="modal fade" id="whatsapp_phone_modal" tabindex="-1" role="dialog" aria-labelledby="whatsapp_phone_modal_label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= lang('Common.close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="whatsapp_phone_modal_label">
                    <span class="glyphicon glyphicon-phone"></span>&nbsp;<?= lang('Sales.send_whatsapp') ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="whatsapp_phone_input"><?= lang('Sales.whatsapp_phone_label') ?></label>
                    <input type="tel"
                           id="whatsapp_phone_input"
                           class="form-control"
                           placeholder="+91 98765 43210"
                           autocomplete="tel">
                    <p class="help-block"><?= lang('Sales.whatsapp_no_phone') ?></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('Common.cancel') ?></button>
                <button type="button" class="btn btn-success" id="whatsapp_phone_send">
                    <span class="glyphicon glyphicon-send"></span>&nbsp;<?= lang('Sales.send_whatsapp') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?= view('partial/print_receipt', ['print_after_sale' => $print_after_sale, 'selected_printer' => 'receipt_printer']) ?>

<?php if (empty($public_view)): ?>
<div class="print_hide" id="control_buttons" style="text-align: right;">
    <a href="javascript:printdoc();">
        <div class="btn btn-info btn-sm" id="show_print_button"><?= '<span class="glyphicon glyphicon-print">&nbsp;</span>' . lang('Common.print') ?></div>
    </a>
    <?php if (!empty($customer_email)): ?>
        <a href="javascript:void(0);">
            <div class="btn btn-info btn-sm" id="show_email_button"><?= '<span class="glyphicon glyphicon-envelope">&nbsp;</span>' . lang('Sales.send_receipt') ?></div>
        </a>
    <?php endif; ?>
    <a href="javascript:void(0);">
        <div class="btn btn-success btn-sm" id="show_whatsapp_button">
            <span class="glyphicon glyphicon-phone"></span>&nbsp;<?= lang('Sales.send_whatsapp') ?>
        </div>
    </a>
    <?= anchor('sales', '<span class="glyphicon glyphicon-shopping-cart">&nbsp;</span>' . lang('Sales.register'), ['class' => 'btn btn-info btn-sm', 'id' => 'show_sales_button']) ?>
    <?php
    $employee = model(Employee::class);
    if ($employee->has_grant('reports_sales', session('person_id'))): ?>
        <?= anchor('sales/manage', '<span class="glyphicon glyphicon-list-alt">&nbsp;</span>' . lang('Sales.takings'), ['class' => 'btn btn-info btn-sm', 'id' => 'show_takings_button']) ?>
    <?php endif; ?>
</div>
<?php endif; ?>

<?= view('sales/' . $template) ?>

<?= view('partial/footer') ?>
