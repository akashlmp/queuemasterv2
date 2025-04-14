<nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
    <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
        <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
    </a>
    <a href="#" class="sidebar-toggler flex-shrink-0">
        <i class="fa fa-bars"></i>
    </a>
    <form class="d-none d-md-flex ms-4 mt-2">
        <input class="form-control border-0" type="search" placeholder="Search">
    </form>
    <div class="navbar-nav align-items-center ms-auto">

        <a class="dropdown-item" href="#">
            <div class="d-flex align-items-center">
                <form action="{{ route('admin.logout') }}" method="GET" class="w-100">
                    @csrf
                    <button type="submit" class="logoutbutton pe-2 w-100 d-flex align-center btn btn-outline-danger mt-2"><span class="material-symbols-outlined logoutbutton pe-2">logout</span>
                    </button>
                </form>
            </div>
        </a>
    </div>
</nav>