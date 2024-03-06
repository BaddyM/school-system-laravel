@extends('common.header')

@section('title')
    Home
@endsection

@section('body')
    <div class="container-fluid">
        <div class="card-container">
            <div class="card col-md-3 border-0 card-one">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-block">
                            <p class="mb-0 h2 text-white">{{ count($students) }}</p>
                            <p class="mb-0 text-white">Active Students</p>
                        </div>
                        <div>
                            <i class="bi bi-mortarboard-fill" style="font-size:40px;"></i>
                        </div>
                    </div>
                </div>
            </div>{{-- card 1 --}}

            <div class="card col-md-3 border-0 card-two">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-block">
                            <p class="mb-0 h2 text-white">{{ count($subjects) }}</p>
                            <p class="mb-0 text-white">Subjects</p>
                        </div>
                        <div>
                            <i class="bi bi-book-half" style="font-size:40px;"></i>
                        </div>
                    </div>
                </div>
            </div>{{-- card 2 --}}

            <div class="card col-md-3 border-0 card-three">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-block">
                            <p class="mb-0 h2 text-white">{{ count($staff) }}</p>
                            <p class="mb-0 text-white">Active Staff</p>
                        </div>
                        <div>
                            <i class="bi bi-people-fill text-white" style="font-size:40px;"></i>
                        </div>
                    </div>
                </div>
            </div>{{-- card 3 --}}

        </div>{{-- cards --}}

        @php
            $planner = [
                [   'date' => '04-02-2024',
                    'activity' => 'Term Re-open'
                ],
                [   'date' => '05-02-2024',
                    'activity' => 'Term Re-open'
                ],
                [   'date' => '10-03-2024',
                    'activity' => 'General Cleaning'
                ],
                [   'date' => '11-03-2024',
                    'activity' => 'Guidance & Counselling'
                ],
                [   'date' => '15-03-2024',
                    'activity' => 'Books checking'
                ],
                [   'date' => '07-04-2024',
                    'activity' => 'Visitation Day'
                ],
                [   'date' => '18-04-2024',
                    'activity' => 'PTA Meeting'
                ],
                [   'date' => '27-04-2024',
                    'activity' => 'Holiday Break off'
                ]
            ];
        @endphp

        <div class="mt-4">
            <p class="mb-2 h5">Term Planner <span class="text-danger fst-italic h6">(Scroll for more)</span></p>
            <div class="term-planner">
                @foreach ($planner as $p)
                <div class="d-flex justify-content-between mb-2">
                    <div>
                        <p class="mb-0">{{ date('d M, Y', strtotime($p['date'])) }}</p>
                    </div>
                    <span> - </span>
                    <div>
                        <p class="mb-0">{{ $p['activity'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>{{-- term-planner --}}

    </div>
@endsection

@push('body-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/datatable.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.bundle.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    
@endpush
