import React, { useState } from 'react';
import { ChevronDown, Book, Users, Calendar, ClipboardCheck } from 'lucide-react';

const facultyData = {
  name: "Mrs. A.ANANYA",
  role: "Assistant Professor",
  department: "AI",
  email: "ananya.a@university.edu",
  totalStudents: 62,
  courses: [
    { id: "18AIC301", name: "Machine Learning with AI Services", icon: "ML" },
    { id: "18AIC302", name: "Data Analysis and Business Intelligence", icon: "DA" },
    { id: "18AIC303", name: "Computer Networks", icon: "CN" },
    { id: "18AIC304", name: "Embedded Systems with AI", icon: "ES" }
  ],
  stats: [
    { id: 1, number: 4, label: "Active Courses", icon: Book },
    { id: 2, number: 62, label: "Total Students", icon: Users },
    { id: 3, number: 12, label: "Upcoming Classes", icon: Calendar },
    { id: 4, number: 8, label: "Pending Assignments", icon: ClipboardCheck }
  ]
};

const ProfilePopup = ({ isVisible, profile }) => {
  if (!isVisible) return null;

  return (
    <div className="absolute top-16 right-0 bg-white p-4 rounded-xl shadow-lg w-72 z-50">
      <h3 className="text-gray-800 font-semibold mb-2">{profile.name}</h3>
      <p className="text-gray-600 text-sm mb-1">{profile.role}</p>
      <p className="text-gray-600 text-sm mb-1">Department: {profile.department}</p>
      <p className="text-gray-600 text-sm mb-1">{profile.email}</p>
      <p className="text-gray-600 text-sm">Total Students: {profile.totalStudents}</p>
    </div>
  );
};

const StatCard = ({ stat }) => {
  const Icon = stat.icon;
  return (
    <div className="bg-white p-6 rounded-xl shadow-sm text-center">
      <div className="flex justify-center mb-3">
        <Icon className="text-indigo-500" size={24} />
      </div>
      <div className="text-3xl font-semibold text-indigo-500 mb-2">{stat.number}</div>
      <div className="text-gray-500 text-sm">{stat.label}</div>
    </div>
  );
};

const CourseCard = ({ course }) => (
  <div className="bg-white p-5 rounded-xl shadow-sm flex items-center gap-4">
    <div className="w-12 h-12 bg-indigo-500 rounded-xl flex items-center justify-center text-white font-medium">
      {course.icon}
    </div>
    <div>
      <h3 className="text-gray-800 font-medium mb-1">{course.id}</h3>
      <p className="text-gray-500 text-sm">{course.name}</p>
    </div>
  </div>
);

const FacultyDashboard = () => {
  const [showProfile, setShowProfile] = useState(false);

  const toggleProfile = (e) => {
    e.stopPropagation();
    setShowProfile(!showProfile);
  };

  React.useEffect(() => {
    const handleClickOutside = () => setShowProfile(false);
    document.addEventListener('click', handleClickOutside);
    return () => document.removeEventListener('click', handleClickOutside);
  }, []);

  return (
    <div className="min-h-screen bg-gray-50 p-6">
      {/* Header */}
      <div className="bg-gradient-to-r from-indigo-500 to-purple-500 p-6 rounded-2xl mb-6 text-white flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-semibold mb-2">Faculty Dashboard</h1>
          <p>Welcome back, {facultyData.name}</p>
        </div>
        <div className="relative">
          <button
            onClick={toggleProfile}
            className="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center border-2 border-white border-opacity-50 text-white"
          >
            AA
          </button>
          <ProfilePopup isVisible={showProfile} profile={facultyData} />
        </div>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {facultyData.stats.map(stat => (
          <StatCard key={stat.id} stat={stat} />
        ))}
      </div>

      {/* Courses Section */}
      <h2 className="text-xl font-semibold text-gray-800 mb-6">My Courses</h2>
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {facultyData.courses.map(course => (
          <CourseCard key={course.id} course={course} />
        ))}
      </div>
    </div>
  );
};

export default FacultyDashboard;