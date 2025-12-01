@extends('frontend.layouts.app')

@section('title', 'Ajukan Peminjaman')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Ajukan Peminjaman</h1>
        <p class="mt-2 text-gray-600">Isi formulir berikut untuk mengajukan peminjaman barang</p>
    </div>

    <form method="POST" action="{{ route('frontend.peminjaman.store') }}" class="space-y-6" id="peminjaman-form">
        @csrf
        
        <!-- Debug info -->
        @if(config('app.debug'))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-sm text-yellow-800">
                <strong>Debug:</strong> Action URL: {{ route('frontend.peminjaman.store') }}
            </p>
        </div>
        @endif

        <!-- Display validation errors -->
        @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Pilih Barang -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Pilih Barang</h3>
            </div>
            <div class="px-6 py-4">
                @if($barang)
                    <!-- Barang sudah dipilih -->
                    <input type="hidden" name="barang_id" value="{{ $barang->id }}">
                    <div class="border border-gray-200 rounded-lg p-4 bg-blue-50">
                        <div class="flex items-center space-x-4">
                            @if($barang->foto)
                                <img src="{{ Storage::url($barang->foto) }}" alt="{{ $barang->nama }}" class="w-16 h-16 object-cover rounded-lg">
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900">{{ $barang->nama }}</h4>
                                <p class="text-sm text-gray-600">{{ $barang->kode_barang }} - {{ $barang->kategori->nama }}</p>
                                <p class="text-sm text-gray-600">Lokasi: {{ $barang->lokasi }}</p>
                                <p class="text-sm font-medium text-green-600">Stok Tersedia: {{ $barang->stok_tersedia }} unit</p>
                                @if($barang->harga_sewa_per_hari > 0 || $barang->biaya_deposit > 0)
                                <div class="mt-2 space-y-1">
                                    @if($barang->harga_sewa_per_hari > 0)
                                    <p class="text-sm font-medium text-blue-600">
                                        Harga Sewa: {{ $barang->formatted_harga_sewa }}/hari
                                    </p>
                                    @endif
                                    @if($barang->biaya_deposit > 0)
                                    <p class="text-sm font-medium text-purple-600">
                                        Deposit: {{ $barang->formatted_deposit }}
                                    </p>
                                    @endif
                                </div>
                                @endif
                            </div>
                            <a href="{{ route('frontend.barang.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                Ganti Barang
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Pilih Barang  -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Barang Belum Dipilih</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Anda harus memilih barang yang akan dipinjam terlebih dahulu sebelum melanjutkan.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('frontend.barang.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="mr-2 -ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Lihat Daftar Barang
                            </a>
                        </div>
                        <input type="hidden" name="barang_id" id="selected_barang_id" value="{{ old('barang_id') }}">
                        @error('barang_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>
        </div>

        <!-- Detail Peminjaman -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Detail Peminjaman</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <!-- Jumlah -->
                <div>
                    <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah yang Dipinjam</label>
                    <input type="number" name="jumlah" id="jumlah" min="1" max="{{ $barang ? $barang->stok_tersedia : '' }}" value="{{ old('jumlah', 1) }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('jumlah')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">
                        @if($barang)
                            Maksimal {{ $barang->stok_tersedia }} unit
                        @else
                            Sesuaikan dengan stok yang tersedia
                        @endif
                    </p>
                </div>

                <!-- Tanggal Pinjam -->
                <div>
                    <label for="tanggal_pinjam" class="block text-sm font-medium text-gray-700">Tanggal Pinjam</label>
                    <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" 
                           min="{{ date('Y-m-d') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('tanggal_pinjam')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Kembali -->
                <div>
                    <label for="tanggal_kembali_rencana" class="block text-sm font-medium text-gray-700">Tanggal Rencana Kembali</label>
                    <input type="date" name="tanggal_kembali_rencana" id="tanggal_kembali_rencana" 
                           value="{{ old('tanggal_kembali_rencana', date('Y-m-d', strtotime('+7 days'))) }}" 
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('tanggal_kembali_rencana')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1" id="durasi_info">Durasi: 7 hari</p>
                </div>

                <!-- Keperluan -->
                <div>
                    <label for="keperluan" class="block text-sm font-medium text-gray-700">Keperluan/Tujuan Peminjaman (Opsional)</label>
                    <textarea name="keperluan" id="keperluan" rows="4" placeholder="Jelaskan untuk apa barang ini akan digunakan (opsional)..." 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('keperluan') }}</textarea>
                    @error('keperluan')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">Maksimal 500 karakter (opsional)</p>
                </div>
            </div>
        </div>

        <!-- Ringkasan Biaya -->
        <div class="bg-white shadow rounded-lg" id="ringkasan-biaya" style="display: none;">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Ringkasan Biaya</h3>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Biaya Sewa (<span id="durasi-display">-</span> × <span id="jumlah-display">-</span> unit):</span>
                        <span class="text-sm font-medium" id="display-biaya-sewa">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Deposit (<span id="jumlah-deposit-display">-</span> unit):</span>
                        <span class="text-sm font-medium" id="display-biaya-deposit">-</span>
                    </div>
                    <hr class="my-3">
                    <div class="flex justify-between">
                        <span class="text-base font-semibold text-gray-900">Total Pembayaran:</span>
                        <span class="text-base font-bold text-blue-600" id="display-total-biaya">-</span>
                    </div>
                </div>
                <div class="mt-4 p-3 bg-yellow-50 rounded-md">
                    <p class="text-sm text-yellow-800">
                        <strong>Catatan:</strong> Deposit akan dikembalikan setelah barang dikembalikan dalam kondisi baik.
                        Denda keterlambatan 5% per hari dari total biaya sewa.
                    </p>
                </div>
            </div>
        </div>

        {{-- <!-- Terms & Conditions -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" required 
                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="font-medium text-gray-700">Saya setuju dengan syarat dan ketentuan</label>
                        <ul class="mt-2 text-gray-600 space-y-1">
                            <li>• Bertanggung jawab atas barang yang dipinjam</li>
                            <li>• Mengembalikan barang sesuai jadwal yang ditentukan</li>
                            <li>• Mengganti jika barang hilang atau rusak</li>
                            <li>• Menggunakan barang sesuai keperluan yang disebutkan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('frontend.barang.index') }}" 
               class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 focus:ring-2 focus:ring-gray-500">
                Batal
            </a>
            <button type="submit" id="submit-button"
                    class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 0h10a2 2 0 002-2v-3a2 2 0 00-2-2H9a2 2 0 00-2 2v3a2 2 0 002 2z"></path>
                </svg>
                Ajukan Peminjaman & Bayar
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - Inisialisasi aplikasi peminjaman');
    
    const form = document.querySelector('form');
    const barangId = document.querySelector('input[name="barang_id"]');
    const jumlah = document.getElementById('jumlah');
    const tanggalPinjam = document.getElementById('tanggal_pinjam');
    const tanggalKembali = document.getElementById('tanggal_kembali_rencana');
    const durasiInfo = document.getElementById('durasi_info');
    const ringkasanBiaya = document.getElementById('ringkasan-biaya');
    const submitButton = document.querySelector('button[type="submit"]');

    // Update minimum tanggal kembali saat tanggal pinjam berubah
    if (tanggalPinjam) {
        tanggalPinjam.addEventListener('change', function() {
            if (tanggalKembali) {
                const minReturn = new Date(this.value);
                minReturn.setDate(minReturn.getDate() + 1);
                tanggalKembali.min = minReturn.toISOString().split('T')[0];
                
                // Auto set return date to 7 days later if not set
                if (!tanggalKembali.value) {
                    const autoReturn = new Date(this.value);
                    autoReturn.setDate(autoReturn.getDate() + 7);
                    tanggalKembali.value = autoReturn.toISOString().split('T')[0];
                }
                updateDurasi();
                updateRingkasanBiaya();
            }
        });
    }

    // Event listeners untuk update ringkasan biaya
    [jumlah, tanggalPinjam, tanggalKembali].forEach(element => {
        if (element) {
            element.addEventListener('change', function() {
                updateDurasi();
                updateRingkasanBiaya();
            });
            element.addEventListener('input', function() {
                updateDurasi();
                updateRingkasanBiaya();
            });
        }
    });

    function updateDurasi() {
        if (tanggalPinjam && tanggalKembali && tanggalPinjam.value && tanggalKembali.value) {
            const start = new Date(tanggalPinjam.value);
            const end = new Date(tanggalKembali.value);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            if (durasiInfo) {
                durasiInfo.textContent = `Durasi: ${diffDays} hari`;
            }
        }
    }

    function updateRingkasanBiaya() {
        console.log('updateRingkasanBiaya called');
        
        if (!barangId || !barangId.value || !jumlah || !tanggalPinjam || !tanggalKembali) {
            console.log('Missing elements');
            return;
        }

        if (!jumlah.value || !tanggalPinjam.value || !tanggalKembali.value) {
            console.log('Missing values');
            if (ringkasanBiaya) {
                ringkasanBiaya.style.display = 'none';
            }
            if (submitButton) {
                submitButton.disabled = true;
            }
            return;
        }

        console.log('Fetching barang details...');
        
        // Route untuk mendapatkan detail barang
        const routeUrl = '{{ route("frontend.peminjaman.get-barang-details") }}';
        
        // Fetch detail barang dan hitung biaya
        fetch(routeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                barang_id: barangId.value,
                jumlah: jumlah.value,
                tanggal_pinjam: tanggalPinjam.value,
                tanggal_kembali: tanggalKembali.value
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Update tampilan ringkasan biaya
            const durasiDisplay = document.getElementById('durasi-display');
            const jumlahDisplay = document.getElementById('jumlah-display');
            const jumlahDepositDisplay = document.getElementById('jumlah-deposit-display');
            const displayBiayaSewa = document.getElementById('display-biaya-sewa');
            const displayBiayaDeposit = document.getElementById('display-biaya-deposit');
            const displayTotalBiaya = document.getElementById('display-total-biaya');
            
            if (durasiDisplay) durasiDisplay.textContent = data.perhitungan.jumlah_hari + ' hari';
            if (jumlahDisplay) jumlahDisplay.textContent = data.perhitungan.jumlah_barang + ' unit';
            if (jumlahDepositDisplay) jumlahDepositDisplay.textContent = data.perhitungan.jumlah_barang + ' unit';
            if (displayBiayaSewa) displayBiayaSewa.textContent = data.perhitungan.formatted_biaya_sewa;
            if (displayBiayaDeposit) displayBiayaDeposit.textContent = data.perhitungan.formatted_biaya_deposit;
            if (displayTotalBiaya) displayTotalBiaya.textContent = data.perhitungan.formatted_total;
            
            // Tampilkan ringkasan biaya
            if (ringkasanBiaya) {
                ringkasanBiaya.style.display = 'block';
            }
            if (submitButton) {
                submitButton.disabled = false;
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            if (ringkasanBiaya) {
                ringkasanBiaya.style.display = 'none';
            }
            if (submitButton) {
                submitButton.disabled = true;
            }
        });
    }

    // Validasi form sebelum submit
    if (form) {
        form.addEventListener('submit', function(event) {
            console.log('Form submitted');
            
            // Debug: Log form data
            const formData = new FormData(form);
            console.log('Form data:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            // Basic validation
            if (jumlah) {
                const max = parseInt(jumlah.getAttribute('max') || 0);
                const value = parseInt(jumlah.value || 0);
                
                if (max > 0 && value > max) {
                    event.preventDefault();
                    alert(`Jumlah maksimal yang dapat dipinjam adalah ${max} unit`);
                    jumlah.value = max;
                    jumlah.focus();
                    return false;
                }
                
                if (value < 1) {
                    event.preventDefault();
                    alert('Jumlah minimal peminjaman adalah 1 unit');
                    jumlah.value = 1;
                    jumlah.focus();
                    return false;
                }
            }
            
            // Validate required fields
            const barangIdInput = document.querySelector('input[name="barang_id"]');
            if (!barangIdInput || !barangIdInput.value) {
                event.preventDefault();
                alert('Silakan pilih barang terlebih dahulu');
                return false;
            }
            
            // Check if all required fields are filled
            const requiredFields = ['tanggal_pinjam', 'tanggal_kembali_rencana'];
            for (let fieldName of requiredFields) {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (!field || !field.value.trim()) {
                    event.preventDefault();
                    alert(`Field ${fieldName} harus diisi`);
                    if (field) field.focus();
                    return false;
                }
            }
            
            console.log('Form validation passed, submitting...');
            
            // Add a visual indicator that form is being submitted
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memproses...';
                
                // Re-enable after 10 seconds as fallback
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 0h10a2 2 0 002-2v-3a2 2 0 00-2-2H9a2 2 0 00-2 2v3a2 2 0 002 2z"></path></svg>Ajukan Peminjaman & Bayar';
                }, 10000);
            }
            
            // Form will proceed to submit
        });
    }

    // Validasi input jumlah
    if (jumlah) {
        // Pastikan hanya angka yang diinput
        jumlah.addEventListener('keypress', function(e) {
            const charCode = e.which ? e.which : e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                e.preventDefault();
                return false;
            }
            return true;
        });

        // Validasi saat nilai berubah
        jumlah.addEventListener('input', function() {
            const max = parseInt(this.getAttribute('max') || 0);
            const value = parseInt(this.value || 0);
            
            if (max > 0 && value > max) {
                this.value = max;
            }
            
            if (value < 1 && this.value !== '') {
                this.value = 1;
            }
        });
    }

    // Initial check
    updateDurasi();
    updateRingkasanBiaya();
});
</script>
@endsection