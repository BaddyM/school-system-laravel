@extends('common.header')

@section('title')
    Olevel - Results
@endsection

<style>
    tr {
        vertical-align: middle;
    }
</style>

@section('body')
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Enter O-level Results</h5>

        <form action="" method="post">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <label for="" class="form-label h6 fw-bold">Select Class</label>
                    <select id="classname" class="form-select rounded-0">
                        @foreach ($classes as $class)
                            <option value="{{ $class->class }}">{{ $class->class }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="" class="form-label h6 fw-bold">Select Subject</label>
                    <select id="subject" class="form-select rounded-0">
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="" class="form-label h6 fw-bold">Select Result Table</label>

                    @if (!empty($results))
                        <select id="result_set" class="form-select rounded-0 text-uppercase">
                            @foreach ($results as $result)
                                @php
                                    $set = explode('_', $result->table_name);
                                @endphp
                                <option value="{{ $set[0] }}">{{ $set[0] }}</option>
                            @endforeach
                        </select>
                    @else
                        <p class="mb-0 h5 fw-bold text-danger">
                            O Level table empty
                        </p>
                    @endif
                </div>
            </div>

            <button class="submit-btn mt-3" id="show_table">submit</button>
        </form>

        <div class="mt-3 d-none" id="table_container">
            <p class="fw-bold h5 text-center mb-3 subject_header">Mathematics</p>

            <form action="" method="post" id="std_marks_form">
                <input type="hidden" id="subject_buffer">
                <input type="hidden" id="result_set_buffer">
                <input type="hidden" id="classname_buffer">

                <table class="table" id="marks_table">
                    <thead>
                        <tr class="table-dark">
                            <th scope="col">#</th>
                            <th scope="col">Student Name</th>
                            <th scope="col">Class</th>
                            <th scope="col" class="subject_header"></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

                <button class="submit-btn mt-2" id="save_student" type="submit">Save</button>
            </form>
        </div>

    </div>
@endsection


@push('body-scripts')
    <script src="{{ asset('/') }}js/jquery.min.js"></script>
    <script src="{{ asset('/') }}js/datatable.min.js"></script>
    <script src="{{ asset('/') }}js/custom.js"></script>
    <script>
        $(document).ready(function() {
            var std_ids = [];
            var std_marks_ids = [];

            $('#show_table').on('click', function(e) {
                e.preventDefault();
                //Show the table container
                $("#table_container").removeClass('d-none');

                var classname = $("#classname").val();
                var subject = $("#subject").val();
                var result_set = $("#result_set").val();
                var paper = 1;

                $(".subject_header").text(subject);
                $("#result_set_buffer").val(result_set);   
                $("#classname_buffer").val(classname);  
                $("#subject_buffer").val(subject);         

                //Empty the table body before append
                $("tbody").empty();

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    data: {
                        classname: classname,
                        result_set:result_set,
                        subject:subject,
                        paper:paper
                    },
                    url: "{{ route('olevel.show') }}",
                    success: function(data) {
                        var row_data;
                        var std_name;
                        var row_count = 0;
                        $.each(data, function(key, value) {
                            std_ids.push(value.std_id);
                            if (value.mname == '' || value.mname == null || value
                                .mname == 'NULL') {
                                std_name = value.lname + " " + value.fname;
                            } else {
                                std_name = value.lname + " " + value.mname + " " + value
                                    .fname;
                            }
                            row_count += 1;
                            row_data = "<tr>\
                                            <td>" + row_count + "</td>\
                                            <td>" + std_name + "</td>\
                                            <td class='classname'></td>\
                                            <td><input type='number' min=0 max=3 class='form-control rounded-0 std_marks'\
                                                value='"+((value.mark == null || value.mark == 'NULL' || value.mark == '')?'':parseFloat(value.mark))+"' name='std_marks[]' placeholder='Enter "+subject+" Mark' data-std_id = '"+value.std_id+"'></td>\
                                        </tr>";
                            $('tbody').append(row_data);
                            $(".classname").text(classname);  
                        });
                    },
                    error: function(error) {
                        alert('Failed');
                    }
                });
            })

            //Limit the input mark
            $(document).on('keyup', ".std_marks", function() {
                var input_value = $(this).val();
                if (input_value > 3.0 || input_value < 0) {
                    $(this).val(null)
                }
            })

            //Save student marks
            $("#save_student").on('click', function(e) {
                e.preventDefault();
                var marks = [];
                var subject = $("#subject_buffer").val();
                var classname = $("#classname_buffer").val();
                var result_set = $("#result_set_buffer").val();
                var level = 'O Level';

                $("input[name='std_marks[]']").each(function() {
                    var std_id = $(this).data('std_id');
                    var std_mark = $(this).val();
                    marks.push([std_id, std_mark]);
                });

                for (var i = 0; i < std_ids.length; i++) {
                    var marks_std_combo = [];
                    marks_std_combo.push(std_ids[i], marks[i]);

                    //Student ID with Marks
                    std_marks_ids.push(marks_std_combo);
                }

                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('add.results.olevel') }}",
                    data: {
                        marks: marks,
                        classname: classname,
                        result_set: result_set,
                        subject: subject,
                        level: level
                    },
                    success: function(data) {
                        alert(data);
                        location.reload();
                    },
                    error: function(error) {
                        alert('Failed to Save Subject details');
                    }
                });

                //console.log(marks);
            })

        });
    </script>
@endpush
