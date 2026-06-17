<?php

namespace Tests\Feature\Jobs;

use App\Jobs\UpdateExchangeRatesJob;
use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class UpdateExchangeRatesJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Настраиваем конфиг для тестов
        config()->set('services.bank.url', 'https://bank.example.com/api/rates');
        config()->set('services.target_currencies', ['USD', 'EUR', 'RUB']);
    }

    public function test_it_aborts_if_bank_url_is_not_configured(): void
    {
        config()->set('services.bank.url', null);

        Log::shouldReceive('error')->once()->with('URL банка не настроен в конфигурации.');

        $job = new UpdateExchangeRatesJob;
        $job->handle($this->app->make(CurrencyService::class));

        // Проверяем, что в базу ничего не записалось
        $this->assertDatabaseCount('exchange_rates', 0);
    }

    public function test_it_parses_xml_and_updates_rates_successfully(): void
    {
        // 1. Имитируем успешный ответ банка (валидный XML)
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <rates>
                    <value iso="USD" buy="3.1500" scale="1"/>
                    <value iso="EUR" buy="3.4500" scale="1"/>
                    <value iso="PLN" buy="0.8000" scale="1"/> </rates>';

        Http::fake([
            'https://bank.example.com/api/rates' => Http::response($xml, 200),
        ]);

        // 2. Мокаем CurrencyService, чтобы проверить вызов forgetCache()
        $this->instance(
            CurrencyService::class,
            Mockery::mock(CurrencyService::class, function (MockInterface $mock) {
                $mock->shouldReceive('forgetCache')->once();
            })
        );

        // 3. Запускаем джобу
        $job = new UpdateExchangeRatesJob;
        $job->handle(app(CurrencyService::class));

        // 4. Проверяем БД
        $this->assertDatabaseHas('exchange_rates', ['name' => 'BYN', 'rate' => '1.0000', 'scale' => 1]);
        $this->assertDatabaseHas('exchange_rates', ['name' => 'USD', 'rate' => '3.1500', 'scale' => 1]);
        $this->assertDatabaseHas('exchange_rates', ['name' => 'EUR', 'rate' => '3.4500', 'scale' => 1]);

        // RUB в XML не было, он не должен был добавиться (так как успешный сценарий не вызывает fallback)
        $this->assertDatabaseMissing('exchange_rates', ['name' => 'RUB']);
        // PLN проигнорирована, так как ее нет в конфиге target_currencies
        $this->assertDatabaseMissing('exchange_rates', ['name' => 'PLN']);
    }

    public function test_it_uses_fallback_rates_on_http_error(): void
    {
        // 1. Имитируем падение сервера банка (500 ошибка)
        Http::fake([
            'https://bank.example.com/api/rates' => Http::response('Internal Server Error', 500),
        ]);

        $this->instance(
            CurrencyService::class,
            Mockery::mock(CurrencyService::class, function (MockInterface $mock) {
                $mock->shouldReceive('forgetCache')->once();
            })
        );

        Log::shouldReceive('info')->twice(); // Начало и конец
        Log::shouldReceive('warning')->once()->withArgs(function ($message) {
            return str_contains($message, 'Использую дефолтные значения');
        });

        $job = new UpdateExchangeRatesJob;
        $job->handle(app(CurrencyService::class));

        // 2. Проверяем, что сработал fallback
        $this->assertDatabaseHas('exchange_rates', ['name' => 'BYN', 'rate' => '1.0000']);
        $this->assertDatabaseHas('exchange_rates', ['name' => 'USD', 'rate' => '2.8267']);
        $this->assertDatabaseHas('exchange_rates', ['name' => 'EUR', 'rate' => '3.3061']);
        $this->assertDatabaseHas('exchange_rates', ['name' => 'RUB', 'rate' => '0.0377']);
    }

    public function test_it_uses_fallback_rates_on_invalid_xml(): void
    {
        Http::fake([
            'https://bank.example.com/api/rates' => Http::response('This is absolutely completely not XML', 200),
        ]);

        $this->instance(
            CurrencyService::class,
            Mockery::mock(CurrencyService::class, function (MockInterface $mock) {
                $mock->shouldReceive('forgetCache')->once();
            })
        );

        $job = new UpdateExchangeRatesJob;
        $job->handle(app(CurrencyService::class));

        $this->assertDatabaseHas('exchange_rates', ['name' => 'USD', 'rate' => '2.8267']);
    }
}
