@extends('common.header')

@section('title')
    Alevel - Results
@endsection

<style>
    tr {
        vertical-align: middle;
    }
</style>

@section('body')
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Enter A-level Results</h5>

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
                    <label for="" class="form-label h6 fw-bold">Select Paper</label>
                    <select id="paper_num" class="form-select rounded-0">
                        @foreach ($papers as $paper)
                            <option value="{{ $paper->paper }}">{{ $paper->paper }}</option>
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
                            A Level table empty
                        </p>
                    @endif
                </div>
            </div>

            <div class="row justify-content-between">
                <button class="submit-btn mt-3 col-md-1" id="show_table">submit</button>
                <button class="submit-btn mt-3 col-md-1 d-none" id="print_marklist">print</button>
            </div>

        </form>

        <div class="mt-3 d-none w-100" id="table_container">
            <p class="fw-bold h5 text-center mb-3 subject_header">Mathematics</p>

            <form method="post" id="std_marks_form">
                @csrf
                <input type="hidden" name="subject_buffer">
                <input type="hidden" name="result_set_buffer">
                <input type="hidden" name="classname_buffer">
                <input type="hidden" name="paper_num_buffer">

                <div class="overflow-scroll">
                    <table class="table" id="marks_table">
                        <thead>
                            <tr class="table-dark">
                                <th scope="col">#</th>
                                <th scope="col">Student Name</th>
                                <th style="width:120px;" scope="col">Class</th>
                                <th scope="col" >Paper</th>
                                <th style="width:250px;" scope="col">Mark</th>
                            </tr>
                        </thead>
                        <tbody>
    
                        </tbody>
                    </table>
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

            $('#show_table').on('click', function(e) {
                e.preventDefault();

                //Disable this button
                $(this).addClass('submit-btn-disabled').removeClass('submit-btn').prop('disabled',true);

                var classname = $("#classname").val();
                var subject = $("#subject").val();
                var result_set = $("#result_set").val();
                var paper = $("#paper_num").val();
                var level = 'A Level';

                $(".subject_header").text(subject);
                $("input[name='result_set_buffer']").val(result_set);   
                $("input[name='classname_buffer']").val(classname);  
                $("input[name='subject_buffer']").val(subject); 
                $("input[name='paper_num_buffer']").val(paper);          

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
                        paper:paper,
                        level:level
                    },
                    url: "{{ route('olevel.show') }}",
                    success: function(data) {
                        var counter = 0;     
                        //Show the table container
                        $("#table_container").removeClass('d-none');

                        //Show the print button
                        $("#print_marklist").removeClass('d-none');

                        //Enable the button again
                        $('#show_table').removeClass('submit-btn-disabled').addClass('submit-btn').prop('disabled',false);

                        if((data.data).length > 0){
                            $.each(data.data,function(k,v){
                                if(v.mname == null || v.mname == '' || v.mname == 'NULL'){
                                    var std_name = v.lname+" "+v.fname;
                                }else{
                                    var std_name = v.lname+" "+ v.mname +" "+v.fname;
                                }                                

                                var row_data = "<tr>\
                                                <td>"+(counter += 1)+"</td>\
                                                <td>"+(std_name)+"</td>\
                                                <td>"+(classname)+"</td>\
                                                <td>"+(paper)+"</td>\
                                                <td>\
                                                    <input type='hidden' name='std_id[]' value='"+v.std_id+"'>\
                                                    <input type='number' name='std_mark[]' value='"+v.mark+"' class='std_marks form-control rounded-0' placeholder='"+subject+"'>\
                                                </td>\
                                            </tr>"
                                $("#marks_table tbody").append(row_data);
                            });
                        }else{
                            $("#marks_table tbody").append("<tr>\
                                    <td colspan=5 class='text-center'> <img style='width:100px;' class='fluid' src='/images/icon/empty_set.png'></td>\
                                </tr>");
                        }
                        
                    },
                    error: function(error) {
                        $("#table_container").addClass('d-none');
                        //Enable the button again
                        $('#show_table').removeClass('submit-btn-disabled').addClass('submit-btn').prop('disabled',false);
                        alert('Error!');                        
                    }
                });
            })

            //Limit the input mark
            $(document).on('keyup', ".std_marks", function() {
                var input_value = $(this).val();
                if (input_value > 100 || input_value < 0) {
                    $(this).val(null);
                }
            })

            //Save student marks
            $("#std_marks_form").on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('add.results.alevel') }}",
                    data:new FormData(this),
                    processData:false,
                    contentType:false,
                    cache:false,
                    success: function(data) {
                        alert(data);
                        //Show the table container
                        $("#table_container").addClass('d-none');
                        $("#print_marklist").addClass('d-none');
                        $('tbody').empty();
                        $("form")[0].reset();
                    },
                    error: function(error) {
                        alert('Failed to Save Subject details');
                    }
                });
            });

            //Print Marklist
            $("#print_marklist").on('click',function(e){
                e.preventDefault();
                var classname = $("#classname").val();
                var subject = $("#subject").val();
                var paper = $("#paper_num").val();
                var level = 'A Level';

                $.ajax({
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        window.open('/Results/marklist_print/'+classname+'/'+paper+'/'+subject+'/'+level+'','_blank');
                    },
                    error: function(error) {
                        alert('Failed to Print Marklist');
                    }
                });
            });

        });
    </script>
@endpush
