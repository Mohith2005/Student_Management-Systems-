-- Comprehensive Database Setup for Student Management System
-- This file combines all database setup and sample data

-- Create and use the database
DROP DATABASE IF EXISTS student_management;
CREATE DATABASE student_management;
USE student_management;

-- Create roles table
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default roles
INSERT INTO roles (role_name, description) VALUES
('admin', 'System administrator with full access'),
('faculty', 'Faculty members with teaching privileges'),
('student', 'Students enrolled in courses');

-- Create students table
CREATE TABLE students (
    id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    course VARCHAR(100),
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    role_id INT DEFAULT 3,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Create faculty table
CREATE TABLE faculty (
    id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    joining_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    role_id INT DEFAULT 2,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Create courses table
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_code VARCHAR(20) UNIQUE NOT NULL,
    course_name VARCHAR(100) NOT NULL,
    department VARCHAR(100),
    credits INT,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Create faculty_courses table
CREATE TABLE faculty_courses (
    faculty_id INT,
    course_id INT,
    PRIMARY KEY (faculty_id, course_id),
    FOREIGN KEY (faculty_id) REFERENCES faculty(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

-- Create student_courses table
CREATE TABLE student_courses (
    student_id INT,
    course_id INT,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    grade VARCHAR(2) DEFAULT NULL,
    PRIMARY KEY (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

-- Create attendance table
CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    course_id INT,
    date DATE,
    status ENUM('present', 'absent') DEFAULT 'present',
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

-- Insert sample faculty data
INSERT INTO faculty (id, name, email, password, department, status) VALUES
(1001, 'Dr. John Smith', 'john.smith@example.com', 'FAC1001', 'Computer Science', 'active'),
(1002, 'Prof. Mary Johnson', 'mary.johnson@example.com', 'FAC1002', 'Mathematics', 'active'),
(1003, 'Dr. Robert Brown', 'robert.brown@example.com', 'FAC1003', 'Physics', 'active'),
(1004, 'Prof. Sarah Davis', 'sarah.davis@example.com', 'FAC1004', 'Computer Science', 'active'),
(1005, 'Dr. Michael Wilson', 'michael.wilson@example.com', 'FAC1005', 'Mathematics', 'active');

-- Insert sample student data
INSERT INTO students (id, name, email, password, course, status) VALUES
(2001, 'Alice Johnson', 'alice.johnson@example.com', 'STU2001', 'Computer Science', 'active'),
(2002, 'Bob Williams', 'bob.williams@example.com', 'STU2002', 'Mathematics', 'active'),
(2003, 'Charlie Brown', 'charlie.brown@example.com', 'STU2003', 'Physics', 'active'),
(2004, 'Diana Miller', 'diana.miller@example.com', 'STU2004', 'Computer Science', 'active'),
(2005, 'Edward Davis', 'edward.davis@example.com', 'STU2005', 'Mathematics', 'active'),
(2006, 'Frank Wilson', 'frank.wilson@example.com', 'STU2006', 'Physics', 'active'),
(2007, 'Grace Taylor', 'grace.taylor@example.com', 'STU2007', 'Computer Science', 'active'),
(2008, 'Henry Patel', 'henry.patel@example.com', 'STU2008', 'Mathematics', 'active'),
(2009, 'Isabel Santos', 'isabel.santos@example.com', 'STU2009', 'Physics', 'active'),
(2010, 'Jack Murphy', 'jack.murphy@example.com', 'STU2010', 'Computer Science', 'active');

-- Insert sample courses
INSERT INTO courses (course_code, course_name, department, credits, description) VALUES
('CS101', 'Introduction to Programming', 'Computer Science', 3, 'Basic concepts of programming'),
('CS102', 'Data Structures', 'Computer Science', 4, 'Fundamental data structures and algorithms'),
('MATH101', 'Calculus I', 'Mathematics', 4, 'Introduction to calculus'),
('MATH102', 'Linear Algebra', 'Mathematics', 3, 'Vectors, matrices and linear transformations'),
('PHY101', 'Physics I', 'Physics', 4, 'Classical mechanics'),
('PHY102', 'Physics II', 'Physics', 4, 'Electricity and magnetism');

-- Assign courses to faculty
INSERT INTO faculty_courses (faculty_id, course_id) VALUES
(1001, 1), -- Dr. Smith - Intro to Programming
(1001, 2), -- Dr. Smith - Data Structures
(1002, 3), -- Prof. Johnson - Calculus I
(1002, 4), -- Prof. Johnson - Linear Algebra
(1003, 5), -- Dr. Brown - Physics I
(1003, 6); -- Dr. Brown - Physics II

-- Enroll students in courses
INSERT INTO student_courses (student_id, course_id) VALUES
(2001, 1), -- Alice - Intro to Programming
(2001, 2), -- Alice - Data Structures
(2002, 3), -- Bob - Calculus I
(2002, 4), -- Bob - Linear Algebra
(2003, 5), -- Charlie - Physics I
(2003, 6), -- Charlie - Physics II
(2004, 1), -- Diana - Intro to Programming
(2004, 2), -- Diana - Data Structures
(2005, 3), -- Edward - Calculus I
(2005, 4); -- Edward - Linear Algebra

-- Create helpful views
CREATE VIEW student_course_view AS
SELECT 
    s.id as student_id,
    s.name as student_name,
    c.course_code,
    c.course_name,
    sc.enrollment_date,
    sc.grade
FROM 
    students s
    JOIN student_courses sc ON s.id = sc.student_id
    JOIN courses c ON sc.course_id = c.id;

CREATE VIEW faculty_course_view AS
SELECT 
    f.id as faculty_id,
    f.name as faculty_name,
    c.course_code,
    c.course_name,
    c.department
FROM 
    faculty f
    JOIN faculty_courses fc ON f.id = fc.faculty_id
    JOIN courses c ON fc.course_id = c.id;
