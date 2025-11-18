<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="removeModal-{{ $student->id }}" tabindex="-1" aria-labelledby="removeModalLabel-{{ $student->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="removeModalLabel-{{ $student->id }}">Konfirmasi Pengeluaran Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin mengeluarkan siswa <strong>{{ $student->full_name }}</strong> dari kelas {{ $class->name }} untuk tahun ajaran {{ $class->academic_year }}?
        <br><small>(Siswa tidak dihapus permanen, hanya dikeluarkan dari kelas ini).</small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <form action="{{ route('walikelas.my-class.remove-student', $student->id) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Ya, Keluarkan</button>
        </form>
      </div>
    </div>
  </div>
</div>