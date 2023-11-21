
<div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark">
    <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
        <a href="/" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class="fs-5 d-none d-sm-inline">Menu KI Tugas 2</span>
        </a>
        <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
            <x-sidebar-item href="{{ route('profile.index') }}" title="Profile" icon="person-circle">
            </x-sidebar-item>
            <x-sidebar-item href="{{ route('informasi.index') }}" title="Bank Data" icon="people">
            </x-sidebar-item>
            <x-sidebar-item href="{{ route('file.index') }}" title="Bank File" icon="file-lock">
            </x-sidebar-item>
            <x-sidebar-item href="{{ route('share.show') }}" title="Share Key" icon="share">
            </x-sidebar-item>
            <x-sidebar-item href="{{ route('share.informasi.index') }}" title="Informasi User Lain" icon="share">
            </x-sidebar-item>
            <x-sidebar-item href="{{ route('share.file.index') }}" title="File User Lain" icon="share">
            </x-sidebar-item>
            <x-sidebar-item href="{{ route('autentikasi.logout') }}" title="Logout" icon="door-open">
            </x-sidebar-item>
        </ul>
    </div>
</div>
