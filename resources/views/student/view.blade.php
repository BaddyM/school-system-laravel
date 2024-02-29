@extends('common.header')

@section('title')
    View Student
@endsection

<style>
    th {
        vertical-align: middle;
        font-size: 13px;
    }

    td {
        vertical-align: middle;
        font-size: 14px;
    }

    .std-img {
        border-radius: 60px;
    }
</style>

@section('body')
    <div>
        <h5 class="mb-0 text-uppercase fw-bold" style="color: purple;">View Student Bio Data</h5>

        <div class="mt-3 col-md-7">
            <div class="d-flex" style="gap:30px;">
                <div class="col-md-4">
                    <label for="" class="fw-bold">Select Class</label>
                    <select id="classname" class="form-select p-2 rounded-0">
                        <?php 
                        for($i=1; $i<=6; $i++){    
                    ?>
                        <option value="Senior <?php echo $i; ?>">Senior <?php echo $i; ?></option>
                        <?php } ?>
                    </select>{{-- Select Class --}}
                </div>
    
                <div class="col-md-4">
                    <label for="" class="fw-bold">Select Category</label>
                    <select id="category" class="form-select p-2 rounded-0">
                        @foreach ($status as $s)
                        <option value="{{ $s->status }}">{{ $s->status }}</option>
                        @endforeach
                    </select>{{-- Select Student Status --}}
                </div>
            </div>
        </div>{{-- Select Class here --}}

        <div class="button-container">
            <button class="submit-btn mt-3 bg-gradient" id="view-std-data" type="button">Submit</button>
            <button class="submit-btn mt-3 bg-gradient nav-link d-none" id="print-std-data" type="button">Print</button>
        </div>

        <div class="mt-3 update-students-table-container">
            <table class="table" id="update-students-table">
                <thead>
                    <tr style="background: rgb(82, 82, 254); color:white;" class="bg-gradient">
                        <th scope="col">ID</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Class</th>
                        <th scope="col">Stream</th>
                        <th scope="col">Image</th>
                        <th scope="col">Status</th>
                        <th scope="col">Year Of Entry</th>
                        <th scope="col">Combination</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>

    </div>

    @include('student.view_std_modal')
@endsection

