// Add this script tag at the end of each HTML file

// Common utility function to handle course card clicks
function handleCourseCardClick(userType) {
  const courseCards = document.querySelectorAll('.course-card');

  courseCards.forEach(card => {
      card.addEventListener('click', () => {
          // Get course info for tracking
          const courseTitle = card.querySelector('.course-header div h3') || 
                              card.querySelector('.course-info h3');

          // Redirect based on user type
          if (userType === 'faculty') {
              window.location.href = 'faculty_course.html';
          } else if (userType === 'student') {
              window.location.href = 'student_course.html';
          }
      });

      // Add hover effect for better UX
      card.style.cursor = 'pointer';
  });
}

// Handle video lecture clicks in student course page
function handleVideoLectureClick() {
  const videoLectures = document.querySelectorAll('.card-content .list-item');

  videoLectures.forEach(lecture => {
      if (lecture.textContent.includes('Lecture')) {
          lecture.addEventListener('click', () => {
              window.location.href = 'Test.html'; // Redirect to test page
          });
          lecture.style.cursor = 'pointer';
      }
  });
}

// Initialize navigation for faculty dashboard
function initFacultyDashboard() {
  handleCourseCardClick('faculty');
}

// Initialize navigation for student dashboard
function initStudentDashboard() {
  handleCourseCardClick('student');
}

// Initialize navigation for student course page
function initStudentCourse() {
  handleVideoLectureClick();
}
