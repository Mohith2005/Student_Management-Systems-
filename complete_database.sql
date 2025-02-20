-- Complete Student Management System Database
-- This file combines database.sql, additional_features.sql, and user_credentials.sql

-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS student_management;
USE student_management;

-- Create students table
CREATE TABLE IF NOT EXISTS students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    course VARCHAR(100),
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Create faculty table
CREATE TABLE IF NOT EXISTS faculty (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    joining_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Create courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_code VARCHAR(20) UNIQUE NOT NULL,
    course_name VARCHAR(100) NOT NULL,
    department VARCHAR(100),
    credits INT,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Create faculty_courses table (for mapping faculty to courses)
CREATE TABLE IF NOT EXISTS faculty_courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_id INT,
    course_id INT,
    assigned_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Create student_courses table (for enrollment)
CREATE TABLE IF NOT EXISTS student_courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    course_id INT,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    grade VARCHAR(2),
    status ENUM('enrolled', 'completed', 'dropped') DEFAULT 'enrolled',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Create attendance table
CREATE TABLE IF NOT EXISTS attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    course_id INT,
    date DATE,
    status ENUM('present', 'absent', 'late') DEFAULT 'present',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Create assignments table
CREATE TABLE IF NOT EXISTS assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    due_date DATETIME,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Create student_assignments table (for submission tracking)
CREATE TABLE IF NOT EXISTS student_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    assignment_id INT,
    submission_date TIMESTAMP,
    grade DECIMAL(5,2),
    feedback TEXT,
    status ENUM('pending', 'submitted', 'graded') DEFAULT 'pending',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE
);

-- Create announcements table
CREATE TABLE IF NOT EXISTS announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT,
    posted_by INT,
    posted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    target_audience ENUM('all', 'students', 'faculty') DEFAULT 'all',
    FOREIGN KEY (posted_by) REFERENCES faculty(id) ON DELETE SET NULL
);

-- Create events table
CREATE TABLE IF NOT EXISTS events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    event_date DATETIME,
    venue VARCHAR(200),
    organizer_id INT,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES faculty(id) ON DELETE SET NULL
);

-- Create timetable table
CREATE TABLE IF NOT EXISTS timetable (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT,
    faculty_id INT,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
    start_time TIME,
    end_time TIME,
    room_number VARCHAR(50),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE
);

-- Create exam_schedule table
CREATE TABLE IF NOT EXISTS exam_schedule (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT,
    exam_type ENUM('midterm', 'final', 'quiz'),
    exam_date DATE,
    start_time TIME,
    end_time TIME,
    room_number VARCHAR(50),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Create student_attendance_report view
CREATE OR REPLACE VIEW student_attendance_report AS
SELECT 
    s.id as student_id,
    s.name as student_name,
    c.course_name,
    COUNT(a.id) as total_classes,
    SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as classes_attended,
    (SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) * 100.0 / COUNT(a.id)) as attendance_percentage
FROM 
    students s
    JOIN student_courses sc ON s.id = sc.student_id
    JOIN courses c ON sc.course_id = c.id
    LEFT JOIN attendance a ON s.id = a.student_id AND c.id = a.course_id
GROUP BY 
    s.id, s.name, c.course_name;

-- Insert sample courses
INSERT INTO courses (course_code, course_name, department, credits, description) VALUES
('CS101', 'Introduction to Computer Science', 'Computer Science', 3, 'Basic concepts of programming and computer science'),
('MATH201', 'Advanced Mathematics', 'Mathematics', 4, 'Advanced mathematical concepts and applications'),
('PHY101', 'Physics Fundamentals', 'Physics', 3, 'Basic concepts of physics and mechanics');

