package com.jobportal.controller;

import com.jobportal.dto.ApiResponse;
import com.jobportal.dto.UserDTO;
import com.jobportal.service.UserService;
import com.jobportal.model.User;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/users")
@CrossOrigin(origins = "*", maxAge = 3600)
public class UserApiController {

    private final UserService userService;

    public UserApiController(UserService userService) {
        this.userService = userService;
    }

    @GetMapping("/{id}")
    public ResponseEntity<ApiResponse<UserDTO>> getUserById(@PathVariable Long id) {
        UserDTO user = userService.getUserById(id);
        if (user != null) {
            return ResponseEntity.ok(new ApiResponse<>(true, "User retrieved successfully", user));
        }
        return ResponseEntity.notFound().build();
    }

    @GetMapping("/email/{email}")
    public ResponseEntity<ApiResponse<UserDTO>> getUserByEmail(@PathVariable String email) {
        UserDTO user = userService.getUserByEmail(email);
        if (user != null) {
            return ResponseEntity.ok(new ApiResponse<>(true, "User retrieved successfully", user));
        }
        return ResponseEntity.notFound().build();
    }

    @PutMapping("/{id}")
    public ResponseEntity<ApiResponse<String>> updateUser(@PathVariable Long id, @RequestBody UserDTO userDTO) {
        try {
            User user = new User();
            user.setName(userDTO.getName());
            user.setRole(userDTO.getRole());
            user.setResumePath(userDTO.getResumePath());
            
            userService.updateUser(id, user);
            return ResponseEntity.ok(new ApiResponse<>(true, "User updated successfully", "User ID: " + id));
        } catch (Exception e) {
            return ResponseEntity.badRequest().body(new ApiResponse<>(false, "Error updating user"));
        }
    }

    @PostMapping("/{id}/change-password")
    public ResponseEntity<ApiResponse<String>> changePassword(@PathVariable Long id, @RequestParam String newPassword) {
        if (newPassword == null || newPassword.length() < 6) {
            return ResponseEntity.badRequest().body(new ApiResponse<>(false, "Password must be at least 6 characters"));
        }
        
        try {
            userService.updatePassword(id, newPassword);
            return ResponseEntity.ok(new ApiResponse<>(true, "Password changed successfully", "User ID: " + id));
        } catch (Exception e) {
            return ResponseEntity.badRequest().body(new ApiResponse<>(false, "Error changing password"));
        }
    }

    @PostMapping("/{id}/update-resume")
    public ResponseEntity<ApiResponse<String>> updateResume(@PathVariable Long id, @RequestParam String resumePath) {
        try {
            userService.updateResume(id, resumePath);
            return ResponseEntity.ok(new ApiResponse<>(true, "Resume updated successfully", "User ID: " + id));
        } catch (Exception e) {
            return ResponseEntity.badRequest().body(new ApiResponse<>(false, "Error updating resume"));
        }
    }

    @GetMapping("/check-email/{email}")
    public ResponseEntity<ApiResponse<Boolean>> checkEmailExists(@PathVariable String email) {
        boolean exists = userService.emailExists(email);
        return ResponseEntity.ok(new ApiResponse<>(true, "Email check completed", exists));
    }

    @GetMapping("/stats/total")
    public ResponseEntity<ApiResponse<Long>> getTotalUsersCount() {
        long count = userService.getTotalUsersCount();
        return ResponseEntity.ok(new ApiResponse<>(true, "Total users count", count));
    }

    @GetMapping("/stats/by-role/{role}")
    public ResponseEntity<ApiResponse<Long>> getUserCountByRole(@PathVariable String role) {
        long count = userService.getUserCountByRole(role);
        return ResponseEntity.ok(new ApiResponse<>(true, "User count by role: " + role, count));
    }
}
