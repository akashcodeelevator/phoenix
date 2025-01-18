<div class="sidebar-header">
    <div class="logo-icon">
        <img src="{{ asset('user/assets/images/logo-icon.png') }}" class="logo-img" alt="">
    </div>
    <div class="logo-name flex-grow-1">
        <h5 class="mb-0">Maxton</h5>
    </div>
    <div class="sidebar-close">
        <span class="material-icons-outlined">close</span>
    </div>
</div>
<div class="sidebar-nav">
    <ul class="metismenu" id="sidenav">
        <li>
            <a href="javascript:;">
                <div class="parent-icon"><i class="material-icons-outlined">home</i></div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="material-icons-outlined">games</i></div>
                <div class="menu-title">My Account</div>
            </a>
            <ul>
                <li><a href="{{ route('user.myprofile') }}"><i
                            class="material-icons-outlined">arrow_right</i>Profile</a></li>
                <li><a href="{{ route('user.editprofile') }}"><i class="material-icons-outlined">arrow_right</i>Edit
                        Profile</a></li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="material-icons-outlined">turned_in_not</i></div>
                <div class="menu-title">My Genealogy</div>
            </a>
            <ul>
                <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Directs</a></li>
                <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Generation</a></li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="material-icons-outlined">library_books</i></div>
                <div class="menu-title">Fund</div>
            </a>
            <ul>
                <li><a href="{{ route('user.fundtransfer') }}"><i class="material-icons-outlined">arrow_right</i>Fund
                        Transfer</a></li>
                <li><a href="{{ route('user.fund.requests') }}"><i class="material-icons-outlined">arrow_right</i>Fund
                        Transfer History</a></li>
                <li><a href="{{ route('user.fundconvert') }}"><i class="material-icons-outlined">arrow_right</i>Fund
                        Convert</a></li>
                <li><a href="{{ route('user.fundhistory') }}"><i class="material-icons-outlined">arrow_right</i>Fund
                        Convert History</a></li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="material-icons-outlined">favorite</i></div>
                <div class="menu-title">Payout Reports</div>
            </a>
            <ul>
                <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Staking Income</a></li>
                <li><a href="#"><i class="material-icons-outlined">arrow_right</i>Level Income</a></li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="material-icons-outlined">card_giftcard</i></div>
                <div class="menu-title">Topup</div>
            </a>
            <ul>
                <li><a href="{{ route('user.topup') }}"><i class="material-icons-outlined">arrow_right</i>Member Topup</a></li>
                <li><a href="{{ route('user.viptopup') }}"><i class="material-icons-outlined">arrow_right</i>VIP Club Topup</a></li>
                <li><a href="{{ route('user.upgrade') }}"><i class="material-icons-outlined">arrow_right</i>Upgrade</a></li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="has-arrow">
                <div class="parent-icon"><i class="material-icons-outlined">view_agenda</i></div>
                <div class="menu-title">Withdrawal</div>
            </a>
            <ul>
                <li><a href="{{ route('user.withdraw' ) }}"><i class="material-icons-outlined">arrow_right</i>Withdrawal</a></li>
                <li><a href="{{ route('user.withdrawhistory' ) }}"><i class="material-icons-outlined">arrow_right</i>Withdrawal Report</a></li>
            </ul>
        </li>
        <li>
            <a href="#">
                <div class="parent-icon"><i class="material-icons-outlined">filter_none</i></div>
                <div class="menu-title">Orders</div>
            </a>
        </li>
        <li>
            <a href="#">
                <div class="parent-icon"><i class="material-icons-outlined">event_note</i></div>
                <div class="menu-title">Report</div>
            </a>
        </li>
        <li>
            <a href="#">
                <div class="parent-icon"><i class="material-icons-outlined">border_all</i></div>
                <div class="menu-title">News & Event</div>
            </a>
        </li>
        <li>
            <a href="#">
                <div class="parent-icon"><i class="material-icons-outlined">settings_applications</i></div>
                <div class="menu-title">Support</div>
            </a>
        </li>
        <li>
            <a href="{{ route('user.logout') }}" onclick="event.preventDefault();  document.getElementById('logout-form').submit();">
                <div class="parent-icon"><i class="material-icons-outlined">web</i></div>
                <div class="menu-title">{{ __('Logout') }}</div>
            </a>
            <form id="logout-form" action="{{ route('user.logout') }}" method="POST" class="d-none">
              @csrf
          </form>
        </li>

    </ul>
</div>
