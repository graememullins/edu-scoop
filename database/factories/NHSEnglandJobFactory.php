<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\NHSEnglandJob;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NHSEnglandJob>
 */
class NHSEnglandJobFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NHSEnglandJob::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 0; // Static counter to alternate keyword_id
    
        return [
            'job_id' => Str::uuid(), // Unique job ID
            'job_link' => $this->faker->url(),
            'keyword_id' => $this->faker->numberBetween(1, 8), // Random number between 1 and 8
            'source_id' => '1',
            'job_title' => $this->faker->jobTitle(),
            'posted_date' => $this->faker->dateTimeBetween('2025-01-01', 'now')->format('Y-m-d'),
            'closing_date' => $this->faker->dateTimeBetween('now', '2025-04-30')->format('Y-m-d'),
            'trust' => $this->faker->company(),
            'reference_number' => strtoupper(Str::random(8)),
            'band' => $this->faker->numberBetween(1, 9),
            'contact_job_title' => $this->faker->jobTitle(),
            'contact_name' => $this->faker->name(),
            'contact_email' => $this->faker->safeEmail(),
            'contact_phone' => $this->faker->phoneNumber(),
            'address_line_1' => $this->faker->streetAddress(),
            'address_line_2' => $this->faker->secondaryAddress(),
            'town' => $this->faker->city(),
            'postcode' => $this->faker->postcode(),
            'is_scraped' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];        
    }    
}