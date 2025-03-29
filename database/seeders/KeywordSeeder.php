<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Profession;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KeywordSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now()->toDateTimeString(); // Current date and time

        // Define keywords for each profession
        $keywordsByProfession = [
            'Pharmacy' => [
                ['keyword' => 'Pharmacist', 'last_run' => $now, 'status' => '1'],
                ['keyword' => 'Pharmacy Technician', 'last_run' => $now, 'status' => '1'],
                ['keyword' => 'Dispenser', 'last_run' => $now, 'status' => '1'],
                ['keyword' => 'Pharmacy Assistant', 'last_run' => $now, 'status' => '1'],
            ],
            'Radiography' => [
                ['keyword' => 'Radiographer', 'last_run' => $now, 'status' => '1'],
                 ['keyword' => 'Mammographer', 'last_run' => $now, 'status' => '1'],
                 ['keyword' => 'Sonographer', 'last_run' => $now, 'status' => '1'],
                 ['keyword' => 'Radiographer Assistant', 'last_run' => $now, 'status' => '1'],
            ],
            // 'Biomedical Science' => [
            //     ['keyword' => 'Biomedical Scientist', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'BMS', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Anatomical Pathology Technician', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'APT', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Medical Laboratory Assistant', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'MLA', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Phlebotomy', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Phlebotomist', 'last_run' => $now, 'status' => '1'],
            // ],
            // 'Sterile Services' => [
            //     ['keyword' => 'Sterile Services Technician', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Decontamination Technician', 'last_run' => $now, 'status' => '1'],
            // ],
            // 'Audiology' => [
            //     ['keyword' => 'Audiologist', 'last_run' => $now, 'status' => '1'],
            // ],
            // 'Occupational Therapy' => [
            //     ['keyword' => 'Occupational Therapist', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'OT', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Occupational Therapy Assistant', 'last_run' => $now, 'status' => '1'],
            // ],
            // 'Physiotherapy' => [
            //     ['keyword' => 'Physiotherapist', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Physio', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Physio Assistant', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Wheelchair Therapist', 'last_run' => $now, 'status' => '1'],
            // ],
            // 'Mental Health' => [
            //     ['keyword' => 'Social Worker', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'CAMHS Practitioner', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Mental Health Practitioner', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Community Psychiatric Nurses', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'CPN', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Registered Mental Health Nurse', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'RMN', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Care Coordinator', 'last_run' => $now, 'status' => '1'],
            // ],
            // 'Operating Theatre' => [
            //     ['keyword' => 'ODP', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Operating Department Practitioner', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Scrub Nurse', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Recovery Nurse', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Anaesthetic Nurse', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Surgical First Assistant', 'last_run' => $now, 'status' => '1'],
            // ],
            // 'Speech and Language' => [
            //     ['keyword' => 'Speech and Language Therapist', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'SLT', 'last_run' => $now, 'status' => '1'],
            //     ['keyword' => 'Speech and Language Assistant', 'last_run' => $now, 'status' => '1'],
            // ],
        ];

        // Iterate over each profession and its keywords
        foreach ($keywordsByProfession as $professionName => $keywords) {
            $profession = Profession::where('name', $professionName)->first();

            if ($profession) {
                foreach ($keywords as $keywordData) {
                    DB::table('keywords')->insert([
                        'profession_id' => $profession->id,
                        'keyword' => $keywordData['keyword'],
                        'last_run' => $keywordData['last_run'],
                        'status' => $keywordData['status'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
