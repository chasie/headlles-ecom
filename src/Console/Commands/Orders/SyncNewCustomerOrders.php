<?php

namespace HeadlessEcom\Console\Commands\Orders;

use Illuminate\Console\Command;
use HeadlessEcom\Jobs\Orders\MarkAsNewCustomer;
use HeadlessEcom\Models\Order;
use Symfony\Component\Console\Command\Command as CommandAlias;

class SyncNewCustomerOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'headless-ecom:orders:sync-new';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates historic orders to whether they were a new customer or not.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        Order::orderBy('id')->chunk(500, function ($orders) {
            foreach ($orders as $order) {
                MarkAsNewCustomer::dispatch($order->id);
            }
        });

        return CommandAlias::SUCCESS;
    }
}
