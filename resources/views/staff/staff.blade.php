@extends('common.header')

@section('title')
    Staff
@endsection

@section('body')

<style>
    .table thead{
        background:brown;
        color:white;
    }
    tr th{
        text-align: center;
    }
    tr td{
        vertical-align: middle;
    }
    input, select, textarea{
        color:blue !important;
    }
</style>

<div class="container-fluid">
    <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Update/View Staff</h5>

    <div class="overflow-scroll p-2">
        <table class="table table-hover" id="staff-table">
            <thead class="bg-gradient">
                <th scope="col">#</th>
                <th scope="col">Last Name</th>
                <th scope="col">Middle Name</th>
                <th scope="col">First Name</th>
                <th scope="col">Position</th>
                <th scope="col">Gender</th>
                <th scope="col">Location</th>
                <th scope="col">Status</th>
                <th scope="col">Entered</th>
                <th scope="col">Updated</th>
                <th scope="col">Action</th>
            </thead>
            <tbody>
                @php
                    $counter = 0;
                @endphp
                @foreach ($staff as $s)
                    <tr>
                        <td>{{ $counter += 1 }}</td>
                        <td>{{ ucfirst($s->lname) }}</td>
                        <td>{{ ucfirst($s->mname) }}</td>
                        <td>{{ ucfirst($s->fname) }}</td>
                        <td>{{ ucfirst($s->position) }}</td>
                        <td>{{ $s->gender }}</td>
                        <td>{{ $s->location }}</td>
                        <td>
                            @if($s->status == 'continuing')
                                <span class="badge bg-success">{{ $s->status }}</span>
                            @elseif($s->status == 'dismissed' || $s->status == 'suspended')
                                <span class="badge bg-danger">{{ $s->status }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $s->status }}</span>
                            @endif
                        </td>
                        <td>{{ date('D, d M, Y h:i a',strtotime($s->created_at)) }}</td>
                        <td>
                            @if($s->updated_at != null)
                                {{ date('D, d M, Y h:i a',strtotime($s->updated_at)) }}
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="" method="post">
                                @csrf
                                <button class="btn btn-outline-primary btn-sm rounded-0 view_staff" type="button" value="{{ $s->id }}" name="">View</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('staff.staff-details-modal')

@endsection

@push('body-scripts')
    <script src="{{ asset('') }}js/jquery.min.js"></script>
    <script src="{{ asset('js/datatable.min.js') }}"></script>
    <script>
        $(document).ready(function(){
            //Add Datatble to Staff
            $("#staff-table").DataTable();

            //Close Modal
            $("#close_btn").on('click',function(){
                $("#staffModal").modal('hide');
            })

            //Fetch the Staff data to Modal
            $(".view_staff").on('click',function(){
                var id = $(this).val();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('staff.data.to.modal') }}",
                    data: {
                        id:id
                    },
                    success: function(data) {
                        //console.log(data[0]);

                        function capitalize(string_val){
                            var first_letter = (string_val[0]).toUpperCase();
                            var other_letters = string_val.slice(1);
                            var capitalized = first_letter+other_letters;
                            return capitalized;
                        }

                        $('input[name="staff_id"]').val(data[0].id);
                        $('input[name="lname"]').val(capitalize(data[0].lname));

                        if(!(data[0].mname == null) && !(data[0].mname == "")){
                            $('input[name="mname"]').val(capitalize(data[0].mname));
                        }else{
                            $('input[name="mname"]').val(null);
                        }

                        $('input[name="fname"]').val(capitalize(data[0].fname));
                        $('input[name="email"]').val(data[0].email);
                        $('input[name="contact"]').val('0'+(data[0].contact));
                        $('input[name="nin"]').val(data[0].nin);
                        $('input[name="location"]').val(data[0].location);
                        $('textarea[name="subjects"]').val(data[0].subjects);
                        $('input[name="classname"]').val(data[0].class);
                        $('select[name="gender"]').val(data[0].gender);
                        $('select[name="position"]').val(data[0].position);
                        $('select[name="status"]').val(data[0].status);                        

                        $("#staffModal").modal('show');
                    },
                    error: function(error) {
                        alert('Failed to Save Staff details');
                    }
                });
            });

        });
    </script>
@endpush