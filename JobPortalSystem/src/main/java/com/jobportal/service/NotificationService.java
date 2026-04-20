package com.jobportal.service;

import org.springframework.stereotype.Service;

@Service
public class NotificationService {

    public void sendWelcomeEmail(String toEmail, String userName) {
        try {
            String subject = "Welcome to JobPortal!";
            String body = "Hello " + userName + ",\n\n" +
                    "Welcome to JobPortal! We're excited to have you on board.\n" +
                    "You can now browse jobs, post positions, or manage applications.\n\n" +
                    "Best regards,\nJobPortal Team";
            
            // Email sending logic would go here
            // For now, this is a placeholder
            System.out.println("Email sent to: " + toEmail + "\nSubject: " + subject);
        } catch (Exception e) {
            System.err.println("Failed to send welcome email: " + e.getMessage());
        }
    }

    public void sendApplicationConfirmation(String toEmail, String jobTitle) {
        try {
            String subject = "Application Submitted Successfully";
            String body = "Thank you for applying for the " + jobTitle + " position.\n\n" +
                    "We have received your application and will review it shortly.\n" +
                    "You will be notified when there are updates.\n\n" +
                    "Best regards,\nJobPortal Team";
            
            System.out.println("Email sent to: " + toEmail + "\nSubject: " + subject);
        } catch (Exception e) {
            System.err.println("Failed to send application confirmation email: " + e.getMessage());
        }
    }

    public void sendApplicationStatusUpdate(String toEmail, String jobTitle, String status) {
        try {
            String subject = "Application Status Update - " + jobTitle;
            String body = "Your application for the " + jobTitle + " position has been " + status + ".\n\n" +
                    "Thank you for your interest in our company.\n\n" +
                    "Best regards,\nJobPortal Team";
            
            System.out.println("Email sent to: " + toEmail + "\nSubject: " + subject);
        } catch (Exception e) {
            System.err.println("Failed to send status update email: " + e.getMessage());
        }
    }

    public void sendJobPostedNotification(String employerEmail, String jobTitle) {
        try {
            String subject = "Job Posted Successfully";
            String body = "Your job posting for " + jobTitle + " is now live!\n\n" +
                    "Job seekers can now apply for this position.\n\n" +
                    "Best regards,\nJobPortal Team";
            
            System.out.println("Email sent to: " + employerEmail + "\nSubject: " + subject);
        } catch (Exception e) {
            System.err.println("Failed to send job posting notification: " + e.getMessage());
        }
    }

    public void sendNewApplicationNotification(String employerEmail, String applicantName, String jobTitle) {
        try {
            String subject = "New Application Received";
            String body = applicantName + " has applied for the " + jobTitle + " position.\n\n" +
                    "Review their application in your dashboard.\n\n" +
                    "Best regards,\nJobPortal Team";
            
            System.out.println("Email sent to: " + employerEmail + "\nSubject: " + subject);
        } catch (Exception e) {
            System.err.println("Failed to send new application notification: " + e.getMessage());
        }
    }

    public void sendPasswordResetLink(String toEmail, String resetLink) {
        try {
            String subject = "Password Reset Request";
            String body = "We received a request to reset your password.\n\n" +
                    "Click the link below to reset your password:\n" +
                    resetLink + "\n\n" +
                    "If you didn't request this, please ignore this email.\n\n" +
                    "Best regards,\nJobPortal Team";
            
            System.out.println("Email sent to: " + toEmail + "\nSubject: " + subject);
        } catch (Exception e) {
            System.err.println("Failed to send password reset email: " + e.getMessage());
        }
    }
}
