@extends('common.header')

@section('title')
    Olevel - Marksheet
@endsection

@section('body')
    <div class="container-fluid">
        <h5 class="text-center text-uppercase fw-bold mb-3">O level Marksheet</h5>
        <div class="mb-3">
            <form action="" method="post">
                @csrf
                <div class="row">
                    <div class="col-sm-6 form-floating">
                        <select name="classname" id="class" class="form-select">
                            @for ($i = 1; $i <= 4; $i++)
                                <option value="senior{{ $i }}">Senior {{ $i }}</option>
                            @endfor
                        </select>
                        <label for="" class="form-label fw-bold" style="color:blue;">Select Class</label>
                    </div> {{-- Select class here --}}

                    <div class="col-sm-6 form-floating">
                        <select name="result_set" id="resultset" class="form-select">
                            @foreach ($result_set as $result)
                                <option value="{{ $result->name }}">{{ $result->name }}</option>
                            @endforeach
                        </select>
                        <label for="" class="form-label fw-bold" style="color:red;">Select Result Set</label>
                    </div>{{-- -Select Result set here --}}
                </div>
                <button class="btn bg-primary bg-gradient text-white mt-3 rounded-1" id="olevel-btn" type="button">SUBMIT</button>
            </form>
        </div><!-- Select class here -->

        <hr>

        <div class="card d-none marksheet">
            <div class="card-header">
                <form action="" class="m-0">
                    @csrf
                    <input type="hidden" id="class_name">
                    <input type="hidden" id="result_set">
                    <a id="marksheet-link" target="_blank">
                        <button class="btn btn-warning bg-gradient px-3 rounded-1 text-uppercase fw-bold border border-secondary" type="button" id="download-btn">Download</button>
                    </a>
                </form>
            </div>
        <div class="card-body overflow-scroll pt-3 pb-3">
            <table id="olevel" class="table table-striped table-hover">
                <thead class="bg-primary bg-gradient text-white">
                    <th class="text-uppercase" scope="col">No.</th>
                    <th scope="col">Student_ID</th>
                    <th scope="col">student_Name</th>
                    <th scope="col">resultset</th>
                    <th scope="col">Class</th>
                    @php
                        $subjects = [
                            "Mathematics",
                            "History",
                            "Luganda",
                            "CRE",
                            "Agriculture",
                            "Physics",
                            "Chemistry",
                            "Biology",
                            "Geography",
                            "Entrepreneurship",
                            "English",
                            "ICT",
                            "Art",
                            "Kiswahili"
                        ];
                    @endphp                    
                        @foreach ($subjects as $s)
                        <th scope="col">
                            {{ $s }}
                        </th>
                        @endforeach
                        <th scope="col">Identifier</th>
                        <th scope="col">Comment</th>
                        <th scope="col">Position</th>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
    </div>
@endsection

@include('common.scripts')

<script>
    $(document).ready(function() {

        var result_set = $("#resultset").val();

        function datatable(result, classname){
            $("#olevel").DataTable({
                        serverSide: true,
                        processing: true,
                        autoWidth: false,
                        searchable: true,
                        stateSave: true,
                        ajax: {
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type:'post',
                            url:'{{ route("table.olevel.display") }}',
                            data:{
                                result : result,
                                classname : classname
                            }
                        },
                        columns:[
                            {
                                data:'DT_RowIndex'
                            },
                            {
                                data: 'stdID',
                                render:function(data,type,row){
                                    return '<div class="text-primary fw-bold stdid">'+data+'</div>';
                                }
                            },
                            {
                                data: 'stdFName',
                                render: function(data,type,row){
                                    return '<div><span class="text-danger fw-bold">'+row.stdFName+'</span> '+row.stdLName+'</div>'
                                }
                            },
                            {
                                data: 'resultset',
                                render: function(){
                                    return '<div class="text-uppercase text-success fw-bold">'+result+'</div>';
                                }
                            },
                            {
                                data: 'class'
                            },
                            {
                                data: 'Mathematics'
                            },
                            {
                                data: 'History'
                            },
                            {
                                data: 'Luganda'
                            },
                            {
                                data: 'CRE'
                            },
                            {
                                data: 'Agriculture'
                            },
                            {
                                data: 'Physics'
                            },
                            {
                                data: 'Chemistry'
                            },
                            {
                                data: 'Biology'
                            },
                            {
                                data: 'Geography'
                            },
                            {
                                data: 'Entrepreneurship'
                            },
                            {
                                data: 'English'
                            },
                            {
                                data: 'ICT'
                            },
                            {
                                data: 'Art'
                            },
                            {
                                data: 'Kiswahili'
                            },
                            {
                                data: 'identifier'
                            },
                            {
                                data: 'comment',
                                render: function(data,type,row){
                                    var indent = row.identifier;
                                    var comment;

                                    if(indent >= 2.5 && indent <= 3.0){
                                        comment = '<div class="fw-bold badge bg-success">OUTSTANDING<div>';
                                    }else if(indent >= 1.5 && indent <= 2.4){
                                        comment = '<div class="fw-bold badge bg-info">MODERATE<div>';
                                    }else if(indent >= 0.1 && indent <= 1.4){
                                        comment = '<div class="badge bg-secondary fw-bold">BASIC<div>';
                                    }else{
                                        comment = '<div class="text-danger fw-bold">No LOs<div>';
                                    }

                                    return comment;
                                }
                            },
                            {
                                data: 'position'
                            }
                        ],
                        columnDefs:[
                            {
                                target:[1,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21],
                                className:'dt-center'
                            }
                        ]
                    })
        }

        $("#olevel-btn").on('click', function() {
            var classname = $("#class").val()
            var result = $("#resultset").val()

            $("#class_name").val(classname)
            $("#result_set").val(result)

            //Dislay the table
            $(".marksheet").removeClass('d-none')

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:"POST",
                url:'{{ route("table.olevel.display.marksheet") }}',
                data:{
                    result : result,
                    classname :classname
                },
                success:function(response){
                    //console.log("Data fetched successfully")
                    var result = response.result;
                    var classname = response.class

                    $("#olevel").DataTable().destroy()
                    //Display Table
                    datatable(result, classname)
                }
            })

        })

        //Download the marksheet
        $("#download-btn").on('click',function(){
            var classname = $("#class_name").val()
            var result = $("#result_set").val()

            var link = $("#marksheet-link").attr('href','/marksheet/olevel-marksheet-pdf/'+classname+'/'+result+'')
            //console.log($("#marksheet-link").attr('href'))
            window.open($("#marksheet-link").attr('href'), '_blank')
        })

    })
</script>
