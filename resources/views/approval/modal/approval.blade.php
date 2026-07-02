 <div class="modal fade" id="approveModal" tabindex="-1">
     <div class="modal-dialog">
         <form method="POST" id="approveForm">
             @csrf

             <input type="hidden" name="action" value="approve">

             <div class="modal-content">

                 <div class="modal-header">
                     <h5 class="modal-title">
                         Approve Inspection
                     </h5>

                     <button class="btn-close" data-bs-dismiss="modal">
                     </button>
                 </div>

                 <div class="modal-body">

                     <div class="mb-3">
                         <label class="form-label">
                             Inspection No
                         </label>

                         <input class="form-control" id="inspectionNo" readonly>
                     </div>

                     <div class="mb-3">
                         <label class="form-label">
                             VIN
                         </label>

                         <input type="text" name="vin" class="form-control" required maxlength="17"
                             placeholder="Input VIN">
                     </div>

                 </div>

                 <div class="modal-footer">

                     <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">
                         Cancel
                     </button>

                     <button class="btn btn-success">
                         Approve
                     </button>

                 </div>

             </div>
         </form>
     </div>
 </div>
