<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('invoice.index', [
            'invoices' => Invoice::all(),
        ]);
    }

    public function create()
    {
        return view('invoice.create', [
            'clients' => Client::all(),
        ]);
    }

    public function store()
    {
        Invoice::create([
            'number'          => request('number'),
            'date'            => request('date') ?: null,
            'due_date'        => request('due_date') ?: null,
            'amount'          => request('amount') ? intval(floatval(request('amount')) * 100) : null,
            'total_hours'     => request('total_hours'),
            'client_id'       => request('client_id'),
            'is_sent'         => request('is_sent') ?: false,
            'is_paid'         => request('is_paid') ?: false,
            'description'     => request('description'),
        ]);

        return redirect(route('invoice.index'));
    }

    public function show(Invoice $invoice)
    {
        return view('invoice.show', ['invoice' => $invoice]);
    }

    public function edit(Invoice $invoice)
    {
        return view('invoice.edit', [
            'invoices' => $invoice,
            'clients' => Client::all(),
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->validate($request, [
            'number'      => 'required',
            'client_id' => 'exists:clients,id',
        ]);

        $invoice->update($request->all());

        return redirect()
            ->route('invoice.index')
            ->with(['messages' => ['success' => 'Invoice '.$invoice->number.' updated.']]);
    }
}
