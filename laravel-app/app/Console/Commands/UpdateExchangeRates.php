<?php

namespace App\Console\Commands;

use App\Jobs\UpdateExchangeRatesJob;
use Illuminate\Console\Command;

class UpdateExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get currency rates from bank';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Отправка задачи в очередь...');

        UpdateExchangeRatesJob::dispatchSync();

        $this->info('Готово!');
    }
}
