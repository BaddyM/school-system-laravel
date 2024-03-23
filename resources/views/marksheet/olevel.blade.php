@extends('common.header')

@section('title')
    Olevel - Marksheet
@endsection

@section('body')
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">O level Marksheet</h5>

        <div class="my-5">
            <form action="" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <select name="" id="class_name" class="form-select rounded-0">
                            @foreach ($classes as $class)
                                <option value="{{ $class->class }}">{{ $class->class }}</option>
                            @endforeach
                        </select>
                    </div>{{-- Select Class --}}

                    <div class="col-md-3">
                        <select name="" id="table_name" class="form-select rounded-0 text-uppercase">
                            @foreach ($results as $result)
                                <option value="{{ $result->table_name }}">{{ explode('_', $result->table_name)[0] }}
                                </option>
                            @endforeach
                        </select>
                    </div>{{-- Select Table --}}
                </div>

                <button class="submit-btn mt-3" id="submit_btn">Submit</button>
            </form>
        </div>

        <div class="container table_container d-none overflow-scroll">
            <div class="d-flex justify-content-end">
                <div>

                </div>
                <form action="" method="post" class="mb-3">
                    <input type="hidden" id="class_name_buffer">
                    <input type="hidden" id="table_name_buffer">
                    <button class="btn btn-warning bg-gradient rounded-0 px-4 text-uppercase"
                        id="print_marksheet">print</button>
                </form>
            </div>
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Class</th>
                        @foreach ($subjects as $subjects)
                            <th scope="col">{{ $subjects->name }}</th>
                        @endforeach
                        <th scope="col">Identifier</th>
                        <th scope="col">Position</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
@endsection


@push('body-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $("#submit_btn").on('click', function(event) {
                event.preventDefault();

                //Display the results
                $(".table_container").removeClass('d-none');

                var classname = $("#class_name").val();
                var tablename = $("#table_name").val();
                var level = 'O Level';
                var paper = 1;
                //console.log("Class = " + classname + ", Table = " + tablename);

                //Add buffer variables
                $("#class_name_buffer").val(classname);
                $("#table_name_buffer").val(tablename);

                //Empty the previous table first
                $('tbody').empty();

                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('marksheet') }}",
                    data: {
                        classname: classname,
                        tablename: tablename,
                        level: level,
                        paper: paper
                    },
                    success: function(response) {
                        if (response != 'empty') {
                            var position = 0;
                            $.each((response), function(k, v) {
                                position += 1;
                                var array_objects = Object.values(v);
                                var tr = '';

                                //Push the position
                                array_objects.push(position);

                                //Get identifier 
                                var identifier_pos = (array_objects.length - 2);
                                var class_pos = array_objects[1];

                                if (class_pos == 'Senior 1' || class_pos ==
                                    'Senior 2') {
                                    array_objects[identifier_pos] = (parseFloat((
                                            array_objects[identifier_pos]) / 12))
                                        .toFixed(1);
                                } else if (class_pos == 'Senior 3' || class_pos ==
                                    'Senior 4') {
                                    array_objects[identifier_pos] = (parseFloat((
                                            array_objects[identifier_pos]) / 9))
                                        .toFixed(1);
                                }

                                $.each(array_objects, function(key, value) {
                                    //console.log(value);

                                    if (value == null || value == 'NULL' ||
                                        value == '') {
                                        value = ' '
                                    }

                                    var td = '<td>' + value + '</td>';
                                    tr += td;
                                });

                                //console.log("Response = " + response.data);

                                $('tbody').append('<tr>\
                                            ' + tr + '\
                                        </tr>');

                            });
                        } else {
                            $('tbody').append(
                                '<tr><td class="text-danger fw-bold">Table Empty</td></tr>');
                        }

                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            });

            //Print marksheet
            $("#print_marksheet").on('click', function(e) {
                e.preventDefault()
                var classname = $("#class_name_buffer").val();
                var tablename = $("#table_name_buffer").val();
                var level = 'O Level';

                $.ajax({
                    type: "get",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/marksheet/marksheet-print/"+classname+"/"+tablename+"/"+level+"",
                    data: {
                        classname: classname,
                        tablename: tablename,
                        level:level
                    },
                    success: function(response) {
                        //console.log(response);
                        window.open("/marksheet/marksheet-print/"+classname+"/"+tablename+"/"+level+"",'_blank')
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            })
        });
    </script>
@endpush
