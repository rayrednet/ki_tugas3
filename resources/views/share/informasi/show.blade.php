<x-app-layout>
    <div class="row d-flex flex-column">
        <p class="fs-4 text-dark my-3">Dapatkan Informasi User Lain</p>
        <hr>
        <div class="container">
            <div class="d-flex flex-column">
                <p>Profile User</p>
                <table class="table">
                    <tbody>
                        @foreach ($profile as $key => $value)
                            <tr>
                                <td>{{ $key }}</td>
                                <td>{{ $value }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex flex-column mt-5">
                <p>Informasi User</p>
                <table class="table">
                    <tbody>
                        @foreach ($daftar_informasi as $informasi)
                            <tr>
                                <td>{{ $informasi['nama_informasi'] }}</td>
                                <td>{{ $informasi['isi_informasi'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
