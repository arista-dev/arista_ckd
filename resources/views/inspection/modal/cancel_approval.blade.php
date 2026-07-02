 <div class="modal fade" id="cancelApprovalModal" tabindex="-1">
     <div class="modal-dialog">
         {{-- <form method="POST" id="rejectForm">
             @csrf --}}

         <input type="hidden" name="action" value="reject">

         <div class="modal-content">

             <div class="modal-header">
                 <h5 class="modal-title">
                     Cancel Approval
                 </h5>

                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>

             <div class="modal-body">


                 <h5 class="fw-bold mb-1">Perhatian</h5>
                 <p class="text-muted mb-0" style="font-size:13px;">
                     Status akan diganti ke OPEN
                 </p>

             </div>

             <div class="modal-footer">

                 <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">
                     Cancel
                 </button>

                 <button class="btn btn-success" type="submit" name="action" value="submit" id="btnCancelApproval">
                     Submit
                 </button>

             </div>

         </div>
         {{-- </form> --}}
     </div>
 </div>
