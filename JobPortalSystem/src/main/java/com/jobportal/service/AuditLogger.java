package com.jobportal.service;

import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import org.springframework.stereotype.Service;

@Service
public class AuditLogger {

    private static final DateTimeFormatter formatter = DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss");

    public void logUserRegistration(String email, String name, String role) {
        String message = String.format("[%s] USER_REGISTRATION - Email: %s, Name: %s, Role: %s",
                LocalDateTime.now().format(formatter), email, name, role);
        System.out.println(message);
    }

    public void logUserLogin(String email) {
        String message = String.format("[%s] USER_LOGIN - Email: %s",
                LocalDateTime.now().format(formatter), email);
        System.out.println(message);
    }

    public void logJobCreated(Long jobId, String jobTitle, String employerEmail) {
        String message = String.format("[%s] JOB_CREATED - JobID: %d, Title: %s, Employer: %s",
                LocalDateTime.now().format(formatter), jobId, jobTitle, employerEmail);
        System.out.println(message);
    }

    public void logJobUpdated(Long jobId, String jobTitle, String employerEmail) {
        String message = String.format("[%s] JOB_UPDATED - JobID: %d, Title: %s, Employer: %s",
                LocalDateTime.now().format(formatter), jobId, jobTitle, employerEmail);
        System.out.println(message);
    }

    public void logJobDeleted(Long jobId, String jobTitle, String employerEmail) {
        String message = String.format("[%s] JOB_DELETED - JobID: %d, Title: %s, Employer: %s",
                LocalDateTime.now().format(formatter), jobId, jobTitle, employerEmail);
        System.out.println(message);
    }

    public void logApplicationSubmitted(Long applicationId, Long jobId, String applicantEmail) {
        String message = String.format("[%s] APPLICATION_SUBMITTED - AppID: %d, JobID: %d, Applicant: %s",
                LocalDateTime.now().format(formatter), applicationId, jobId, applicantEmail);
        System.out.println(message);
    }

    public void logApplicationStatusChanged(Long applicationId, String oldStatus, String newStatus) {
        String message = String.format("[%s] APPLICATION_STATUS_CHANGED - AppID: %d, Status: %s -> %s",
                LocalDateTime.now().format(formatter), applicationId, oldStatus, newStatus);
        System.out.println(message);
    }

    public void logUserProfileUpdated(String email) {
        String message = String.format("[%s] USER_PROFILE_UPDATED - Email: %s",
                LocalDateTime.now().format(formatter), email);
        System.out.println(message);
    }

    public void logPasswordChanged(String email) {
        String message = String.format("[%s] PASSWORD_CHANGED - Email: %s",
                LocalDateTime.now().format(formatter), email);
        System.out.println(message);
    }

    public void logAdminAction(String adminEmail, String action, String details) {
        String message = String.format("[%s] ADMIN_ACTION - Admin: %s, Action: %s, Details: %s",
                LocalDateTime.now().format(formatter), adminEmail, action, details);
        System.out.println(message);
    }

    public void logError(String errorType, String errorMessage, String userId) {
        String message = String.format("[%s] ERROR - Type: %s, User: %s, Message: %s",
                LocalDateTime.now().format(formatter), errorType, userId, errorMessage);
        System.err.println(message);
    }
}
