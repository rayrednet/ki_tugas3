<x-app-layout>
    <div class="row d-flex flex-column">
        <p class="fs-4 text-dark my-3">Daftar File User Lain</p>
        <hr>
        <div class="container">
            @if(count($daftar_file) == 0)
                <p class="fs-5 fw-bold text-center">Daftar file kosong, user ini belum mengupload file apapun.</p>
            @endif
            <div class="row mx-2 d-flex flex-row">
                @foreach ($daftar_file as $file)
                    <div class="col-lg-6">
                        <div class="my-3">
                            <!-- Content for the column -->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3 text-center">{{ $file['nama_file'] }}</h5>
                                    <div class="d-flex justify-content-around">
                                        <form method="post" action="{{ route('share.file.download') }}">
                                            @csrf
                                            <input type="text" name="key_user" value="{{ $key }}" hidden readonly />
                                            <input type="text" name="id" value="{{ $file['id'] }}" hidden readonly />
                                            <button type="submit" class="btn btn-block btn-light border border-dark"><i class="fs-6 bi-download"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
