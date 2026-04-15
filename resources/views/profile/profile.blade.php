@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
@php
    $user = auth()->user();
@endphp

<div class="profile-wrap">

    {{-- BANNER --}}
    <div class="banner-card">
        <div class="banner-img-placeholder"></div>

        <div class="banner-body">
            <div class="banner-left">
                <div class="avatar-wrap">
                    @if($user->avatar)
                        <img src="{{ asset('storage/'.$user->avatar) }}" class="avatar-img">
                    @else
                        <div class="avatar-initials">
                            {{ strtoupper(substr($user->name,0,1)) }}
                        </div>
                    @endif
                </div>

                <div>
                    <h4>{{ $user->name }}</h4>
                    <span class="badge-role">{{ $user->role ?? 'User' }}</span>
            
    </div>

    {{-- INFO --}}
    <div class="info-card">
        <h6>Personal Information</h6>

        <div class="info-grid">
            <div>
                <label>Email</label>
                <div>{{ $user->email }}</div>
            </div>

            <div>
                <label>Phone</label>
                <div>{{ $user->phone ?? '-' }}</div>
            </div>

            <div>
                <label>Location</label>
                <div>{{ $user->location ?? '-' }}</div>
            </div>

            <div>
                <label>Company</label>
                <div>{{ $user->company ?? '-' }}</div>
            </div>
        </div>
    </div>

</div>
@endsection