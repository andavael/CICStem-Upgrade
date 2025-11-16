-- ============================================
-- CICStem Database Schema - Tutors Without Subjects
-- Database: cicstem_db
-- ============================================

-- Enable UUID extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ============================================
-- 1. STUDENTS TABLE
-- ============================================
CREATE TABLE students (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    sr_code VARCHAR(8) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    year_level VARCHAR(20) NOT NULL,
    course_program VARCHAR(255) NOT NULL,
    status VARCHAR(20) DEFAULT 'Active',
    terms_accepted BOOLEAN DEFAULT FALSE,
    terms_accepted_at TIMESTAMP,
    email_verified_at TIMESTAMP,
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT chk_student_year_level CHECK (year_level IN ('First Year', 'Second Year')),
    CONSTRAINT chk_student_status CHECK (status IN ('Active', 'Inactive')),
    CONSTRAINT chk_student_sr_code_format CHECK (sr_code ~ '^[0-9]{2}-[0-9]{5}$'),
    CONSTRAINT chk_student_email_format CHECK (email ~ '^[0-9]{2}-[0-9]{5}@g\.batstate-u\.edu\.ph$')
);

CREATE INDEX idx_students_email ON students(email);
CREATE INDEX idx_students_sr_code ON students(sr_code);
CREATE INDEX idx_students_status ON students(status);

-- ============================================
-- 2. TUTORS TABLE (No Subjects)
-- ============================================
CREATE TABLE tutors (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    sr_code VARCHAR(8) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    year_level VARCHAR(20) NOT NULL,
    course_program VARCHAR(255) NOT NULL,
    tutor_level_preference VARCHAR(30) NOT NULL,
    gwa DECIMAL(3,2),
    resume_path VARCHAR(255),
    status VARCHAR(20) DEFAULT 'Pending',
    is_approved BOOLEAN DEFAULT FALSE,
    terms_accepted BOOLEAN DEFAULT FALSE,
    terms_accepted_at TIMESTAMP,
    email_verified_at TIMESTAMP,
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT chk_tutor_year_level CHECK (year_level IN ('Third Year', 'Fourth Year or above')),
    CONSTRAINT chk_tutor_level_pref CHECK (tutor_level_preference IN ('First-Year Students', 'Second-Year Students')),
    CONSTRAINT chk_tutor_status CHECK (status IN ('Pending', 'Active', 'Inactive')),
    CONSTRAINT chk_tutor_sr_code_format CHECK (sr_code ~ '^[0-9]{2}-[0-9]{5}$'),
    CONSTRAINT chk_tutor_email_format CHECK (email ~ '^[0-9]{2}-[0-9]{5}@g\.batstate-u\.edu\.ph$'),
    CONSTRAINT chk_tutor_sr_code_year CHECK (CAST(SUBSTRING(sr_code, 1, 2) AS INTEGER) >= 23),
    CONSTRAINT chk_gwa_range CHECK (gwa IS NULL OR (gwa >= 1.00 AND gwa <= 5.00))
);

CREATE INDEX idx_tutors_email ON tutors(email);
CREATE INDEX idx_tutors_sr_code ON tutors(sr_code);
CREATE INDEX idx_tutors_status ON tutors(status);
CREATE INDEX idx_tutors_is_approved ON tutors(is_approved);

-- ============================================
-- 3. ADMINS TABLE (Simplified)
-- ============================================
CREATE TABLE admins (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 5. TERMS_ACCEPTANCE TABLE
-- ============================================
CREATE TABLE terms_acceptance (
    id SERIAL PRIMARY KEY,
    user_type VARCHAR(20) NOT NULL,
    user_id INTEGER NOT NULL,
    terms_version VARCHAR(20) DEFAULT '1.0',
    ip_address VARCHAR(45),
    user_agent TEXT,
    accepted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT chk_user_type CHECK (user_type IN ('student', 'tutor', 'admin'))
);

CREATE INDEX idx_terms_user_type_id ON terms_acceptance(user_type, user_id);

-- ============================================
-- 6. PASSWORD_RESET_TOKENS TABLE
-- ============================================
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    user_type VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT chk_reset_user_type CHECK (user_type IN ('student', 'tutor', 'admin'))
);

-- ============================================
-- 7. SESSIONS TABLE
-- ============================================
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_type VARCHAR(20),
    user_id INTEGER,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);

