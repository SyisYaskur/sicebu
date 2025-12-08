<div class="modal fade" id="deleteModal-{{ $income->id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $income->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel-{{ $income->id }}">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      {{-- REVISI: Tambahkan class 'text-wrap' dan 'text-center' agar rapi --}}
      <div class="modal-body text-center text-wrap" style="word-break: break-word;">
        <p class="mb-1">Apakah Anda yakin ingin menghapus data pemasukan sebesar:</p>
        
        <h4 class="text-danger my-2">Rp {{ number_format($income->amount, 0, ',', '.') }}</h4>
        
        <p class="mb-0">
            pada tanggal <strong>{{ $income->date->format('d M Y') }}</strong>?
        </p>
        
        @if($income->description)
            <div class="mt-3 p-2 bg-light rounded text-muted small fst-italic">
                "{{ $income->description }}"
            </div>
        @endif
      </div>

      <div class="modal-footer justify-content-center"> {{-- Tombol di tengah --}}
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