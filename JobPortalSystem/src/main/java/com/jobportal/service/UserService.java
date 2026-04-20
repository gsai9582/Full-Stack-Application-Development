package com.jobportal.service;

import com.jobportal.dto.UserDTO;
import com.jobportal.model.User;
import com.jobportal.repo.UserRepository;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import java.util.Optional;

@Service
public class UserService {

    private final UserRepository userRepository;
    private final PasswordEncoder passwordEncoder;

    public UserService(UserRepository userRepository, PasswordEncoder passwordEncoder) {
        this.userRepository = userRepository;
        this.passwordEncoder = passwordEncoder;
    }

    public UserDTO getUserById(Long id) {
        return userRepository.findById(id)
                .map(this::convertToDTO)
                .orElse(null);
    }

    public UserDTO getUserByEmail(String email) {
        return userRepository.findByEmail(email)
                .map(this::convertToDTO)
                .orElse(null);
    }

    public User getUserEntityByEmail(String email) {
        return userRepository.findByEmail(email).orElse(null);
    }

    public User createUser(User user) {
        user.setPassword(passwordEncoder.encode(user.getPassword()));
        return userRepository.save(user);
    }

    public User updateUser(Long id, User userDetails) {
        Optional<User> user = userRepository.findById(id);
        if (user.isPresent()) {
            User existingUser = user.get();
            existingUser.setName(userDetails.getName());
            existingUser.setRole(userDetails.getRole());
            existingUser.setResumePath(userDetails.getResumePath());
            return userRepository.save(existingUser);
        }
        return null;
    }

    public User updatePassword(Long id, String newPassword) {
        Optional<User> user = userRepository.findById(id);
        if (user.isPresent()) {
            User existingUser = user.get();
            existingUser.setPassword(passwordEncoder.encode(newPassword));
            return userRepository.save(existingUser);
        }
        return null;
    }

    public User updateResume(Long id, String resumePath) {
        Optional<User> user = userRepository.findById(id);
        if (user.isPresent()) {
            User existingUser = user.get();
            existingUser.setResumePath(resumePath);
            return userRepository.save(existingUser);
        }
        return null;
    }

    public boolean emailExists(String email) {
        return userRepository.findByEmail(email).isPresent();
    }

    public long getUserCountByRole(String role) {
        return userRepository.findAll().stream()
                .filter(u -> u.getRole().equals(role))
                .count();
    }

    public long getTotalUsersCount() {
        return userRepository.count();
    }

    private UserDTO convertToDTO(User user) {
        return new UserDTO(user.getId(), user.getName(), user.getEmail(), 
                user.getRole(), user.getResumePath());
    }
}
