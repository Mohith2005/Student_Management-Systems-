-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS student_management;
USE student_management;

-- Create faculty table
CREATE TABLE IF NOT EXISTS faculty (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create students table
CREATE TABLE IF NOT EXISTS students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    enrollment_number VARCHAR(20) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_name VARCHAR(100) NOT NULL,
    course_code VARCHAR(20) UNIQUE NOT NULL,
    faculty_id INT NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create student_courses table (for enrollment)
CREATE TABLE IF NOT EXISTS student_courses (
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create assignments table
CREATE TABLE IF NOT EXISTS assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    due_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create tests table
CREATE TABLE IF NOT EXISTS tests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    test_date DATETIME NOT NULL,
    duration INT NOT NULL, -- in minutes
    max_score INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create student_tests table
CREATE TABLE IF NOT EXISTS student_tests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    test_id INT NOT NULL,
    score DECIMAL(5,2),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (test_id) REFERENCES tests(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create student_assignments table
CREATE TABLE IF NOT EXISTS student_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    assignment_id INT NOT NULL,
    submission_text TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    grade DECIMAL(5,2),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (assignment_id) REFERENCES assignments(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create messages table
CREATE TABLE IF NOT EXISTS messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    sender_type ENUM('student', 'faculty') NOT NULL,
    receiver_type ENUM('student', 'faculty') NOT NULL,
    content TEXT NOT NULL,
    sent_time DATETIME NOT NULL,
    read_status TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create video_lectures table
CREATE TABLE IF NOT EXISTS video_lectures (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    video_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create student_videos table
CREATE TABLE IF NOT EXISTS student_videos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    video_id INT NOT NULL,
    watched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    watch_duration INT DEFAULT 0,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (video_id) REFERENCES video_lectures(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert test data for faculty
INSERT INTO faculty (name, email, password, department) VALUES
('Dr. John Smith', 'john.smith@example.com', 'password123', 'Computer Science'),
('Dr. Sarah Johnson', 'sarah.johnson@example.com', 'password123', 'Mathematics');

-- Insert test data for students
INSERT INTO students (name, email, password, enrollment_number) VALUES
('Alice Brown', 'alice.brown@example.com', 'password123', 'EN001'),
('Bob Wilson', 'bob.wilson@example.com', 'password123', 'EN002');

-- Insert test courses
INSERT INTO courses (course_name, course_code, faculty_id, description) VALUES
('Introduction to Programming', 'CS101', 1, 'Basic programming concepts using Python'),
('Advanced Mathematics', 'MATH201', 2, 'Advanced calculus and linear algebra');

-- Enroll students in courses
INSERT INTO student_courses (student_id, course_id) VALUES
(1, 1),
(1, 2),
(2, 1);

-- Create test assignments
INSERT INTO assignments (course_id, title, description, due_date) VALUES
(1, 'Python Basics', 'Create a simple calculator using Python', '2025-03-01 23:59:59'),
(2, 'Calculus Assignment', 'Solve the given differential equations', '2025-03-05 23:59:59');

-- Create test data for tests
INSERT INTO tests (course_id, title, description, test_date, duration, max_score) VALUES
(1, 'Python Basics Test', 'Test covering basic Python concepts', '2025-03-15 10:00:00', 120, 100),
(2, 'Calculus Midterm', 'Midterm exam covering differential calculus', '2025-03-20 14:00:00', 180, 100);

-- Insert some test scores
INSERT INTO student_tests (student_id, test_id, score) VALUES
(1, 1, 85.5),
(2, 1, 92.0),
(1, 2, 88.0);

-- Add some test video lectures
INSERT INTO video_lectures (course_id, title, description, video_url) VALUES
(1, 'Introduction to Python', 'Basic Python syntax and concepts', 'https://example.com/video1'),
(2, 'Calculus Fundamentals', 'Introduction to differential calculus', 'https://example.com/video2');
