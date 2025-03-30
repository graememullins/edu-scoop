<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeachingJob>
 */
class TeachingJobFactory extends Factory
{
    public function definition(): array
    {
        return [
            'job_id' => Str::uuid()->toString(),
            'job_link' => $this->faker->url(),
            'keyword_id' => $this->faker->randomElement([1, 2]),
            'source_id' => null,
            'job_title' => $this->faker->jobTitle(),
            'posted_date' => $this->faker->date(),
            'closing_date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'start_date' => $this->faker->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d'),
            'posted_by' => $this->faker->company(),
            'subject' => $this->faker->randomElement(['Maths', 'Science', 'English', 'History', 'Music']),
            'education_phase' => $this->faker->randomElement(['Primary', 'Secondary']),
            'age_range' => $this->faker->randomElement(['5-11', '11-16', '16-18']),
            'school_size' => $this->faker->numberBetween(100, 1500) . ' pupils',
            'school_type' => $this->faker->randomElement(['Academy', 'Community School', 'Faith School']),
            'contract_type' => $this->faker->randomElement(['Permanent', 'Temporary', 'Fixed-term']),
            'reference_number' => strtoupper(Str::random(10)),
            'key_stages' => $this->faker->randomElement(['KS1', 'KS2', 'KS3', 'KS4', 'KS5']),
            'contact_job_title' => $this->faker->randomElement(['Headteacher', 'Recruitment Officer', 'HR Advisor']),
            'contact_name' => $this->faker->name(),
            'contact_email' => $this->faker->safeEmail(),
            'contact_phone' => $this->faker->phoneNumber(),
            'address_line_1' => $this->faker->streetAddress(),
            'address_line_2' => $this->faker->optional()->secondaryAddress(),
            'town' => $this->faker->city(),
            'post_code' => $this->faker->postcode(),
            'is_scraped' => true,
        ];
    }
}