<?php

namespace app\Libraries;

/**
 * WhatsApp library
 *
 * Builds wa.me click-to-chat deeplinks so a cashier can send
 * a receipt or invoice to a customer's WhatsApp number with one click.
 * No server-side WhatsApp session or API credentials are required.
 *
 * Usage:
 *   $lib = new Whatsapp_lib();
 *   $url = $lib->buildChatUrl('+919876543210', 'Your receipt is ready!');
 *   // Opens https://wa.me/919876543210?text=Your+receipt+is+ready%21
 */
class Whatsapp_lib
{
    private const WA_BASE_URL = 'https://wa.me/';

    /**
     * Formats a phone number for use in a wa.me URL.
     *
     * Strips all non-digit characters (spaces, dashes, parentheses, plus signs)
     * and optionally prepends a country code when no international prefix is present.
     *
     * Examples:
     *   '+91 98765 43210' -> '919876543210'
     *   '9876543210'      -> '919876543210' (with defaultCountryCode '91')
     *   '09876543210'     -> '919876543210' (leading 0 stripped)
     *
     * @param string $phone             Raw phone number from DB or user input.
     * @param string $defaultCountryCode Country code (digits only) used when
     *                                   the number does not start with '+'.
     * @return string Digits-only international number, or empty string if blank.
     */
    public function formatPhone(string $phone, string $defaultCountryCode = '91'): string
    {
        $phone = trim($phone);

        if ($phone === '') {
            return '';
        }

        $hasPlus = str_starts_with($phone, '+');

        // Strip every non-digit character
        $digits = preg_replace('/\D/', '', $phone);

        if ($digits === '') {
            return '';
        }

        if ($hasPlus) {
            // Already had an international prefix — use as-is
            return $digits;
        }

        // Strip a leading trunk-access digit '0' (common in many countries)
        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        // Prepend the default country code
        $cc = preg_replace('/\D/', '', $defaultCountryCode);

        return $cc . $digits;
    }

    /**
     * Builds a wa.me click-to-chat URL.
     *
     * Opening this URL in a browser tab will launch WhatsApp Web (desktop) or
     * the WhatsApp app (mobile) with the given phone number and pre-filled text.
     * The cashier just taps/clicks "Send" inside WhatsApp.
     *
     * @param string $phone   Phone number — will be auto-formatted via formatPhone().
     * @param string $message Pre-filled message text (plain text, not HTML).
     * @param string $defaultCountryCode Country code used when $phone has no prefix.
     * @return string Ready-to-use wa.me URL, or empty string if phone is invalid.
     */
    public function buildChatUrl(string $phone, string $message, string $defaultCountryCode = '91'): string
    {
        $formatted = $this->formatPhone($phone, $defaultCountryCode);

        if ($formatted === '') {
            return '';
        }

        $url = self::WA_BASE_URL . $formatted;

        if ($message !== '') {
            $url .= '?text=' . rawurlencode($message);
        }

        return $url;
    }

    /**
     * Builds a clean, professional receipt message containing a public signature-signed link.
     *
     * @param string $saleId        Display sale ID, e.g. "POS 9".
     * @param string $total         Formatted total amount, e.g. "$15.00".
     * @param string $companyName   Business name shown at the top of the message.
     * @param string $publicUrl     Public signature-signed URL to view the receipt.
     * @return string Plain-text message ready for rawurlencode().
     */
    public function buildLinkReceiptMessage(string $saleId, string $total, string $companyName, string $publicUrl): string
    {
        $lines = [];
        $lines[] = '*' . $companyName . '*';
        $lines[] = lang('Sales.receipt') . ': ' . $saleId;
        $lines[] = lang('Sales.total') . ': ' . $total;
        $lines[] = '';
        $lines[] = 'View your detailed receipt online:';
        $lines[] = $publicUrl;
        $lines[] = '';
        $lines[] = lang('Sales.whatsapp_receipt_footer');

        return implode("\n", $lines);
    }
}
