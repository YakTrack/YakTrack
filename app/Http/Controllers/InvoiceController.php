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
}
