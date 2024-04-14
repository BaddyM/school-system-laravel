@extends('common.header')

@section('title')
    Classes
@endsection

@section('body')
<div class="container-fluid">
    <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Classes</h5>

    <div class="row justify-content-between">
        <div class="col-md-4">
            <p class="mb-2 text-center fw-bold h5 text-uppercase" style="color: purple;">
                <span class="underline">Classes in School</span>
            </p>
            <div class="class_list">
                @foreach ($classes as $class)
                    <div class="d-flex justify-content-between align-items-center">
                        <div>{{ $class->class }}</div>
                        <div class="fw-bold">{{ $class->level }}</div>
                        <div>
                            <form method="post" class="mb-0 delete_class_form">
                                @csrf
                                <input type="hidden" value="{{ $class->id }}" name="delete_id">
                                <button class="btn btn-outline-danger rounded-5" type="submit"><i class="fa fa-x"></i></button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-md-4">
            <p class="mb-2 text-center fw-bold h5 text-uppercase" style="color: purple;">
                <span class="underline">Add Class</span>
            </p>

            <form method="post" id="add_class_form">
                @csrf
                <div>
                    <label class="form-label fw-bold h6">Class</label>
                    <input type="text" class="form-control rounded-0" name="classname" placeholder="Enter Class" required>
                </div>

                <div class="mt-2">
                    <label class="form-label fw-bold h6">Level</label>
                    <select name="level" class="form-select rounded-0">
                        @php
                            $level = array('O Level', 'A Level');
                        @endphp
                        @foreach ($level as $l)
                            <option value="{{ $l }}">{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="submit-btn mt-3" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('body-scripts')
    <script src="{{ asset('') }}js/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            //Add Class here
            $("#add_class_form").on('submit',function(e){
                e.preventDefault();
                $.ajax({
                    type:"POST",
                    url:"{{ route('class.add') }}",
                    data:new FormData(this),
                    processData:false,
                    contentType:false,
                    cache:false,
                    success:function(response){
                        alert(response);
                        location.reload();
                    },error:function(){
                        alert("Failed to Add Class!");
                    }
                });
            });

            //Delete Class
            $(".delete_class_form").on('submit',function(e){
                e.preventDefault();
                const confirm_delete = confirm("Are you sure?");
                if(confirm_delete == true){
                    $.ajax({
                        type:"POST",
                        url:"{{ route('class.delete') }}",
                        data:new FormData(this),
                        processData:false,
                        contentType:false,
                        cache:false,
                        success:function(response){
                            alert(response);
                            location.reload();
                        },error:function(){
                            alert("Failed to Delete Class!");
                        }
                    });
                }
            });

        });
    </script>
@endpush