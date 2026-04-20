package com.jobportal.controller;

import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import com.jobportal.model.User;
import com.jobportal.repo.UserRepository;
import com.jobportal.service.UserService;
import com.jobportal.service.NotificationService;
import com.jobportal.service.AuditLogger;
import com.jobportal.util.ValidationUtil;

@Controller
public class RegistrationController {

    private final UserRepository userRepository;
    private final PasswordEncoder passwordEncoder;
    private final UserService userService;
    private final NotificationService notificationService;
    private final AuditLogger auditLogger;

    public RegistrationController(UserRepository userRepository, PasswordEncoder passwordEncoder,
                                 UserService userService, NotificationService notificationService,
                                 AuditLogger auditLogger) {
        this.userRepository = userRepository;
        this.passwordEncoder = passwordEncoder;
        this.userService = userService;
        this.notificationService = notificationService;
        this.auditLogger = auditLogger;
    }

    @GetMapping("/register")
    public String showRegistrationForm() {
        return "register";
    }

    @PostMapping("/register")
    public String registerUser(@RequestParam String name,
                               @RequestParam String email,
                               @RequestParam String password,
                               @RequestParam String role,
                               RedirectAttributes redirectAttributes) {
        
        // Validate inputs
        if (!ValidationUtil.isValidEmail(email)) {
            redirectAttributes.addFlashAttribute("error", "Invalid email format");
            return "redirect:/register";
        }
        
        if (!ValidationUtil.isValidPassword(password)) {
            redirectAttributes.addFlashAttribute("error", "Password must be at least 6 characters");
            return "redirect:/register";
        }
        
        if (!ValidationUtil.isValidName(name)) {
            redirectAttributes.addFlashAttribute("error", "Name must be between 2 and 100 characters");
            return "redirect:/register";
        }
        
        if (!ValidationUtil.isValidRole(role)) {
            redirectAttributes.addFlashAttribute("error", "Invalid role selected");
            return "redirect:/register";
        }
        
        if (userRepository.findByEmail(email).isPresent()) {
            auditLogger.logError("REGISTRATION_ERROR", "Duplicate email", email);
            redirectAttributes.addFlashAttribute("error", "Email already exists");
            return "redirect:/register";
        }
        
        try {
            User user = new User();
            user.setName(name);
            user.setEmail(email);
            user.setPassword(passwordEncoder.encode(password));
            user.setRole(role);
            userRepository.save(user);
            
            auditLogger.logUserRegistration(email, name, role);
            notificationService.sendWelcomeEmail(email, name);
            
            redirectAttributes.addFlashAttribute("success", "Registration successful! Please sign in.");
            return "redirect:/login";
        } catch (Exception e) {
            auditLogger.logError("REGISTRATION_ERROR", e.getMessage(), email);
            redirectAttributes.addFlashAttribute("error", "An error occurred during registration");
            return "redirect:/register";
        }
    }
}