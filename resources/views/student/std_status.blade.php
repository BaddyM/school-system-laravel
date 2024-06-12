@extends('common.header')

@section('title')
    Student Status
@endsection

@section('body')
    <div>
        <h5 class="mb-0 text-uppercase fw-bold text-center" style="color: purple;">Update/View Student Status</h5>
        <div>
            <div class="col-md-4">
                <label for="" class="fw-bold">Select Class</label>
                <select id="classname" class="form-select p-2 rounded-0">
                    @foreach ($classes as $class)
                    <option value="{{ $class->class }}">{{ $class->class }}</option>
                    @endforeach                        
                </select>{{-- Select Class --}}
            </div>
            <button class="submit-btn mt-3 bg-gradient" id="view-std-data" type="button">Submit</button>
        </div>

        <div class="mt-3 update-students-table-container">
            <table class="table" id="update-std-status-table">
                <thead>
                    <tr style="background: rgb(82, 82, 254); color:white;" class="bg-gradient">
                        <th scope="col" class="text-center"><input type="checkbox" id="check_all" name="check_all"></th>
                        <th scope="col">First Name</th>
                        <th scope="col">Middle Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Class</th>
                        <th scope="col">Stream</th>
                        <th scope="col">Status</th>
                        <th scope="col">Year_Of_Entry</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <form action="" class="mt-3 d-flex" style="gap:20px;">
                <div class="col-md-4">
                    <label for="" class="fw-bold">Select Category</label>
                    <select id="std_status" class="form-select p-2 rounded-0">
                        @foreach ($status as $s)
                        <option value="{{ $s->status }}">{{ $s->status }}</option>
                        @endforeach
                    </select>{{-- Select Student Status --}}
                </div>
                <button class="submit-btn mt-3 bg-gradient" id="update-std-status" type="button">update</button>
            </form>
        </div>
    </div>
@endsection

@push('body-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('/js/datatable.min.js') }}"></script>
    <script>
        //Hide the table container first
        $(".update-students-table-container").hide();

        function data_table(classname) {
                var category = status.toLowerCase();
                //VIEW STUDENTS DATATABLE 
                //Destroy the previous table and reinitialize
                $('#update-std-status-table').DataTable().destroy();

                //Create a new Datatable
                var updateTable = $('#update-std-status-table').DataTable({
                    serverSide: true,
                    processing: true,
                    ajax: {
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        data: {
                            classname: classname,
                        },
                        url: "{{ route('student.status.dt') }}"
                    },
                    columns: [
                        {
                            data: 'std_id',
                            render:function(data,type,row){
                                return '<input type="checkbox" value="'+data+'" name="std_id_box" id="std_id">';
                            },
                            orderable:false,
                        },
                        {
                            data: 'fname',
                            render:(data, type, row)=>{
                                return '<div class="text-uppercase">'+data+'</div>';
                            }
                        },
                        {
                            data: 'mname',
                            render:(data, type, row)=>{
                                if(data == '' || data == 'NULL' || data == null){
                                    var mname = '<div class="text-uppercase"></div>';
                                }else{
                                    var mname = '<div class="text-uppercase">'+data+'</div>';
                                }
                                return mname;
                            }
                        },
                        {
                            data: 'lname',
                            render:(data, type, row)=>{
                                lname = '<div class="text-uppercase">'+data+'</div>'
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
                                } else if (data == 'dismissed' || data == 'removed' || data == 'disabled') {
                                    display = '<span class="badge bg-danger"><p class="mb-0 h6">' +
                                        data + '</p></span>';
                                }
                                return display;
                            }
                        },
                        {
                            data: 'year_of_entry'
                        },
                    ],
                    columnDefs: [{
                            target: [0, 1, 2, 3, 4, 5, 6,7],
                            className: "dt-center"
                        }
                    ],
                });
            }

        $("#view-std-data").on('click',function(){
            var std_class = $('#classname').val();
            console.log("Class = "+std_class);
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route("student.status.dt") }}',
                data: {
                    std_class: std_class
                },
                dataType: 'json',
                success: (response) => {
                    $(".update-students-table-container").show();
                    $("#update-std-status-table").css('padding-top', '10px');
                    data_table(std_class);

                    //Disable the submit button
                    $("#view-std-data").removeClass('submit-btn').addClass(
                        'submit-btn-disabled').prop('disabled', true);
                },
                error:function(){
                    alert("Failed!");
                }
            });
        });

        //On change on the class input
        $("#classname").on('change',function(){
            //Enable the submit button
            $("#view-std-data").addClass('submit-btn').removeClass(
                'submit-btn-disabled').prop('disabled', false);
        });

        //Submit the selected inputs
        $("#update-std-status").on('click',function(e){
            e.preventDefault();
            var selected = [];
            var std_status = $("#std_status").val();

            //Check all selected values
            $('input[name="std_id_box"]:checked').each(function(){
                selected.push(this.value);
            });
            console.log("Selected = "+selected);

            //Save to DB
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route("student.status.update") }}',
                data: {
                    selected: selected,
                    std_status:std_status
                },
                success: (response) => {
                    alert(response);
                    $('#update-std-status-table').DataTable().draw();
                },
                error:function(response){
                    alert("Failed to Update Status");
                }
            });
        })
        
    </script>
@endpush
