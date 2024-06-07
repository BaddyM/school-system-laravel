<div class="modal fade" id="userProfileModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content bg-muted">
            <div class="modal-header justify-content-between bg-info">
                <div></div>
                <h5 class="modal-title text-uppercase fw-bold" id="modalTitleId">My Profile</h5>
                <div title="Close">
                    <button class="btn-close" type="button" role="close" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center align-items-center spinner_container">
                    <div style="color:purple;" class="spinner-border spinner-border-lg" role="status">
                    </div>
                </div>{{-- Spinner container --}}
                
                <div class="d-none user_form_container">
                    <form method="post" class="update_user_form">
                        @csrf
                        <input type="hidden" name="user_update_id">
                        <div class="mb-3">
                            <label class="form-label h6 fw-bold">Name</label>
                            <input type="text" class="form-control rounded-0" placeholder="Enter Name" name="username">
                        </div>
    
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-3 mb-3">
                                <label class="form-label h6 fw-bold">Gender: </label>
                                @php
                                    $gender = ['female', 'male'];
                                @endphp
                                <select name="gender" class="form-select rounded-0">
                                    @foreach ($gender as $g)
                                        <option value="{{ $g }}">{{ ucfirst($g) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label h6 fw-bold">Email</label>
                                <input type="email" class="form-control rounded-0" placeholder="Enter Email"
                                    name="email">
                            </div>
                        </div>
    
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-5 mb-3">
                                <label class="form-label h6 fw-bold">Password</label>
                                <div class="d-flex align-items-center justify-content-between">
                                    <input type="password" class="form-control rounded-0" placeholder="Password"
                                        name="password" id="update_password_home">
                                    <div class="bg-secondary form-control rounded-0 display_password"
                                        style="width:50px; cursor:pointer;">
                                        <i class="fa fa-eye"></i>
                                    </div>
                                </div>
                            </div>
    
                            <div class="col-md-5 mb-3">
                                <label class="form-label h6 fw-bold">Confirm Password</label>
                                <div class="d-flex align-items-center justify-content-between">
                                    <input type="password" class="form-control rounded-0" placeholder="Confirm Password"
                                        name="confirm_password" id="update_confirm_password_home">
                                    <div class="bg-secondary form-control rounded-0 display_confirm_password"
                                        style="width:50px; cursor:pointer;">
                                        <i class="fa fa-eye"></i>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- Password --}}
    
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-5 mb-3">
                                <img id="user_img" src="" style="cursor:pointer;"
                                    class="img-fluid img-thumbnail shadow-sm w-50" alt="">
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label h6 fw-bold">Status:</label>
                                <div>
                                    <div class="align-items-center d-flex mb-2">
                                        <span class="mb-0 h6">Active: </span>
                                        &nbsp;&nbsp;
                                        <input type="checkbox" name="active" class="form-control-check"
                                            style="width:20px; height:20px;">
                                    </div>
    
                                    <div class="align-items-center d-flex mb-2">
                                        <span class="mb-0 h6">Email Verified: </span>
                                        &nbsp;&nbsp;
                                        <input type="checkbox" name="verify_email" class="form-control-check"
                                            style="width:20px; height:20px;">
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <div class="d-flex justify-content-between">
                            <button title="Update" class="submit-btn px-4 bg-gradient rounded-0 text-uppercase mt-3"
                                value="" type="submit">update</button>
    
                            @if (Auth::user()->is_super_admin == 1)
                                <button title="Delete" class="btn btn-danger px-4 bg-gradient rounded-0 text-uppercase mt-3"
                                    value="" type="submit">delete</button>
                            @endif
                        </div>
    
                        <input type="hidden" name="department">
                        <input type="hidden" name="priviledge">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>{{-- My Profile Modal --}}

<div class="modal fade" id="updateImageModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    Update User Image
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" enctype="multipart/form-data" class="update_user_image_form">
                    @csrf
                    <input type="hidden" name="update_user_image_id">
                    <label class="form-label fw-bold align-items-center"> <i class="bi bi-cloud-plus-fill h4"></i> Add
                        New User Image</label>
                    <input type="file" accept=".jpeg, .png, .jpg" name="user_image"
                        class="form-control rounded-0" required>
                    <button class="submit-btn-disabled mt-3 update_img_btn" type="submit" disabled>Update</button>
                </form>
            </div>
        </div>
    </div>
</div>{{-- Change Image modal --}}
