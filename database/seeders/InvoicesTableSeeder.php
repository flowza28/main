<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class InvoicesTableSeeder extends Seeder
{
    public function run(): void
    {
        Customer::with('package')->get()->each(function (Customer $customer) {
            Invoice::updateOrCreate([
                'invoice_number' => 'ISP-' . now()->format('Ym') . '-' . $customer->id,
            ], [
                'customer_id' => $customer->id,
                'amount' => $customer->package->price,
                'due_date' => now()->endOfMonth(),
                'status' => 'belum',
                'description' => 'Tagihan paket ' . $customer->package->name,
            ]);
        });
    }
}
