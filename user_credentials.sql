USE student_management;

-- Clear existing sample data
TRUNCATE TABLE student_assignments;
TRUNCATE TABLE student_courses;
TRUNCATE TABLE attendance;
TRUNCATE TABLE assignments;
TRUNCATE TABLE faculty_courses;
DELETE FROM students;
DELETE FROM faculty;

-- Insert Faculty Members with IDs and Passwords (password is same as their ID for demonstration)
INSERT INTO faculty (id, name, email, password, department) VALUES
(1001, 'Dr. John Smith', 'john.smith@faculty.edu', '$2y$10$8X4mK8jOJ5K5tZ1QhW6YeuFzxP9TxUL6Dp0nQN9qIyeWTiYJNzQeq', 'Computer Science'),  -- Password: FAC1001
(1002, 'Dr. Sarah Johnson', 'sarah.johnson@faculty.edu', '$2y$10$vN9uY2X7kJ5L8Y1qHw6YeuFzxP9TxUL6Dp0nQN9qIyeWTiYJNzQeq', 'Mathematics'),     -- Password: FAC1002
(1003, 'Prof. Michael Brown', 'michael.brown@faculty.edu', '$2y$10$pK8mJ9jOJ5K5tZ1QhW6YeuFzxP9TxUL6Dp0nQN9qIyeWTiYJNzQeq', 'Physics'),        -- Password: FAC1003
(1004, 'Dr. Emily Davis', 'emily.davis@faculty.edu', '$2y$10$rL2mK8jOJ5K5tZ1QhW6YeuFzxP9TxUL6Dp0nQN9qIyeWTiYJNzQeq', 'Chemistry'),          -- Password: FAC1004
(1005, 'Prof. Robert Wilson', 'robert.wilson@faculty.edu', '$2y$10$tN4mK8jOJ5K5tZ1QhW6YeuFzxP9TxUL6Dp0nQN9qIyeWTiYJNzQeq', 'English');        -- Password: FAC1005

-- Insert Students with IDs and Passwords (password is same as their ID for demonstration)
INSERT INTO students (id, name, email, password, course) VALUES
(2001, 'Alice Cooper', 'alice.cooper@student.edu', '$2y$10$vB7uY2X7kJ5L8Y1qHw6YeuFzxP9TxUL6Dp0nQN9qIyeWTiYJNzQeq', 'Computer Science'),    -- Password: STU2001
(2002, 'Bob Wilson', 'bob.wilson@student.edu', '$2y$10$pK8mJ9jOJ5K5tZ1QhW6YeuFzxP9TxUL6Dp0nQN9qIyeWTiYJNzQeq', 'Mathematics'),            -- Password: STU2002
(2003, 'Carol Martinez', 'carol.martinez@student.edu', '$2y$10$rL2mK8jOJ5K5tZ1QhW6YeuFzxP9TxUL6Dp0nQN9qIyeWTiYJNzQeq', 'Physics'),          -- Password: STU2003
(2004, 'David Thompson', 'david.thompson@student.edu', '$2y$10$tN4mK8jOJ5K5tZ1QhW6YeuFzxP9TxUL6Dp0nQN9qIyeWTiYJNzQeq', 'Chemistry'),        -- Password: STU2004
(2005, 'Emma Rodriguez', 'emma.rodriguez@student.edu', '$2y$10$vN9uY2X7kJ5L8Y1qHw6YeuFzxP9TxUL6Dp0nQN9qIyeWTiYJNzQeq', 'English'),          -- Password: STU2005
(2006, 'Frank Lee', 'frank.lee@student.edu', '$2y$10$8X4mK8jOJ5K5tZ1QhW6YeuFzxP9TxUL6Dp0nQN9qIyeWTiYJNzQeq', 'Computer Science'),         -- Password: STU2006
(2007, 'Grace Kim', 'grace.kim@student.edu', '$2y$10$pK8mJ9jOJ5K5tZ1QhW6YeuFzxP9TxUL6Dp0nQN9qIyeWTiYJNzQeq', 'Mathematics'),              -- Password: STU2007
(2008, 'Henry Patel', 'henry.patel@student.edu', '$2y$10$rL2mK8jOJ5K5tZ1QhW6YeuFzxP9TxUL6Dp0nQN9qIyeWTiYJNzQeq', 'Physics'),              -- Password: STU2008
(2009, 'Isabel Santos', 'isabel.santos@student.edu', '$2y$10$tN4mK8jOJ5K5tZ1QhW6YeuFzxP9TxUL6Dp0nQN9qIyeWTiYJNzQeq', 'Chemistry'),         -- Password: STU2009
(2010, 'Jack Murphy', 'jack.murphy@student.edu', '$2y$10$vN9uY2X7kJ5L8Y1qHw6YeuFzxP9TxUL6Dp0nQN9qIyeWTiYJNzQeq', 'English');              -- Password: STU2010

-- Recreate faculty course assignments
INSERT INTO faculty_courses (faculty_id, course_id) VALUES
(1001, 1), -- Dr. John Smith - Computer Science
(1002, 2), -- Dr. Sarah Johnson - Mathematics
(1003, 3), -- Prof. Michael Brown - Physics
(1004, 1), -- Dr. Emily Davis - Computer Science
(1005, 2); -- Prof. Robert Wilson - Mathematics

-- Recreate student course enrollments
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

-- Create a credentials reference view (for administration purposes only)
CREATE OR REPLACE VIEW user_credentials_reference AS
SELECT 'FACULTY' as user_type, id as user_id, name, email, CONCAT('FAC', id) as login_password
FROM faculty
UNION
SELECT 'STUDENT' as user_type, id as user_id, name, email, CONCAT('STU', id) as login_password
FROM students
ORDER BY user_type, user_id;
