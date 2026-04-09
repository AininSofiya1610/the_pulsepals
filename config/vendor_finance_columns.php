<?php

/**
 * ═══════════════════════════════════════════════════════════════════════════
 *  VENDOR FINANCE — COLUMN MAPPING (Single Source of Truth)
 * ═══════════════════════════════════════════════════════════════════════════
 *
 *  This config drives:
 *    1. Excel import template — column headers are generated FROM this config
 *    2. Import validation     — incoming headers are matched AGAINST this config
 *    3. Export reports        — column headers are pulled FROM this config
 *
 *  RULES
 *  ─────
 *  • 'header'      — The Excel column header text (what the user sees in the file)
 *  • 'db_field'    — The database column this maps to (null = auto-calculated, not stored directly)
 *  • 'required'    — Whether this column must be filled by the user during import
 *  • 'auto'        — If true, value is auto-calculated during import (user value ignored)
 *  • 'type'        — Data type hint: string | date | numeric
 *  • 'width'       — Column width in Excel units
 *  • 'sample'      — Example value shown in the template's sample row
 *
 *  To add/remove/rename a column, simply update this ONE file.
 *  All template generation, import validation, and export will reflect the change.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Template Configuration
    |--------------------------------------------------------------------------
    */
    'template_title'    => 'VENDOR FINANCE IMPORT TEMPLATE',
    'template_filename' => 'Vendor_Finance_Import_Template.xlsx',
    'sheet_name'        => 'Vendor Finance Import',
    'header_row'        => 4,   // Row number where column headers sit
    'data_start_row'    => 5,   // First data row (sample data row)
    'date_format'       => 'DD-MM-YYYY',
    'instruction'       => '⚠️  Fill data from row 5 onwards. Do NOT rename headers. Date format: DD-MM-YYYY (e.g. 25-02-2026). Status & Balance are AUTO-CALCULATED by system — your values will be overwritten.',

    /*
    |--------------------------------------------------------------------------
    | Sample Row — skip-detection during import
    |--------------------------------------------------------------------------
    | If a row matches ALL of these values, it is treated as the example row
    | and silently skipped during import.
    */
    'sample_row_match' => [
        'invoice_no'  => 'VND-001',
        'vendor_name' => 'Vendor ABC Sdn Bhd',
    ],

    /*
    |--------------------------------------------------------------------------
    | Column Definitions (ORDER MATTERS — determines Excel column A, B, C…)
    |--------------------------------------------------------------------------
    */
    'columns' => [

        [
            'header'   => 'Invoice No',
            'db_field' => 'invoice_no',
            'required' => true,
            'auto'     => false,
            'type'     => 'string',
            'width'    => 20,
            'sample'   => 'VND-001',
        ],

        [
            'header'   => 'Vendor',
            'db_field' => 'vendor_name',
            'required' => true,
            'auto'     => false,
            'type'     => 'string',
            'width'    => 28,
            'sample'   => 'Vendor ABC Sdn Bhd',
        ],

        [
            'header'   => 'Invoice Date',
            'db_field' => 'invoice_date',
            'required' => true,
            'auto'     => false,
            'type'     => 'date',
            'width'    => 18,
            'sample'   => '01-03-2026',
        ],

        [
            'header'   => 'Due Date',
            'db_field' => 'due_date',
            'required' => true,
            'auto'     => false,
            'type'     => 'date',
            'width'    => 18,
            'sample'   => '01-04-2026',
        ],

        [
            'header'   => 'Status',
            'db_field' => null,          // auto-calculated, not stored directly
            'required' => false,
            'auto'     => true,
            'type'     => 'string',
            'width'    => 14,
            'sample'   => '(auto)',
        ],

        [
            'header'   => 'Invoice (RM)',
            'db_field' => 'invoice',
            'required' => true,
            'auto'     => false,
            'type'     => 'numeric',
            'width'    => 18,
            'sample'   => 5000.00,
        ],

        [
            'header'   => 'Amount Paid (RM)',
            'db_field' => 'paid_amount',
            'required' => false,
            'auto'     => false,
            'type'     => 'numeric',
            'width'    => 18,
            'sample'   => 2000.00,
        ],

        [
            'header'   => 'Balance (RM)',
            'db_field' => null,          // auto-calculated: invoice - paid_amount
            'required' => false,
            'auto'     => true,
            'type'     => 'numeric',
            'width'    => 16,
            'sample'   => '(auto)',
        ],

    ],
];
