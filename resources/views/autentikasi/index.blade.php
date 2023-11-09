<x-guest-layout>
    <div class="col-12 d-flex flex-fill justify-content-center align-items-center">
        <div class="col-6">
            <ul class="nav nav-pills nav-justified mb-3" id="ex1" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-login" data-bs-toggle="pill" role="tab"
                        data-bs-target="#pills-login" aria-selected="true" type="button">Login</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-register" data-bs-toggle="pill" role="tab"
                        data-bs-target="#pills-register" aria-selected="false">Register</button>
                </li>
            </ul>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> {{ $errors->all()[0] }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif


            <div class="tab-content">
                <div class="tab-pane fade show active" id="pills-login" role="tabpanel" aria-labelledby="tab-login">
                    @include('autentikasi.login')
                </div>
                <div class="tab-pane fade" id="pills-register" role="tabpanel" aria-labelledby="tab-register">
                    @include('autentikasi.register')
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
