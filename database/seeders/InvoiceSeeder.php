<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $invoices = [
            [
                'booking_id' => 1,
                'invoice_number' => 'INV-2024-001',
                'amount' => 15000,
                'paid_amount' => 15000,
                'due_date' => '2024-01-20',
                'status' => 'paid',
                'pdf_path' => 'invoices/INV-2024-001.pdf',
            ],
            [
                'booking_id' => 2,
                'invoice_number' => 'INV-2024-002',
                'amount' => 6000,
                'paid_amount' => 6000,
                'due_date' => '2024-01-25',
                'status' => 'paid',
                'pdf_path' => 'invoices/INV-2024-002.pdf',
            ],
            [
                'booking_id' => 3,
                'invoice_number' => 'INV-2024-003',
                'amount' => 4500,
                'paid_amount' => 0,
                'due_date' => '2024-01-30',
                'status' => 'pending',
                'pdf_path' => 'invoices/INV-2024-003.pdf',
            ],
            [
                'booking_id' => 4,
                'invoice_number' => 'INV-2024-004',
                'amount' => 9000,
                'paid_amount' => 9000,
                'due_date' => '2024-02-05',
                'status' => 'paid',
                'pdf_path' => 'invoices/INV-2024-004.pdf',
            ],
            [
                'booking_id' => 5,
                'invoice_number' => 'INV-2024-005',
                'amount' => 3500,
                'paid_amount' => 3500,
                'due_date' => '2024-02-10',
                'status' => 'paid',
                'pdf_path' => 'invoices/INV-2024-005.pdf',
            ],
            [
                'booking_id' => 6,
                'invoice_number' => 'INV-2024-006',
                'amount' => 12000,
                'paid_amount' => 12000,
                'due_date' => '2024-02-15',
                'status' => 'paid',
                'pdf_path' => 'invoices/INV-2024-006.pdf',
            ],
            [
                'booking_id' => 7,
                'invoice_number' => 'INV-2024-007',
                'amount' => 7000,
                'paid_amount' => 0,
                'due_date' => '2024-02-20',
                'status' => 'pending',
                'pdf_path' => 'invoices/INV-2024-007.pdf',
            ],
            [
                'booking_id' => 8,
                'invoice_number' => 'INV-2024-008',
                'amount' => 5500,
                'paid_amount' => 5500,
                'due_date' => '2024-02-25',
                'status' => 'paid',
                'pdf_path' => 'invoices/INV-2024-008.pdf',
            ],
            [
                'booking_id' => 9,
                'invoice_number' => 'INV-2024-009',
                'amount' => 18000,
                'paid_amount' => 18000,
                'due_date' => '2024-03-01',
                'status' => 'paid',
                'pdf_path' => 'invoices/INV-2024-009.pdf',
            ],
            [
                'booking_id' => 10,
                'invoice_number' => 'INV-2024-010',
                'amount' => 4000,
                'paid_amount' => 4000,
                'due_date' => '2024-03-05',
                'status' => 'paid',
                'pdf_path' => 'invoices/INV-2024-010.pdf',
            ],
            [
                'booking_id' => 11,
                'invoice_number' => 'INV-2024-011',
                'amount' => 11000,
                'paid_amount' => 0,
                'due_date' => '2024-03-10',
                'status' => 'pending',
                'pdf_path' => 'invoices/INV-2024-011.pdf',
            ],
            [
                'booking_id' => 12,
                'invoice_number' => 'INV-2024-012',
                'amount' => 2000,
                'paid_amount' => 2000,
                'due_date' => '2024-03-15',
                'status' => 'paid',
                'pdf_path' => 'invoices/INV-2024-012.pdf',
            ],
            [
                'booking_id' => 13,
                'invoice_number' => 'INV-2024-013',
                'amount' => 6000,
                'paid_amount' => 6000,
                'due_date' => '2024-03-20',
                'status' => 'paid',
                'pdf_path' => 'invoices/INV-2024-013.pdf',
            ],
            [
                'booking_id' => 14,
                'invoice_number' => 'INV-2024-014',
                'amount' => 22000,
                'paid_amount' => 22000,
                'due_date' => '2024-03-25',
                'status' => 'paid',
                'pdf_path' => 'invoices/INV-2024-014.pdf',
            ],
            [
                'booking_id' => 15,
                'invoice_number' => 'INV-2024-015',
                'amount' => 3000,
                'paid_amount' => 3000,
                'due_date' => '2024-03-30',
                'status' => 'paid',
                'pdf_path' => 'invoices/INV-2024-015.pdf',
            ],
            [
                'booking_id' => 16,
                'invoice_number' => 'INV-2024-016',
                'amount' => 14000,
                'paid_amount' => 0,
                'due_date' => '2024-04-05',
                'status' => 'pending',
                'pdf_path' => 'invoices/INV-2024-016.pdf',
            ],
            [
                'booking_id' => 17,
                'invoice_number' => 'INV-2024-017',
                'amount' => 7500,
                'paid_amount' => 7500,
                'due_date' => '2024-04-10',
                'status' => 'paid',
                'pdf_path' => 'invoices/INV-2024-017.pdf',
            ],
            [
                'booking_id' => 18,
                'invoice_number' => 'INV-2024-018',
                'amount' => 2500,
                'paid_amount' => 2500,
                'due_date' => '2024-04-15',
                'status' => 'paid',
                'pdf_path' => 'invoices/INV-2024-018.pdf',
            ],
            [
                'booking_id' => 19,
                'invoice_number' => 'INV-2024-019',
                'amount' => 20000,
                'paid_amount' => 20000,
                'due_date' => '2024-04-20',
                'status' => 'paid',
                'pdf_path' => 'invoices/INV-2024-019.pdf',
            ],
            [
                'booking_id' => 20,
                'invoice_number' => 'INV-2024-020',
                'amount' => 9000,
                'paid_amount' => 9000,
                'due_date' => '2024-04-25',
                'status' => 'paid',
                'pdf_path' => 'invoices/INV-2024-020.pdf',
            ],
        ];

        foreach ($invoices as $invoice) {
            DB::table('invoices')->insert(array_merge($invoice, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
