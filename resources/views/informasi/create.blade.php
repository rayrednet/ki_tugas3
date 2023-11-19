<x-app-layout>
    <div class="row d-flex flex-column">
        <p class="fs-4 text-dark my-3">Tambah Informasi Pribadi</p>
        <hr>
        <div class="container">
            <form action="{{ route('informasi.store') }}" method="post">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ $errors->all()[0] }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="form-outline mb-4">
                    <label class="form-label" for="nama">Nama Informasi</label>
                    <input type="text" id="nama_informasi" name="nama_informasi" class="form-control" required />
                </div>
                <div class="form-outline mb-4">
                    <label class="form-label" for="nama">Isi Informasi</label>
                    <input type="text" id="isi_informasi" name="isi_informasi" class="form-control" required />
                </div>

                <div class="form-outline mb-4">
                    <label class="form-label" for="enkripsi_digunakan">Enkripsi</label>
                    <?php
                        $daftarPilihan = ['aes-cbc', 'aes-cfb', 'aes-ofb', 'aes-ctr', 'des-cbc', 'des-cfb', 'des-ofb', 'des-ctr', 'rc4'];
                    ?>
                    <select name="enkripsi_digunakan" required>
                        <option selected>-- Belum diisi --
                        </option>
                        @foreach ($daftarPilihan as $pilihan)
                            <option value="{{ $pilihan }}">{{ $pilihan }}</option>
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
