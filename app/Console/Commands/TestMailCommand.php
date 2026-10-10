<?php

namespace App\Console\Commands;

use App\Mail\CustomerVerificationCodeMail;
use App\Mail\OrderReadyForClaimMail;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email : The destination recipient email address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch test verification and ready-for-claim emails to verify mail transport';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $targetEmail = $this->argument('email');
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info(" 🧺 TROWA LAUNDRY · EMAIL SYSTEM DIAGNOSTIC RUNNER");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->line("Target Recipient: <comment>{$targetEmail}</comment>");
        $this->line("Mailer Driver   : <comment>" . config('mail.default') . "</comment>");
        $this->line("From Address    : <comment>" . config('mail.from.address') . "</comment>");
        $this->line("From Name       : <comment>" . config('mail.from.name') . "</comment>");
        $this->newLine();

        // 1. Test Verification Code Email
        $this->line("1. Sending test [Account Verification Code] email...");
        try {
            $dummyUser = new User([
                'name' => 'Test Customer',
                'email' => $targetEmail,
            ]);
            $dummyCode = '839201';

            Mail::to($targetEmail)->send(new CustomerVerificationCodeMail($dummyUser, $dummyCode));
            $this->info("   ✔ Verification Code email dispatched successfully!");
        } catch (\Throwable $e) {
            $this->error("   ✖ Verification Code email failed: " . $e->getMessage());
        }

        $this->newLine();

        // 2. Test Ready for Claim Email
        $this->line("2. Sending test [Fresh Laundry Ready for Pick-up] email...");
        try {
            $dummyCustomer = new Customer([
                'name' => 'Test Customer',
                'email' => $targetEmail,
            ]);
            $dummyOrder = new Order([
                'order_number' => 'TL-SAMPLE-888',
                'customer_name' => 'Test Customer',
                'weight_kg' => 12.5,
                'number_of_loads' => 2,
                'services' => 'Wash, Dry & Fold, Downy Fabric Conditioner',
                'soap_preference' => 'Ariel Powder + Downy Passion Sachet',
                'total_price' => 250.00,
                'payment_status' => 'paid',
                'status' => 'ready_for_pickup',
            ]);
            $dummyOrder->setRelation('customer', $dummyCustomer);

            Mail::to($targetEmail)->send(new OrderReadyForClaimMail($dummyOrder));
            $this->info("   ✔ Ready for Claim email dispatched successfully!");
        } catch (\Throwable $e) {
            $this->error("   ✖ Ready for Claim email failed: " . $e->getMessage());
        }

        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        if (config('mail.default') === 'log') {
            $this->comment("NOTE: Current MAIL_MAILER is set to 'log'.");
            $this->comment("All email contents were written to: storage/logs/laravel.log");
            $this->comment("To send real emails to real inboxes, configure SMTP in your .env file.");
        } else {
            $this->info("Emails were delivered via your active mail transport (" . config('mail.default') . ").");
        }
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        return Command::SUCCESS;
    }
}
