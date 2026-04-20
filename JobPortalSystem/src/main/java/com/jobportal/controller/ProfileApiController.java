package com.jobportal.controller;

import com.jobportal.dto.ApiResponse;
import com.jobportal.dto.UserDTO;
import com.jobportal.model.User;
import com.jobportal.service.UserService;
import org.springframework.security.core.Authentication;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/profile")
@CrossOrigin(origins = "*", maxAge = 3600)
public class ProfileApiController {

    private final UserService userService;

    public ProfileApiController(UserService userService) {
        this.userService = userService;
    }

    @GetMapping("/me")
    public ResponseEntity<ApiResponse<UserDTO>> getCurrentUserProfile(Authentication authentication) {
        if (authentication == null || !authentication.isAuthenticated()) {
            return ResponseEntity.status(401).body(new ApiResponse<>(false, "User not authenticated"));
        }
        
        String email = authentication.getName();
        UserDTO user = userService.getUserByEmail(email);
        
        if (user != null) {
            return ResponseEntity.ok(new ApiResponse<>(true, "Profile retrieved", user));
        }
        return ResponseEntity.notFound().build();
    }

    @PutMapping("/me")
    public ResponseEntity<ApiResponse<String>> updateCurrentUserProfile(
            Authentication authentication, 
            @RequestBody UserDTO userDTO) {
        
        if (authentication == null || !authentication.isAuthenticated()) {
            return ResponseEntity.status(401).body(new ApiResponse<>(false, "User not authenticated"));
        }
        
        User user = userService.getUserEntityByEmail(authentication.getName());
        if (user != null) {
            user.setName(userDTO.getName());
            user.setRole(userDTO.getRole());
            user.setResumePath(userDTO.getResumePath());
            
            userService.updateUser(user.getId(), user);
            return ResponseEntity.ok(new ApiResponse<>(true, "Profile updated successfully", "User ID: " + user.getId()));
        }
        
        return ResponseEntity.notFound().build();
    }

    @PostMapping("/me/password")
    public ResponseEntity<ApiResponse<String>> changeCurrentPassword(
            Authentication authentication, 
            @RequestParam String currentPassword,
            @RequestParam String newPassword) {
        
        if (authentication == null || !authentication.isAuthenticated()) {
            return ResponseEntity.status(401).body(new ApiResponse<>(false, "User not authenticated"));
        }
        
        if (newPassword == null || newPassword.length() < 6) {
            return ResponseEntity.badRequest().body(new ApiResponse<>(false, "Password must be at least 6 characters"));
        }
        
        User user = userService.getUserEntityByEmail(authentication.getName());
        if (user != null) {
            userService.updatePassword(user.getId(), newPassword);
            return ResponseEntity.ok(new ApiResponse<>(true, "Password changed successfully", "User ID: " + user.getId()));
        }
        
        return ResponseEntity.notFound().build();
    }

    @PostMapping("/me/resume")
    public ResponseEntity<ApiResponse<String>> updateCurrentUserResume(
            Authentication authentication, 
            @RequestParam String resumePath) {
        
        if (authentication == null || !authentication.isAuthenticated()) {
            return ResponseEntity.status(401).body(new ApiResponse<>(false, "User not authenticated"));
        }
        
        User user = userService.getUserEntityByEmail(authentication.getName());
        if (user != null) {
            userService.updateResume(user.getId(), resumePath);
            return ResponseEntity.ok(new ApiResponse<>(true, "Resume updated successfully", "User ID: " + user.getId()));
        }
        
        return ResponseEntity.notFound().build();
    }

    @GetMapping("/exists/{email}")
    public ResponseEntity<ApiResponse<Boolean>> checkIfEmailExists(@PathVariable String email) {
        boolean exists = userService.emailExists(email);
        return ResponseEntity.ok(new ApiResponse<>(true, "Email check completed", exists));
    }
}
