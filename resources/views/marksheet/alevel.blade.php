@extends('common.header')

@section('title')
    Alevel - Marksheet
@endsection

@section('body')
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">A level Marksheet</h5>

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

        <div class="container table_container d-none">
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
            <div class="overflow-scroll">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Class</th>
                            @foreach ($subjects as $subjects)
                                @if ($subjects->name == 'SubICT' || $subjects->name == 'GeneralPaper' || $subjects->name == 'SubMath')
                                    <th scope="col">{{ substr($subjects->name, 0, 8) }} {{ $subjects->paper }}</th>
                                @else
                                <th scope="col">{{ substr($subjects->name, 0, 3) }} {{ $subjects->paper }}</th>
                                @endif
                            @endforeach
                            <th scope="col">Points</th>
                            <th scope="col">Position</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
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
                var level = 'A Level';
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

                                //Convert into array
                                var array_objects = Object.values(v);
                                var tr = '';

                                //Push the position
                                array_objects.push(position);

                                //Get points 
                                var points_pos = (array_objects.length - 2);
                                var class_pos = array_objects[1];
                                //array_objects[points_pos] = '-';

                                $.each(array_objects, function(key, value) {
                                    //console.log(value);
                                    var td = '<td>' + (((value) === null ||
                                        value === 'NULL' || value ===
                                        " ") ? " " : value) + '</td>';
                                    tr += td;
                                });

                                //console.log("Response = " + response.data);

                                $('tbody').append('<tr>\
                                                ' + tr + '\
                                            </tr>');

                            });
                        } else {
                            $('tbody').append('<tr><td colspan="25" class="text-center text-danger fw-bold">Table Empty</td></tr>');
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
                var level = 'A Level';

                $.ajax({
                    type: "get",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/marksheet/marksheet-print/" + classname + "/" + tablename + "/" + level +
                        "",
                    data: {
                        classname: classname,
                        tablename: tablename,
                        level: level
                    },
                    success: function(response) {
                        //console.log(response);
                        window.open("/marksheet/marksheet-print/" + classname + "/" +
                            tablename + "/" + level + "", '_blank')
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            })
        });
    </script>
@endpush
