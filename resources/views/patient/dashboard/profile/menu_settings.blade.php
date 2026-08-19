<nav class="settings-tab mb-1">
    <ul class="nav nav-tabs-bottom" role="tablist">
        <li class="nav-item" role="presentation">
                <a class="nav-link @if($activeTab === 'profile')active @endif" href="{{ route('patient.settings') }}">Profile</a>
            </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link @if($activeTab === 'change_password')active @endif " href="{{ route('patient.change_password') }}">Change Password</a>
        </li>
    </ul>
</nav>