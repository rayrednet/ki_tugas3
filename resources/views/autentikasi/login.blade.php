<form method="post" action="{{ route('autentikasi.login') }}">
    @csrf
    <!-- Email input -->
    <div class="form-outline mb-4">
        <label class="form-label" for="username">Username</label>
        <input type="text" id="username" name="username" class="form-control" required/>
    </div>

    <!-- Password input -->
    <div class="form-outline mb-4">
        <label class="form-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" required/>
    </div>
    <!-- Submit button -->
    <div class="justify-content-center d-flex">
        <button type="submit" class="btn btn-primary btn-block mb-4 px-4">Login</button>
    </div>
</form>
