<?php

namespace App\Http\Controllers\Sprint;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Sprint;

class InvoiceController extends Controller
{
    public function store(Sprint $sprint)
    {
        $billableSessions = $sprint->sessions()->whereBillable()->get();

        $invoice = Invoice::create([
            'number'          => request('number'),
            'date'            => request('date') ?: null,
            'due_date'        => request('due_date') ?: null,
            'amount'          => request('amount') ? intval(floatval(request('amount')) * 100) : null,
            'total_hours'     => $billableSessions->totalDurationInHours(),
            'client_id'       => $sprint->project->client_id,
            'is_sent'         => request('is_sent') ?: false,
            'is_paid'         => request('is_paid') ?: false,
            'description'     => request('description') ?? '',
        ]);

        $billableSessions->each->attachToInvoice($invoice);

        return redirect()
            ->route('invoice.edit', ['invoice' => $invoice->id])
            ->with('success', 'You have created invoice "'.$invoice->number.'"');
    }
}
