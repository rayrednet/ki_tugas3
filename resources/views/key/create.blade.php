<x-app-layout>
    <div class="row d-flex flex-column">
        <p class="fs-4 text-dark my-3">Masukkan Key RSA Anda untuk Signature</p>
        <hr>
        <div class="container">
            <form action="{{ route('key.store') }}" method="post">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ $errors->all()[0] }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="form-outline mb-4">
                    <label class="form-label" for="nama">Private Key</label>
                    <input type="text" id="private_key" name="private_key" class="form-control" value="" required />
                </div>
                <div class="justify-content-center d-flex">
                    <button type="submit" class="btn btn-primary btn-block mb-4 px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