-- Insert Faculty Members with IDs and Passwords
INSERT INTO faculty (id, name, email, password, department) VALUES
(1001, 'Dr. John Smith', 'john.smith@faculty.edu', '$2y$10$HxD7DlHgYkLJxQxF9rXL4.1XEuYBtNO1E4KXELpkqnMkqQJZGc.Uy', 'Computer Science'),  -- Password: FAC1001
(1002, 'Dr. Sarah Johnson', 'sarah.johnson@faculty.edu', '$2y$10$HxD7DlHgYkLJxQxF9rXL4.2XEuYBtNO1E4KXELpkqnMkqQJZGc.Uy', 'Mathematics'),     -- Password: FAC1002
(1003, 'Prof. Michael Brown', 'michael.brown@faculty.edu', '$2y$10$HxD7DlHgYkLJxQxF9rXL4.3XEuYBtNO1E4KXELpkqnMkqQJZGc.Uy', 'Physics'),        -- Password: FAC1003
(1004, 'Dr. Emily Davis', 'emily.davis@faculty.edu', '$2y$10$HxD7DlHgYkLJxQxF9rXL4.4XEuYBtNO1E4KXELpkqnMkqQJZGc.Uy', 'Chemistry'),          -- Password: FAC1004
(1005, 'Prof. Robert Wilson', 'robert.wilson@faculty.edu', '$2y$10$HxD7DlHgYkLJxQxF9rXL4.5XEuYBtNO1E4KXELpkqnMkqQJZGc.Uy', 'English');        -- Password: FAC1005

-- Insert Students with IDs and Passwords
INSERT INTO students (id, name, email, password, course) VALUES
(2001, 'Alice Cooper', 'alice.cooper@student.edu', '$2y$10$HxD7DlHgYkLJxQxF9rXL4.6XEuYBtNO1E4KXELpkqnMkqQJZGc.Uy', 'Computer Science'),    -- Password: STU2001
(2002, 'Bob Wilson', 'bob.wilson@student.edu', '$2y$10$HxD7DlHgYkLJxQxF9rXL4.7XEuYBtNO1E4KXELpkqnMkqQJZGc.Uy', 'Mathematics'),            -- Password: STU2002
(2003, 'Carol Martinez', 'carol.martinez@student.edu', '$2y$10$HxD7DlHgYkLJxQxF9rXL4.8XEuYBtNO1E4KXELpkqnMkqQJZGc.Uy', 'Physics'),          -- Password: STU2003
(2004, 'David Thompson', 'david.thompson@student.edu', '$2y$10$HxD7DlHgYkLJxQxF9rXL4.9XEuYBtNO1E4KXELpkqnMkqQJZGc.Uy', 'Chemistry'),        -- Password: STU2004
(2005, 'Emma Rodriguez', 'emma.rodriguez@student.edu', '$2y$10$HxD7DlHgYkLJxQxF9rXL4.0XEuYBtNO1E4KXELpkqnMkqQJZGc.Uy', 'English'),          -- Password: STU2005
(2006, 'Frank Lee', 'frank.lee@student.edu', '$2y$10$HxD7DlHgYkLJxQxF9rXL4.aXEuYBtNO1E4KXELpkqnMkqQJZGc.Uy', 'Computer Science'),         -- Password: STU2006
(2007, 'Grace Kim', 'grace.kim@student.edu', '$2y$10$HxD7DlHgYkLJxQxF9rXL4.bXEuYBtNO1E4KXELpkqnMkqQJZGc.Uy', 'Mathematics'),              -- Password: STU2007
(2008, 'Henry Patel', 'henry.patel@student.edu', '$2y$10$HxD7DlHgYkLJxQxF9rXL4.cXEuYBtNO1E4KXELpkqnMkqQJZGc.Uy', 'Physics'),              -- Password: STU2008
(2009, 'Isabel Santos', 'isabel.santos@student.edu', '$2y$10$HxD7DlHgYkLJxQxF9rXL4.dXEuYBtNO1E4KXELpkqnMkqQJZGc.Uy', 'Chemistry'),         -- Password: STU2009
(2010, 'Jack Murphy', 'jack.murphy@student.edu', '$2y$10$HxD7DlHgYkLJxQxF9rXL4.eXEuYBtNO1E4KXELpkqnMkqQJZGc.Uy', 'English');              -- Password: STU2010

