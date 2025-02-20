USE student_management;

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

-- Insert sample faculty data
INSERT INTO faculty (name, email, password, department) VALUES
('Dr. John Smith', 'john.smith@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Computer Science'),
('Dr. Sarah Johnson', 'sarah.johnson@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Mathematics'),
('Prof. Michael Brown', 'michael.brown@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Physics');

-- Insert sample students
INSERT INTO students (name, email, password, course) VALUES
('Alice Cooper', 'alice.cooper@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Computer Science'),
('Bob Wilson', 'bob.wilson@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Mathematics'),
('Carol Martinez', 'carol.martinez@example.com', '$2y$10$abcdefghijklmnopqrstuv', 'Physics');

-- Insert sample assignments
INSERT INTO assignments (course_id, title, description, due_date) VALUES
(1, 'Programming Basics', 'Write a program to demonstrate basic programming concepts', '2025-03-01 23:59:59'),
(2, 'Mathematical Proofs', 'Submit proofs for the given theorems', '2025-03-05 23:59:59'),
(3, 'Physics Lab Report', 'Submit detailed report of the pendulum experiment', '2025-03-10 23:59:59');

-- Insert sample timetable
INSERT INTO timetable (course_id, faculty_id, day_of_week, start_time, end_time, room_number) VALUES
(1, 1, 'Monday', '09:00:00', '10:30:00', 'CS-101'),
(2, 2, 'Tuesday', '11:00:00', '12:30:00', 'MATH-201'),
(3, 3, 'Wednesday', '14:00:00', '15:30:00', 'PHY-101');

-- Insert sample exam schedule
INSERT INTO exam_schedule (course_id, exam_type, exam_date, start_time, end_time, room_number) VALUES
(1, 'midterm', '2025-04-01', '09:00:00', '11:00:00', 'EXAM-101'),
(2, 'midterm', '2025-04-03', '09:00:00', '11:00:00', 'EXAM-102'),
(3, 'midterm', '2025-04-05', '09:00:00', '11:00:00', 'EXAM-103');

-- Insert sample announcements
INSERT INTO announcements (title, content, posted_by, target_audience) VALUES
('Mid-term Examination Schedule', 'Mid-term examinations will be held from April 1st to April 5th, 2025', 1, 'all'),
('Faculty Meeting', 'Important faculty meeting on March 1st, 2025', 1, 'faculty'),
('Student Council Elections', 'Student council elections will be held on March 15th, 2025', 2, 'students');

-- Insert sample events
INSERT INTO events (title, description, event_date, venue, organizer_id) VALUES
('Annual Tech Fest', 'Annual technology festival with competitions and workshops', '2025-03-20 09:00:00', 'College Auditorium', 1),
('Mathematics Olympiad', 'Inter-college mathematics competition', '2025-03-25 10:00:00', 'Mathematics Department', 2),
('Science Exhibition', 'Annual science project exhibition', '2025-03-30 09:00:00', 'Science Block', 3);
