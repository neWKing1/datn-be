<?php

namespace App\Console\Commands;

use App\Models\Voucher;
use Illuminate\Console\Command;

class VoucherCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:voucher-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = date('Y-m-d H:i:s', strtotime(now()));

        $vouchers = Voucher::where('status', '!=', 'finished')->get();

        $currentTime = now();
        foreach ($vouchers as $voucher) {
            $this->updateVocuherStatus($voucher, $currentTime);
        }
    }
    protected function updateVocuherStatus($voucher, $currentTime)
    {
        if ($currentTime > $voucher->start_date && $currentTime < $voucher->end_date) {
            $voucher->update(['status' => 'happenning']);
        } else if ($currentTime > $voucher->end_date) {
            $voucher->update(['status' => 'finished']);
        } else if ($currentTime < $voucher->start_date) {
            $voucher->update(['status' => 'upcoming']);
        }
    }
}
