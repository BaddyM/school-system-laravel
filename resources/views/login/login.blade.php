<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OACMIS - Login</title>
    <link rel="stylesheet" href="{{ asset('/') }}css/bootstrap.css">
    <link rel="stylesheet" href="{{ asset('/') }}css/custom.css">
    <link rel="stylesheet" href="{{ asset('/') }}css/responsive.css">
    <link rel="stylesheet" href="{{ asset('/') }}css/bootstrap-icons/bootstrap-icons.css">
</head>

<body class="login-body">

    <div class="login-container">
        <p class="mb-0 text-center h2 fw-bold text-white align-items-center">OACMIS - LOGIN <i
                class="bi bi-lock-fill"></i></p>
        <form action="{{ route('login.validate') }}" method="post" id="login-form">
            @csrf
            <div>
                {{-- check if there exists any errors --}}
                @if($errors->any())
                    <div class="alert alert-danger rounded-2 bg-gradient px-2 py-2 bg-gradient my-2">
                        @foreach ($errors->all() as $error)
                        {{ $error }}
                        @endforeach
                    </div>
                @endif
            </div>
            <div>
                <label for="" class="form-label fw-bold">Email</label>
                <input type="text" name="email" class="form-control rounded-0" id="email">
                <div class="email-errors bg-danger text-white rounded-1 px-2 py-2 bg-gradient my-2 d-none">
                    
                </div>
            </div>

            <div class="mt-2">
                <label for="" class="form-label fw-bold">Password</label>
                <input type="password" name="password" class="form-control rounded-0" id="password">
                <div class="password-errors bg-danger text-white rounded-1 px-2 py-2 bg-gradient my-2 d-none">
                    
                </div>
            </div>

            <button class="btn mt-2 mb-3 w-100 bg-gradient submit-btn rounded-0" type="submit" id="login-btn" disabled>Login</button>
        </form>
    </div>

    <script src="{{ asset('/') }}js/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#login-btn").removeClass('submit-btn').addClass('submit-btn-disabled');

            $("#email, #password").on('change',function(){
                $("#login-btn").addClass('submit-btn').removeClass('submit-btn-disabled').prop('disabled',false);
            })
        })
    </script>
</body>

</html>
