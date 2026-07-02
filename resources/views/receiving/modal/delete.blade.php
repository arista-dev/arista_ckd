<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="deleteForm">
            @csrf

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalTitle">
                        Hapus Receiving
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <p class="text-muted mb-4">
                        Apakah Anda yakin ingin menghapus receiving ini?
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-1">Receiving No</label>
                        <div id="deleteReceivingNo" class="text-muted"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-1">Container No</label>
                        <div id="deleteContainerNo" class="text-muted"></div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold mb-1">Model</label>
                        <div id="deleteModel" class="text-muted"></div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
