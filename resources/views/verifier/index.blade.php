<x-app-layout>
    <div class="row d-flex flex-column">
        <p class="fs-4 text-dark my-3">Verifikasi Penandatangan</p>
        <hr>
        <div class="container">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">User Tersedia</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($daftar_user as $user)
                        <tr>
                            <td>{{ $user['username'] }}</td>
                            <td>
                                <button type="button" class="btn btn-primary btn-request" data-bs-toggle="modal"
                                    data-bs-target="#modal"
                                    data-user="{{ json_encode(['username' => $user['username'], 'id' => $user['id']]) }}">
                                    Verifikasi
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bagikan Informasi Anda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('verifikasi.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="text" id="user_id" name="user_id" class="form-control" value="" readonly
                            hidden required />
                        <div class="form-outline mb-4">
                            <label class="form-label" for="nama">Username</label>
                            <input type="text" id="username" name="username" class="form-control" disabled />
                        </div>
                        <div class="form-outline mb-4">
                            <label class="form-label" for="nama">File PDF</label>
                            <input type="file" id="pdf" name="pdf" class="form-control" accept=".pdf"
                                required />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/verify.js'])
    @endpush


</x-app-layout>
