<x-app-layout>
    <div class="row d-flex flex-column">
        <p class="fs-4 text-dark my-3">Daftar File</p>
        <hr>
        <div class="col d-flex justify-content-end mb-3">
            <a class="btn btn-block btn-primary fs-5" href="{{ route('file.create') }}"><i class="fs-5 bi-file-earmark-arrow-up"></i> Upload
                File</a>
        </div>
        <div class="container">
            @if(count($daftar_file) == 0)
                <p class="fs-5 fw-bold text-center">Daftar file kosong, anda belum mengupload file apapun.</p>
            @endif
            <div class="row mx-2 d-flex flex-row">
                @foreach ($daftar_file as $file)
                    <div class="col-lg-6">
                        <div class="my-3">
                            <!-- Content for the column -->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3 text-center">{{ $file['nama_file'] }}</h5>
                                    {{-- <img src="{{ $file['nama_file_fisik'] }}" alt="{{ $file['nama_file'] }}"> --}}
                                    <div class="d-flex justify-content-around">
                                        <form method="get" action="{{ route('file.show', ['id' => $file['id']]) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-block btn-light border border-dark"><i class="fs-6 bi-download"></i></button>
                                        </form>
                                        <form method="post" action="{{ route('file.delete', ['id' => $file['id']]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-block btn-light border border-dark"><i class="fs-6 bi-trash"></i></button>
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
