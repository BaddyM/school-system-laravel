<div class="modal fade" id="staffModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
        <div class="modal-content bg-muted">
            <div class="modal-header justify-content-between bg-info">
                <div></div>
                <h5 class="modal-title text-uppercase fw-bold" id="modalTitleId">Staff details</h5>
                <div id="close_btn" title="Close"><i class="fa fa-x" style="font-size:18px; color:red; font-weight:bold; cursor:pointer;"></i></div>
            </div>
            <div class="modal-body">
                <div class="row justify-content-between">
                    <div class="col-md-3">
                        <img class="img-thumbnail img-fluid" style="width:200px; height:200px; object-fit:Contain;"
                            src="{{ asset('/images/static/male.jpg') }}" alt="" id="staff_image">
                    </div>{{-- Image Section --}}

                    <div class="col-md-8">
                        <form action="" method="post" class="mb-0">
                            <input type="hidden" name="staff_id">
                            <div class="row justify-content-between">
                                <div class="col mb-2">
                                    <label for="" class="form-label fw-bold">Last Name</label>
                                    <input type="text" class="form-control rounded-0" name="lname" placeholder="Last Name">
                                </div>
    
                                <div class="col mb-2">
                                    <label for="" class="form-label fw-bold">Middle Name</label>
                                    <input type="text" class="form-control rounded-0" name="mname" placeholder="Middle Name">
                                </div>
    
                                <div class="col mb-2">
                                    <label for="" class="form-label fw-bold">First Name</label>
                                    <input type="text" class="form-control rounded-0" name="fname" placeholder="First Name">
                                </div>
                            </div>{{-- Staff Name --}}

                            <div class="row justify-content-between">
                                <div class="col mb-2">
                                    <label for="" class="form-label fw-bold">Gender</label>
                                    <select name="gender" class="form-select rounded-0">
                                        @php
                                            $gender = array('Male','Female');
                                        @endphp
                                        @foreach ($gender as $g)
                                            <option value="{{ $g }}">{{ $g }}</option>
                                        @endforeach
                                    </select>
                                </div>
    
                                <div class="col mb-2">
                                    <label for="" class="form-label fw-bold">Position</label>
                                    <select name="position" class="form-select rounded-0">
                                        @foreach ($positions as $position)
                                            <option value="{{ $position->position }}">{{ ucfirst($position->position) }}</option>
                                        @endforeach
                                    </select>
                                </div>
    
                                <div class="col mb-2">
                                    <label for="" class="form-label fw-bold">Status</label>
                                    <select name="status" class="form-select rounded-0">
                                        @foreach ($status as $s)
                                            <option value="{{ $s->status }}">{{ ucfirst($s->status) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row justify-content-between">
                                <div class="col mb-2">
                                    <label for="" class="form-label fw-bold">Email</label>
                                    <input type="text" class="form-control rounded-0" name="email" placeholder="Email">
                                </div>
    
                                <div class="col mb-2">
                                    <label for="" class="form-label fw-bold">Contact</label>
                                    <input type="number" class="form-control rounded-0" name="contact" placeholder="Contact">
                                </div>
                            </div>

                            <div class="row justify-content-between">
                                <div class="col mb-2">
                                    <label for="" class="form-label fw-bold">NIN</label>
                                    <input type="text" class="form-control rounded-0" name="nin" placeholder="NIN">
                                </div>
    
                                <div class="col mb-2">
                                    <label for="" class="form-label fw-bold">Location</label>
                                    <input type="text" class="form-control rounded-0" name="location" placeholder="Location">
                                </div>
                            </div>

                            <div class="row justify-content-between">
                                <div class="col mb-2">
                                    <label for="" class="form-label fw-bold">Subjects</label>
                                    <textarea class="form-control rounded-0" name="subjects" placeholder="Subjects"></textarea>
                                </div>
    
                                <div class="col mb-2">
                                    <label for="" class="form-label fw-bold">Class</label>
                                    <select name="classname" class="form-select rounded-0">
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->class }}">{{ ucfirst($class->class) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </form>
                    </div>{{-- Other data here --}}
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button id="edit-btn" class="submit-btn px-4 bg-gradient rounded-0 text-uppercase" value=""
                    type="button">update</button>
            </div>
        </div>
    </div>
</div>
