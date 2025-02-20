-- Drop foreign key constraints first
ALTER TABLE students DROP FOREIGN KEY IF EXISTS students_ibfk_1;
ALTER TABLE faculty DROP FOREIGN KEY IF EXISTS faculty_ibfk_1;

-- Create roles table if it doesn't exist
CREATE TABLE IF NOT EXISTS roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default roles if they don't exist
INSERT IGNORE INTO roles (id, role_name, description) VALUES
(1, 'admin', 'System administrator with full access'),
(2, 'faculty', 'Faculty members with teaching privileges'),
(3, 'student', 'Students enrolled in courses');

-- Add role_id column to students table if it doesn't exist
ALTER TABLE students 
ADD COLUMN IF NOT EXISTS role_id INT DEFAULT 3;

-- Add role_id column to faculty table if it doesn't exist
ALTER TABLE faculty 
ADD COLUMN IF NOT EXISTS role_id INT DEFAULT 2;

-- Add foreign key constraints
ALTER TABLE students
ADD CONSTRAINT students_ibfk_1
FOREIGN KEY (role_id) REFERENCES roles(id);

ALTER TABLE faculty
ADD CONSTRAINT faculty_ibfk_1
FOREIGN KEY (role_id) REFERENCES roles(id);

-- Update existing records with correct role IDs
UPDATE students SET role_id = 3 WHERE role_id IS NULL;
UPDATE faculty SET role_id = 2 WHERE role_id IS NULL;
