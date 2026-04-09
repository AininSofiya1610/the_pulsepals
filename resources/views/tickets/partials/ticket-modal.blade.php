<!-- Ticket Details Modal (View - Read Only) -->
<div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shad-modal">
            <div class="modal-header border-bottom px-6 py-4">
                <h5 class="modal-title font-weight-bold text-gray-900" id="ticketModalLabel">
                    Ticket: <span id="modalTicketId"></span>
                </h5>
                <div class="d-flex align-items-center gap-2">
                    @can('update-ticket-status')
                    <button type="button" class="shad-btn shad-btn-outline shad-btn-sm" id="editTicketBtn">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </button>
                    @endcan
                    <button type="button" class="close w-auto h-auto p-0 m-0 ml-2 text-gray-400" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body px-6 py-4" id="ticketModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-3 text-gray-500">Loading ticket details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Ticket Modal -->
<div class="modal fade" id="editTicketModal" tabindex="-1" role="dialog" aria-labelledby="editTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shad-modal">
            <div class="modal-header border-bottom px-6 py-4">
                <h5 class="modal-title font-weight-bold text-gray-900" id="editTicketModalLabel">
                    Edit Ticket: <span id="editModalTicketId"></span>
                </h5>
                <button type="button" class="close w-auto h-auto p-0 m-0 text-gray-400" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-6 py-4" id="editTicketModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-3 text-gray-500">Loading edit form...</p>
                </div>
            </div>
            <div class="modal-footer border-top bg-gray-50 px-6 py-4">
                <button type="button" class="shad-btn shad-btn-ghost" data-dismiss="modal">Cancel</button>
                <button type="button" class="shad-btn shad-btn-primary ml-2" id="saveEditTicketBtn">
                    <i class="fas fa-save mr-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

