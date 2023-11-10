@props(['href', 'icon', 'title'])

<li class="nav-item">
    <a href="{{ $href }}" class="nav-link align-middle px-0 d-flex align-items-center text-light">
        <i class="fs-4 bi-{{ $icon }}"></i> <span class="ms-1 d-none d-sm-inline">{{ $title }}</span>
    </a>
</li>
