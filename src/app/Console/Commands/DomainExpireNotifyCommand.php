<?php

namespace App\Console\Commands;

use App\Models\Domain;
use Illuminate\Console\Command;
use App\Notifications\DomainExpireNotification;

class DomainExpireNotifyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain_expire_notify:send {--period=* : Periods in days --period=14 --period=7}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users in advance about domains expiration';

    protected const DAYS_IN_ADVANCE = [30, 7, 3, 2, 1, 0];

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

        $maxDay = array_key_first(self::DAYS_IN_ADVANCE);

        $fromDate = (int)(self::DAYS_IN_ADVANCE[$maxDay] + 1);

        $domains = Domain::where('expires', '<=', now()->addDays($fromDate)->toDateTimeLocalString())
            ->where('expires', '>=', now()->toDateTimeLocalString())->get();

        foreach ($domains as $domain) {
            $diffInDays = now()->diffInDays($domain->expires);

            if (in_array($diffInDays, self::DAYS_IN_ADVANCE)) {
                $domain->user->notify(new DomainExpireNotification($domain));
            }
        }

        return 0;
    }
}
