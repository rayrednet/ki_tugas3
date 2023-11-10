<x-app-layout>
    <div class="row d-flex flex-column">
        <p class="fs-4 text-dark my-3">Edit Data Profile</p>
        <hr>
        <div class="container">
            <form action="{{ route('profile.update') }}" method="post">
                @method('PATCH')
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ $errors->all()[0] }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="form-outline mb-4">
                    <label class="form-label" for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" class="form-control"
                        value="{{ $profile['nama'] }}" required />
                </div>
                <div class="form-outline mb-4">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                        value="{{ $profile['email'] }}" required />
                </div>
                <div class="form-outline mb-4">
                    <label class="form-label" for="tanggal_lahir">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control"
                        value="{{ $profile['tanggal_lahir'] }}" required />
                </div>
                <div class="form-outline mb-4">
                    <label class="form-label" for="alamat">Alamat</label>
                    <input type="text" id="alamat" name="alamat" class="form-control"
                        value="{{ $profile['alamat'] }}" required />
                </div>
                <div class="form-outline mb-4">
                    <label class="form-label" for="nomor_telepon">Nomor Telepon</label>
                    <input type="text" id="nomor_telepon" name="nomor_telepon" class="form-control"
                        value="{{ $profile['nomor_telepon'] }}" required />
                </div>

                <div class="form-outline mb-4">
                    <label class="form-label" for="nomor_telepon">Enkripsi</label>
                    <?php
                        $daftarPilihan = ['aes-cbc', 'aes-cfb', 'aes-ofb', 'aes-ctr', 'des-cbc', 'des-cfb', 'des-ofb', 'des-ctr', 'rc4'];
                    ?>
                    <select name="enkripsi_digunakan" required>
                        <option {{ $profile['enkripsi_digunakan'] == null ? 'selected' : '' }}>-- Belum diisi --
                        </option>
                        @foreach ($daftarPilihan as $pilihan)
                            <option value="{{ $pilihan }}"
                                {{ $profile['enkripsi_digunakan'] == $pilihan ? 'selected' : '' }}>{{ $pilihan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="justify-content-center d-flex">
                    <button type="submit" class="btn btn-primary btn-block mb-4 px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
