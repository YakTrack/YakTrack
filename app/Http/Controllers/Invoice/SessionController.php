<?php

namespace App\Http\Controllers\Invoice;

use App\Models\Invoice;
use App\Models\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SessionController extends Controller
{
    public function update(Invoice $invoice, Session $session)
    {
        $session->attachToInvoice($invoice);

        return redirect()->route('invoice.session.index', ['invoice' => $invoice]);
    }
}
