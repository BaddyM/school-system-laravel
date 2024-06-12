@extends('common.header')

@section('title')
    Promotion
@endsection

<style>
    tr {
        vertical-align: middle;
    }

    th,
    td {
        text-transform: capitalize !important;
    }
</style>

@section('body')
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Promote Students</h5>

        <div class="col-md-4 bg-white p-4 mb-3">
            <form method="post" id="select_promote_std_form">
                @csrf
                <div class="mb-3">
                    <p><span class="text-danger fst-italic fw-bold">Note</span> Promote starting from upper classes to lower classes!</p>
                    <label class="form-label fw-bold h6">Select Class <span class="badge purple-badge">current</span></label>
                    <select name="classname" class="form-select rounded-0" required>
                        @foreach ($classes as $class)
                            <option value="{{ $class->class }}">{{ $class->class }}</option>
                        @endforeach
                    </select>
                </div>

                <button class="submit-btn" type="submit">Select</button>
            </form>
        </div>

        <div class="student_container d-none">
            <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Student's List</h5>
            <form id="promote_std_form" method="post">
                @csrf
                <div class="mb-3 card">
                    <div class="card-body">
                        <table class="table table-hover table-responsive" id="std_list_table">
                            <thead>
                                <tr style="background: purple; color:white;">
                                    <th scope="col"><form><input type="checkbox" id="select_all" class="form-check-input rounded-0"
                                        style="height: 20px; width:20px;"></form></th>
                                    <th scope="col">Student Name</th>
                                    <th scope="col">Current Class</th>
                                </tr>
                            </thead>
                            <tbody>
    
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-8 bg-white p-4 row justify-content-between align-items-center">
                    <div class="mb-3 col-md-6">
                        <label class="form-label fw-bold h6">Select Class to Promote <span
                                class="badge blue-badge">next</span></label>
                        <select name="classname" class="form-select rounded-0" required>
                            @foreach ($classes as $class)
                                <option value="{{ $class->class }}">{{ $class->class }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label fw-bold h6">Select Academic Year</label>
                        <select name="year" class="form-select rounded-0" required>
                            @foreach ($academic_year as $year)
                                <option value="{{ $year->year }}">{{ $year->year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button class="submit-btn mt-3" type="submit">Promote</button>
            </form>
        </div>

        <div class="spinner_body d-none">
            <div class="d-flex justify-content-center align-items-center">
                <div style="color:purple;" class="spinner-border spinner-border-lg" role="status">
                </div>
            </div>
        </div>{{-- spinner --}}

    </div>
@endsection


@push('body-scripts')
    <script src="{{ asset('/') }}js/jquery.min.js"></script>
    <script src="{{ asset('/') }}js/datatable.min.js"></script>
    <script src="{{ asset('/') }}js/custom.js"></script>
    <script>
        $(document).ready(function() {
            //Fetch Student data
            $("#select_promote_std_form").on('submit', function(e) {
                e.preventDefault();

                //Empty previous set
                $("#std_list_table tbody").empty();

                $(".student_container").addClass('d-none');
                $(".spinner_body").removeClass('d-none');

                $.ajax({
                    type: "POST",
                    url: "{{ route('student.promote.list') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data) {
                        if(data.length > 0){
                            var row_data;
                            $.each(data, function(k, v) {
                                row_data = "<tr>\
                                                    <td><input type='checkbox' name='std_id[]' value='" + v.std_id + "' class='checkboxes form-check-input rounded-0' style='height: 20px; width:20px;'></td>\
                                                    <td style='text-transform:capitalize;'>" + v.lname + " " + ((v.mname ==
                                        null || v.mname == '' || v.mname == 'NULL') ?
                                    '' : v.mname) + " " + v.fname + "</td>\
                                                    <td>" + v.class + "</td>\
                                                </tr>";
                                $("#std_list_table tbody").append(row_data);
                            });
                        }else{
                            $("#std_list_table tbody").append("<tr>\
                                <td colspan=3 class='text-center'> <img style='width:100px;' class='fluid' src='/images/icon/empty_set.png'></td>\
                            </tr>");
                        }

                        $(".student_container").removeClass('d-none');
                        $(".spinner_body").addClass('d-none');
                    },
                    error: function() {
                        alert("Failed to Fetch Student Data!");
                    }
                });
            });

            //Promote
            $("#promote_std_form").on('submit', function(e) {
                e.preventDefault();

                $(".student_container").addClass('d-none');
                $(".spinner_body").addClass('d-none');
                $("#select_all").prop('checked',false);

                //Clear the Forms
                $("form")[0].reset();

                $.ajax({
                    type: "POST",
                    url: "{{ route('student.promote.promote') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data) {
                        alert(data);
                    },
                    error: function() {
                        alert("Failed to Promote Student!");
                    }
                });
            });

            //Select all
            $("#select_all").on('change',function(){
                const checked_val = $(this).prop('checked');
                if(checked_val == true){
                    $(".checkboxes").prop('checked',true);
                }else{
                    $(".checkboxes").prop('checked',false);
                }
            });
        });
    </script>
@endpush
