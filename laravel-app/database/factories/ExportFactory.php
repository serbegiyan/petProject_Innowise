<?php

namespace Database\Factories;

use App\Enums\ExportStatus;
use App\Models\Export;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Export> */
class ExportFactory extends Factory
{
    protected $model = Export::class;

    public function definition(): array
    {
        return [
            'file_name' => $this->faker->word().'.csv',
            'file_path' => 'exports/'.$this->faker->uuid().'.csv',
            'status' => ExportStatus::PENDING,
            'size' => $this->faker->numberBetween(1024, 10485),
            'error_message' => null,
        ];
    }

    /**
     * Состояние для завершенного экспорта
     */
    public function completed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => ExportStatus::COMPLETED,
        ]);
    }

    /**
     * Состояние для проваленного экспорта
     */
    public function failed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => ExportStatus::FAILED,
            'error_message' => 'S3 Connection timeout',
        ]);
    }
}
