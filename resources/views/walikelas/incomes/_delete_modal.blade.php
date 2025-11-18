<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal-{{ $income->id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $income->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel-{{ $income->id }}">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin menghapus data pemasukan sebesar 
        <strong>Rp {{ number_format($income->amount, 0, ',', '.') }}</strong> 
        pada tanggal <strong>{{ $income->date->format('d M Y') }}</strong>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <form action="{{ route('walikelas.incomes.destroy', $income->id) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>