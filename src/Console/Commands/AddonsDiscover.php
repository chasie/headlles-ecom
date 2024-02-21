<?php

namespace HeadlessEcom\Console\Commands;

use Illuminate\Console\Command;
use HeadlessEcom\Addons\Manifest;

class AddonsDiscover extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'headless-ecom:addons:discover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild the cached addon package manifest';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(Manifest $manifest)
    {
        $manifest->build();

        foreach (array_keys($manifest->manifest) as $package) {
            $this->line("Discovered Addon: <info>{$package}</info>");
        }

        $this->info('Addon manifest generated successfully.');
    }
}
