@extends('admin.layouts.app')

@section('title', 'Add New Student')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add New Student</h1>
    <div class="page-actions">
        <a href="{{ route('admin.users.index', ['tab' => 'students']) }}" class="btn btn-secondary">Back to List</a>
    </div>
</div>

<div class="content-panel">
    <form action="{{ route('admin.students.store') }}" method="POST">
        @csrf
        
        <div class="form-grid">
            <div class="form-group">
                <label for="first_name">First Name <span class="required">*</span></label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required>
                @error('first_name')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="middle_name">Middle Name</label>
                <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name') }}">
                @error('middle_name')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="last_name">Last Name <span class="required">*</span></label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required>
                @error('last_name')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="sr_code">SR Code <span class="required">*</span></label>
                <input type="text" name="sr_code" id="sr_code" placeholder="XX-XXXXX" value="{{ old('sr_code') }}" required>
                @error('sr_code')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group form-grid-full">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email" name="email" id="email" placeholder="XX-XXXXX@g.batstate-u.edu.ph" value="{{ old('email') }}" required>
                @error('email')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" name="password" id="password" placeholder="Minimum 8 characters" required>
                @error('password')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="password_confirmation">Confirm Password <span class="required">*</span></label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>
            
            <div class="form-group">
                <label for="year_level">Year Level <span class="required">*</span></label>
                <select name="year_level" id="year_level" required>
                    <option value="">Select Year Level</option>
                    <option value="1st Year" {{ old('year_level') === '1st Year' ? 'selected' : '' }}>1st Year</option>
                    <option value="2nd Year" {{ old('year_level') === '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                    <option value="3rd Year" {{ old('year_level') === '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                    <option value="4th Year" {{ old('year_level') === '4th Year' ? 'selected' : '' }}>4th Year</option>
                </select>
                @error('year_level')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="course_program">Course Program <span class="required">*</span></label>
                <input type="text" name="course_program" id="course_program" placeholder="e.g., BS Computer Science" value="{{ old('course_program') }}" required>
                @error('course_program')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="status">Status <span class="required">*</span></label>
                <select name="status" id="status" required>
                    <option value="Active" {{ old('status') === 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div style="display: flex; gap: 12px; margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Create Student</button>
            <a href="{{ route('admin.users.index', ['tab' => 'students']) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection