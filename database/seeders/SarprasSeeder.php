<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SarprasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Academic Periods
        $academicPeriods = [
            ['year' => '2024/2025', 'semester' => 'ganjil', 'start_date' => '2024-07-01', 'end_date' => '2024-12-31', 'is_active' => false],
            ['year' => '2024/2025', 'semester' => 'genap', 'start_date' => '2025-01-01', 'end_date' => '2025-06-30', 'is_active' => false],
            ['year' => '2025/2026', 'semester' => 'ganjil', 'start_date' => '2025-07-01', 'end_date' => '2025-12-31', 'is_active' => false],
            ['year' => '2025/2026', 'semester' => 'genap', 'start_date' => '2026-01-01', 'end_date' => '2026-06-30', 'is_active' => true],
        ];

        foreach ($academicPeriods as $period) {
            DB::table('academic_periods')->insert($period + ['created_at' => now(), 'updated_at' => now()]);
        }
        $this->command->info('Academic periods seeded!');

        // Create Sarpras Categories
        $categories = [
            ['name' => 'Bangunan', 'description' => 'Infrastruktur bangunan sekolah', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Alat Peraga', 'description' => 'Peralatan pembelajaran dan praktikum', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Fasilitas', 'description' => 'Fasilitas penunjang kegiatan sekolah', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Teknologi', 'description' => 'Perangkat teknologi dan digital', 'is_active' => true, 'sort_order' => 4],
        ];

        foreach ($categories as $category) {
            DB::table('sarpras_categories')->insert($category + ['created_at' => now(), 'updated_at' => now()]);
        }
        $this->command->info('Sarpras categories seeded!');

        // Create Sarpras Types
        $types = [
            // Bangunan (category_id: 1)
            ['sarpras_category_id' => 1, 'name' => 'Ruang Kelas', 'unit' => 'ruang', 'is_active' => true, 'sort_order' => 1],
            ['sarpras_category_id' => 1, 'name' => 'Ruang Guru', 'unit' => 'ruang', 'is_active' => true, 'sort_order' => 2],
            ['sarpras_category_id' => 1, 'name' => 'Ruang Kepala Sekolah', 'unit' => 'ruang', 'is_active' => true, 'sort_order' => 3],
            ['sarpras_category_id' => 1, 'name' => 'Laboratorium IPA', 'unit' => 'ruang', 'is_active' => true, 'sort_order' => 4],
            ['sarpras_category_id' => 1, 'name' => 'Laboratorium Komputer', 'unit' => 'ruang', 'is_active' => true, 'sort_order' => 5],
            ['sarpras_category_id' => 1, 'name' => 'Perpustakaan', 'unit' => 'ruang', 'is_active' => true, 'sort_order' => 6],
            ['sarpras_category_id' => 1, 'name' => 'Toilet', 'unit' => 'ruang', 'is_active' => true, 'sort_order' => 7],
            ['sarpras_category_id' => 1, 'name' => 'Mushola', 'unit' => 'ruang', 'is_active' => true, 'sort_order' => 8],
            ['sarpras_category_id' => 1, 'name' => 'UKS', 'unit' => 'ruang', 'is_active' => true, 'sort_order' => 9],

            // Alat Peraga (category_id: 2)
            ['sarpras_category_id' => 2, 'name' => 'Meja Siswa', 'unit' => 'buah', 'is_active' => true, 'sort_order' => 1],
            ['sarpras_category_id' => 2, 'name' => 'Kursi Siswa', 'unit' => 'buah', 'is_active' => true, 'sort_order' => 2],
            ['sarpras_category_id' => 2, 'name' => 'Papan Tulis', 'unit' => 'buah', 'is_active' => true, 'sort_order' => 3],
            ['sarpras_category_id' => 2, 'name' => 'Mikroskop', 'unit' => 'buah', 'is_active' => true, 'sort_order' => 4],
            ['sarpras_category_id' => 2, 'name' => 'Globe', 'unit' => 'buah', 'is_active' => true, 'sort_order' => 5],
            ['sarpras_category_id' => 2, 'name' => 'Peta', 'unit' => 'buah', 'is_active' => true, 'sort_order' => 6],

            // Fasilitas (category_id: 3)
            ['sarpras_category_id' => 3, 'name' => 'Lapangan Olahraga', 'unit' => 'unit', 'is_active' => true, 'sort_order' => 1],
            ['sarpras_category_id' => 3, 'name' => 'Kantin', 'unit' => 'unit', 'is_active' => true, 'sort_order' => 2],
            ['sarpras_category_id' => 3, 'name' => 'Parkir Motor', 'unit' => 'unit', 'is_active' => true, 'sort_order' => 3],
            ['sarpras_category_id' => 3, 'name' => 'Taman Sekolah', 'unit' => 'unit', 'is_active' => true, 'sort_order' => 4],

            // Teknologi (category_id: 4)
            ['sarpras_category_id' => 4, 'name' => 'Komputer', 'unit' => 'unit', 'is_active' => true, 'sort_order' => 1],
            ['sarpras_category_id' => 4, 'name' => 'Proyektor', 'unit' => 'unit', 'is_active' => true, 'sort_order' => 2],
            ['sarpras_category_id' => 4, 'name' => 'Printer', 'unit' => 'unit', 'is_active' => true, 'sort_order' => 3],
            ['sarpras_category_id' => 4, 'name' => 'LCD TV', 'unit' => 'unit', 'is_active' => true, 'sort_order' => 4],
            ['sarpras_category_id' => 4, 'name' => 'CCTV', 'unit' => 'unit', 'is_active' => true, 'sort_order' => 5],
        ];

        foreach ($types as $type) {
            DB::table('sarpras_types')->insert($type + ['created_at' => now(), 'updated_at' => now()]);
        }
        $this->command->info('Sarpras types seeded!');

        $this->command->info('SARPRAS master data seeded successfully!');
    }
}
