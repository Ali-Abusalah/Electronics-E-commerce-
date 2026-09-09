<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tax invoice configuration
    |--------------------------------------------------------------------------
    | Everything the printed tax invoice (PDF) needs: the seller identity shown
    | in the header and encoded in the QR code, the VAT rate applied on every
    | item and the invoice currency.
    |
    | Replace the placeholder values below with your real company details —
    | either directly in this file or through the INVOICE_* environment
    | variables.
    */

    // Company / seller details displayed on the invoice
    'company_name' => env('INVOICE_COMPANY_NAME', 'DCTech Shop'),
    'company_tagline' => env('INVOICE_COMPANY_TAGLINE', 'Your one-stop shop for the latest electronics'),
    'company_address' => env('INVOICE_COMPANY_ADDRESS', 'Amman, Jordan'),
    'company_phone' => env('INVOICE_COMPANY_PHONE', '+962 6 000 0000'),
    'company_email' => env('INVOICE_COMPANY_EMAIL', 'sales@dctech.com'),

    // Seller tax registration number (الرقم الضريبي) — appears on the invoice
    // and inside the QR code so it can be verified.
    'vat_number' => env('INVOICE_VAT_NUMBER', '1234567890'),

    // VAT / sales-tax percentage applied on the value of every item.
    'tax_rate' => (float) env('INVOICE_TAX_RATE', 16),

    // Invoice currency (used only for displaying amounts on the invoice).
    'currency_code' => env('INVOICE_CURRENCY_CODE', 'JOD'),
    'currency_symbol' => env('INVOICE_CURRENCY_SYMBOL', 'JD'),

    // Render the box reserved for the company stamp (ختم الشركة).
    'show_stamp_box' => true,

    'footer_note' => env('INVOICE_FOOTER_NOTE', 'Thank you for your purchase!'),
];
