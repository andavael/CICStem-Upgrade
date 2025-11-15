@extends('admin.layouts.app')

@section('title', isset($student) ? 'Student Profile' : 'Tutor Profile')

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ isset($student) ? 'Student' : 'Tutor' }} Profile</h1>
    <div class="page-actions">
        <a href="{{ route('admin.users.index', ['tab' => isset($student) ? 'students' : 'tutors']) }}" class="btn btn-secondary">Back to List</a>

        @if(isset($student))
        <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-edit">Edit</a>
        <form action="{{ route('admin.students.destroy', $student) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this student?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-delete">Delete</button>
        </form>
        @else
        <a href="{{ route('admin.tutors.edit', $tutor) }}" class="btn btn-edit">Edit</a>
        <form action="{{ route('admin.tutors.destroy', $tutor) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this tutor?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-delete">Delete</button>
        </form>
        @endif
    </div>
</div>

<div class="content-panel">
    <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">Personal Information</h2>

    <div style="display: grid; grid-template-columns: 200px 1fr; gap: 16px; max-width: 800px;">
        <div style="font-weight: 600; color: #495057;">SR Code:</div>
        <div>{{ isset($student) ? $student->sr_code : $tutor->sr_code }}</div>

        <div style="font-weight: 600; color: #495057;">Full Name:</div>
        <div>{{ isset($student) ? $student->full_name : $tutor->full_name }}</div>

        <div style="font-weight: 600; color: #495057;">Email:</div>
        <div>{{ isset($student) ? $student->email : $tutor->email }}</div>

        <div style="font-weight: 600; color: #495057;">Year Level:</div>
        <div>{{ isset($student) ? $student->year_level : $tutor->year_level }}</div>

        <div style="font-weight: 600; color: #495057;">Course Program:</div>
        <div>{{ isset($student) ? $student->course_program : $tutor->course_program }}</div>

        @if(isset($tutor))
        <div style="font-weight: 600; color: #495057;">GWA:</div>
        <div><span class="badge badge-success">{{ $tutor->gwa }}</span></div>

        <div style="font-weight: 600; color: #495057;">Tutor Level Preference:</div>
        <div>{{ $tutor->tutor_level_preference }}</div>

        <div style="font-weight: 600; color: #495057;">Resume:</div>
        <div>
            @if($tutor->resume_path)
            <a href="{{ route('admin.tutors.downloadResume', $tutor) }}" class="btn btn-view btn-sm">Download Resume</a>
            @else
            <span class="badge badge-secondary">No resume uploaded</span>
            @endif
        </div>
        @endif

        <div style="font-weight: 600; color: #495057;">Status:</div>
        <div>
            @php $status = isset($student) ? $student->status : $tutor->status; @endphp
            <span class="badge {{ $status === 'Active' ? 'badge-success' : 'badge-secondary' }}">{{ $status }}</span>
        </div>

        @if(isset($tutor))
        <div style="font-weight: 600; color: #495057;">Approval Status:</div>
        <div>
            <span class="badge {{ $tutor->is_approved ? 'badge-success' : 'badge-warning' }}">
                {{ $tutor->is_approved ? 'Approved' : 'Pending' }}
            </span>
        </div>
        @endif

        <div style="font-weight: 600; color: #495057;">Registered:</div>
        <div>{{ \Carbon\Carbon::parse(isset($student) ? $student->created_at : $tutor->created_at)->format('F d, Y h:i A') }}</div>
    </div>
</div>

@if(isset($tutor) && $tutor->subjects->count() > 0)
<div class="content-panel">
    <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">Subjects</h2>
    <div style="display: flex; flex-wrap: wrap; gap: 12px;">
        @foreach($tutor->subjects as $subject)
        <span class="badge badge-info" style="font-size: 14px; padding: 8px 16px;">
            {{ $subject->name }} ({{ $subject->code }})
        </span>
        @endforeach
    </div>
</div>
@endif
@endsection
