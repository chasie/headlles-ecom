<?php

namespace HeadlessEcom\Console\Commands;

use Illuminate\Console\Command;
use HeadlessEcom\Addons\Manifest;
use Symfony\Component\Console\Command\Command as CommandAlias;

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
     * @param  Manifest  $manifest
     * @return int
     */
    public function handle(Manifest $manifest): int
    {
        $manifest->build();

        foreach (array_keys($manifest->manifest) as $package) {
            $this->line("Discovered Addon: <info>{$package}</info>");
        }

        $this->info('Addon manifest generated successfully.');

        return CommandAlias::SUCCESS;
    }
}
