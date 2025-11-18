<!-- Modal untuk Tampil Bukti (Versi Sederhana) -->
<div class="modal fade" id="proofModal" tabindex="-1" aria-labelledby="proofModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered"> {{-- Modal besar & tengah --}}
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="proofModalLabel">Bukti Pengeluaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      {{-- Body (tempat gambar) --}}
      <div class="modal-body text-center" style="overflow: auto; max-height: 70vh; background-color: #f8f9fa;">
        {{-- img-fluid membuat gambar responsif di dalam modal --}}
        <img id="modalProofImage" src="" alt="Bukti Pengeluaran" class="img-fluid">
      </div>
      
      {{-- REVISI FOOTER --}}
      <div class="modal-footer justify-content-between">
        {{-- Keterangan di kiri --}}
        <span id="modalDescription" class="text-muted small" style="max-width: 50%;"></span>
        
        {{-- Tombol di kanan --}}
        <div>
            {{-- Tombol "Buka di Tab Baru" (link <a>) --}}
            <a href="#" id="modalOpenNewTab" class="btn btn-secondary" target="_blank" rel="noopener noreferrer">
                <i class="bx bx-export me-1"></i> Buka di Tab Baru
            </a>
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
      {{-- AKHIR REVISI FOOTER --}}

    </div>
  </div>
</div>