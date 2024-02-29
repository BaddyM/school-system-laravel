<style>

    .confirm-modal {
        background: linear-gradient(1.3deg, rgb(91, 117, 163) 11.4%, rgb(68, 98, 128) 77%);
    }

    .yes-button{
        background: purple;
    }

    .no-button{
        background: red;
    }

    .yes-button,.no-button{
        color: white !important;
        transition: .3s all ease-in-out;
    }

    .yes-button:hover,.no-button:hover{
    background:green;  
    }

</style>
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content confirm-modal">
            <div class="modal-header justify-content-center text-white align-items-center">
                <h3><i class="bi  bi-exclamation-triangle"></i> Alert!</h3>
            </div>

            <div class="modal-body text-center">
                <h5 class="modal-title text-white">
                    Do you confirm the details?
                </h5>
            </div>

            <div class="modal-footer text-center justify-content-center mb-0">
                <div class="w-100 text-center d-flex justify-content-center" style="gap:30px;">
                    <button class="btn yes-button px-5 py-2 bg-gradient" id="yes-confirm">
                        Yes
                    </button>
                    <button class="btn no-button px-5 py-2 bg-gradient" id="no-confirm">
                        No
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
