@extends('common.header')

@section('title')
    School Details
@endsection

<style>
    label {
        margin-top: 10px;
    }
</style>

@section('body')
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Update School Details</h5>

        <div class="row justify-content-between">
            <div class="col-md-4 mb-3">
                <form action="" method="post" id="save_school" enctype="multipart/form-data">
                    <div>
                        <label for="" class="form-label fw-bold">School Name</label>
                        <input type="text" class="form-control rounded-0" name="school_name" id="school_name" required>
                    </div>

                    <div>
                        <label for="" class="form-label fw-bold">School Motto</label>
                        <input type="text" class="form-control rounded-0" name="school_motto" id="school_motto" required>
                    </div>

                    <div>
                        <label for="" class="form-label fw-bold">School Address</label>
                        <input type="text" class="form-control rounded-0" name="school_address" id="school_address" required>
                    </div>

                    <div>
                        <label for="" class="form-label fw-bold">School Contacts</label>
                        <input type="text" class="form-control rounded-0" name="school_contacts" id="school_contacts" required>
                    </div>

                    <div>
                        <label for="" class="form-label fw-bold">School Badge</label>
                        <input type="file" accept=".jpg, .png, .jpeg" class="form-control rounded-0" name="school_badge"
                            id="school_badge" required>
                    </div>

                    <button class="submit-btn mt-3" id="save_school_btn" type="submit">Save</button>
                </form>
            </div>

            <div class="col-md-7 bg-white p-lg-2">
                <p class="fw-bold text-center h5">Current School Deatils</p>

                @foreach ($data as $d)
                    <div>
                        <div class="h5 d-flex" style="gap:20px;">
                            <div class="fw-bold">School Name: </div>
                            <div id="school_name_buffer">{{ $d->school_name }}</div>
                        </div>

                        <div class="h5 d-flex" style="gap:20px;">
                            <div class="fw-bold">School Motto: </div>
                            <div id="school_motto_buffer">{{ $d->motto }}</div>
                        </div>

                        <div class="h5 d-flex" style="gap:20px;">
                            <div class="fw-bold">School Address: </div>
                            <div id="school_address_buffer">{{ $d->address }}</div>
                        </div>

                        <div class="h5 d-flex" style="gap:20px;">
                            <div class="fw-bold">School Contacts: </div>
                            <div id="school_contact_buffer">{{ $d->contact }}</div>
                        </div>

                        <div class="h5 d-flex" style="gap:20px;">
                            <div class="fw-bold">School Badge: </div>
                            <div>
                                @if ($d->school_badge == '' || $d->school_badge == null || $d->school_badge == 'NULL')
                                    <div class="fw-bold text-danger">
                                        No Image
                                    </div>
                                @else
                                    <img src="{{ asset('/') }}school_badge/{{ $d->school_badge }}" class="img-fluid w-50"
                                        alt="" id="school_badge_buffer">
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('body-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#save_school").on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('setting.school.save') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data) {
                        $("#save_school")[0].reset();
                        alert("Successfully Updated School Details");

                        console.log(data);

                        //Update the Front-end
                        $("#school_name_buffer").text(data[0].school_name);
                        $("#school_address_buffer").text(data[0].address);
                        $("#school_contact_buffer").text(data[0].contact);
                        $("#school_motto_buffer").text(data[0].motto);

                        if (data[0].school_badge == 'NULL' || data[0].school_badge ==
                            null || data[0].school_badge == '') {
                            alert("No School Badge");
                        } else {
                            $("#school_badge_buffer").attr('src', '/school_badge/' + data[0]
                                .school_badge + '');
                        }
                    },
                    error: function(error) {
                        alert('Failed to Save school details');
                    }
                });
            });
        });
    </script>
@endpush
