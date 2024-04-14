@extends('common.header')

@section('title')
    User
@endsection

@section('body')
<div class="container-fluid">
    <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Users</h5>

    <div class="card rounded-0 border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-hover" id="users-table">
                <thead>
                    <tr class="table-light">
                        <th scope="col">#</th>
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                        <th scope="col">Dept</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $counter = 0;
                    @endphp
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $counter += 1 }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->dept }}</td>
                            <td>
                                @if($user->is_admin == 1)
                                    <span class="badge bg-primary">admin</span>
                                @elseif($user->is_super_admin == 1)
                                    <span class="badge bg-success">super admin</span>
                                @elseif($user->is_teacher == 1)
                                    <span class="badge" style="background:purple;">teacher</span>
                                @elseif($user->is_bursar == 1)
                                    <span class="badge bg-warning text-dark">bursar</span>
                                @elseif($user->is_librarian == 1)
                                    <span class="badge bg-info">librarian</span>
                                @else
                                    <span class="badge bg-secondary">other</span>
                                @endif

                            </td>
                            <td>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('body-scripts')
    <script src="{{ asset('') }}js/jquery.min.js"></script>
    <script src="{{ asset('') }}js/datatable.min.js"></script>
    <script>
        $(document).ready(function(){
            //Datatable
            $("#users-table").DataTable();

            //Add Stream here
            $("#add_user_form").on('submit',function(e){
                e.preventDefault();
                $.ajax({
                    type:"POST",
                    url:"{{ route('stream.add') }}",
                    data:new FormData(this),
                    processData:false,
                    contentType:false,
                    cache:false,
                    success:function(response){
                        alert(response);
                        location.reload();
                    },error:function(){
                        alert("Failed to Add User!");
                    }
                });
            });

            //Delete User
            $(".delete_user_form").on('submit',function(e){
                e.preventDefault();
                const confirm_delete = confirm("Are you sure?");
                if(confirm_delete == true){
                    $.ajax({
                        type:"POST",
                        url:"{{ route('stream.delete') }}",
                        data:new FormData(this),
                        processData:false,
                        contentType:false,
                        cache:false,
                        success:function(response){
                            alert(response);
                            location.reload();
                        },error:function(){
                            alert("Failed to Delete User!");
                        }
                    });
                }
            });

        });
    </script>
@endpush