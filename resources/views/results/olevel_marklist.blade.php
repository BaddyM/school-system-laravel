@extends('common.header')

@section('title')
    O-Level Marklist
@endsection

@section('body')
    <style>
        td {
            vertical-align: middle;
        }
    </style>
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">O'Level Marklist</h5>


        <div class="mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form method="post" id="select_marklist_form">
                        @csrf
                        <div class="row justify-content-between">
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold h6">Select Class</label>
                                <select name="classname" id="classname" class="form-select rounded-0">
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->class }}">{{ $class->class }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold h6">Select Subject</label>
                                <select name="subject" id="subject" class="form-select rounded-0" required>
                                    <option value=""></option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold h6">Select Topic</label>
                                <select name="topic" id="select_topic" class="form-select rounded-0" required>

                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold h6">Select Table</label>
                                <select name="table_name" class="form-select rounded-0" required>
                                    @foreach ($table_name as $t)
                                        <option value="{{ explode('_',($t->table_name))[0] }}">{{ strtoupper(explode('_',($t->table_name))[0]) }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <button id="select_date_btn" class="submit-btn mt-3" type="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="mb-4 d-none marklist_body">
            <div class="card">
                <div class="card-header p-3">
                    <div class="row align-items-center">
                        <div class="col-md-3 mb-2">
                            <p class="mb-0 h6"><span class="fw-bold">Subject:</span> <span id="subject_display"></span></p>
                        </div>
                        <div class="col-md-3 mb-2">
                            <p class="mb-0 h6"><span class="fw-bold">Topic:</span> <span id="topic_display"></span></p>
                        </div>
                        <div class="col-md-3 mb-2">
                            <p class="mb-0 h6"><span class="fw-bold">Class:</span> <span class="badge blue-badge" id="class_display"></span></p>
                        </div>
                        <div class="col-md-3 mb-2">
                           <button class="btn btn-warning bg-gradient px-4 fw-bold rounded-0" id="print_olevel_marklist" type="button">Print</button>
                        </div>
                    </div>
                </div>
                <div class="card-body overflow-scroll">
                    <table class="table table-hover table-responsive" id="olevel_table">
                        <thead>
                            <tr class="bg-gradient" style="background:purple; color:white;">
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Score</th>
                                <th scope="col">Competence</th>
                                <th scope="col">Remark</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="spinner_body d-none">
            <div class="d-flex justify-content-center align-items-center">
                <div style="color:purple;" class="spinner-border spinner-border-lg" role="status">
                </div>
            </div>{{-- spinner --}}
        </div>
    </div>
    </div>
@endsection

@push('body-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/datatable.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.bundle.js') }}"></script>
    <script>
        $(document).ready(function() {
            //Fill the topics
            $("#subject, #classname").on('change', function() {
                var subject = $("#subject").val();
                var classname = $("#classname").val();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('fetch.topics.olevel') }}",
                    data: {
                        subject: subject,
                        classname: classname
                    },
                    success: function(data) {
                        //Empty the selection first
                        $("#select_topic").empty();

                        $.each(data, function(k, v) {
                            var option_data = "<option value='" + v.topic + "'>" + v
                                .topic + "</option>"
                            $("#select_topic").append(option_data);
                        });
                    },
                    error: function(error) {
                        alert('Failed to Fetch Topics');
                    }
                });
            });

            //Fetch the results
            $("#select_marklist_form").on("submit", function(e){
                e.preventDefault();

                $(".spinner_body").removeClass("d-none");
                $(".marklist_body").addClass("d-none");

                var subject = $("#subject").val();
                var classname = $("#classname").val();
                var topic = $("#select_topic").val();

                $.ajax({
                    type: "POST",
                    url: "{{ route('olevel.marklist.fetch') }}",
                    data: new FormData(this),
                    processData:false,
                    contentType:false,
                    cache:false,
                    success: function(data) {
                        $(".spinner_body").addClass("d-none");
                        $(".marklist_body").removeClass("d-none");

                        //Empty the previous table
                        $("#olevel_table tbody").empty();

                        //Display
                        $("#subject_display").text(subject);
                        $("#topic_display").text(topic);
                        $("#class_display").text(classname);

                        if(data.length > 0){
                            var counter = 0;
                            $.each(data,function(k,v){
                                var row_data = "<tr>\
                                        <td>"+(counter += 1)+"</td>\
                                        <td>"+(v.lname+" "+((v.mname == null || v.mname == 'NULL' || v.mname == '')?"":v.mname)+" "+v.fname)+"</td>\
                                        <td>"+(v.score == null?'':v.score)+"</td>\
                                        <td>"+(v.competence == null?'':v.competence)+"</td>\
                                        <td>"+(v.remark == null?'':v.remark)+"</td>\
                                    </tr>";
                                
                                    $("#olevel_table tbody").append(row_data);
                            });
                        }else{
                            $("#olevel_table tbody").append("<tr>\
                                <td colspan=6 class='text-center'> <img style='width:100px;' class='fluid' src='/images/icon/empty_set.png'></td>\
                            </tr>");
                        }
                    },
                    error: function(error) {
                        alert('Failed to Fetch Results');
                    }
                });
            });

            //Print Marklist
            $("#print_olevel_marklist").on('click',function(e){
                e.preventDefault();

                var subject = $("#subject").val();
                var classname = $("#classname").val();
                var topic = $("#select_topic").val();
                var term = $("#term").text();
                var year = $("#year").text();
                var table = ($("select[name='table_name']").val())+"_"+term+"_"+year;

                window.open("/Results/olevel_print_marklist/"+table+"/"+classname+"/"+subject+"/"+topic+"", "_blank");
            })
        });
    </script>
@endpush
