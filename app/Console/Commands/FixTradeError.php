<?php

namespace App\Console\Commands;

use App\Models\Trade;
use Illuminate\Console\Command;

class FixTradeError extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:trade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $items = Trade::where('payment_status', Trade::PaymentStatus_Success)
            ->whereIn('payment_type', [Trade::PaymentType_Alipay, Trade::PaymentType_WeChat])
            ->whereRaw('payment_money != (refund_money + withdraw_money + platform_money)')->get();
        foreach ($items as $item)
        {
            $item->updateInfo();
            $item->save();
        }
    }
}
