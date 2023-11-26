<x-app-layout>
    <div class="row d-flex flex-column">
        <p class="fs-4 text-dark my-3">Permintaan Data User Lain</p>
        <hr>
        <div class="container">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Username Peminta</th>
                        <th scope="col">Jenis Kontak Peminta</th>
                        <th scope="col">Kontak Peminta</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permintaan as $user)
                    <tr>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->contact }}</td>
                        <td>{{ $user->address }}</td>
                        <td class="d-flex">
                            <form class="mx-1" action="{{ route('share.decline') }}" method="POST">
                                @csrf
                                <input type="text" name="username" value="{{ $user->username }}" hidden>
                                <button type="submit" class="btn btn-danger btn-request">
                                    <i class="bi-x-lg"></i>
                                </button>
                            </form>
                            <form class="mx-1" action="{{ route('share.accept') }}" method="POST">
                                @csrf
                                <input type="text" name="username" value="{{ $user->username }}" hidden>
                                <button type="submit" class="btn btn-primary btn-request">
                                    <i class="bi-check-lg"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>   
    
    @push('scripts')
    @vite(['resources/js/sharingKey.js'])
    @endpush
    
    
</x-app-layout>
