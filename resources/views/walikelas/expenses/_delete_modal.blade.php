<!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteModal-{{ $expense->id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $expense->id }}" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel-{{ $expense->id }}">Konfirmasi Hapus</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Apakah Anda yakin ingin menghapus data pengeluaran untuk:
            <br><strong>"{{ $expense->description }}"</strong>
            <br>sebesar <strong>Rp {{ number_format($expense->amount, 0, ',', '.') }}</strong>?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <form action="{{ route('walikelas.expenses.destroy', $expense->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </form>
          </div>
        </div>
      </div>
    </div>