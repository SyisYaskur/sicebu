@extends('layouts.pengelola.app')
@section('title', 'Buat Penyaluran Dana')

@push('styles')
{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .amount-input { text-align: right; }
    /* Warna status */
    .status-match { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
    .status-short { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
    .status-over { background-color: #fff3cd; color: #664d03; border: 1px solid #ffecb5; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Penyaluran /</span> Buat Baru</h4>

    <form action="{{ route('pengelola.disbursements.store') }}" method="POST" id="disbursementForm" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Info Penyaluran</h5></div>
                    <div class="card-body">
                        @include('components.alert')
                        
                        <div class="mb-3">
                            <label class="form-label">Tujuan Penyaluran <span class="text-danger">*</span></label>
                            <input type="text" name="purpose" class="form-control" placeholder="Cth: Uang Orang Sakit" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="disbursement_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Penyaluran (Rp) <span class="text-danger">*</span></label>
                            <input type="text" name="total_amount" id="targetAmount" class="form-control amount-input fs-4 fw-bold text-primary" placeholder="0" required autocomplete="off">
                            <small class="text-muted">Masukkan jumlah total yang ingin disalurkan.</small>
                        </div>
                        
                        <div id="statusBox" class="p-3 rounded mb-3 text-center fw-bold d-none">
                            <span id="statusText"></span>
                            <div id="statusDiff" class="fs-5"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
    <label class="form-label">Bukti Penyaluran (Opsional)</label>
    <input type="file" name="proof_image" class="form-control">
    <div class="form-text">Format: JPG, PNG. Max: 2MB.</div>
</div>
                        <button type="submit" class="btn btn-primary w-100 mt-3" id="submitBtn" disabled>Simpan & Salurkan</button>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Sumber Dana (Ambil dari Kelas)</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addRowBtn">
                            <i class="bx bx-plus me-1"></i> Tambah Kelas
                        </button>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover" id="allocationTable">
                            <thead>
                                <tr>
                                    <th style="width: 45%">Pilih Kelas (Sumber)</th>
                                    <th style="width: 20%">Saldo Saat Ini</th>
                                    <th style="width: 30%">Jumlah Diambil (Rp)</th>
                                    <th style="width: 5%"></th>
                                </tr>
                            </thead>
                            <tbody id="allocationBody"></tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                         <span class="text-muted small">Total Terambil:</span>
                         <h4 class="mb-0 text-primary" id="currentTotalDisplay">Rp 0</h4>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Template Baris JS --}}
<template id="rowTemplate">
    <tr>
        <td>
            <select class="form-select class-select" style="width: 100%;" required>
                <option value="" disabled selected>-- Cari Kelas --</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" data-balance="{{ $class->current_balance }}">
                        {{ $class->search_label }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <span class="badge bg-label-secondary current-balance-display">Rp 0</span>
        </td>
        <td>
            <input type="text" class="form-control amount-input item-amount" placeholder="0" required>
            <div class="invalid-feedback" style="display:none;">Saldo Kurang!</div>
        </td>
        <td>
            <button type="button" class="btn btn-icon btn-label-danger remove-row"><i class="bx bx-trash"></i></button>
        </td>
    </tr>
</template>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('allocationBody');
    const rowTemplate = document.getElementById('rowTemplate');
    const addBtn = document.getElementById('addRowBtn');
    const targetInput = document.getElementById('targetAmount');
    const currentTotalDisplay = document.getElementById('currentTotalDisplay');
    const statusBox = document.getElementById('statusBox');
    const statusText = document.getElementById('statusText');
    const statusDiff = document.getElementById('statusDiff');
    const submitBtn = document.getElementById('submitBtn');
    
    let rowCount = 0;

    // Fungsi Format Angka (10000 -> 10.000)
    function formatNumber(n) {
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    // Fungsi Parse Angka (10.000 -> 10000)
    function parseNumber(n) {
        return parseFloat(n.replace(/\./g, '')) || 0;
    }

    // Inisialisasi Select2 pada elemen baru
    function initSelect2(element) {
        $(element).select2({
            theme: 'bootstrap-5',
            placeholder: '-- Cari Kelas (Ketik Nama/Tingkat) --',
            width: '100%'
        });
    }

    // Validasi & Hitung Total
    function calculate() {
        const target = parseNumber(targetInput.value);
        let currentTotal = 0;
        let allValid = true;

        document.querySelectorAll('.item-amount').forEach(input => {
            const val = parseNumber(input.value);
            const row = input.closest('tr');
            const select = row.querySelector('.class-select');
            const balanceBadge = row.querySelector('.current-balance-display');
            
            // Validasi Saldo Per Baris
            let maxBalance = 0;
            if (select.selectedIndex > 0) {
                maxBalance = parseFloat(select.options[select.selectedIndex].getAttribute('data-balance'));
            }

            if (val > maxBalance) {
                input.classList.add('is-invalid');
                row.querySelector('.invalid-feedback').style.display = 'block';
                allValid = false;
            } else {
                input.classList.remove('is-invalid');
                row.querySelector('.invalid-feedback').style.display = 'none';
            }

            currentTotal += val;
        });

        // Update Display Total
        currentTotalDisplay.innerText = 'Rp ' + currentTotal.toLocaleString('id-ID');

        // Update Status Box
        statusBox.classList.remove('d-none', 'status-match', 'status-short', 'status-over');
        
        const diff = target - currentTotal;

        if (target === 0) {
            statusBox.classList.add('d-none');
            submitBtn.disabled = true;
        } else if (diff === 0) {
            // PAS
            statusBox.classList.add('status-match');
            statusText.innerText = "STATUS: PAS";
            statusDiff.innerText = "Siap Disimpan";
            submitBtn.disabled = !allValid; // Enable jika saldo valid
        } else if (diff > 0) {
            // KURANG (Target > Ambil)
            statusBox.classList.add('status-short');
            statusText.innerText = "STATUS: KURANG";
            statusDiff.innerText = "Kurang Rp " + diff.toLocaleString('id-ID');
            submitBtn.disabled = true;
        } else {
            // LEBIH (Target < Ambil)
            statusBox.classList.add('status-over');
            statusText.innerText = "STATUS: BERLEBIH";
            statusDiff.innerText = "Lebih Rp " + Math.abs(diff).toLocaleString('id-ID');
            submitBtn.disabled = true;
        }
    }

    // Event Listener untuk Input Target
    targetInput.addEventListener('input', function(e) {
        this.value = formatNumber(this.value);
        calculate();
    });

    // Tambah Baris
    addBtn.addEventListener('click', function() {
        const clone = rowTemplate.content.cloneNode(true);
        const tr = clone.querySelector('tr');
        const select = tr.querySelector('select');
        const input = tr.querySelector('input');
        
        // Set name array
        select.name = `allocations[${rowCount}][class_id]`;
        input.name = `allocations[${rowCount}][amount]`;

        // Event Listener Input Uang di Baris
        input.addEventListener('input', function() {
            this.value = formatNumber(this.value);
            calculate();
        });

        // Event Listener Select Ganti
        // Karena Select2 menggunakan jQuery, kita pakai on change jQuery
        // Nanti di init
        
        tableBody.appendChild(tr);
        initSelect2(select); // Aktifkan Select2

        // Listener khusus Select2 change
        $(select).on('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const balance = selectedOption.getAttribute('data-balance');
            const row = this.closest('tr');
            row.querySelector('.current-balance-display').innerText = 'Rp ' + parseInt(balance).toLocaleString('id-ID');
            calculate(); // Re-validate saldo
        });

        rowCount++;
    });

    // Hapus Baris
    tableBody.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            e.target.closest('tr').remove();
            calculate();
        }
    });

    // Tambah baris pertama otomatis
    addBtn.click();
});
</script>
@endpush