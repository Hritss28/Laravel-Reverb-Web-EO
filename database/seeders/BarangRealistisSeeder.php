<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangRealistisSeeder extends Seeder
{
    public function run(): void
    {
        // Data barang yang realistis berdasarkan kategori
        $barangData = $this->getBarangData();

        foreach ($barangData as $kategoriKode => $items) {
            $kategori = Kategori::where('kode', $kategoriKode)->first();
            
            if (!$kategori) {
                $this->command->warn("Kategori {$kategoriKode} tidak ditemukan!");
                continue;
            }

            foreach ($items as $item) {
                Barang::updateOrCreate(
                    ['kode_barang' => $item['kode_barang']], // Kriteria pencarian
                    array_merge($item, ['kategori_id' => $kategori->id]) // Data yang akan dimasukkan
                );
            }
        }

        $this->command->info('✅ Seeder barang realistis berhasil dijalankan!');
    }

    private function getBarangData(): array
    {
        return [
            'FUR' => [ // Furniture
                [
                    'nama' => 'Meja Kerja Executive',
                    'kode_barang' => 'FUR-001',
                    'deskripsi' => 'Meja kerja executive kayu jati dengan laci dan kunci',
                    'stok' => 5,
                    'harga_sewa_per_hari' => 50000,
                    'biaya_deposit' => 200000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Ruang Direktur',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Kursi Kantor Ergonomis',
                    'kode_barang' => 'FUR-002',
                    'deskripsi' => 'Kursi kantor ergonomis dengan sandaran punggung adjustable',
                    'stok' => 15,
                    'harga_sewa_per_hari' => 25000,
                    'biaya_deposit' => 100000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Ruang Kantor',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Lemari Arsip 4 Laci',
                    'kode_barang' => 'FUR-003',
                    'deskripsi' => 'Lemari arsip 4 laci dengan kunci pengaman',
                    'stok' => 8,
                    'harga_sewa_per_hari' => 30000,
                    'biaya_deposit' => 150000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Ruang Arsip',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Meja Rapat 12 Orang',
                    'kode_barang' => 'FUR-004',
                    'deskripsi' => 'Meja rapat kayu mahoni untuk 12 orang dengan kabel management',
                    'stok' => 2,
                    'harga_sewa_per_hari' => 100000,
                    'biaya_deposit' => 500000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Ruang Rapat Utama',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Rak Buku Kantor',
                    'kode_barang' => 'FUR-005',
                    'deskripsi' => 'Rak buku kantor 5 tingkat dengan backing panel',
                    'stok' => 10,
                    'harga_sewa_per_hari' => 20000,
                    'biaya_deposit' => 80000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Perpustakaan',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Sofa Ruang Tamu 3 Dudukan',
                    'kode_barang' => 'FUR-006',
                    'deskripsi' => 'Sofa kulit sintetis 3 dudukan untuk ruang tamu kantor',
                    'stok' => 3,
                    'harga_sewa_per_hari' => 75000,
                    'biaya_deposit' => 300000,
                    'kondisi' => 'rusak ringan',
                    'lokasi' => 'Lobby',
                    'tersedia' => true
                ]
            ],

            'ELK' => [ // Elektronik
                [
                    'nama' => 'Laptop Dell Latitude 5520',
                    'kode_barang' => 'ELK-001',
                    'deskripsi' => 'Laptop Dell Latitude 5520 Intel i7, 16GB RAM, 512GB SSD',
                    'stok' => 10,
                    'harga_sewa_per_hari' => 150000,
                    'biaya_deposit' => 8000000,
                    'kondisi' => 'baik',
                    'lokasi' => 'IT Storage',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Proyektor Epson EB-X41',
                    'kode_barang' => 'ELK-002',
                    'deskripsi' => 'Proyektor Epson EB-X41 3600 lumens XGA resolution',
                    'stok' => 5,
                    'harga_sewa_per_hari' => 200000,
                    'biaya_deposit' => 5000000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Ruang AV',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Printer HP LaserJet Pro M404dn',
                    'kode_barang' => 'ELK-003',
                    'deskripsi' => 'Printer laser monochrome dengan duplex printing',
                    'stok' => 7,
                    'harga_sewa_per_hari' => 75000,
                    'biaya_deposit' => 3000000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Ruang Cetak',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Monitor LG 24 Inch 4K',
                    'kode_barang' => 'ELK-004',
                    'deskripsi' => 'Monitor LG 24 inch 4K UHD dengan USB-C connection',
                    'stok' => 12,
                    'harga_sewa_per_hari' => 100000,
                    'biaya_deposit' => 4000000,
                    'kondisi' => 'baik',
                    'lokasi' => 'IT Storage',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Sound System Portable JBL',
                    'kode_barang' => 'ELK-005',
                    'deskripsi' => 'Sound system portable JBL dengan microphone wireless',
                    'stok' => 3,
                    'harga_sewa_per_hari' => 250000,
                    'biaya_deposit' => 2500000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Ruang Audio',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Kamera Canon EOS R5',
                    'kode_barang' => 'ELK-006',
                    'deskripsi' => 'Kamera mirrorless Canon EOS R5 dengan lens 24-70mm',
                    'stok' => 2,
                    'harga_sewa_per_hari' => 500000,
                    'biaya_deposit' => 45000000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Studio Foto',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Tablet iPad Pro 12.9 inch',
                    'kode_barang' => 'ELK-007',
                    'deskripsi' => 'iPad Pro 12.9 inch dengan Apple Pencil dan keyboard',
                    'stok' => 6,
                    'harga_sewa_per_hari' => 200000,
                    'biaya_deposit' => 15000000,
                    'kondisi' => 'rusak ringan',
                    'lokasi' => 'IT Storage',
                    'tersedia' => false
                ]
            ],

            'ATK' => [ // Alat Tulis Kantor
                [
                    'nama' => 'Mesin Fotocopy Canon iR2625',
                    'kode_barang' => 'ATK-001',
                    'deskripsi' => 'Mesin fotocopy Canon iR2625 dengan fitur scan dan print',
                    'stok' => 3,
                    'harga_sewa_per_hari' => 300000,
                    'biaya_deposit' => 15000000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Ruang Fotocopy',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Whiteboard Magnetic 120x90cm',
                    'kode_barang' => 'ATK-002',
                    'deskripsi' => 'Whiteboard magnetic dengan frame aluminum dan pen holder',
                    'stok' => 8,
                    'harga_sewa_per_hari' => 25000,
                    'biaya_deposit' => 200000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Ruang Rapat',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Flipchart Stand Portable',
                    'kode_barang' => 'ATK-003',
                    'deskripsi' => 'Flipchart stand portable dengan magnetic surface',
                    'stok' => 5,
                    'harga_sewa_per_hari' => 35000,
                    'biaya_deposit' => 150000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Ruang Training',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Shredder Dokumen Fellowes',
                    'kode_barang' => 'ATK-004',
                    'deskripsi' => 'Paper shredder cross-cut dengan kapasitas 12 lembar',
                    'stok' => 4,
                    'harga_sewa_per_hari' => 50000,
                    'biaya_deposit' => 1500000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Ruang Sekretariat',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Laminating Machine A3',
                    'kode_barang' => 'ATK-005',
                    'deskripsi' => 'Mesin laminating A3 dengan thermal system',
                    'stok' => 2,
                    'harga_sewa_per_hari' => 75000,
                    'biaya_deposit' => 2000000,
                    'kondisi' => 'perlu perbaikan',
                    'lokasi' => 'Ruang Cetak',
                    'tersedia' => false
                ]
            ],

            'KND' => [ // Kendaraan
                [
                    'nama' => 'Toyota Avanza 2022',
                    'kode_barang' => 'KND-001',
                    'deskripsi' => 'Toyota Avanza 2022 7 seater dengan AC dan audio system',
                    'stok' => 2,
                    'harga_sewa_per_hari' => 400000,
                    'biaya_deposit' => 5000000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Parkir Kantor',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Honda Vario 160cc',
                    'kode_barang' => 'KND-002',
                    'deskripsi' => 'Honda Vario 160cc matik dengan smart key system',
                    'stok' => 5,
                    'harga_sewa_per_hari' => 100000,
                    'biaya_deposit' => 2000000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Parkir Motor',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Isuzu Elf Pickup',
                    'kode_barang' => 'KND-003',
                    'deskripsi' => 'Isuzu Elf pickup untuk transportasi barang',
                    'stok' => 1,
                    'harga_sewa_per_hari' => 600000,
                    'biaya_deposit' => 8000000,
                    'kondisi' => 'rusak ringan',
                    'lokasi' => 'Parkir Belakang',
                    'tersedia' => false
                ]
            ],

            'PRL' => [ // Peralatan
                [
                    'nama' => 'Generator Listrik 5000W',
                    'kode_barang' => 'PRL-001',
                    'deskripsi' => 'Generator listrik portable 5000W dengan engine Honda',
                    'stok' => 2,
                    'harga_sewa_per_hari' => 350000,
                    'biaya_deposit' => 15000000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Gudang Teknik',
                    'tersedia' => true
                ],
                [
                    'nama' => 'AC Portable 1.5 PK',
                    'kode_barang' => 'PRL-002',
                    'deskripsi' => 'AC portable 1.5 PK dengan remote control dan timer',
                    'stok' => 6,
                    'harga_sewa_per_hari' => 150000,
                    'biaya_deposit' => 4000000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Ruang AC',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Vacuum Cleaner Industrial',
                    'kode_barang' => 'PRL-003',
                    'deskripsi' => 'Vacuum cleaner industrial untuk cleaning service',
                    'stok' => 3,
                    'harga_sewa_per_hari' => 100000,
                    'biaya_deposit' => 2500000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Ruang Cleaning',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Pressure Washer Karcher',
                    'kode_barang' => 'PRL-004',
                    'deskripsi' => 'Pressure washer Karcher untuk pembersihan luar ruangan',
                    'stok' => 2,
                    'harga_sewa_per_hari' => 200000,
                    'biaya_deposit' => 6000000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Gudang Alat',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Stepladder Aluminum 3 Meter',
                    'kode_barang' => 'PRL-005',
                    'deskripsi' => 'Tangga lipat aluminum 3 meter dengan safety lock',
                    'stok' => 4,
                    'harga_sewa_per_hari' => 50000,
                    'biaya_deposit' => 800000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Gudang Alat',
                    'tersedia' => true
                ],
                [
                    'nama' => 'Water Dispenser Hot & Cold',
                    'kode_barang' => 'PRL-006',
                    'deskripsi' => 'Water dispenser dengan fitur hot dan cold water',
                    'stok' => 8,
                    'harga_sewa_per_hari' => 25000,
                    'biaya_deposit' => 1500000,
                    'kondisi' => 'baik',
                    'lokasi' => 'Pantry',
                    'tersedia' => true
                ]
            ]
        ];
    }
}
