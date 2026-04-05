<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class GenerateExpiringInvoices extends Command
{
    protected $signature = 'invoices:generate-expiring';
    protected $description = 'Generate invoices for customers whose service will expire within 2 days.';

    public function handle(): int
    {
        $count = Invoice::generateExpiringInvoices();

        $this->info("Generated {$count} expiring invoice(s).");

        return Command::SUCCESS;
    }
}
