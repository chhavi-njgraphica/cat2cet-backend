<aside class="sidebar-wrapper">
    <div class="sidebar-header">
        <div class="logo-icon">
            {{-- <img src="{{ asset('assets/frontend/images/Logo/logo.png') }}" alt=""> --}}
            <h4>Score Calculator</h4>
        </div>
        {{-- <div class="logo-name flex-grow-1">
            <img src="{{ asset('assets/frontend/images/Logo/logo.png') }}" alt="">
        </div> --}}
        <div class="sidebar-close">
            <span class="material-icons-outlined">close</span>
        </div>
    </div>
    <div class="sidebar-nav" data-simplebar="true">     

        <!--navigation-->
        <ul class="metismenu" id="sidenav">
            <li>
                <a href="{{route('backend.dashboard')}}">
                    <div class="parent-icon"><i
                            class="material-icons-outlined">home</i>
                    </div>
                    <div class="menu-title">Dashboard</div>
                </a>
            </li>
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon">
                        <i class="material-icons-outlined">widgets</i>
                    </div>
                    <div class="menu-title">CAT Calculator</div>
                </a>
                <ul>
                    <li>
                        <a href="{{route('backend.student-result')}}">
                            <div class="parent-icon"><i
                                    class="material-icons-outlined">leaderboard</i>
                            </div>
                            <div class="menu-title">Student Result</div>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('backend.colleges.index')}}">
                            <div class="parent-icon"><i
                                    class="material-icons-outlined">school</i>
                            </div>
                            <div class="menu-title">Colleges</div>
                        </a>
                    </li>

                    <li>
                        <a href="{{route('backend.users')}}">
                            <div class="parent-icon"><i
                                    class="material-icons-outlined">group</i>
                            </div>
                            <div class="menu-title">Users</div>
                        </a>
                    </li>
                </ul>
            </li>
            
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon">
                        <i class="material-icons-outlined">widgets</i>
                    </div>
                    <div class="menu-title">XAT Calculator</div>
                </a>
                <ul>
                    <li>
                        <a href="{{route('backend.xat-student-result')}}">
                            <div class="parent-icon"><i
                                    class="material-icons-outlined">leaderboard</i>
                            </div>
                            <div class="menu-title">Student Result</div>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('backend.xat-colleges.index')}}">
                            <div class="parent-icon"><i
                                    class="material-icons-outlined">school</i>
                            </div>
                            <div class="menu-title">Colleges</div>
                        </a>
                    </li>

                    <li>
                        <a href="{{route('backend.xat-users')}}">
                            <div class="parent-icon"><i
                                    class="material-icons-outlined">group</i>
                            </div>
                            <div class="menu-title">Users</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon">
                        <i class="material-icons-outlined">widgets</i>
                    </div>
                    <div class="menu-title">SNAP Calculator</div>
                </a>
                <ul>
                    <li>
                        <a href="{{route('backend.snap-student-result')}}">
                            <div class="parent-icon"><i
                                    class="material-icons-outlined">leaderboard</i>
                            </div>
                            <div class="menu-title">Student Result</div>
                        </a>
                    </li>

                    <li>
                        <a href="{{route('backend.snap-users')}}">
                            <div class="parent-icon"><i
                                    class="material-icons-outlined">group</i>
                            </div>
                            <div class="menu-title">Users</div>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

    </div>
</aside>
