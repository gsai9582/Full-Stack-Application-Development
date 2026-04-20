# Job Portal Backend - API Documentation

## Overview
The Job Portal backend provides a comprehensive REST API for managing jobs, users, applications, and user profiles with role-based access control.

## Base URL
```
http://localhost:8081/api
```

## Authentication
Most API endpoints require authentication. Use Spring Security login to obtain authentication.

---

## Job Management APIs

### Get All Jobs
```
GET /api/jobs
Response: List of all jobs with details
```

### Get Job by ID
```
GET /api/jobs/{id}
Response: Single job details
```

### Search Jobs
```
GET /api/jobs/search?query={searchTerm}
Response: List of jobs matching the search query
```

### Get Jobs by Employer
```
GET /api/jobs/employer/{email}
Response: All jobs posted by a specific employer
```

### Create Job (Employer Only)
```
POST /api/jobs
Body: {
  "title": "Software Engineer",
  "location": "New York",
  "skills": "Java, Spring Boot, React",
  "salary": "$80000-$120000",
  "description": "Looking for experienced developer",
  "postedBy": "employer@example.com"
}
Response: Job created successfully
```

### Update Job (Employer Only)
```
PUT /api/jobs/{id}
Body: Job details to update
Response: Job updated successfully
```

### Delete Job (Employer Only)
```
DELETE /api/jobs/{id}
Response: Job deleted successfully
```

### Get Total Jobs Count
```
GET /api/jobs/stats/total
Response: {"success": true, "data": 25}
```

---

## User Management APIs

### Get User by ID
```
GET /api/users/{id}
Response: User details (name, email, role, etc.)
```

### Get User by Email
```
GET /api/users/email/{email}
Response: User details
```

### Update User Profile
```
PUT /api/users/{id}
Body: {
  "name": "John Doe",
  "role": "JOB_SEEKER",
  "resumePath": "/path/to/resume"
}
Response: User updated successfully
```

### Change Password (Authenticated)
```
POST /api/users/{id}/change-password?newPassword={password}
Response: Password changed successfully
```

### Update Resume
```
POST /api/users/{id}/update-resume?resumePath={path}
Response: Resume updated successfully
```

### Check if Email Exists
```
GET /api/users/check-email/{email}
Response: {"success": true, "data": true/false}
```

### Get Total Users Count
```
GET /api/users/stats/total
Response: {"success": true, "data": 100}
```

### Get User Count by Role
```
GET /api/users/stats/by-role/{role}
Response: {"success": true, "data": 50}
```

---

## Job Application APIs

### Get Applications by Applicant
```
GET /api/applications/applicant/{userId}
Response: List of applications submitted by user
```

### Get Applications by Job
```
GET /api/applications/job/{jobId}
Response: List of applications for a specific job
```

### Get Applications by Employer
```
GET /api/applications/employer/{email}
Response: All applications for jobs posted by employer
```

### Get Application by ID
```
GET /api/applications/{id}
Response: Single application details
```

### Update Application Status
```
PUT /api/applications/{id}/status?status={status}
Allowed statuses: Submitted, Approved, Rejected
Response: Application status updated
```

### Delete Application
```
DELETE /api/applications/{id}
Response: Application deleted
```

### Get Total Applications Count
```
GET /api/applications/stats/total
Response: {"success": true, "data": 150}
```

### Get Applications by Status
```
GET /api/applications/stats/by-status/{status}
Response: Count of applications with specific status
```

### Get User's Applications Count
```
GET /api/applications/stats/by-user/{userId}
Response: Total applications submitted by user
```

---

## Profile APIs (Authenticated Users)

### Get Current User Profile
```
GET /api/profile/me
Response: Current logged-in user details
```

### Update Current User Profile
```
PUT /api/profile/me
Body: {
  "name": "Jane Doe",
  "role": "JOB_SEEKER",
  "resumePath": "/uploads/resume.pdf"
}
Response: Profile updated successfully
```

### Change Password (Current User)
```
POST /api/profile/me/password
Parameters: 
  - currentPassword: Current password
  - newPassword: New password
Response: Password changed successfully
```

### Update Resume (Current User)
```
POST /api/profile/me/resume?resumePath=/path
Response: Resume updated successfully
```

### Check Email Existence
```
GET /api/profile/exists/{email}
Response: {"success": true, "data": true/false}
```

---

## Statistics APIs (Admin Only)

### Get Dashboard Statistics
```
GET /api/statistics/dashboard
Response: {
  "totalJobs": 25,
  "totalUsers": 100,
  "totalApplications": 150,
  "jobSeekers": 70,
  "employers": 25,
  "admins": 5,
  "submittedApplications": 120,
  "approvedApplications": 20,
  "rejectedApplications": 10
}
```

### Get Job Statistics
```
GET /api/statistics/jobs
Response: Job count and metrics
```

### Get User Statistics
```
GET /api/statistics/users
Response: User count by role
```

### Get Application Statistics
```
GET /api/statistics/applications
Response: Application count by status
```

---

## Response Format

All API responses follow this format:

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { /* Response data */ },
  "timestamp": "2024-04-19T10:30:00"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "data": null,
  "timestamp": "2024-04-19T10:30:00"
}
```

---

## User Roles

1. **JOB_SEEKER**: Can browse jobs, apply for positions, manage applications
2. **EMPLOYER**: Can post jobs, view applicants, manage job listings
3. **ADMIN**: Can access all statistics, manage users, jobs, and applications

---

## Validation Rules

- **Email**: Must be a valid email format
- **Password**: Minimum 6 characters
- **Name**: Between 2 and 100 characters
- **Role**: Must be JOB_SEEKER, EMPLOYER, or ADMIN

---

## Backend Services

### 1. UserService
Handles user creation, retrieval, updates, and role-based operations.

### 2. JobService
Manages job CRUD operations, searching, and job listings by employer.

### 3. JobApplicationService
Handles application submissions, status updates, and retrieval.

### 4. NotificationService
Sends email notifications for registration, applications, and status updates.

### 5. AuditLogger
Tracks user actions like registration, login, job creation, and application management.

### 6. ValidationUtil
Provides validation for email, password, name, phone numbers, and roles.

---

## Database Models

### User Entity
- id, name, email, password (encrypted), role, resumePath

### Job Entity
- id, title, location, skills, salary, description, postedBy

### JobApplication Entity
- id, job (FK), applicant (FK), resumeFilename, status

---

## Future Enhancements

- Email integration with SMTP
- Advanced search with filters
- Job recommendations
- User activity history
- Application timeline tracking
- Resume parsing and skill extraction
- Messaging system between employers and job seekers
