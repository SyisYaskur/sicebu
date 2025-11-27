@extends('layouts.admin.app')
@section('title', 'Edit Data Siswa')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master Siswa /</span> Edit Lengkap</h4>
    @include('components.alert')

    <form action="{{ route('superadmin.students.update', $student->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- KOLOM KIRI: DATA PRIBADI & FISIK -->
            <div class="col-xl-8 col-lg-7">
                
                <!-- 1. Identitas Utama -->
                <div class="card mb-4">
                    <div class="card-header bg-light"><h5 class="mb-0"><i class="bx bx-user me-2"></i> Identitas Utama</h5></div>
                    <div class="card-body mt-3">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $student->full_name) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">NIS</label>
                                <input type="text" name="student_number" class="form-control" value="{{ old('student_number', $student->student_number) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">NISN</label>
                                <input type="text" name="national_student_number" class="form-control" value="{{ old('national_student_number', $student->national_student_number) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">NIK</label>
                                <input type="text" name="national_identification_number" class="form-control" value="{{ old('national_identification_number', $student->national_identification_number) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tempat, Tanggal Lahir</label>
                                <input type="text" name="birth_place_date" class="form-control" value="{{ old('birth_place_date', $student->birth_place_date) }}" placeholder="Contoh: Bandung, 20 Mei 2008">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select" required>
                                    <option value="Laki-Laki" {{ $student->gender == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="Perempuan" {{ $student->gender == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Agama</label>
                                <select name="religion" class="form-select">
                                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu'] as $agama)
                                        <option value="{{ $agama }}" {{ $student->religion == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address', $student->address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Data Fisik & Hobi -->
                <div class="card mb-4">
                    <div class="card-header bg-light"><h5 class="mb-0"><i class="bx bx-body me-2"></i> Data Fisik & Minat</h5></div>
                    <div class="card-body mt-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Anak Ke-</label>
                                <input type="number" name="birth_order" class="form-control" value="{{ old('birth_order', $student->birth_order) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jml Saudara</label>
                                <input type="number" name="siblings" class="form-control" value="{{ old('siblings', $student->siblings) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tinggi (cm)</label>
                                <input type="number" name="height_cm" class="form-control" value="{{ old('height_cm', $student->height_cm) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Berat (kg)</label>
                                <input type="number" name="weight_kg" class="form-control" value="{{ old('weight_kg', $student->weight_kg) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hobi</label>
                                <input type="text" name="hobby" class="form-control" value="{{ old('hobby', $student->hobby) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Cita-cita</label>
                                <input type="text" name="aspiration" class="form-control" value="{{ old('aspiration', $student->aspiration) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Data Orang Tua -->
                <div class="card mb-4">
                    <div class="card-header bg-light"><h5 class="mb-0"><i class="bx bx-group me-2"></i> Data Orang Tua</h5></div>
                    <div class="card-body mt-3">
                        
                        {{-- AYAH --}}
                        <h6 class="fw-bold text-primary">Data Ayah</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Nama Ayah</label>
                                <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name', $student->guardian_name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Telepon (WA)</label>
                                <input type="text" name="guardian_phone" class="form-control" value="{{ old('guardian_phone', $student->guardian_phone) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pekerjaan</label>
                                <input type="text" name="guardian_occupation" class="form-control" value="{{ old('guardian_occupation', $student->guardian_occupation) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pendidikan</label>
                                <input type="text" name="guardian_education" class="form-control" value="{{ old('guardian_education', $student->guardian_education) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Penghasilan (Rp)</label>
                                <input type="number" name="guardian_income" class="form-control" value="{{ old('guardian_income', $student->guardian_income) }}">
                            </div>
                        </div>

                        <hr>

                        {{-- IBU --}}
                        <h6 class="fw-bold text-danger mt-3">Data Ibu</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Ibu</label>
                                <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name', $student->mother_name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" name="mother_phone" class="form-control" value="{{ old('mother_phone', $student->mother_phone) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pekerjaan</label>
                                <input type="text" name="mother_occupation" class="form-control" value="{{ old('mother_occupation', $student->mother_occupation) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pendidikan</label>
                                <input type="text" name="mother_education" class="form-control" value="{{ old('mother_education', $student->mother_education) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Penghasilan (Rp)</label>
                                <input type="number" name="mother_income" class="form-control" value="{{ old('mother_income', $student->mother_income) }}">
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- WALI (Optional) --}}
                <div class="card mb-4">
                    <div class="card-header bg-light"><h5 class="mb-0">Data Wali (Jika tidak dengan Ortu)</h5></div>
                    <div class="card-body mt-3">
                        <div class="row g-3">
                             <div class="col-md-6">
                                <label class="form-label">Nama Wali</label>
                                <input type="text" name="custodian_name" class="form-control" value="{{ old('custodian_name', $student->custodian_name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Telepon</label>
                                <input type="text" name="custodian_phone" class="form-control" value="{{ old('custodian_phone', $student->custodian_phone) }}">
                            </div>
                             <div class="col-md-6">
                                <label class="form-label">Pekerjaan</label>
                                <input type="text" name="custodian_occupation" class="form-control" value="{{ old('custodian_occupation', $student->custodian_occupation) }}">
                            </div>
                             <div class="col-md-6">
                                <label class="form-label">Pendidikan</label>
                                <input type="text" name="custodian_education" class="form-control" value="{{ old('custodian_education', $student->custodian_education) }}">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- KANAN: KELAS & AKSI -->
            <div class="col-xl-4 col-lg-5">
                
                {{-- Card Kelas --}}
                <div class="card mb-4">
                    <div class="card-header bg-label-primary text-primary"><h5 class="mb-0">Penempatan Kelas</h5></div>
                    <div class="card-body mt-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tahun Ajaran Aktif</label>
                            <input type="text" class="form-control" value="{{ $currentYear }}" readonly disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Kelas Saat Ini</label>
                            <select name="class_id" class="form-select select2">
                                <option value="">-- Tidak Masuk Kelas / Lulus --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" 
                                        {{ ($currentClassAssign && $currentClassAssign->class_id == $class->id) ? 'selected' : '' }}>
                                        {{ $class->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Pilih kelas untuk memindahkan siswa.</div>
                        </div>
                    </div>
                </div>

                {{-- Card Aksi (Sticky) --}}
                <div class="card shadow-lg border-primary" style="position: sticky; top: 100px;">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 btn-lg mb-2">
                            <i class="bx bx-save me-1"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('superadmin.students.show', $student->id) }}" class="btn btn-outline-secondary w-100">
                            Batal
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script>
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
    });
</script>
@endpush