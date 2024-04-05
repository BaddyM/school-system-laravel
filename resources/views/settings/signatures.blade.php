@extends('common.header')

@section('title')
    Signatures
@endsection

@section('body')
    <style>
        tr th {
            color: white;
            text-align: center;
        }

        tr td {
            text-align: center;
            vertical-align: middle;
            font-size: 14px;
        }
    </style>

    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Update/Add Signatures</h5>

        <div class="my_alert">
            
        </div>
        
        <script>
            var alertList = document.querySelectorAll(".alert");
            alertList.forEach(function (alert) {
                new bootstrap.Alert(alert);
            });
        </script>
        
        

        <div class="row justify-content-between">
            <div class="col-md-5">
                @if (count($data)>0)
                    <table class="table w-100">
                        <thead>
                            <tr style="background:purple;">
                                <th scope="col">#</th>
                                <th scope="col">Signatory</th>
                                <th scope="col">Signature</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $counter = 0;
                            @endphp
                            @foreach ($data as $d)
                                <tr>
                                    <td>{{ $counter += 1 }}</td>
                                    <td>{{ $d->signatory }}</td>
                                    <td><img class="img-fluid w-50 img-thumbnail"
                                            src="{{ asset('/images/signatures/' . $d->signature . '') }}" alt="">
                                    </td>
                                    <td>
                                        <form action="" method="post" class="delete_form">
                                            <input type="hidden" name="delete_id" value="{{ $d->id }}">
                                            <button title="Delete" class="btn btn-outline-danger rounded-5"
                                            type="submit"><i class="fa fa-x"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                <p class="text-center fw-bold text-danger">Empty Set</p>
                @endif
            </div>

            <div class="col-md-5">
                <form method="post" id="signature_form">
                    @csrf
                    <div class="mb-3">
                        <label for="" class="form-label fw-bold">Signatory</label>
                        <select name="signatory" id="" class="form-select rounded-0 border-dark" required>
                            <option value="head-teacher">Head Teacher</option>
                            <option value="dos">D.O.S</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label fw-bold">Upload Signature</label>
                        <input type="file" accept=".png, .jpg, .jpeg" class="form-control rounded-0 border-dark"
                            name="signature" required>
                    </div>

                    <button class="submit-btn" type="submit">Upload</button>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('body-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            //Add a signature
            $("#signature_form").on('submit', function(e) {
                e.preventDefault();
                $('.alert').alert()
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('setting.signatures.upload') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data) {
                        $("#signature_form")[0].reset();
                        //alert("Successfully Uploaded Signature")
                        $(".my_alert").append('<div class="alert alert-success alert-dismissible fade show" role="alert">\
                            <button\
                                type="button"\
                                class="btn-close"\
                                data-bs-dismiss="alert"\
                                aria-label="Close"></button>\
                            <strong>Success</strong> Signature Successfully Saved\
                        </div>')

                        setTimeout(function(){
                            location.reload();
                        },2000);
                    },
                    error: function(error) {
                        alert('Failed to Save Subject details');
                    }
                });
            });

            //Delete a Signature
            $(".delete_form").on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('setting.signatures.delete') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data) {
                        $(".delete_form")[0].reset();
                        alert("Successfully Deleted Signature");
                        location.reload();
                    },
                    error: function(error) {
                        alert('Failed to Delete Signature');
                    }
                });
            });

        });
    </script>
@endpush
