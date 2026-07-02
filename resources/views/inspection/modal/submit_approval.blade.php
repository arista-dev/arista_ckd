 <div class="modal fade" id="submitApprovalModal" tabindex="-1">
     <div class="modal-dialog">
         {{-- <form method="POST" id="rejectForm">
             @csrf --}}

         <input type="hidden" name="action" value="reject">

         <div class="modal-content">

             <div class="modal-header">
                 <h5 class="modal-title">
                     Submit Inspection
                 </h5>

                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>

             <div class="modal-body">


                 <h5 class="fw-bold mb-1">Perhatian</h5>
                 <p class="text-muted mb-0" style="font-size:13px;">
                     Seluruh record akan dikunci dan tidak dapat diedit kembali. Pastikan data sudah benar sebelum
                     lanjut pengajuan approval.
                 </p>

             </div>

             <div class="modal-footer">

                 <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">
                     Cancel
                 </button>

                 <button class="btn btn-success" type="submit" name="action" value="submit" id="btnSubmitApproval">
                     Submit
                 </button>

             </div>

         </div>
         {{-- </form> --}}
     </div>
 </div>
