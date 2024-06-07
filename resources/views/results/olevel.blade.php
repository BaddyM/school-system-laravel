@extends('common.header')

@section('title')
    Olevel - Results
@endsection

<style>
    tr {
        vertical-align: middle;
    }
    th, td{
        text-transform: capitalize !important;
    }
</style>

@section('body')
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Enter O-level Results</h5>

        <form method="post">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-3">
                    <label class="form-label h6 fw-bold">Select Class</label>
                    <select id="classname" class="form-select rounded-0" required>
                        @foreach ($classes as $class)
                            <option value="{{ $class->class }}">{{ $class->class }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label h6 fw-bold">Select Subject</label>
                    <select id="subject" class="form-select rounded-0" required>
                        <option value=""></option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label h6 fw-bold">Select Topic</label>
                    <select id="select_topic" class="form-select rounded-0" required>
                        
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

            <div class="row justify-content-between">
                <button class="submit-btn mt-3 col-md-1" id="show_table">submit</button>
                <button class="submit-btn mt-3 col-md-1 d-none" id="print_marklist">print</button>
            </div>
        </form>

        <div class="mt-3 d-none" id="table_container">
            <p class="fw-bold h5 text-center mb-3 subject_header">Mathematics</p>

            <form method="post" id="std_marks_form">
                <input type="hidden" name="subject_buffer">
                <input type="hidden" name="result_set_buffer">
                <input type="hidden" name="classname_buffer">
                <input type="hidden" name="topic_buffer">

                <div class="card">
                    <div class="card-body">
                        <div class="overflow-scroll">
                            <table class="table" id="marks_table">
                                <thead>
                                    <tr class="table-dark">
                                        <th scope="col">#</th>
                                        <th scope="col">Student Name</th>
                                        <th scope="col">Class</th>
                                        <th scope="col">Score</th>
                                        <th scope="col">Topic</th>
                                        <th scope="col">Competence</th>
                                        <th scope="col">Remark on Competence</th>
                                    </tr>
                                </thead>
                                <tbody>
            
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

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

            //Fill the topics
            $("#subject").on('change',function(){
                var subject = $(this).val();
                var classname = $("#classname").val();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('fetch.topics.olevel') }}",
                    data: {
                        subject:subject,
                        classname:classname
                    },
                    success: function(data) {
                        //Empty the selection first
                        $("#select_topic").empty();

                        $.each(data, function(k,v){
                            var option_data = "<option value='"+v.topic+"'>"+v.topic+"</option>"
                            $("#select_topic").append(option_data);
                        });
                    },
                    error: function(error) {
                        alert('Failed to Fetch Topics');
                    }
                });
            })

            $('#show_table').on('click', function(e) {
                e.preventDefault();
                //Empty the previous form
                $("form")[0].reset();

                var classname = $("#classname").val();
                var subject = $("#subject").val();
                var result_set = $("#result_set").val();
                var topic = $("#select_topic").val();
                var level = 'O Level';

                $(".subject_header").text(subject);
                $("input[name='result_set_buffer']").val(result_set);   
                $("input[name='classname_buffer']").val(classname);  
                $("input[name='subject_buffer']").val(subject);
                $("input[name='topic_buffer']").val(topic);   

                if(topic != null){
                    //Disable the button
                    $(this).addClass('submit-btn-disabled').removeClass('submit-btn').prop('disabled',true);

                    //Empty the table body before appending
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
                            topic:topic,
                            level:level
                        },
                        url: "{{ route('olevel.show') }}",
                        success: function(response) {
                            //Enable the submit button
                            $('#show_table').removeClass('submit-btn-disabled').addClass('submit-btn').prop('disabled',false);

                            //Show the table container
                            $("#table_container").removeClass('d-none');
                            //Show the print button
                            $("#print_marklist").removeClass('d-none');

                            var row_data;
                            var std_name;
                            var row_count = 0;
                            $.each(response.data, function(key, value) {
                                std_ids.push(value.std_id);
                                if (value.mname == '' || value.mname == null || value
                                    .mname == 'NULL') {
                                    std_name = value.lname + " " + value.fname;
                                } else {
                                    std_name = value.lname + " " + value.mname + " " + value
                                        .fname;
                                }
                                row_count += 1;
                                row_data = '<tr>\
                                                <td>' + row_count + '</td>\
                                                <td>' + std_name + '</td>\
                                                <td style="width:100px !important;" class="classname"></td>\
                                                <td>\
                                                    <input type="hidden" name="std_ids[]" value="'+value.std_id+'">\
                                                    <input type="number" style="width:150px;" min=0 step=0.1 class="form-control rounded-0 std_marks"\
                                                    value="'+((value.mark == null || value.mark == "NULL" || value.mark == "")?"":parseFloat(value.mark))+'" name="std_marks[]"" placeholder="Score"></td>\
                                                    <td class="topic">\
                                                    </td>\
                                                    <td>\
                                                        <textarea placeholder="Competence" name="competence[]" style="width:200px !important;" rows=6 class="form-control rounded-0">'+((value.competence) != null ? value.competence : "")+'</textarea>\
                                                    </td>\
                                                    <td>\
                                                        <textarea placeholder="Remark" name="remark[]" style="width:200px !important;" rows=6 class="form-control rounded-0">'+((value.remark) != null ? value.remark : "")+'</textarea>\
                                                    </td>\
                                            </tr>';
                                $('tbody').append(row_data);
                                $(".classname").text(classname);
                                $(".topic").text(topic);  
                            });
                        },
                        error: function(error) {
                            alert('Failed');
                        }
                    });
                }else{
                    alert('Select a Topic!');
                }
            })

            //Limit the input mark
            $(document).on('keyup', ".std_marks", function() {
                var input_value = $(this).val();
                if (input_value > 3.0 || input_value < 0) {
                    $(this).val(null)
                }
            })

            //Save student marks
            $("#std_marks_form").on('submit', function(e) {
                e.preventDefault();

                $(this).addClass('submit-btn-disabled').removeClass('submit-btn').prop('disabled',true);
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('add.results.olevel') }}",
                    data: new FormData(this),
                    processData:false,
                    cache:false,
                    contentType:false,
                    success: function(data) {
                        $("#save_student").removeClass('submit-btn-disabled').addClass('submit-btn').prop('disabled',false);
                        alert(data);
                        $("#table_container").addClass('d-none');
                        $("#marks_table tbody").empty();
                        $("form")[0].reset();
                        $("#print_marklist").addClass('d-none');
                    },
                    error: function(error) {
                        alert('Failed to Save Subject details');
                    }
                });
            });

            //Print Marklist
            $("#print_marklist").on('click',function(e){
                e.preventDefault();
                var subject = $("input[name='subject_buffer']").val();
                var classname = $("input[name='classname_buffer']").val();
                var level = 'O Level';
                $.ajax({
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        window.open('/Results/marklist_print/'+classname+'/1/'+subject+'/'+level+'','_blank');
                    },
                    error: function(error) {
                        alert('Failed to Print Marklist');
                    }
                });
            });

        });

        

    </script>
@endpush
