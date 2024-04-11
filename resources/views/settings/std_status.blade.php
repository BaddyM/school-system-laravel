@extends('common.header')

@section('title')
    Status List
@endsection

<style>
    label,
    p {
        margin-top: 10px;
        font-weight: bold;
        font-size: 14px;
    }
</style>

@section('body')
    <style>
        tr th {
            color: white;
            text-align: center;
        }

        td {
            text-align: center;
            vertical-align: middle;
            padding: 7px !important;
        }
    </style>
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Add/Update Status List</h5>

        <div class="row justify-content-between">
            <div class="col-md-4 bg-light">              
                <div>
                    <h5 class="mb-0 text-uppercase fw-bold text-center mb-3 mt-3" style="color: purple;">
                        <span class="underline">
                            Add New Status</span>
                    </h5>
                    <form action="" method="post" id="add_status_form">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <label for="">Status</label>
                                <input type="text" name="add_status"
                                    class="form-control rounded-0" placeholder="Status" required>
                            </div>
                        </div>

                        <button class="submit-btn mt-3" type="submit">add</button>
                    </form>
                </div>
            </div>
            <div class="card col-md-7 bg-white pt-3">
                <div class="card-header bg-light">
                    <p class="text-center mb-0 h5 fw-bold">Status List</p>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr class="bg-dark">
                                <th scope="col">#</th>
                                <th scope="col">Status</th>
                                @if (Auth::user()->is_admin == 1 || Auth::user()->is_super_admin == 1)
                                    <th scope="col">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $counter = 0;
                            @endphp
                            @foreach ($data as $d)
                                <tr>
                                    <td>{{ $counter += 1 }}</td>
                                    <td>{{ $d->status }}</td>
                                    @if (Auth::user()->is_admin == 1 || Auth::user()->is_super_admin == 1)
                                        @if($d->status != 'continuing' && $d->status != 'suspended' && $d->status != 'dismissed' && $d->status != 'completed')
                                        <td>
                                            <form action="" method="post" class="delete_status_form mb-0">
                                                <input type="hidden" value="{{ $d->id }}" name="delete_status">
                                                <button title="Delete" class="btn btn-outline-danger py-2 rounded-5"
                                                    type="submit">
                                                    <i class="fa fa-x"></i>
                                                </button>
                                            </form>
                                        </td>
                                        @else
                                            <td>
                                                <span class="badge" style="background:purple;">default</span>
                                            </td>
                                        @endif
                                    @endif
                                </tr>                                
                            @endforeach
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

            //Add Status
            $("#add_status_form").on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('setting.status.add') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data) {
                        alert(data);
                        location.reload();
                    },
                    error: function(error) {
                        alert('Failed to Update Status');
                    }
                });
            });

            //Delete Status
            $(".delete_status_form").on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('setting.status.delete') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data) {
                        alert(data);
                        location.reload();
                    },
                    error: function(error) {
                        alert('Failed to Update Status');
                    }
                });
            });

        });
    </script>
@endpush
