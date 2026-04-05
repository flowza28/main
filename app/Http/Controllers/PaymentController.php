<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'amount' => 'required|integer|min:1',
            'method' => 'required|string|max:50',
            'paid_at' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'amount' => $data['amount'],
            'method' => $data['method'],
            'paid_at' => $data['paid_at'],
            'note' => $data['note'] ?? null,
        ]);

        $invoice->status = 'lunas';
        $invoice->paid_at = $data['paid_at'];
        $invoice->save();

        return redirect()->route('invoices.show', $invoice)->with('success', 'Pembayaran berhasil disimpan.');
    }
}
