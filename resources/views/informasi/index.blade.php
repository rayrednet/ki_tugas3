<x-app-layout>
    <div class="row d-flex flex-column">
        <p class="fs-4 text-dark my-3">Informasi Pribadi</p>
        <hr>
        <div class="col d-flex justify-content-end">
            <a class="btn btn-block btn-primary fs-5" href="{{ route('informasi.create') }}"><i class="fs-5 bi-person-add"></i> Tambah Informasi</a>
        </div>
        <div class="container">
            <table class="table">
                <tbody>
                    @foreach($daftar_informasi as $informasi)
                    <tr>
                        <td>{{ $informasi['nama_informasi'] }}</td>
                        <td>{{ $informasi['isi_informasi'] }}</td>
                        <td>
                            <div class="d-flex">
                                <a href="{{ route('informasi.edit', ['id' => $informasi['id']]) }}" class="btn btn-block btn-light border border-dark"><i class="fs-6 bi-pencil-square"></i></a>
                                <form action="{{ route('informasi.delete', ['id' => $informasi['id']]) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-block btn-light border border-dark"><i class="fs-6 bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
