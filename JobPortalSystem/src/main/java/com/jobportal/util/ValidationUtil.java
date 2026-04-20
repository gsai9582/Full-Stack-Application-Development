package com.jobportal.util;

import java.util.regex.Pattern;

public class ValidationUtil {
    
    private static final Pattern EMAIL_PATTERN = 
        Pattern.compile("^[A-Za-z0-9+_.-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$");
    
    private static final Pattern PHONE_PATTERN = 
        Pattern.compile("^\\d{10}$");
    
    private static final int MIN_PASSWORD_LENGTH = 6;
    private static final int MIN_NAME_LENGTH = 2;
    private static final int MAX_NAME_LENGTH = 100;
    
    public static boolean isValidEmail(String email) {
        if (email == null || email.isBlank()) {
            return false;
        }
        return EMAIL_PATTERN.matcher(email).matches();
    }
    
    public static boolean isValidPassword(String password) {
        if (password == null) {
            return false;
        }
        return password.length() >= MIN_PASSWORD_LENGTH;
    }
    
    public static boolean isValidName(String name) {
        if (name == null || name.isBlank()) {
            return false;
        }
        int length = name.length();
        return length >= MIN_NAME_LENGTH && length <= MAX_NAME_LENGTH;
    }
    
    public static boolean isValidPhoneNumber(String phone) {
        if (phone == null || phone.isBlank()) {
            return false;
        }
        return PHONE_PATTERN.matcher(phone).matches();
    }
    
    public static boolean isValidRole(String role) {
        if (role == null || role.isBlank()) {
            return false;
        }
        return role.equals("JOB_SEEKER") || role.equals("EMPLOYER") || role.equals("ADMIN");
    }
    
    public static String sanitizeInput(String input) {
        if (input == null) {
            return "";
        }
        return input.trim();
    }
}
