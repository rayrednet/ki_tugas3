<x-app-layout>
    <div class="row d-flex flex-column">
        <p class="fs-4 text-dark my-3">Permintaan Data</p>
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
                    @foreach($user_lain as $user)
                    <tr>
                        <td>{{ $user['username'] }}</td>
                        <td>
                            <button type="button" class="btn btn-primary btn-request" data-bs-toggle="modal" data-bs-target="#modal" data-username="{{ json_encode(['username' => $user['username']]) }}">
                                Open Modal
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
                <h5 class="modal-title">Minta Akses Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('share.store') }}" method="POST">
                    @csrf
                    <div class="form-outline mb-4">
                        <label class="form-label" for="nama">Username</label>
                        <input type="text" id="username" name="username" class="form-control"
                            value=" " readonly required  />
                    </div>
                    <div class="form-outline mb-4">
                        <label class="form-label" for="kontak">Jenis Kontak</label>
                        <select name="kontak" required>
                            <option selected> -- Belum diisi -- 
                            </option>
                                <option value="email">
                                    Email
                                </option>
                                <option value="whatsapp">
                                    WhatsApp
                                </option>
                                <option value="telegram">
                                    Telegram
                                </option>
                        </select>
                    </div>
                    <div class="form-outline mb-4">
                        <label class="form-label" for="tujuan">Kontak Anda</label>
                        <input type="text" id="tujuan" name="tujuan" class="form-control"
                            value="" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Minta Akses Data</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    
    @push('scripts')
    @vite(['resources/js/sharingKey.js'])
    @endpush
    
    
</x-app-layout>
