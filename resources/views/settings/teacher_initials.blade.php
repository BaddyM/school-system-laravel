@extends('common.header')

@section('title')
    Initials
@endsection

@section('body')
<style>
    tr td{
        vertical-align: middle;
    }

    tr th, tr td{
        text-align: center;
    }
</style>
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Teacher Initials</h5>
        
        <!-- Modal Body -->
        <div class="modal fade" id="teacher_initials_modal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">
                            Edit Teacher's Initials
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" id="initials_edit_form">
                            @csrf
                            <input type="hidden" name="update_initials_id">
                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Select Subjects</label>
                                <select name="subject_edit" class="form-select rounded-0 border-dark" required>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
        
                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Select Class</label>
                                <select name="classname_edit" class="form-select rounded-0 border-dark" required>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->class }}">{{ $class->class }}</option>
                                    @endforeach
                                </select>
                            </div>
        
                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Teacher's Name</label>
                                <input type="text" class="form-control rounded-0 border-dark" name="teacher_name_edit" placeholder="Enter Teacher's Name" required>
                            </div>
        
                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Teacher's Initials</label>
                                <input type="text" class="form-control rounded-0 border-dark" name="initials_edit" placeholder="Enter Teacher's Initials" required>
                            </div>
        
                            <button class="submit-btn" type="submit">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>       
               

        <div class="row justify-content-between">
            <div class="col-md-7 overflow-scroll">
                @if (count($data) > 0)
                <table class="table">
                    <thead>
                        <tr class="table-dark">
                            <th scope="col">#</th>
                            <th scope="col">Subject</th>
                            <th scope="col">Class</th>
                            <th scope="col">Teacher's Name</th>
                            <th scope="col">Initials</th>
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
                                <td>{{ $d->subject }}</td>
                                <td>{{ $d->class }}</td>
                                <td>{{ $d->teacher_name }}</td>
                                <td>{{ $d->initials }}</td>
                                <td>
                                    <form action="" method="post">
                                        <button title="Edit" value="{{ $d->id }}" class="btn btn-outline-success rounded-5 edit_btn"><i class="fa fa-pen"></i></button>
                                        <button title="Delete" value="{{ $d->id }}"  class="btn btn-outline-danger rounded-5 delete_btn"><i class="fa fa-x"></i></button>
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

            <div class="col-md-4">
                <form action="" method="post" id="initials_form">
                    @csrf
                    <div class="mb-3">
                        <label for="" class="form-label fw-bold">Select Subjects</label>
                        <select name="subject" class="form-select rounded-0 border-dark" required>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label fw-bold">Select Class</label>
                        <select name="classname" class="form-select rounded-0 border-dark" required>
                            @foreach ($classes as $class)
                                <option value="{{ $class->class }}">{{ $class->class }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label fw-bold">Teacher's Name</label>
                        <input type="text" class="form-control rounded-0 border-dark" name="teacher_name" placeholder="Enter Teacher's Name" required>
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label fw-bold">Teacher's Initials</label>
                        <input type="text" class="form-control rounded-0 border-dark" name="initials" placeholder="Enter Teacher's Initials" required>
                    </div>

                    <button class="submit-btn" type="submit">Save</button>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('body-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            //Add teacher initials
            $("#initials_form").on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('setting.initials.save') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data) {
                        $("#initials_form")[0].reset();
                        alert("Successfully Added Initials");
                        location.reload();
                    },
                    error: function(error) {
                        alert('Failed to Add Initials');
                    }
                });
            });

            //Display Initials in Modal
            $(".edit_btn").on('click',function(e){
                e.preventDefault();
                var id = $(this).val();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:"{{ route('setting.initials.edit') }}",
                    data:{
                        id:id
                    },
                    success:function(data){
                        //console.log(data.data[0]);
                        //Show the modal
                        $("#teacher_initials_modal").modal('show');

                        //Add the data to the inputs
                        $("input[name='update_initials_id']").val(data.data[0].id);
                        $("select[name='subject_edit']").val(data.data[0].subject);
                        $("select[name='classname_edit']").val(data.data[0].class);
                        $("input[name='teacher_name_edit']").val(data.data[0].teacher_name);
                        $("input[name='initials_edit']").val(data.data[0].initials);
                    },
                    error:function(){
                        alert("Failed to Delete Signature");
                    }
                });
                
            });

            //Update Edited Values
            $("#initials_edit_form").on('submit', function(e) {
                e.preventDefault();
                //Hide the modal
                $("#teacher_initials_modal").modal('hide');
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('setting.initials.update') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data) {
                        $("#initials_form")[0].reset();
                        alert("Successfully Updated Initials");
                        location.reload();
                    },
                    error: function(error) {
                        alert('Failed to Add Initials');
                    }
                });
            });

            //Delete Initials
            $(".delete_btn").on('click',function(e){
                e.preventDefault();
                var id = $(this).val();
                console.log("id = "+id);

                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:"{{ route('setting.initials.delete') }}",
                    data:{
                        id:id
                    },
                    success:function(){
                        alert('Successfully Deleted Initial');
                        location.reload();
                    },
                    error:function(){
                        alert("Failed to Delete Signature");
                    }
                });
            });

        });
    </script>
@endpush