-- Create faculty course assignments
INSERT INTO faculty_courses (faculty_id, course_id) VALUES
(1001, 1), -- Dr. John Smith - Computer Science
(1002, 2), -- Dr. Sarah Johnson - Mathematics
(1003, 3), -- Prof. Michael Brown - Physics
(1004, 1), -- Dr. Emily Davis - Computer Science
(1005, 2); -- Prof. Robert Wilson - Mathematics

-- Create student course enrollments
INSERT INTO student_courses (student_id, course_id) VALUES
(2001, 1), -- Alice Cooper - CS101
(2002, 2), -- Bob Wilson - MATH201
(2003, 3), -- Carol Martinez - PHY101
(2004, 1), -- David Thompson - CS101
(2005, 2), -- Emma Rodriguez - MATH201
(2006, 1), -- Frank Lee - CS101
(2007, 2), -- Grace Kim - MATH201
(2008, 3), -- Henry Patel - PHY101
(2009, 1), -- Isabel Santos - CS101
(2010, 2); -- Jack Murphy - MATH201

-- Insert sample assignments
INSERT INTO assignments (course_id, title, description, due_date) VALUES
(1, 'Programming Basics', 'Write a program to demonstrate basic programming concepts', '2025-03-01 23:59:59'),
(2, 'Mathematical Proofs', 'Submit proofs for the given theorems', '2025-03-05 23:59:59'),
(3, 'Physics Lab Report', 'Submit detailed report of the pendulum experiment', '2025-03-10 23:59:59');

-- Insert sample timetable
INSERT INTO timetable (course_id, faculty_id, day_of_week, start_time, end_time, room_number) VALUES
(1, 1001, 'Monday', '09:00:00', '10:30:00', 'CS-101'),
(2, 1002, 'Tuesday', '11:00:00', '12:30:00', 'MATH-201'),
(3, 1003, 'Wednesday', '14:00:00', '15:30:00', 'PHY-101');

-- Insert sample exam schedule
INSERT INTO exam_schedule (course_id, exam_type, exam_date, start_time, end_time, room_number) VALUES
(1, 'midterm', '2025-04-01', '09:00:00', '11:00:00', 'EXAM-101'),
(2, 'midterm', '2025-04-03', '09:00:00', '11:00:00', 'EXAM-102'),
(3, 'midterm', '2025-04-05', '09:00:00', '11:00:00', 'EXAM-103');

-- Insert sample announcements
INSERT INTO announcements (title, content, posted_by, target_audience) VALUES
('Mid-term Examination Schedule', 'Mid-term examinations will be held from April 1st to April 5th, 2025', 1001, 'all'),
('Faculty Meeting', 'Important faculty meeting on March 1st, 2025', 1001, 'faculty'),
('Student Council Elections', 'Student council elections will be held on March 15th, 2025', 1002, 'students');

-- Insert sample events
INSERT INTO events (title, description, event_date, venue, organizer_id) VALUES
('Annual Tech Fest', 'Annual technology festival with competitions and workshops', '2025-03-20 09:00:00', 'College Auditorium', 1001),
('Mathematics Olympiad', 'Inter-college mathematics competition', '2025-03-25 10:00:00', 'Mathematics Department', 1002),
('Science Exhibition', 'Annual science project exhibition', '2025-03-30 09:00:00', 'Science Block', 1003);

-- Create a credentials reference view (for administration purposes only)
CREATE OR REPLACE VIEW user_credentials_reference AS
SELECT 'FACULTY' as user_type, id as user_id, name, email, CONCAT('FAC', id) as login_password
FROM faculty
UNION
SELECT 'STUDENT' as user_type, id as user_id, name, email, CONCAT('STU', id) as login_password
FROM students
ORDER BY user_type, user_id;
