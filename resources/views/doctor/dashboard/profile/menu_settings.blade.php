<!-- Settings List -->
<div class="setting-tab">
    <div class="appointment-tabs">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link @if($activeTab === 'profile')active @endif" href="{{ route('doctor.profile') }}">Basic Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if($activeTab === 'experience')active @endif" href="{{ route('doctor.experience') }}">Experience</a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if($activeTab === 'education')active @endif" href="{{ route('doctor.education') }}">Education</a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if($activeTab === 'clinics')active @endif" href="{{ route('doctor.clinics') }}">Clinics</a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if($activeTab === 'business')active @endif" href="{{ route('doctor.business') }}">Business Hours</a>
            </li>
        </ul>
    </div>
</div>
<!-- /Settings List -->
