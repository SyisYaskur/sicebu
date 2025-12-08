<!-- Modal Konfirmasi Hapus (Pengeluaran) -->
<div class="modal fade" id="deleteModal-{{ $expense->id }}" tabindex="-1"
    aria-labelledby="deleteModalLabel-{{ $expense->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel-{{ $expense->id }}">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Sama seperti pemasukan --}}
            <div class="modal-body text-center text-wrap" style="word-break: break-word;">
                <p class="mb-1">Apakah Anda yakin ingin menghapus data pengeluaran sebesar:</p>

                <h4 class="text-danger my-2">
                    Rp {{ number_format($expense->amount, 0, ',', '.') }}
                </h4>

                <p class="mb-0">
                    pada tanggal <strong>{{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}</strong>?
                </p>

                @if($expense->description)
                    <div class="mt-3 p-2 bg-light rounded text-muted small fst-italic">
                        "{{ $expense->description }}"
                    </div>
                @endif
            </div>

            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

                <form action="{{ route('walikelas.expenses.destroy', $expense->id) }}"
                      method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>

        </div>
    </div>
</div>
