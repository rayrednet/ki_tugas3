<x-app-layout>
    <div class="row d-flex flex-column">
        <p class="fs-4 text-dark my-3">Data Profile</p>
        <hr>
        <div class="col d-flex justify-content-end">
            @if($generate_key)
            <a class="btn btn-block btn-primary fs-5 mx-1" href="{{ route('key.create') }}"><i class="fs-5 bi-filetype-key"></i> Store Own Key Signature</a>
                <a class="btn btn-block btn-primary fs-5 mx-1" href="{{ route('key.index') }}"><i class="fs-5 bi-key"></i> Generate Key Signature</a>
            @endif
            <a class="btn btn-block btn-primary fs-5 mx-1" href="{{ route('profile.edit') }}"><i class="fs-5 bi-person-circle"></i> Edit Profile</a>
        </div>
        <div class="container">
            <table class="table">
                <tbody>
                    @foreach($profile as $key => $value)
                    <tr>
                        <td>{{ $key }}</td>
                        <td>{{ $value }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
