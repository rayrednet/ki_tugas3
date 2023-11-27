<x-app-layout>
    <div class="row d-flex flex-column">
        <p class="fs-4 text-dark my-3">Berbagi Data</p>
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
                                    Berikan Akses
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
                    <form action="{{ route('share.store') }}" method="POST">
                        @csrf
                        <input type="text" id="user_id" name="user_id" class="form-control" value="" readonly
                            hidden required />
                        <div class="form-outline mb-4">
                            <label class="form-label" for="nama">Username</label>
                            <input type="text" id="username" name="username" class="form-control"
                                disabled />
                        </div>
                        <div class="form-outline mb-4">
                            <input class="form-check-input" type="checkbox" value="true" name="profile">
                            <label class="form-check-label" for="flexCheckDefault">
                                Profile
                            </label>
                        </div>
                        <div class="form-outline mb-4">
                            <label class="form-label" for="nama">Informasi Anda</label>
                            <div class="border rounded-2 px-2"
                                style="max-height: 160px; overflow: auto">
                                <table class="table">
                                    <tbody>
                                        @foreach ($daftar_informasi as $informasi)
                                            <tr>
                                                <td><input class="form-check-input" type="checkbox" value="{{ $informasi['id'] }}" name="informasi[]"></td>
                                                <td>{{ $informasi['nama_informasi'] }}</td>
                                                <td>{{ $informasi['isi_informasi'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-outline mb-4">
                            <label class="form-label" for="nama">File Anda</label>
                            <div class="border rounded-2 px-2"
                                style="max-height: 160px; overflow: auto">
                                <table class="table">
                                    <tbody>
                                        @foreach ($daftar_file as $file)
                                            <tr>
                                                <td><input class="form-check-input" type="checkbox" value="{{ $file['id'] }}" name="file[]"></td>
                                                <td>{{ $file['nama_file'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Berikan Akses</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/sharingKey.js'])
    @endpush


</x-app-layout>
