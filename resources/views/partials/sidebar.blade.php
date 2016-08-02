<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <li class="{{ active()->route('home') }}">
                <a href="{{ route('home') }}" >
                    <i class="fa fa-dashboard fa-fw"></i>
                    <span>Home</span>
                </a>
            </li>
            <li class="{{ active()->route('client.*') }}">
                <a href="{{ route('client.index') }}">
                    <i class="fa fa-users fa-fw"></i>
                    <span>Clients</span>
                </a>
            </li>
            <li class="{{ active()->route('project.*') }}">
                <a href="{{ route('project.index') }}">
                    <i class="fa fa-briefcase fa-fw"></i>
                    <span>Projects</span>
                </a>
            </li>
        </ul>
    </section>
<!-- /.sidebar -->
</aside>
