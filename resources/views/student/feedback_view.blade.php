@extends('student.layouts.app')

@section('title', 'View Feedback')
@section('page-title', 'View Feedback')

@section('content')
<div class="page-header">
    <h1 class="page-title">Your Feedback</h1>
    <div class="page-actions">
        <a href="{{ route('student.feedback.index') }}" class="btn btn-secondary">Back to Feedback</a>
    </div>
</div>

<div class="content-panel">
    <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">Session Details</h2>
    
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 12px;">
            <div style="font-weight: 600;">Subject:</div>
            <div>{{ $session->subject }}</div>
            
            <div style="font-weight: 600;">Session Code:</div>
            <div>{{ $session->session_code }}</div>
            
            <div style="font-weight: 600;">Tutor:</div>
            <div>{{ $session->tutor ? $session->tutor->full_name : 'N/A' }}</div>
            
            <div style="font-weight: 600;">Date:</div>
            <div>{{ \Carbon\Carbon::parse($session->session_date)->format('F d, Y') }}</div>
        </div>
    </div>
    
    <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">Your Feedback</h2>
    
    <div style="background: white; border: 2px solid #dee2e6; border-radius: 12px; padding: 30px;">
        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 14px; font-weight: 600; color: #495057; margin-bottom: 12px;">
                Rating
            </label>
            <div class="rating-display">
                @for($i = 1; $i <= 5; $i++)
                    <span class="star {{ $i <= $feedback->rating ? '' : 'empty' }}">★</span>
                @endfor
                <span style="margin-left: 12px; font-size: 18px; font-weight: 600; color: #495057;">
                    {{ $feedback->rating }} / 5
                </span>
            </div>
        </div>
        
        @if($feedback->comment)
        <div>
            <label style="display: block; font-size: 14px; font-weight: 600; color: #495057; margin-bottom: 12px;">
                Comments
            </label>
            <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; line-height: 1.6; color: #212529;">
                {{ $feedback->comment }}
            </div>
        </div>
        @endif
        
        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #dee2e6;">
            <div style="color: #6c757d; font-size: 13px;">
                Submitted on {{ \Carbon\Carbon::parse($feedback->created_at)->format('F d, Y h:i A') }}
            </div>
        </div>
    </div>
</div>

<style>
.rating-display {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 32px;
}

.rating-display .star {
    color: #ffc107;
}

.rating-display .star.empty {
    color: #dee2e6;
}
</style>
@endsection