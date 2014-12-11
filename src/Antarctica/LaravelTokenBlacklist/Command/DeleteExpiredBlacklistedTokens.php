<?php

namespace Antarctica\LaravelTokenBlacklist\Command;

use Illuminate\Support\Facades\Log;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Antarctica\LaravelTokenBlacklist\Repository\TokenBlacklistRepositoryInterface;

class DeleteExpiredBlacklistedTokens extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'auth-tokens:delete-expired-blacklisted-tokens';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Delete expired blacklisted authentication tokens';

    /**
     * @var
     */
    private $Blacklist;

    /**
     * @param TokenBlacklistRepositoryInterface $Blacklist
     */
    public function __construct(TokenBlacklistRepositoryInterface $Blacklist)
    {
        parent::__construct();
        $this->Blacklist = $Blacklist;
    }

    /**
     * When a command should run
     *
     * @param Scheduler|Schedulable $scheduler
     * @return \Indatus\Dispatcher\Scheduling\Schedulable
     */
	public function schedule(Schedulable $scheduler)
	{
		return $scheduler->daily()->hours(0);  // midnight
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $expiredBlacklistedTokens = $this->Blacklist->findAllExpired();
        $expiredBlacklistedTokensCount = count($expiredBlacklistedTokens);

        $this->Blacklist->deleteAllExpired();

        // The number of expired blacklisted tokens should now be 0
        $expiredBlacklistedTokensCountCheck = count($this->Blacklist->findAllExpired());

        if ($expiredBlacklistedTokensCountCheck === 0)
        {
            Log::info('Successfully deleted ' . $expiredBlacklistedTokensCount . ' expired blacklisted authentication tokens');
        }
        else {
            Log::warning('Something went wrong deleting expired blacklisted authentication tokens, ' . $expiredBlacklistedTokensCount . ' should have been removed, ' . $expiredBlacklistedTokensCountCheck . ' still remain.');
        }
	}
}
