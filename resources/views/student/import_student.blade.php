@extends('common.header')

@section('title')
    Import Students
@endsection

<style>
    /*Add a full stop after the ordered list*/
    ol li::after {
        content: '.';
    }

    ul li{
        font-weight: bold;
    }
</style>

@section('body')
    <div class="body-container">
        <h5 class="mb-0 text-uppercase fw-bold text-center" style="color: purple;">Import Student's Bio Data</h5>

        <div class="d-flex justify-content-between">
            <h6 class="d-flex">Note:&nbsp;<span style="color:red; font-style:italic;">Fill in all the mandatory fields</span>
            </h6>
            <div>
                <a href="{{ asset('/') }}student_import/student_import.xlsx"
                    class="nav-link btn btn-secondary rounded-0 text-white bg-gradient px-3 py-2" download>
                    Download Student File
                </a>
            </div>
        </div>

        <ul>
            <li>First Name</li>
            <li>Last Name</li>
            <li>Class</li>
            <li>Stream</li>
            <li>Section</li>
            <li>Gender</li>
            <li>Nationality</li>
        </ul>{{-- Mandatory fields --}}

        <div>
            <h6 class="text-primary fw-bold">>>>>> Procedure to Import multiple students <<<<< </h6>
                    <ol>
                        <li>Download the Student File</li>
                        <li>Fill in the Mandatory fields</li>
                        <li>Save the file</li>
                        <li>Upload the Saved File</li>
                    </ol>
        </div>

        <form method="post" id="std_upload_form" enctype="multipart/form-data">
            <h6 class="text-primary fw-bold">Upload the EXCEL File here</h6>
            <input type="file" accept=".csv, .xls, .xlsx" class="form-control w-50 rounded-0" name="std_upload_file"
                id="std_upload_file">
            <button class="submit-btn-disabled mt-3" type="submit" id="upload-btn" data-bs-toggle="tooltip"
                data-bs-placement="right" data-bs-title="submit" disabled>Submit</button>
        </form>
    </div>
@endsection

@push('body-scripts')
    <script src="{{ asset('') }}js/jquery.min.js"></script>
    <script src="{{ asset('') }}js/bootstrap.bundle.js"></script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        $(document).ready(function() {

            $('.body-container').hide().slideDown();

            $("#std_upload_file").on('change', function() {
                $("#upload-btn").removeClass('submit-btn-disabled').addClass('submit-btn').prop('disabled',
                    false);
            })

            //Upload the file here
            $("#std_upload_form").on('submit', function(event) {
                //Prevent page refresh
                event.preventDefault();
                $.ajax({
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('student.import.file') }}',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(response) {
                        alert(response);
                        $('#std_upload_form')[0].reset();
                        $("#upload-btn").addClass('submit-btn-disabled').removeClass(
                            'submit-btn').prop('disabled', true);
                    },
                    error: function(response) {
                        alert('Error: Records not Added');
                    }
                })
            })
        })
    </script>
@endpush
