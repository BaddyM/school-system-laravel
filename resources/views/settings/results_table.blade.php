@extends('common.header')

@section('title')
    Results Details
@endsection

<style>
    tr{
        vertical-align: middle;
    }
</style>

@section('body')
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Update/Add Results Table</h5>

        <div class="row justify-content-between">
            <div class="col-md-4">
                <form action="" method="post" id="results_table_form">
                    <p class="text-center fw-bold h5">Add Results Table</p>
                    <div>
                        <div>
                            <label for="" class="form-label fw-bold h6">Table Name</label>
                            <input type="text" class="form-control rounded-0" id="table_name" name="table_name" required>
                        </div>

                        <div class="my-3">
                            <label class="form-label fw-bold h6">Select Level</label>
                            <select name="std_level" id="std_level" class="form-select rounded-0">
                                <option value="A Level">A Level</option>
                                <option value="O Level">O Level</option>
                            </select>
                        </div>
                    </div>{{-- Subjects list --}}

                    <p class="fw-bold h6 my-2">Select Subjects for the table</p>

                    <div class="">
                        <p class="fw-bold h6 text-primary">A-Level Subjects List</p>
                        <div class="my-2">
                            @foreach ($alevel as $a)
                                <div class="d-flex align-items-center mb-2" style="gap:7px;">
                                    <input type="checkbox" style="height:20px; width:20px;"
                                        value="{{ $a->name }}_{{ $a->paper }}" name="subject_list">
                                    {{ $a->name }} {{ $a->paper }}
                                </div>
                            @endforeach
                        </div>{{-- A-Level --}}

                        <p class="fw-bold h6 text-danger">O-Level Subjects List</p>
                        <div class="my-2">
                            @foreach ($olevel as $o)
                                <div class="d-flex align-items-center mb-2" style="gap:7px;">
                                    <input type="checkbox" style="height:20px; width:20px;"
                                        value="{{ $o->name }}_{{ $o->paper }}" name="subject_list">
                                    {{ $o->name }} {{ $o->paper }}
                                </div>
                            @endforeach
                        </div>{{-- A-Level --}}
                    </div>

                    <button class="submit-btn mt-3" type="button" id="submit_button">create</button>
                </form>
            </div>

            <div class="col-md-6">
                <p class="text-center fw-bold h5">Tables Available</p>
                <div>
                    <table class="table table-hover">
                        <thead>
                            <tr class="table-dark bg-gradient">
                                <th scope="col">#</th>
                                <th scope="col">Table</th>
                                <th scope="col">Level</th>
                                <th scope="col">Term</th>
                                <th scope="col">Year</th>
                                @if(Auth::user()->is_super_admin == 1)
                                <th scope="col">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $row_num = 1;
                            @endphp
                            @if (!empty($results))
                                @foreach ($results as $r)
                                    @php
                                        $result = explode('_', $r->table_name);
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ $row_num++ }}
                                        </td>

                                        <td class="text-uppercase">
                                            {{ $result[0] }}
                                        </td>

                                        <td>
                                            {{ $r->level }}
                                        </td>

                                        <td>
                                            {{ $result[1] }}
                                        </td>

                                        <td>
                                            {{ $result[2] }}
                                        </td>

                                        {{-- Check if user is super admin --}}
                                        @if(Auth::user()->is_super_admin == 1)
                                        <td>
                                            <form action="" class="m-0">
                                                <button class="btn btn-outline-danger btn-sm delete-table" data-id={{ $r->id }} type="button">Delete</button>
                                            </form>
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center fw-bold text-danger h6">
                                        Empty Set
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('body-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#submit_button').on('click', function(event) {
                event.preventDefault();

                var table_name = ($("#table_name").val()).toLowerCase().replaceAll(" ", "").replaceAll("_",
                    "").replaceAll('.', '').replaceAll('-', '');

                var subject_heads = [];
                var term = parseInt($("#term").text());
                var year = parseInt($("#year").text());
                var std_level = $("#std_level").val();
                console.log("term = " + term + ", year = " + year + ", Table_name = " + table_name+", Level = "+std_level);
                //Subjects Selected
                $('input[name="subject_list"]:checked').each(function() {
                    var checked_val = (this.value).replace(" ", "_");
                    console.log(checked_val)
                    subject_heads.push(checked_val);
                });

                if (table_name == '' || table_name == null) {
                    alert("Table Name is Required");
                } else {
                    $.ajax({
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('setting.results.table.create') }}",
                        data: {
                            table_name: table_name,
                            subject_heads: subject_heads,
                            term: term,
                            year: year,
                            std_level:std_level
                        },
                        success: function(data) {
                            $("#results_table_form")[0].reset();
                            alert(data);
                            location.reload();
                        },
                        error: function(error) {
                            alert('Failed to Create Results Table');
                            location.reload();
                        }
                    });
                }
            });

            //Delete the table
            $('.delete-table').on('click',function(){
                var table_id = $(this).data('id')
                console.log("Table_id = "+table_id);
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('setting.results.table.delete') }}",
                    data: {
                        table_id: table_id
                    },
                    success: function(data) {
                        alert(data);
                        location.reload();
                    },
                    error: function(error) {
                        alert('Failed to Delete Results Table');
                        location.reload();
                    }
                });
            })
        });
    </script>
@endpush
