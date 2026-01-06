<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sekolahs = [
            [
                'npsn' => '10100001',
                'nama' => 'SDN 1 Malinau Kota',
                'jenjang' => 'SD',
                'status' => 'Negeri',
                'alamat' => 'Jl. Jenderal Sudirman No. 1',
                'kelurahan' => 'Malinau Kota',
                'kecamatan' => 'Malinau Kota',
                'kabupaten' => 'Malinau',
                'provinsi' => 'Kalimantan Utara',
                'kode_pos' => '77511',
                'telepon' => '0553-21001',
                'email' => 'sdn1malinau@gmail.com',
                'kepala_sekolah' => 'Drs. Ahmad Yani',
                'nip_kepala_sekolah' => '196501011990031001',
                'tahun_berdiri' => 1985,
                'akreditasi' => 'A',
                'is_active' => true,
            ],
            [
                'npsn' => '10100002',
                'nama' => 'SDN 2 Malinau Kota',
                'jenjang' => 'SD',
                'status' => 'Negeri',
                'alamat' => 'Jl. Diponegoro No. 15',
                'kelurahan' => 'Malinau Kota',
                'kecamatan' => 'Malinau Kota',
                'kabupaten' => 'Malinau',
                'provinsi' => 'Kalimantan Utara',
                'kode_pos' => '77511',
                'telepon' => '0553-21002',
                'email' => 'sdn2malinau@gmail.com',
                'kepala_sekolah' => 'Hj. Siti Aminah, S.Pd',
                'nip_kepala_sekolah' => '197003051995032001',
                'tahun_berdiri' => 1990,
                'akreditasi' => 'B',
                'is_active' => true,
            ],
            [
                'npsn' => '10100003',
                'nama' => 'SMPN 1 Malinau',
                'jenjang' => 'SMP',
                'status' => 'Negeri',
                'alamat' => 'Jl. Pendidikan No. 5',
                'kelurahan' => 'Malinau Kota',
                'kecamatan' => 'Malinau Kota',
                'kabupaten' => 'Malinau',
                'provinsi' => 'Kalimantan Utara',
                'kode_pos' => '77511',
                'telepon' => '0553-21003',
                'email' => 'smpn1malinau@gmail.com',
                'kepala_sekolah' => 'Drs. Bambang Sutrisno, M.Pd',
                'nip_kepala_sekolah' => '196805201993031003',
                'tahun_berdiri' => 1975,
                'akreditasi' => 'A',
                'is_active' => true,
            ],
            [
                'npsn' => '10100004',
                'nama' => 'SMAN 1 Malinau',
                'jenjang' => 'SMA',
                'status' => 'Negeri',
                'alamat' => 'Jl. Kartini No. 10',
                'kelurahan' => 'Malinau Kota',
                'kecamatan' => 'Malinau Kota',
                'kabupaten' => 'Malinau',
                'provinsi' => 'Kalimantan Utara',
                'kode_pos' => '77511',
                'telepon' => '0553-21004',
                'email' => 'sman1malinau@gmail.com',
                'kepala_sekolah' => 'Dr. H. Muhammad Rizal, M.Pd',
                'nip_kepala_sekolah' => '197108151997031001',
                'tahun_berdiri' => 1980,
                'akreditasi' => 'A',
                'is_active' => true,
            ],
            [
                'npsn' => '10100005',
                'nama' => 'SMKN 1 Malinau',
                'jenjang' => 'SMK',
                'status' => 'Negeri',
                'alamat' => 'Jl. Industri No. 20',
                'kelurahan' => 'Malinau Kota',
                'kecamatan' => 'Malinau Kota',
                'kabupaten' => 'Malinau',
                'provinsi' => 'Kalimantan Utara',
                'kode_pos' => '77511',
                'telepon' => '0553-21005',
                'email' => 'smkn1malinau@gmail.com',
                'kepala_sekolah' => 'Ir. Hendra Wijaya, M.T',
                'nip_kepala_sekolah' => '197512101999031002',
                'tahun_berdiri' => 1995,
                'akreditasi' => 'B',
                'is_active' => true,
            ],
        ];

        foreach ($sekolahs as $sekolah) {
            Sekolah::create($sekolah);
        }
    }
}
