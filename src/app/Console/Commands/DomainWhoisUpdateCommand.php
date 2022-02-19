<?php

namespace App\Console\Commands;

use App\Models\Domain;
use Illuminate\Console\Command;

class DomainWhoisUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain_whois:update {--uuid=* : Uuid of domain --uuid=f02b7fd2-a925-484c-bfd6-xxx047965f7 --uuid=f02b7fd2-a925-484c-bfd6-yyy047965f7}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update whois data for domains';

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
     * @return int
     */
    public function handle()
    {
        $options = $this->options();

        $domains = Domain::where('expires', '<', now()->subDays(2)->toDateTimeLocalString())->get();

        if ($domains) {
            foreach ($domains as $domain) {
                (new \App\Http\Actions\DomainWhoisUpdateAction())($domain->uuid);
            }
        }

        return 0;
    }
}