CREATE INDEX idx_sessions_user_type_id ON sessions(user_type, user_id);
CREATE INDEX idx_sessions_last_activity ON sessions(last_activity);

-- ============================================
-- TRIGGERS FOR UPDATED_AT
-- ============================================
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_students_updated_at 
    BEFORE UPDATE ON students
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_tutors_updated_at 
    BEFORE UPDATE ON tutors
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_admins_updated_at 
    BEFORE UPDATE ON admins
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_subjects_updated_at 
    BEFORE UPDATE ON subjects
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TYPE attendance_status AS ENUM ('Pending', 'Present', 'Absent');
CREATE TYPE proficiency_level AS ENUM ('Beginner', 'Intermediate', 'Advanced');
CREATE TYPE announcement_category AS ENUM ('General', 'Event', 'Maintenance', 'Important');
CREATE TYPE announcement_audience AS ENUM ('All', 'Students', 'Tutors');
CREATE TYPE announcement_priority AS ENUM ('Normal', 'High', 'Urgent');

-- ===============================
-- TABLE: session_enrollments
-- ===============================
CREATE TABLE session_enrollments (
    id SERIAL PRIMARY KEY,
    session_id BIGINT NOT NULL,
    student_id BIGINT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    attendance_status attendance_status DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_enrollments_session FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE,
    CONSTRAINT fk_enrollments_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT unique_session_student UNIQUE(session_id, student_id)
);

-- ===============================
-- TABLE: subjects
-- ===============================
CREATE TABLE subjects (
    id SERIAL PRIMARY KEY,
    code VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===============================
-- TABLE: tutor_subjects
-- ===============================
CREATE TABLE tutor_subjects (
    id SERIAL PRIMARY KEY,
    tutor_id BIGINT NOT NULL,
    subject_id BIGINT NOT NULL,
    proficiency_level proficiency_level DEFAULT 'Intermediate',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tutor_subjects_tutor FOREIGN KEY (tutor_id) REFERENCES tutors(id) ON DELETE CASCADE,
    CONSTRAINT fk_tutor_subjects_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    CONSTRAINT unique_tutor_subject UNIQUE(tutor_id, subject_id)
);

-- ===============================
-- TABLE: announcements
-- ===============================
CREATE TABLE announcements (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category announcement_category DEFAULT 'General',
    target_audience announcement_audience DEFAULT 'All',
    priority announcement_priority DEFAULT 'Normal',
    archived_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tutor_sessions (
    id SERIAL PRIMARY KEY,
    session_code VARCHAR(255) UNIQUE NOT NULL,
    subject VARCHAR(255) NOT NULL,
    session_date DATE NOT NULL,
    session_time TIME NOT NULL,
    tutor_id BIGINT NOT NULL REFERENCES tutors(id) ON DELETE CASCADE,
    capacity INTEGER DEFAULT 30,
    year_level VARCHAR(50) NOT NULL,
    google_meet_link VARCHAR(255) NOT NULL,
    description TEXT,
    status VARCHAR(20) NOT NULL DEFAULT 'Scheduled' CHECK (status IN ('Scheduled', 'Ongoing', 'Completed', 'Cancelled')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO subjects (code, name, description) VALUES
('IT 111', 'Introduction to Computing', 'Fundamentals of computing and information technology'),
('CS 111', 'Computer Programming', 'Introduction to programming concepts and logic'),
('GEd 102', 'Mathematics in the Modern World', 'Mathematical reasoning and problem solving'),
('Math 111', 'Linear Algebra', 'Matrices, vectors, and linear transformations'),
('CS 211', 'Object-Oriented Programming', 'OOP concepts and implementation'),
('CS 212', 'Computer Organization w/ Assembly Language', 'Computer architecture and low-level programming'),
('IT 211', 'Calculus-Based Physics', 'Physics principles using calculus'),
('IT 212', 'Discrete Mathematics', 'Mathematical structures and logic for computing');

DELETE FROM tutors;

ALTER TABLE tutors DROP CONSTRAINT chk_tutor_sr_code_year;

ALTER TABLE tutors
ADD CONSTRAINT chk_tutor_sr_code_year
CHECK (CAST(SUBSTRING(sr_code, 1, 2) AS INTEGER) < 23);

CREATE TABLE notifications (
    id SERIAL PRIMARY KEY,
    tutor_id BIGINT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    related_id BIGINT,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
