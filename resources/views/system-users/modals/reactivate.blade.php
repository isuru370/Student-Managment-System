<!-- Reactivate Confirmation Modal -->
<div class="modal fade" id="reactivateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-check me-2"></i>Confirm Reactivation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-user-check fa-3x text-success mb-3"></i>
                </div>
                <h6 class="text-center">Are you sure you want to reactivate this user?</h6>
                <p class="text-muted text-center">They will be able to access the system again.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-success" id="confirmReactivateBtn">
                    <i class="fas fa-user-check me-2"></i>Reactivate User
                </button>
            </div>
        </div>
    </div>
</div>