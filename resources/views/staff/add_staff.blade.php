@extends('common.header')

@section('title')
    Add Staff
@endsection

@section('body')

<style>
    
</style>

<div class="container-fluid">
    <div class="modal fade" id="studentImportModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-md" role="document">
            <div class="modal-content bg-muted">
                <div class="modal-header justify-content-center bg-info">
                    <h5 class="modal-title text-uppercase fw-bold" id="modalTitleId">Response</h5>
                </div>
                <div class="modal-body">
                    <ol class="response">

                    </ol>
                </div>
            </div>
        </div>
    </div>{{-- Reponse Modal --}}
    
    <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Add Staff</h5>
    <form action="" method="post" id="add_staff_form">
        @csrf
        <div class="row justify-content-between mb-3">
            <div class="col-md-3">
                <label for="" class="form-label fw-bold">First Name <i style="color:red;">*</i></label>
                <input type="text" name="fname" class="form-control rounded-0" placeholder="First Name" required>
            </div>{{-- First Name --}}

            <div class="col-md-3">
                <label for="" class="form-label fw-bold">Middle Name </label>
                <input type="text" name="mname" class="form-control rounded-0" placeholder="Middle Name">
            </div>{{-- Middle Name --}}

            <div class="col-md-3">
                <label for="" class="form-label fw-bold">Last Name <i style="color:red;">*</i></label>
                <input type="text" name="lname" class="form-control rounded-0" placeholder="Last Name" required>
            </div>{{-- Last Name --}}
        </div>

        <div class="row justify-content-between mb-3">
            <div class="col-md-3">
                <label for="" class="form-label fw-bold">Position <i style="color:red;">*</i></label>
                <select name="position" class="form-select rounded-0" required>
                    @foreach ($positions as $position)
                        <option value="{{ $position->position }}">{{ ucfirst($position->position) }}</option>
                    @endforeach
                </select>
            </div>{{-- Position --}}

            <div class="col-md-3">
                <label for="" class="form-label fw-bold">Location </label>
                <input type="text" name="location" class="form-control rounded-0" placeholder="Location">
            </div>{{-- Location --}}

            <div class="col-md-3">
                <label for="" class="form-label fw-bold">Gender <i style="color:red;">*</i></label>
                <select name="gender" class="form-select rounded-0" required>
                    @php
                        $gender = array('Male','Female');
                    @endphp
                    @foreach ($gender as $g)
                        <option value="{{ $g }}">{{ $g }}</option>
                    @endforeach
                </select>
            </div>{{-- Gender --}}
        </div>

        <div class="row justify-content-between mb-3">
            <div class="col-md-3">
                <label for="" class="form-label fw-bold">Email ( <code>Required for user account</code> )</label>
                <input type="email" name="staff_email" class="form-control rounded-0" placeholder="Email">
            </div>{{-- Email --}}

            <div class="col-md-3">
                <label for="" class="form-label fw-bold">NIN </label>
                <input type="text" name="nin" class="form-control rounded-0" placeholder="NIN">
            </div>{{-- NIN --}}

            <div class="col-md-3">
                <label for="" class="form-label fw-bold">Image</label>
                <input type="file" accept=".png, .jpg, .jpeg" name="image" class="form-control rounded-0">
            </div>{{-- Image --}}
        </div>

        <div class="row justify-content-between mb-3">
            <div class="col-md-3">
                <label for="" class="form-label fw-bold">Contact <i style="color:red;">*</i></label>
                <input type="number" min="0" name="contact" class="form-control rounded-0" placeholder="Contact">
            </div>{{-- Contact --}}

            <div class="col-md-3">
                
            </div>

            <div class="col-md-3">
                
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <button class="submit-btn px-5" type="submit">Add</button>
            <button class="submit-btn px-5" href="" id="import_staff">Import</button>
        </div>
    </form>

    <div class="mt-4 mb-4 d-none import_section">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Upload Multiple Staff</h5>
        <div id="back" class="mb-3">
            <button title="Back" class="btn btn-outline-primary rounded-5"><i class="fa fa-arrow-left"></i></button>
        </div>{{--  back button --}}
        <div class="">
            <div style="position: relative; float:right;">
                <a href="{{ asset('/') }}imports/staff_import.xlsx" class="nav-link btn btn-warning rounded-0 py-2 px-4" download>Download Import File</a>
            </div>
        </div>
        <form action="" method="post" id="import_staff_form" class="shadow-lg p-4 col-md-4">
            @csrf
            <div class="mb-3">
                <label for="" class="form-label fw-bold">Upload the File</label>
                <input type="file" accept=".xlsx, .xls, .csv" class="form-control rounded-0" name="import_file">
            </div>

            <div class="mb-3">
                <label for="" class="form-label fw-bold">Position</label>
                <select class="form-select rounded-0" name="position">
                    @foreach ($positions as $position)
                        <option value="{{ $position->position }}">{{ ucfirst($position->position) }}</option>
                    @endforeach
                </select>
            </div>

            <button class="submit-btn-disabled px-4 " id="import_btn" type="submit" disabled>submit</button>
        </form>
    </div>
</div>

@endsection

@push('body-scripts')
    <script src="{{ asset('/') }}js/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            //Add single staff records
            $('#add_staff_form').on('submit', function(event) {
                event.preventDefault();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('staff.data.details') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data) {
                        var response = data.split(":");
                        if(response[0] == 'error'){
                            alert(response[1]);
                        }else{
                            //If response is a success
                            alert(response[1]);
                            $("#add_staff_form")[0].reset();
                            location.reload();
                        }
                    },
                    error: function(error) {
                        alert('Failed to Save Staff details');
                    }
                });
            });

            //Display the import section
            $("#import_staff").on('click',function(e){
                e.preventDefault();
                $(".import_section").removeClass('d-none').slideDown(1200);
                $("#add_staff_form").slideUp(1200);
            })

            //Dislay the single add staff section
            $("#back").on('click',function(e){
                e.preventDefault();
                $(".import_section").slideUp(1200);
                $("#add_staff_form").slideDown(1200);
            })

            //Enable the import button
            $("input[name='import_file']").on('change',function(){
                $("#import_btn").removeClass('submit-btn-disabled').addClass('submit-btn').prop('disabled',false);
            });

            //Add Multiple Staff
            $('#import_staff_form').on('submit', function(event) {
                $("#import_btn").addClass('submit-btn-disabled').removeClass('submit-btn').prop('disabled',true);
                event.preventDefault();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('staff.data.import') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data) {
                        $("#studentImportModal").modal('show');
                        var response = [];
                        $.each(data, function(k, v) {
                            //Get the last response from the string response
                            var last_val = (((v.split(" ")).length)-1);
                            var response_val = ((v.split(" "))[last_val]).toLowerCase();

                            if(response_val == 'exists'){
                                response.push("<li style='font-size:16px; color:red; '>" + v + "</li>");
                            }else{
                                response.push("<li style='font-size:16px;'>" + v + "</li>");
                            }
                        });

                        $(".response").empty();
                        $(".response").append(response);

                        $("#import_staff_form")[0].reset();
                        //location.reload();
                    },
                    error: function(error) {
                        alert('Failed to Import Staff details');
                    }
                });
            });
            
        });
    </script>
@endpush