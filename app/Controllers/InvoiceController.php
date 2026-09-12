<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Lease;
use App\Models\NotificationLog;
use App\Models\Payment;

class InvoiceController extends Controller
{
    public function index(): void
    {
        $this->view('invoices.index', [
            'invoices' => Invoice::withDetails(),
        ]);
    }

    public function create(): void
    {
        $leaseId = Request::input('lease_id');

        $this->view('invoices.create', [
            'leases' => array_filter(Lease::withDetails(), fn($l) => $l['status'] === 'active'),
            'selectedLeaseId' => $leaseId ? (int) $leaseId : null,
        ]);
    }

    public function store(): void
    {
        $this->verifyCsrf();

        $leaseId = (int) Request::input('lease_id');
        $descriptions = (array) Request::input('item_description', []);
        $amounts = (array) Request::input('item_amount', []);
        $tax = (float) Request::input('tax', 0);

        $items = [];
        foreach ($descriptions as $i => $description) {
            $description = trim((string) $description);
            $amount = (float) ($amounts[$i] ?? 0);
            if ($description === '' || $amount <= 0) {
                continue;
            }
            $items[] = ['description' => $description, 'amount' => $amount];
        }

        if ($leaseId === 0 || empty($items)) {
            $this->flash('error', 'Select a lease and add at least one line item with an amount.');
            $this->redirect('/invoices/create');
        }

        $subtotal = array_sum(array_column($items, 'amount'));
        $total = $subtotal + $tax;
        $periodStart = Request::input('period_start');
        $periodEnd = Request::input('period_end');
        $dueDate = Request::input('due_date');

        Database::beginTransaction();
        try {
            $invoiceId = Invoice::create([
                'lease_id' => $leaseId,
                'invoice_no' => 'INV-PENDING-' . bin2hex(random_bytes(6)),
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'due_date' => $dueDate,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => 'unpaid',
            ]);

            // Invoice number embeds the row id, so it's finalized once the id exists.
            $invoiceNo = 'INV-' . date('Y') . '-' . str_pad((string) $invoiceId, 5, '0', STR_PAD_LEFT);
            Invoice::update($invoiceId, ['invoice_no' => $invoiceNo]);

            foreach ($items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoiceId,
                    'description' => $item['description'],
                    'amount' => $item['amount'],
                ]);
            }

            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            error_log('[Combo] Invoice creation failed: ' . $e->getMessage());
            $this->flash('error', 'Could not create the invoice. Please try again.');
            $this->redirect('/invoices/create');
        }

        $lease = Lease::findWithDetails($leaseId);
        if ($lease) {
            NotificationLog::queue(
                null,
                'email',
                "Invoice {$invoiceNo} — payment due",
                sprintf(
                    "Dear %s,\n\nInvoice %s for %s — %s (period %s to %s) totaling RM %s is due on %s.\n\nRecipient: %s <%s>",
                    $lease['contact_name'],
                    $invoiceNo,
                    $lease['building_name'],
                    $lease['unit_number'],
                    $periodStart,
                    $periodEnd,
                    number_format($total, 2),
                    $dueDate,
                    $lease['company_name'],
                    $lease['contact_email'] ?? 'no email on file'
                )
            );
        }

        $this->flash('success', 'Invoice created.');
        $this->redirect("/invoices/{$invoiceId}");
    }

    public function show(string $id): void
    {
        $invoice = Invoice::findWithDetails((int) $id);
        if (!$invoice) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }

        $this->view('invoices.show', [
            'invoice' => $invoice,
            'items' => InvoiceItem::forInvoice((int) $id),
            'payments' => Payment::forInvoice((int) $id),
            'amountPaid' => Payment::totalPaid((int) $id),
        ]);
    }

    public function storePayment(string $id): void
    {
        $this->verifyCsrf();

        $invoice = Invoice::find((int) $id);
        if (!$invoice) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }

        $amount = (float) Request::input('amount', 0);
        if ($amount <= 0) {
            $this->flash('error', 'Enter a valid payment amount.');
            $this->redirect("/invoices/{$id}");
        }

        Database::beginTransaction();
        try {
            Payment::create([
                'invoice_id' => (int) $id,
                'amount' => $amount,
                'method' => Request::input('method', 'bank_transfer'),
                'reference_no' => Request::input('reference_no') ?: null,
            ]);

            $totalPaid = Payment::totalPaid((int) $id);
            $status = $totalPaid >= (float) $invoice['total'] ? 'paid' : 'partially_paid';
            Invoice::update((int) $id, ['status' => $status]);

            Database::commit();

            if ($status === 'paid') {
                $details = Invoice::findWithDetails((int) $id);
                if ($details) {
                    NotificationLog::queue(
                        null,
                        'email',
                        "Payment received — {$details['invoice_no']}",
                        sprintf(
                            "Dear %s,\n\nWe've received full payment of RM %s for invoice %s (%s — %s). Thank you.\n\nRecipient: %s <%s>",
                            $details['contact_name'],
                            number_format((float) $details['total'], 2),
                            $details['invoice_no'],
                            $details['building_name'],
                            $details['unit_number'],
                            $details['company_name'],
                            $details['contact_email'] ?? 'no email on file'
                        )
                    );
                }
            }
        } catch (\Throwable $e) {
            Database::rollBack();
            error_log('[Combo] Payment recording failed: ' . $e->getMessage());
            $this->flash('error', 'Could not record the payment.');
            $this->redirect("/invoices/{$id}");
        }

        $this->flash('success', 'Payment recorded.');
        $this->redirect("/invoices/{$id}");
    }

    /** Only allowed while no payments have been recorded, to keep payment history intact. */
    public function destroy(string $id): void
    {
        $this->verifyCsrf();

        if (Payment::totalPaid((int) $id) > 0) {
            $this->flash('error', 'Cannot delete an invoice that has payments recorded.');
            $this->redirect("/invoices/{$id}");
        }

        Invoice::delete((int) $id);
        $this->flash('success', 'Invoice deleted.');
        $this->redirect('/invoices');
    }
}
