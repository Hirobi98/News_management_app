@extends('layouts.app')

@section('content')
<div class="auth-card" style="max-width: 800px; padding: 40px; margin: 40px auto; border: 1px solid var(--border-color); background: var(--card-bg); text-align: left;">
    
    <div style="display: flex; gap: 40px; align-items: flex-start; flex-wrap: wrap;">
        
        <!-- Left Side: Avatar and Basic Info -->
        <div style="flex: 1; text-align: center; min-width: 250px; position: relative;">
            @if($user['profile_picture'])
                <img src="{{ asset('uploads/profiles/' . $user['profile_picture']) }}" alt="Profile Picture" class="profile-avatar" style="margin: 0 auto 20px;">
            @else
                <div class="profile-avatar" style="margin: 0 auto 20px;">
                    <i class="fa-solid fa-user"></i>
                </div>
            @endif
            
            <h1 style="font-family: var(--font-serif); margin-bottom: 5px;">{{ $user['name'] }}</h1>
            <p style="color: var(--text-secondary); font-family: var(--font-sans); margin-top: 0; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px;">{{ $user['email'] }}</p>

            <div style="display: inline-block; border: 1px solid var(--primary-color); color: var(--primary-color); padding: 5px 15px; margin-top: 10px; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; font-family: var(--font-sans);">
                Role: {{ $user['role'] }}
            </div>

            <div class="divider" style="margin: 30px 0;"></div>

            <div>
                <h3 style="margin-bottom: 5px; font-size: 1.1rem; font-family: var(--font-serif);">{{ $user['joined'] }}</h3>
                <p style="color: var(--text-secondary); margin: 0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-family: var(--font-sans);">Status</p>
            </div>
            
            <button id="editProfileBtn" class="btn-icon" style="position: absolute; top: 0; right: 0;" title="Edit Profile">
                <i class="fa-solid fa-pen-to-square"></i>
            </button>
        </div>

        <!-- Right Side: Details and Edit Form -->
        <div style="flex: 2; min-width: 300px; padding-left: 20px; border-left: 1px solid var(--border-color);">
            
            <div id="profileDetails">
                <h3 style="font-family: var(--font-serif); margin-bottom: 15px; font-size: 1.5rem; text-transform: uppercase; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Biography</h3>
                <p style="font-family: var(--font-serif); font-size: 1.1rem; line-height: 1.6; font-style: italic;">{{ $user['bio'] }}</p>
                
                <h3 style="font-family: var(--font-serif); margin-bottom: 15px; font-size: 1.5rem; text-transform: uppercase; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-top: 30px;">Interest</h3>
                <p style="font-family: var(--font-sans); text-transform: uppercase; font-weight: 600; color: var(--secondary-color); letter-spacing: 1px;">{{ $user['interest'] }}</p>
            </div>

            <div id="profileEditForm" style="display: none;">
                <h2 style="font-family: var(--font-serif); margin-bottom: 20px; font-size: 1.5rem; text-transform: uppercase; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Update Archives</h2>
                
                <form action="{{ url('/profile/update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group" style="text-align: left;">
                        <label for="profile_picture">Profile Picture</label>
                        <input type="file" id="profile_picture" name="profile_picture" class="form-control" accept="image/*" style="font-family: var(--font-sans);">
                    </div>

                    <div class="form-group" style="text-align: left;">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" class="form-control" rows="4" placeholder="Tell us about yourself..." style="font-family: var(--font-serif); font-style: italic;">{{ $user['bio'] === 'No biography details provided.' ? '' : $user['bio'] }}</textarea>
                    </div>

                    <div class="form-group" style="text-align: left;">
                        <label for="interest">Primary Interest</label>
                        <input type="text" id="interest" name="interest" class="form-control" placeholder="e.g. Technology, Politics" value="{{ $user['interest'] === 'None specified' ? '' : $user['interest'] }}" style="font-family: var(--font-sans); text-transform: uppercase;">
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" id="cancelEditBtn" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('editProfileBtn').addEventListener('click', function() {
        document.getElementById('profileDetails').style.display = 'none';
        document.getElementById('profileEditForm').style.display = 'block';
    });
    document.getElementById('cancelEditBtn').addEventListener('click', function() {
        document.getElementById('profileDetails').style.display = 'block';
        document.getElementById('profileEditForm').style.display = 'none';
    });
</script>
@endpush
