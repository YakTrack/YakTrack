<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Session;

class SessionController extends Controller
{
    public function update(Invoice $invoice, Session $session)
    {
        $session->attachToInvoice($invoice);

        return redirect()->route('invoice.session.index', ['invoice' => $invoice]);
    }
}
