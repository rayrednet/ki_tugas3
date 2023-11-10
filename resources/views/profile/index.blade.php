<x-app-layout>
    <div class="row d-flex flex-column">
        <p class="fs-4 text-dark my-3">Data Profile</p>
        <hr>
        <div class="col d-flex justify-content-end">
            <a class="btn btn-block btn-primary fs-5" href="{{ route('profile.edit') }}"><i class="fs-5 bi-person-circle"></i> Edit Profile</a>
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
