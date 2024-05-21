@extends('common.header')

@section('title')
    User
@endsection

@section('body')
    <style>
        #update_user_form input,
        #update_user_form select {
            color: purple;
            font-weight: bold;
        }
    </style>

    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Users</h5>

        <div class="mb-4 text-end">
            <button name="add_user_btn" class="submit-btn fw-bold">Add User</button>
        </div><!-- button container -->

        <div class="modal fade" id="newUserModal" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
                <div class="modal-content bg-muted">
                    <div class="modal-header justify-content-between bg-info">
                        <div></div>
                        <h5 class="modal-title text-uppercase fw-bold" id="modalTitleId">Add New User</h5>
                        <div title="Close">
                            <button class="btn-close" type="button" role="close" data-bs-dismiss="modal"></button>
                        </div>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="add_new_user_form">
                            @csrf
                            <div class="row justify-content-between">
                                <div class="col-md-5 mb-3">
                                    <label class="form-label h6 fw-bold">Username</label>
                                    <input type="text" name="username" class="form-control rounded-0" placeholder="Enter Username" required>
                                </div>
    
                                <div class="col-md-5 mb-3">
                                    <label class="form-label h6 fw-bold">Department</label>
                                    <select name="department" class="form-select rounded-0" placeholder="Select Department" required>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->dept }}">{{ ucfirst($department->dept) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row justify-content-between">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label h6 fw-bold">Email</label>
                                    <input type="email" name="email" class="form-control rounded-0" placeholder="Enter Email" required>
                                </div>
    
                                <div class="col-md-5 mb-3">
                                    <label class="form-label h6 fw-bold">Gender</label>
                                    <select name="gender" class="form-select rounded-0" placeholder="Select Gender" required>
                                        @php
                                            $gender = array('male','female')
                                        @endphp
                                        @foreach ($gender as $g)
                                            <option value="{{ $g }}">{{ ucfirst($g) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-5 mb-3">
                                    <label class="form-label h6 fw-bold">Password</label>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <input type="password" class="form-control rounded-0" placeholder="Password"
                                            name="password">
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
                                            name="confirm_password">
                                        <div class="bg-secondary form-control rounded-0 display_confirm_password"
                                            style="width:50px; cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>{{-- Password --}}

                            <div class="mb-3">
                                <label class="form-label h6 fw-bold">Add Image</label>
                                <input type="file" accept=".jpg, .png, .jpeg" name="user_image" class="form-control rounded-0">
                            </div>

                            <button type="submit" class="submit-btn px-5">Add</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>{{-- Add New User Modal --}}

        <div class="modal fade" id="userModal" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
                <div class="modal-content bg-muted">
                    <div class="modal-header justify-content-between bg-info">
                        <div></div>
                        <h5 class="modal-title text-uppercase fw-bold" id="modalTitleId">User Account details</h5>
                        <div title="Close">
                            <button class="btn-close" type="button" role="close" data-bs-dismiss="modal"></button>
                        </div>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="update_user_form">
                            @csrf
                            <input type="hidden" name="user_update_id">
                            <div class="mb-3">
                                <label class="form-label h6 fw-bold">Name</label>
                                <input type="text" class="form-control rounded-0" placeholder="Enter Name"
                                    name="username">
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
                                    <label class="form-label h6 fw-bold">Account Priviledge: </label>
                                    <select name="priviledge" class="form-select rounded-0">
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->dept }}">{{ ucfirst($department->dept) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-5 mb-3">
                                    <label class="form-label h6 fw-bold">Department: </label>
                                    <select name="department" class="form-select rounded-0">
                                        @foreach ($position as $p)
                                            <option value="{{ $p->position }}">{{ ucfirst($p->position) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-5 mb-3">
                                    <label class="form-label h6 fw-bold">Password</label>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <input type="password" class="form-control rounded-0" placeholder="Password"
                                            name="password">
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
                                            name="confirm_password">
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
                                        </div>{{-- Active --}}

                                        <div class="align-items-center d-flex mb-2">
                                            <span class="mb-0 h6">Email Verified: </span>
                                            &nbsp;&nbsp;
                                            <input type="checkbox" name="verify_email" class="form-control-check"
                                                style="width:20px; height:20px;">
                                        </div>{{-- Email Verified --}}
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button title="Update" id="edit-btn"
                                    class="submit-btn px-4 bg-gradient rounded-0 text-uppercase mt-3" value=""
                                    type="submit">update</button>

                                @if (Auth::user()->is_super_admin == 1)
                                    <button title="Delete" id="delete-user-btn"
                                        class="btn btn-danger px-4 bg-gradient rounded-0 text-uppercase mt-3"
                                        value="" type="submit">delete</button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>{{-- Users Modal --}}

        <div class="modal fade" id="updateImageModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId"
            aria-hidden="true" >
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm"role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            Update User Image
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" enctype="multipart/form-data" id="update_user_image_form">
                            @csrf
                            <input type="hidden" name="update_user_image_id">
                            <label class="form-label fw-bold align-items-center"> <i class="bi bi-cloud-plus-fill h4"></i> Add New User Image</label>
                            <input type="file" accept=".jpeg, .png, .jpg" name="user_image" class="form-control rounded-0" required>
                            <button class="submit-btn-disabled mt-3" type="submit" id="update_img_btn" disabled>Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>{{-- Change Image modal --}}        

        <div class="card rounded-0 border-0 shadow-sm">
            <div class="card-body overflow-scroll">
                <table class="table table-hover" id="users-table">
                    <thead>
                        <tr class="table-light">
                            <th scope="col">#</th>
                            <th scope="col">Username</th>
                            <th scope="col">Email</th>
                            <th scope="col">Priviledge</th>
                            <th scope="col">Status</th>
                            @if (Auth::user()->is_admin == 1 || Auth::user()->is_super_admin == 1)
                                <th scope="col">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $counter = 0;
                        @endphp
                        @foreach ($users as $user)
                            <tr style="vertical-align: middle;">
                                @php
                                    $colors = ['text-primary', 'text-danger', 'text-info', 'text-success', 'text-dark'];
                                @endphp

                                <td>{{ $counter += 1 }}</td>
                                <td class="text-uppercase" style="width:200px;"> <i
                                        class="fa fa-user {{ $colors[array_rand($colors)] }}"></i> {{ $user->username }}
                                </td>
                                <td class="fw-bold {{ $colors[array_rand($colors)] }}">{{ $user->email }}</td>
                                <td>
                                    @if ($user->is_admin == 1)
                                        <span class="badge bg-primary">admin</span>
                                    @elseif($user->is_super_admin == 1)
                                        <span class="badge bg-success">super admin</span>
                                    @elseif($user->is_teacher == 1)
                                        <span class="badge" style="background:purple;">teacher</span>
                                    @elseif($user->is_bursar == 1)
                                        <span class="badge bg-warning text-dark">bursar</span>
                                    @elseif($user->is_librarian == 1)
                                        <span class="badge bg-info">librarian</span>
                                    @else
                                        <span class="badge bg-secondary">other</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($user->is_active == 1)
                                        <span class="badge"
                                            style="background: linear-gradient(to bottom,  #09203f, #537895);">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                @if (Auth::user()->is_admin == 1 || Auth::user()->is_super_admin == 1)
                                    <td>
                                        <form method="post" class="mb-0 view_user_form">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <button data-bs-toggle="tooltip" data-bs-placement="left" title="More"
                                                class="border-0 btn btn-outline-primary"><i class="fa fa-list"></i></button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('body-scripts')
    <script src="{{ asset('') }}js/jquery.min.js"></script>
    <script src="{{ asset('') }}js/datatable.min.js"></script>
    <script src="{{ asset('') }}js/bootstrap.bundle.js"></script>
    <script>
        $(document).ready(function() {
            //Datatable
            $("#users-table").DataTable();

            //Show User info
            $(".view_user_form").on('submit', function(e) {
                e.preventDefault();
                //Clear the form
                $("#update_user_form")[0].reset();

                $.ajax({
                    type: "POST",
                    url: "{{ route('users.fetch') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(response) {
                        var id = response.id;
                        var username = response.username;
                        var email = response.email;
                        var dept = response.dept;
                        var is_active = response.is_active;
                        var email_verified = response.email_verified;
                        var image = response.image;
                        var gender = response.gender;

                        //Add data to the inputs
                        $("input[name='user_update_id']").val(id);
                        $("input[name='username']").val(username);
                        $("select[name='gender']").val(gender);
                        $("input[name='email']").val(email);
                        $("select[name='department']").val(dept);

                        if (response.is_admin == 1) {
                            $("select[name='priviledge']").val('admin');
                        } else if (response.is_teacher == 1) {
                            $("select[name='priviledge']").val('teacher');
                        } else if (response.is_bursar == 1) {
                            $("select[name='priviledge']").val('bursar');
                        } else if (response.is_librarian == 1) {
                            $("select[name='priviledge']").val('librarian');
                        } else {
                            $("select[name='priviledge']").val('other');
                        }

                        //Check image
                        if (image == null || image == "" || image == 'NULL') {
                            if (gender == 'male') {
                                var img_src = "/images/static/male.jpg";
                            } else if (gender == 'female') {
                                var img_src = "/images/static/female.jpg";
                            }

                            $("#user_img").attr('src', img_src);
                        } else {
                            var img_src = "/images/users/" + image + "";
                            $("#user_img").attr('src', img_src);
                        }

                        //User-Active
                        if (response.is_active == 1) {
                            $("input[name='active']").prop('checked', true);
                        } else {
                            $("input[name='active']").prop('checked', false);
                        }

                        //Email verified
                        if (response.email_verified == 1) {
                            $("input[name='verify_email']").prop('checked', true);
                        } else {
                            $("input[name='verify_email']").prop('checked', false);
                        }

                        //Show the modal
                        $("#userModal").modal('show');
                    },
                    error: function() {
                        alert("Failed to Fetch User!");
                    }
                });
            });

            //Update User
            $("#update_user_form").on('submit', function(e) {
                e.preventDefault();
                //Check password match
                var pass1 = $("input[name='password']").val();
                var pass2 = $("input[name='confirm_password']").val();

                if(pass1 == pass2){
                    //Hide the modal
                    $("#userModal").modal('hide');
                    $.ajax({
                        type: "POST",
                        url: "{{ route('users.update') }}",
                        data: new FormData(this),
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function(response) {
                            alert(response);
                            location.reload();
                        },
                        error: function() {
                            alert("Failed to Fetch User!");
                        }
                    });
                }else{
                    alert("Error:Password Mismatch!");
                }
                
            });

            //Display password
            $(".display_password").on('click',function(){
                var pass_prop = $("input[name='password']").prop('type');
                if(pass_prop == 'text'){
                    $("input[name='password']").prop('type','password');
                }else{
                    $("input[name='password']").prop('type','text');
                }
            });

            $(".display_confirm_password").on('click',function(){
                var pass_prop = $("input[name='confirm_password']").prop('type');
                if(pass_prop == 'text'){
                    $("input[name='confirm_password']").prop('type','password');
                }else{
                    $("input[name='confirm_password']").prop('type','text');
                }
            });

            //Change Image
            $("#user_img").on('click',function(){
                $("#userModal").modal('hide');
                //Clear the form
                $("#update_user_image_form")[0].reset();
                $("#updateImageModal").modal('show');
            });

            //Enable the image update button
            $("input[name='user_image']").on('change',function(){
                $("#update_img_btn").removeClass('submit-btn-disabled').addClass('submit-btn').prop('disabled',false);
            })

            $("#update_user_image_form").on('submit',function(e){
                e.preventDefault();
                var user_id = $("input[name='user_update_id']").val();
                $("input[name='update_user_image_id']").val(user_id);

                $.ajax({
                    type:'POST',
                    url:'{{ route("users.update.image") }}',
                    data: new FormData(this),
                    contentType:false,
                    processData:false,
                    cache:false,
                    success:function(response){
                        //console.log(response);
                        $("#updateImageModal").modal('hide');
                    },error:function(){
                        alert("Failed to Save Image");
                    }
                })
            });

            //Delete User
            $(".delete_user_form").on('submit', function(e) {
                e.preventDefault();
                const confirm_delete = confirm("Are you sure?");
                if (confirm_delete == true) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('stream.delete') }}",
                        data: new FormData(this),
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function(response) {
                            alert(response);
                            location.reload();
                        },
                        error: function() {
                            alert("Failed to Delete User!");
                        }
                    });
                }
            });

            //Add New User
            $('button[name="add_user_btn"]').on('click',function(){
                $("#newUserModal").modal('show');
            });

            $('#add_new_user_form').on('submit',function(e){
                e.preventDefault();

                //Check password match
                var pass1 = $("input[name='password']").val();
                var pass2 = $("input[name='confirm_password']").val();

                if(pass1 == pass2){
                    //Hide the modal
                    $("#newUserModal").modal('hide');
                    $.ajax({
                        type: "POST",
                        url: "{{ route('users.add') }}",
                        data: new FormData(this),
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function(response) {
                            alert(response);
                            location.reload();
                        },
                        error: function() {
                            alert("Failed to Fetch User!");
                        }
                    });
                }else{
                    alert("Error:Password Mismatch!");
                }
            });

        });
    </script>
@endpush
