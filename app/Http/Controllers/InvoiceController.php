<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Session;
use App\Models\ThirdPartyApplication;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('invoice.index', [
            'invoices' => Invoice::with(['client', 'sessions'])->get(),
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
        return view('invoice.show', [
            'invoice'                => $invoice,
            'thirdPartyApplications' => ThirdPartyApplication::all(),
        ]);
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect(route('invoice.index'));
    }

    public function edit(Invoice $invoice)
    {
        return view('invoice.edit', [
            'invoices' => $invoice,
            'clients'  => Client::all(),
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->validate($request, [
            'number'      => 'string',
            'client_id'   => 'exists:clients,id',
            'sessions.*'  => 'exists:sessions,id',
        ]);

        $invoice->update($request->only([
            'number',
            'date',
            'amount',
            'total_hours',
            'client_id',
            'description',
            'is_paid',
            'is_sent',
        ]));

        $invoice->sessions()->saveMany(Session::findMany($request->sessions));

        if (request()->wantsJson()) {
            return response()
                ->json([
                    'success' => true,
                    'invoice' => $invoice->fresh(),
                ]);
        }

        return redirect()
            ->route('invoice.index')
            ->with(['messages' => ['success' => 'Invoice '.$invoice->number.' updated.']]);
    }
}
