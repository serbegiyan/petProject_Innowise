<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Установка ОС', 'slug' => 'ustanovka-os', 'description' => 'Чистая установка Windows/Linux'],
            ['name' => 'Наклейка пленки', 'slug' => 'naklejka-plenki', 'description' => 'Защита экрана'],
            ['name' => 'Расширенная гарантия', 'slug' => 'garantiya-plus', 'description' => 'Дополнительный год обслуживания'],
            ['name' => 'Перенос данных', 'slug' => 'perenos-dannyh', 'description' => 'Копирование файлов со старого устройства'],
            ['name' => 'Установка драйверов', 'slug' => 'ustanovka-drayverov', 'description' => 'Обновление драйверов устройств'],
            ['name' => 'Настройка сети', 'slug' => 'nastroyka-seti', 'description' => 'Подключение к Wi-Fi и настройка роутера'],
            ['name' => 'Оптимизация производительности', 'slug' => 'optimizatsiya-proizvoditelnosti', 'description' => 'Ускорение работы устройства'],
            ['name' => 'Удаление вирусов', 'slug' => 'udalenie-virusov', 'description' => 'Очистка от вредоносного ПО'],
            ['name' => 'Ремонт экрана', 'slug' => 'remont-ekrana', 'description' => 'Замена разбитого дисплея'],
            ['name' => 'Замена батареи', 'slug' => 'zamena-batarei', 'description' => 'Установка новой аккумуляторной батареи'],
            ['name' => 'Доставка', 'slug' => 'dostavka', 'description' => 'Доставка устройства до клиента'],
        ];

        foreach ($services as $service) {
            \App\Models\Service::firstOrCreate(['slug' => $service['slug']], $service);
        }
    }
}
