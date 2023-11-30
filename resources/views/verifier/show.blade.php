<x-app-layout>
    <div class="row d-flex flex-column">
        <p class="fs-4 text-dark my-3">Hasil Pengecekan</p>
        <hr>
        <div class="container">
            @if($verifier)
                <div class="alert alert-success fade show" role="alert">
                    <strong>Dokumen ditandatangani oleh {{ $username }}!</strong>
                </div>
            @else
                <div class="alert alert-danger fade show" role="alert">
                    <strong>Dokumen TIDAK ditandatangani oleh {{ $username }}!</strong>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
