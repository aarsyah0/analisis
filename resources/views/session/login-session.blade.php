@extends('layouts.user_type.guest')

@section('content')
    <main class="main-content  mt-0">
        <section>
            <div class="page-header min-vh-75">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column mx-auto">
                            <div class="card card-plain mt-8">
                                <div class="card-header pb-0 text-left bg-transparent">
                                    <h3 class="font-weight-bolder"
                                        style="background-image: linear-gradient(310deg, #cb0c9f, #d94db6, #f199d9);
                                            -webkit-background-clip: text;
                                            -webkit-text-fill-color: transparent;
                                            background-clip: text;">
                                        Sign In
                                    </h3>
                                    {{-- <p class="mb-0">Create a new acount<br></p>
                                    <p class="mb-0">OR Sign in with these credentials:</p> --}}
                                </div>
                                <div class="card-body">
                                    <form role="form" method="POST" action="/session">
                                        @csrf
                                        <label>Email</label>
                                        <div class="mb-3">
                                            <input type="email" class="form-control" name="email" id="email"
                                                placeholder="Email" value="admin@gmail.com" aria-label="Email"
                                                aria-describedby="email-addon">
                                            @error('email')
                                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <label>Password</label>
                                        <div class="mb-3">
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="password" id="password"
                                                    placeholder="Password" value="secret" aria-label="Password"
                                                    aria-describedby="password-addon">
                                                <span class="input-group-text" id="password-addon" style="cursor: pointer;"
                                                    onclick="togglePassword()">
                                                    <i class="fa-solid fa-eye" id="eye-icon"></i>
                                                </span>
                                            </div>
                                            @error('password')
                                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <script>
                                            function togglePassword() {
                                                var passwordField = document.getElementById("password");
                                                var eyeIcon = document.getElementById("eye-icon");

                                                // Toggle password visibility
                                                if (passwordField.type === "password") {
                                                    passwordField.type = "text";
                                                    eyeIcon.classList.remove("fa-eye");
                                                    eyeIcon.classList.add("fa-eye-slash");
                                                } else {
                                                    passwordField.type = "password";
                                                    eyeIcon.classList.remove("fa-eye-slash");
                                                    eyeIcon.classList.add("fa-eye");
                                                }
                                            }
                                        </script>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="rememberMe" checked="">
                                            <label class="form-check-label" for="rememberMe">Remember me</label>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn text-white w-100 mt-4 mb-0"
                                                style="background-image: linear-gradient(310deg, #cb0c9f, #d94db6, #f199d9);">Sign
                                                in</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="oblique position-absolute top-0 h-100 d-md-block d-none me-n8">
                                <div class="oblique-image bg-cover position-absolute fixed-top ms-auto h-100 z-index-0 ms-n6"
                                    style="background-image:url('../assets/img/curved-images/tc.jpg')"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
