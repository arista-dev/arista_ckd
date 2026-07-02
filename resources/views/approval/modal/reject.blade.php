 <div class="modal fade" id="rejectModal" tabindex="-1">
     <div class="modal-dialog">
         <form method="POST" id="rejectForm">
             @csrf

             <input type="hidden" name="action" value="reject">

             <div class="modal-content">

                 <div class="modal-header">
                     <h5 class="modal-title" id="rejectModalTitle">
                         Reject Inspection
                     </h5>

                     <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                 </div>

                 <div class="modal-body">

                     <div class="mb-3">
                         <h5 class="fw-bold mb-1">Reject Approval</h5>
                         <p class="text-muted mb-0" style="font-size:13px;">
                             Reject inspection ini? Akan dikembalikan ke Inspector.
                         </p>
                     </div>

                     <div class="mb-3">
                         <label for="rejection_reason" class="form-label fw-semibold">
                             Rejection Reason <span class="text-danger">*</span>
                         </label>

                         <textarea id="rejection_reason" name="rejection_reason" class="form-control" rows="4" maxlength="500"
                             placeholder="Contoh: Qty komponen tidak sesuai dengan hasil inspeksi, mohon lakukan pengecekan ulang." required></textarea>

                         <small class="text-muted">
                             Maksimal 500 karakter.
                         </small>
                     </div>

                 </div>

                 <div class="modal-footer">

                     <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">
                         Cancel
                     </button>

                     <button class="btn btn-danger">
                         Reject
                     </button>

                 </div>

             </div>
         </form>
     </div>
 </div>
