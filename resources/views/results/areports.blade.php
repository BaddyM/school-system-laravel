@extends('common.header')

@section('title')
    Alevel - Reports
@endsection

<style>
    tr {
        vertical-align: middle;
    }
</style>

@section('body')
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">A-level Reports</h5>

        <div class="bg-light p-3 col-md-2 mb-3 bg-white">
            <p class="h6 fst-italic text-danger fw-bold">Note: To print reports</p>
            <ol>
                <li>Select Class</li>
                <li>Select Students</li>
                <li>Select Tables</li>
                <li>Print Reports</li>
            </ol>
        </div>

        <div class="col-md-3">
            <form action="" method="post">
                <p class="fw-bold h6">Select Class</p>
                <select name="" id="classname" class="form-select rounded-0">
                    @foreach ($classes as $class)
                        <option value="{{ $class->class }}">
                            {{ $class->class }}
                        </option>
                    @endforeach
                </select>
                <button class="submit-btn mt-3" id="report_olevel">submit</button>
            </form>
        </div>{{-- select class --}}

        <div class="mt-5 d-none" id="table_container">
            <form action="">
                <p class="fw-bold h6">Select Students</p>
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Student Name</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

                <div class="shadow-lg col-md-4 p-3 rounded-3 mt-5">
                    <p class="fw-bold h6">Select Tables</p>

                    @foreach ($results as $result)
                        <div class="d-flex align-items-center mb-2" style="gap:5px;">
                            <input type="checkbox" class="form-check-input p-2 border-dark rounded-0" name="result_table"
                                value="{{ $result->table_name }}">
                            <p class="mb-0 h6 text-uppercase">{{ (explode('_',$result->table_name))[0] }}</p>
                        </div>
                    @endforeach

                    <button class="submit-btn mt-2" id="print_reports">Print</button>
                </div>
            </form>
        </div>
    </div>
@endsection


@push('body-scripts')
    <script src="{{ asset('/') }}js/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            //Select the class
            $('#report_olevel').on('click', function(e) {
                e.preventDefault();
                $("#table_container").removeClass('d-none')
                var classname = $("#classname").val();
                var level = $("#level").val();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('reports.olevel.class') }}",
                    data: {
                        classname: classname,
                        level: level
                    },
                    success: function(data) {
                        $('tbody').empty();
                        $.each(data, function(k, v) {
                            var html = '';
                            var td = '';
                            if (v.mname == null || v.mname == '' || v.mname == 'NULL') {
                                var std_name = (v.lname) + " " + (v.fname);
                            } else {
                                var std_name = (v.lname) + " " + (v.mname) + " " + (v
                                    .fname);
                            }

                            td +=
                                '<td>\
                                            <input type="checkbox" class="form-check-input p-2 rounded-0 border-dark" name="std_id" value="' +
                                v.std_id + '">\
                                            </td>';
                            td += '<td>' + (std_name) + '</td>';

                            var tr = '<tr>' + td + '</tr>';
                            $('tbody').append(tr);
                        });
                    },
                    error: function(error) {
                        alert('Failed to Save Subject details');
                    }
                });
            });

            //Print Reports
            $("#print_reports").on('click', function(e) {
                e.preventDefault();
                var student_ids = [];
                var tables = [];
                var term = $("#term").text();
                var year = $("#year").text();

                //Disable the button
                $(this).removeClass('submit-btn').addClass('submit-btn-disabled').prop('disabled',true);

                //Select the IDs
                $("input[name='std_id']:checked").each(function() {
                    student_ids.push(this.value);
                });

                //select resultant tables
                $("input[name='result_table']:checked").each(function() {
                    tables.push(this.value);
                });

                $.ajax({
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/Results/report_alevel_print/"+tables+"/"+term+"/"+year+"/"+student_ids+"",
                    success: function(data) {
                        window.open("/Results/report_alevel_print/"+tables+"/"+term+"/"+year+"/"+student_ids+"", '_blank');
                        //Enable the button
                        $('#print_reports').addClass('submit-btn').removeClass('submit-btn-disabled').prop('disabled',false);
                    },
                    error: function(error) {
                        alert('Failed to Print');
                        //Enable the button
                        $('#print_reports').addClass('submit-btn').removeClass('submit-btn-disabled').prop('disabled',false);
                    }
                });
            })

        });
    </script>
@endpush
