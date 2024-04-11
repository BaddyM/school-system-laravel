@extends('common.header')

@section('title')
    Add Student
@endsection

@section('body')
    @include('student.confirm')
    <div class="body-container">
        <h5 class="mb-0 text-uppercase fw-bold text-center" style="color: purple;">Add Student Bio Data</h5>

        <form method="post" class="mt-3" id="add_student_form" enctype="multipart/form-data">
            @csrf
            <!-------------------------------------- STUDENT INFORMATION --------------------------------->
            <h5 class="text-primary fw-bold">1. Student Details</h5>

            <div class="add-student">
                <div class="mb-3">
                    <div class="col-md-3">
                        <label for="" class="form-label">First Name <span style="color:red;">*</span></label>
                        <input type="text" placeholder="Enter First Name" name="fname" id="fname"
                            class="form-control rounded-0 p-2">
                    </div>

                    <div class="col-md-3">
                        <label for="" class="form-label">Middle Name</label>
                        <input type="text" placeholder="Enter Middle Name" name="mname" id="mname"
                            class="form-control rounded-0 p-2">
                    </div>

                    <div class="col-md-3">
                        <label for="" class="form-label">Last Name <span style="color:red;">*</span></label>
                        <input type="text" placeholder="Enter Last Name" name="lname" id="lname"
                            class="form-control rounded-0 p-2">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="col-md-5">
                        <label for="" class="form-label">House</label>
                        <input type="text" placeholder="Enter House" name="house" id="house"
                            class="form-control rounded-0 p-2">
                    </div>
                    <div class="col-md-5">
                        <label for="" class="form-label">Photo</label>
                        <input type="file" accept=".jpeg, .jpg, .png" name="std_image" id="std-image"
                            class="form-control rounded-0 p-2">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="col-md-3">
                        <label for="" class="form-label">Select Class <span style="color:red;">*</span></label>
                        <select name="std_class" class="form-select rounded-0 p-2">
                            @foreach ($classes as $class)
                                <option value="{{ $class->class }}">{{ $class->class }}</option>
                            @endforeach
                        </select>
                    </div>{{-- Select Student Class Here --}}

                    <div class="col-md-3">
                        <label for="" class="form-label">Select Student Stream <span
                                style="color:red;">*</span></label>
                        <select name="std_stream" class="form-select rounded-0 p-2">
                            @foreach ($streams as $stream)
                                <option value="{{ $stream->stream }}">{{ $stream->stream }}</option>
                            @endforeach
                        </select>
                    </div>{{-- Select Student Stream Here --}}

                    <div class="col-md-3">
                        <label for="" class="form-label">Combination <span class="text-danger fst-italic">(For A
                                Level Students)</span></label>
                        <input type="text" name="combination" placeholder="Enter Combination" id="combination"
                            class="form-control rounded-0 p-2">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="col-md-3">
                        <label for="" class="form-label">Select Section <span style="color:red;">*</span></label>
                        <select name="section" class="form-select rounded-0 p-2">
                            <?php 
                            $section = array('Boarding','Day');
                            $count = 0;
                            foreach($section as $sec){    
                            ?>
                            <option value="<?php echo $sec; ?>"> <?php $count++;
                            echo $count . '. ' . $sec; ?></option>
                            <?php } ?>
                        </select>
                    </div>{{-- Select Student Section Here --}}

                    <div class="col-md-3">
                        <label for="" class="form-label">Date of Birth</label>
                        <input type="text" style="cursor: pointer;" name="dob" placeholder="Enter DOB" id="dob"
                            class="form-control rounded-0 p-2">
                    </div>{{-- Select Student DOB Here --}}

                    <div class="col-md-3">
                        <label for="" class="form-label">LIN</label>
                        <input type="text" name="lin" placeholder="Enter Student LIN" id="lin"
                            class="form-control rounded-0 p-2">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="col-md-3">
                        <label for="" class="form-label">Residence</label>
                        <input type="text" name="residence" placeholder="Enter Residence" id="residence"
                            class="form-control rounded-0 p-2">
                    </div>{{-- Select Student Residence Here --}}

                    <div class="col-md-3">
                        <label for="" class="form-label">Select Gender <span style="color:red;">*</span></label>
                        <select name="gender" id="gender_value" class="form-select rounded-0 p-2">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="" class="form-label">Nationality <span style="color:red;">*</span></label>
                        <input type="text" name="nationality" placeholder="Enter Nationality" id="nationality"
                            class="form-control rounded-0 p-2">
                    </div>{{-- Select Student Nationality Here --}}
                </div>

                <!-------------------------------------- PARENT/GUARDIAN INFORMATION --------------------------------->
                <h5 class="text-primary fw-bold">2. Parent/Guardian Details</h5>

                <div class="mb-3">
                    <div class="col-md-3">
                        <label for="" class="form-label">Guardian/Parent's First Name</label>
                        <input type="text" name="gfname_1" placeholder="Enter First Name" id="gfname_1"
                            class="form-control rounded-0 p-2">
                    </div>{{-- Guardian/Parent First Name Here --}}

                    <div class="col-md-3">
                        <label for="" class="form-label">Guardian/Parent's Second Name</label>
                        <input type="text" name="glname_1" placeholder="Enter Second Name" id="glname_1"
                            class="form-control rounded-0 p-2">
                    </div>{{-- Guardian/Parent Second Name Here --}}

                    <div class="col-md-3">
                        <label for="" class="form-label">Occupation</label>
                        <input type="text" name="occupation" placeholder="Enter Occupation" id="occupation"
                            class="form-control rounded-0 p-2">
                    </div>{{-- Student Occupation Here --}}
                </div>

                <div class="mb-3">
                    <div class="col-md-3">
                        <label for="" class="form-label">Guardian/Parent's NIN</label>
                        <input type="text" name="nin" placeholder="Enter NIN" id="nin"
                            class="form-control rounded-0 p-2">
                    </div>{{-- Guardian/Parent NIN Here --}}

                    <div class="col-md-3">
                        <label for="" class="form-label">Guardian/Parent's Contact</label>
                        <input type="number" min="0" name="contact" placeholder="Enter Contact" id="contact"
                            class="form-control rounded-0 p-2">
                    </div>{{-- Guardian/Parent Contact Here --}}

                    <div class="col-md-3">
                        <label for="" class="form-label">Guardian/Parent's Relationship</label>
                        <input type="text" name="relationship" placeholder="Enter Relationship" id="relationship"
                            class="form-control rounded-0 p-2">
                    </div>{{-- Guardian/Parent Relationship Here --}}
                </div>

                <div class="justify-content-start mb-2">
                    <span class="text-danger fst-italic fw-bold">NOTE: Use "IMPORT" for insertion of multiple records</span>
                </div>

                <div class="d-flex justify-content-between align-items-center px-3">
                    <button class="submit-btn" id="add-std-btn" type="button"
                        title="Add Student Records">Submit</button>
                    <div>
                        <a class="nav-link submit-btn me-3" href="{{ route('student.import') }}" style="cursor: pointer;" id="print-std-data">Import</a>
                    </div>
                </div>
            </div>
        </form>

    </div>