@push('body-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/datatable.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.bundle.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            //Remove the Datatable display
            $(".update-students-table-container").hide();

            function data_table(classname,status) {
                var category = status.toLowerCase();
                //VIEW STUDENTS DATATABLE 
                //Destroy the previous table and reinitialize
                $('#update-students-table').DataTable().destroy()

                //Create a new Datatable
                var updateTable = $('#update-students-table').DataTable({
                    serverSide: true,
                    processing: true,
                    ajax: {
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        data: {
                            classname: classname,
                            category:category
                        },
                        url: "{{ route('data.fetch') }}"
                    },
                    columns: [
                        {
                            data: 'DT_RowIndex'
                        },
                        {
                            data: 'fname',
                            render:(data, type, row)=>{
                                return '<div class="text-uppercase">'+data+'</div>';
                            }
                        },
                        {
                            data: 'lname',
                            render:(data, type, row)=>{
                                var lname;
                                if(!(row.mname == null)){
                                    lname = '<div class="text-uppercase">'+row.mname+' '+data+'</div>'
                                }else{
                                    lname = '<div class="text-uppercase">'+data+'</div>'
                                }
                                return lname;
                            }
                        },
                        {
                            data: 'class'
                        },
                        {
                            data:'stream'
                        },
                        {
                            data: 'image',
                            render: function(data, type, row) {
                                var image;

                                if(data == "male.jpg" || data == 'NULL'){
                                    image = '<img src="{{ asset('/') }}images/static/male.jpg" class="img-fluid w-50 std-img" alt="' + row
                                    .fname + '">';
                                }else if(data == "female.jpg"){
                                    image = '<img src="{{ asset('/') }}images/static/female.jpg" class="img-fluid w-50 std-img" alt="' + row
                                    .fname + '">';
                                }else{
                                    image = '<img src="{{ asset('/') }}images/student_photos/' + row
                                    .year_of_entry + '/' + row.image +
                                    '" class="img-fluid w-50 std-img" alt="' + row
                                    .fname + '">'
                                }

                                return image;
                            }
                        },
                        {
                            data: 'status',
                            render: function(data, type, row) {
                                var display;
                                if (data == 'continuing') {
                                    display = '<span class="badge bg-success"><p class="mb-0 h6">' +
                                        data + '</p></span>';
                                } else if (data == 'completed') {
                                    display = '<span class="badge bg-info"><p class="mb-0 h6">' +
                                        data +
                                        '</p></span>';
                                } else if (data == 'suspended') {
                                    display =
                                        '<span class="badge bg-secondary"><p class="mb-0 h6">' +
                                        data + '</p></span>';
                                } else if (data == 'dismissed' || data == 'removed') {
                                    display = '<span class="badge bg-danger"><p class="mb-0 h6">' +
                                        data + '</p></span>';
                                }
                                return display;
                            }
                        },
                        {
                            data: 'year_of_entry'
                        },
                        {
                            data: 'combination',
                            render: function(data, type, row) {
                                if (data == null || data == 0) {
                                    return "-";
                                } else {
                                    return data;
                                }
                            }
                        },
                        {
                            data: 'action',
                            render:(data,type,row)=>{
                                return '<button type="button" class="more-btn bg-gradient" value="'+row.std_id+'">More</button>'
                            }
                        },
                    ],
                    columnDefs: [{
                            target: [5, 6, 7, 8],
                            className: "dt-center"
                        },
                        {
                            target: [3, 4],
                            width: '90px'
                        }
                    ],
                });
            }

            //Fetch the records for display in the DT
            $("#view-std-data").on('click', () => {
                //Display the print button
                $("#print-std-data").removeClass('d-none');

                var classname = $("#classname").val();
                var category = $("#category").val();

                console.log("Class = " +classname+", category = "+category);

                $.ajax({
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('data.fetch') }}',
                    data: {
                        classname: classname,
                        category:category
                    },
                    dataType: 'json',
                    success: (response) => {
                        $(".update-students-table-container").show();
                        $("#update-students-table").css('padding-top', '10px');
                        data_table(classname,category);

                        //Disable the submit button
                        $("#view-std-data").removeClass('submit-btn').addClass(
                            'submit-btn-disabled').prop('disabled', true);
                    }
                })

            });

            //Enable the submit button on Change
            $("#classname, #category").on('change', () => {
                $("#view-std-data").addClass('submit-btn').removeClass('submit-btn-disabled').prop(
                    'disabled', false);
            })

            //Autoupdate the table
            $(window).focus(function() {
                var display = $(".update-students-table-container").css('display');
                if(display == 'block'){
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: "{{ route('data.fetch') }}",
                        success: function() {
                            $("#update-students-table").DataTable().draw();
                        }
                    })
                }
            })

            //Fetch Student details
            $(document).on('click','.more-btn',function(){
                var std_id = $(this).val();

                //Add details to the inputs
                getStdDetails(std_id);
            });

            //ATM Money function
            function atm_money(value){
                var formatted = Intl.NumberFormat("en-US",{
                    maximumDecimalDigits:2,
                    minimumDecimalDigits:2
                })
                return formatted.format(value);
            }

            //var std_id = "74586718980";

            //Disable modal inputs by default
            $(".inner-elements input, .inner-elements select, #std_gender").prop('disabled',true).css('color','black');

            $("#update-std").on('click',function(){
               //Enable modal inputs
                $(".inner-elements input, .inner-elements select, #std_gender").prop('disabled',false).css('color','black'); 
                
                var update_btn_val = ($(this).text()).trim();

                if(update_btn_val == "UPDATE"){
                    $(this).text("DONE");
                }else if(update_btn_val == "DONE"){
                    //Enable modal inputs
                    $(".inner-elements input, .inner-elements select, #std_gender").prop('disabled',true).css('color','black'); 
                    $(this).text("UPDATE");

                    //Update the DB
                    updateDBRecords();
                }
            })

            //function to collect details
            function getStdDetails(std_id){
                $("#viewStudentModal").modal('show');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: "{{ route('data.fetch.modal') }}",
                    data:{
                        std_id:std_id
                    },
                    success: function(data) {
                        console.log(data);

                        if(data.mname != null){
                            $("#std_full_name").val(data.lname+" "+data.fname+" "+data.mname);
                        }else{
                            $("#std_full_name").val(data.lname+" "+data.fname);
                        }
                        $("#std_id").val(data.std_id);

                        if(data.image == 'male.jpg'){
                            $("#std_image").attr('src',`../images/static/male.jpg`);
                        }else if(data.image == 'female.jpg'){
                            $("#std_image").attr('src',`../images/static/female.jpg`);
                        }else{
                            $("#std_image").attr('src',`../images/student_photos/${data.year_of_entry}/${data.image}`);
                        }

                        var std_status = (data.status).toLowerCase();
                        //Punch Student details to modal
                        $("#std_fname").val(data.fname);
                        $("#std_lname").val(data.lname);                       
                        $("#std_class").val(data.class);
                        $("#std_house").val(data.house);
                        $("#std_section").val(data.section);
                        $("#std_status").val(std_status);
                        $("#std_stream").val(data.stream);
                        $("#std_gender").val(data.gender);
                        $("#std_fees").val(atm_money(data.fees));
                        $("#std_reg").val(atm_money(data.registration));
                        $("#std_mname").val(data.mname);
                        $("#std_year").val(data.year_of_entry);
                        $("#std_comb").val(data.combination);
                        $("#std_pass").val(data.password);
                        var total_fees = atm_money((data.fees) + (data.registration) + (data.requirements))
                        $("#std_total_fees").val(total_fees);
                        $("#created_at").val(convert_date(data.created_at));
                        $("#updated_at").val(convert_date(data.updated_at));
                    }
                })
            }

            //Function to Update the DB
            function updateDBRecords(){
                //Get Student Details
                var std_class = $("#std_class").val();
                var std_id = $("#std_id").val();
                var fname = $("#std_fname").val();
                var lname = $("#std_lname").val();
                var house = $("#std_house").val();
                var section = $("#std_section").val();
                var std_status = $("#std_status").val();
                var gender = $("#std_gender").val();
                var std_stream = $("#std_stream").val();
                var fees = ($("#std_fees").val()).replaceAll(",","");
                var mname = $("#std_mname").val();
                var year = $("#std_year").val();
                var combination = $("#std_comb").val();
                var password = $("#std_pass").val();

                var data = {
                    std_class:std_class,
                    std_id:std_id,
                    fname:fname,
                    lname:lname,
                    mname:mname,
                    house:house,
                    section:section,
                    std_status:std_status,
                    std_stream:std_stream,
                    fees:fees,
                    year:year,
                    combination:combination,
                    password:password,
                    gender:gender
                }

                console.log(data);

                /*
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: "{{ route('student.update') }}",
                    data:data,
                    success: function(data) {
                        console.log("Update Successfull");
                        //Update Table
                        $("#update-students-table").DataTable().draw();
                        setTimeout(function(){
                            $("#viewStudentModal").modal('hide');
                        },500);
                    }
                })
                */
            }

            //Reset Modal inputs on hide
            $("#viewStudentModal").on('hidden.bs.modal',function(){
                //Enable modal inputs by default
                $(".inner-elements input, .inner-elements select, #std_gender").prop('disabled',true).css('color','black'); 
                $("#update-std").text("UPDATE");
            })

            function convert_date(date){
                var new_date = new Date(date);

                //Date variables
                var month = new_date.getMonth();
                var week_day = new_date.getDay();
                var year = new_date.getFullYear();
                var day = new_date.getDate();

                //Time variables
                var hour = new_date.getHours();
                var mins = new_date.getMinutes();
                var secs = new_date.getSeconds();

                if(hour >= 0 && hour <=9){
                    hour = `0${hour}`;
                }else if(mins >= 0 && mins <=9){
                    mins = `0${mins}`;
                }else if(secs >= 0 && secs <=9){
                    secs = `0${secs}`;
                }

                const months = ['January','February','March','April','May','June','July',
                                'August','September','October','November','December'];

                const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday",
                                "Friday", "Saturday"];

                const full_date = `${days[week_day]}, ${day} ${months[month]}, ${year} at ${hour}:${mins}:${secs}`

                return full_date;
            }

            //Print the Records
            $("#print-std-data").on('click',function(){
                var class_name = $("#classname").val();
                var category = $("#category").val();

                console.log("Class = "+class_name+", Category = "+category);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "GET",
                    url: "/Student/std_print-data/"+class_name+"/"+category+"",
                    data:{
                        class_name:class_name,
                        category:category
                    },
                    success: function(data) {
                        var class_name = $("#classname").val();
                        var category = $("#category").val();
                        window.open("/Student/std_print-data/"+class_name+"/"+category+"",'_blank');
                    },
                    error:function(data){
                        alert("Printing Failed");
                    }
                })
            })

        });
        
    </script>
@endpush