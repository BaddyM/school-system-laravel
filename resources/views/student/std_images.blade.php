<style>
    .confirm-modal {
        background: linear-gradient(1.3deg, rgb(91, 117, 163) 11.4%, rgb(68, 98, 128) 77%);
    }

    .update-button {
        background: purple;
        color: white;
        font-weight: bold;
    }

    .update-button:hover {
        background: green;
        color: white;
    }

    .img_title {
        font-size: 20px;
        font-weight: bold;
    }

    .img_text p {
        font-size: 16px;
        color: white;
    }

    .cancel-btn {
        color: red;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
    }
</style>
<div class="modal fade" id="studentImageModal" tabindex="-1">
    <form action="post" id="student_update_form" enctype="multipart/form-data">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content confirm-modal">
                <div class="modal-header justify-content-between text-white align-items-center">
                    <p class="mb-0 img_title">Upload New Student Image</p>
                    <div title="Close" class="close-button">
                        <i class="bi bi-x cancel-btn"></i>
                    </div>
                </div>

                <div class="modal-body text-left">
                    <div class="img_text">
                        <p>Student Name: <span class="text-warning fw-bold" id="std_update_name"></span></p>
                        <p>Student Class: <span class="text-warning fw-bold" id="std_update_class"></span></p>
                    </div>
                    <input type="file" accept=".jpg, .jpeg, .png" class="form-control mt-1 w-70 rounded-0"
                        id="update-img" name="std_image">
                </div>

                <div class="modal-footer text-center justify-content-center mb-0">
                    <div class="w-100 text-center d-flex justify-content-center" style="gap:30px;">
                        <input type="hidden" id="std_img_id" name="std_img_id">
                        <button class="btn update-button px-5 py-2 bg-gradient d-none" id="update-std-image"
                            type="submit">
                            UPDATE
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