@endsection

@push('body-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/datatable.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.bundle.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>

    <script>
        $(document).ready(() => {
            $('.body-container').hide().slideDown();

            //Date picker DOB
            $("#dob").datepicker({
                format: 'dd/mm/yyyy',
                startView: 2,
                autoclose:true
            });

            //Change the next/previous buttons
            $('.prev').html('<<');
            $('.next').html('>>');

            //Display the confirm modal
            $("#add-std-btn").on('click', () => {
                //Check if the inputs were filled
                var fname = $("#fname").val();
                var lname = $("#lname").val();
                var house = $("#house").val();
                var nationality = $("#nationality").val();
                var gender = $("#gender_value").val();

                //console.log("Fname = "+fname+", Lname = "+lname+", house = "+house+", Fees = "+initial_fees+", req = "+req);

                if (fname == "" || lname == "") {
                    alert("Enter Full Names");
                } else if (nationality == "") {
                    alert("Enter Student Nationality");
                } else if (gender == "") {
                    alert("Enter Student Gender");
                } else {
                    //Display the modal to continue to the next steps
                    $("#confirmModal").modal('show');
                }

            })

            //Yes comfirm
            $("#yes-confirm").on('click', () => {
                $("#confirmModal").modal('hide');
                var add_std_form = document.getElementById("add_student_form");
                var myForm = new FormData(add_std_form);
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('student.add.db') }}",
                    data: myForm,
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(response) {
                        console.log(response);
                        //Clear the form inputs
                        $("#add_student_form")[0].reset();
                        alert(response);
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            })

            //No confirm
            $("#no-confirm").on('click', () => {
                $("#confirmModal").modal('hide');
            })

        })
    </script>
@endpush
