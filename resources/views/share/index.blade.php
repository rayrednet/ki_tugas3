<x-app-layout>
    <div class="row d-flex flex-column">
        <p class="fs-4 text-dark my-3">Bagikan Key</p>
        <hr>
        <div class="container">
            @if(isset($key))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Key anda!</strong>
                    <p class="text-break my-1 fs-6">{{ $key }}</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <form action="{{ route('share.show') }}" method="get">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ $errors->all()[0] }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="form-outline mb-4">
                    <label class="form-label" for="nama">ID User Tujuan Enkripsi</label>
                    <input type="text" id="user_id" name="user_id" class="form-control" placeholder="Tuliskan ID user yang akan anda berikan key anda..." required />
                </div>
                <div class="justify-content-center d-flex">
                    <button type="submit" class="btn btn-primary btn-block mb-4 px-4">Enkripsi Key!</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
