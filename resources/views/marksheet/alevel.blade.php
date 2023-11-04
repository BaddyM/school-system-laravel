@extends('common.header')

@section('title')
    Alevel - Marksheet
@endsection

@php
    //Subjects here
    $ones = ['general_paper', 'Submath'];

    $twos = ['Mathematics', 'Mathematics2', 'History', 'History2', 'Economics', 'Economics2', 'Agriculture', 'Agriculture2'];

    $threes = ['Luganda', 'Luganda2', 'Luganda3', 'Physics', 'Physics2', 'Physics3', 'Chemistry', 'Chemistry2', 'Chemistry3', 'Biology', 'Biology2', 'Biology3', 'Geography', 'Geography2', 'Geography3', 'Divinity', 'Divinity2', 'Divinity3', 'Literature', 'Literature2', 'Literature3', 'Entrepreneurship', 'Entrepreneurship2', 'Entrepreneurship3'];

    $fours = ['Art', 'Art2', 'Art3', 'Art4'];

    $subs = ['Subict', 'Subict2'];

    $merged = array_merge($twos, $threes, $fours, $ones, $subs);

@endphp

@section('body')
    <div class="container-fluid">
        <h5 class="text-center text-uppercase fw-bold mb-3">A level Marksheet</h5>

        <div class="mb-3">
            <form action="" method="post">
                @csrf
                <div class="row">
                    <div class="col-sm-6 form-floating">
                        <select name="classname" id="class" class="form-select">
                            <option value="senior5">Senior 5</option>
                            <option value="senior6">Senior 6</option>
                        </select>
                        <label for="" class="form-label fw-bold" style="color:blue;">Select Class</label>
                    </div> {{-- Select class here --}}

                    <div class="col-sm-6 form-floating">
                        <select name="result_set" id="resultset" class="form-select">
                            @foreach ($result_set as $result)
                                <option value="{{ $result->result_set }}">{{ $result->result_set }}</option>
                            @endforeach
                        </select>
                        <label for="" class="form-label fw-bold" style="color:red;">Select Result Set</label>
                    </div>{{-- -Select Result set here --}}
                </div>
                <button class="btn submit-btn mt-3 rounded-1" id="alevel-btn" type="button">SUBMIT</button>
            </form>
        </div><!-- Select class here -->

        <hr>

        <div class="card marksheet d-none">
            <div class="card-header">
               <form action="">
                @csrf
                    <input type="hidden" id="result_print" value="">
                    <input type="hidden" id="class_print" value="">
                    <a id="marksheet" target="_blank"><button class="btn btn-warning bg-gradient px-3 rounded-1 text-uppercase fw-bold border border-secondary" id="download-btn" type="button">Download</button></a>
               </form>
            </div>
            <div class="card-body overflow-scroll">
                <table id="alevel" class="table table-striped table-hover">
                    <thead style="background-color:purple !important;" class="text-white">
                        <th class="text-uppercase" scope="col">No.</th>
                        <th scope="col">Student_ID</th>
                        <th scope="col">student_Name</th>
                        <th scope="col">combination</th>
                        <th scope="col">resultset</th>
                        <th scope="col">Class</th>
                        @foreach ($merged as $m)
                            <th class="text-uppercase" scope="col">{{ $m }}</th>
                        @endforeach
                        <th>Points</th>

                    </thead>
                    <tbody class="table-light">

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    @include('common.scripts')

    <script>
        $(document).ready(function() {
            function datatable(result, classname) {
                var alevel = $("#alevel").DataTable({
                    serverSide: true,
                    processing: true,
                    autoWidth: false,
                    searchable: true,
                    stateSave: true,
                    ajax: {
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        data: {
                            classname: classname,
                            result: result
                        },
                        url: "{{ route('marksheet.display') }}"
                    },
                    columns: [{
                            data: "DT_RowIndex"
                        },
                        {
                            data: "stdID",
                            render: function(data, type, row) {
                                return '<div class="fw-bold text-info">' + data + '</div>'
                            }
                        },
                        {
                            data: "stdFName",
                            render: function(data, type, row) {
                                return row.stdFName + ' ' + row.stdLName
                            }
                        },
                        {
                            data: "combination",
                            render: function(data, type, row) {
                                if (!(data == null)) {
                                    return '<div class="fw-bold text-success">' + data + '</div>'
                                } else {
                                    return '<div></div>'
                                }
                            }
                        },
                        {
                            data: "result_set",
                            render:function(){
                                return '<div class="text-uppercase">'+result+'</div>'
                            }
                        },
                        {
                            data: "class"
                        },
                        
                        {
                            data: "Mathematics",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Mathematics2",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "History",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "History2",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },

                        {
                            data: "Economics",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Economics2",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Agriculture",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Agriculture2",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Luganda",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Luganda2",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Luganda3",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Physics",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Physics2",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Physics3",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Chemistry",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Chemistry2",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Chemistry3",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Biology",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Biology2",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Biology3",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Geography",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Geography2",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Geography3",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Divinity",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Divinity2",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Divinity3",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Literature",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Literature2",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Literature3",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Entrepreneurship",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Entrepreneurship2",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Entrepreneurship3",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Art",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Art2",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Art3",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Art4",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "general_paper",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Submath",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Subict",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "Subict2",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return data;
                                } else {
                                    return ''
                                }
                            }
                        },
                        {
                            data: "points",
                            render: function(data, type, row) {
                                if (!(data == 0)) {
                                    return '<div class="text-primary fw-bold">' + data + '</div';
                                } else {
                                    return '<div class="text-danger fw-bold">' + data + '</div';
                                }
                            }
                        }
                    ],
                    columnDefs: [{
                            targets: [0],
                            width: 'auto',
                            className: 'dt-center'
                        },
                        {
                            targets: [1],
                            width: "200px",
                            className: 'dt-center'
                        }
                    ]
                })
            }

            //Fetch records for the table        
            $("#alevel-btn").on('click', function() {
                var classname = $("#class").val()
                var result = $("#resultset").val()

                $("#result_print").val(result)
                $("#class_print").val(classname)

                $.ajax({
                    type: 'post',
                    url: '{{ route("marksheet.a.fetch") }}',
                    data: {
                        classname: classname,
                        result: result
                    },
                    dataType: 'json',
                    success: function(response) {
                        $("#alevel").DataTable().destroy()
                        $(".marksheet").removeClass("d-none")
                        datatable(response.result, response.class)

                    }
                })
            })

            //Print Marksheet here
            $("#download-btn").on('click',function(){
                var result = $("#result_print").val()
                var classname = $("#class_print").val()

                $("#marksheet").attr('href','/marksheet-pdf')
                window.open(($("#marksheet").attr('href')+'/'+result+'/'+classname), '_blank')
            })

        })
    </script>
@endsection
