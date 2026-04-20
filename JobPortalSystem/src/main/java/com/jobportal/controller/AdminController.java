package com.jobportal.controller;

import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

import com.jobportal.repo.JobRepository;
import com.jobportal.repo.UserRepository;

@Controller
public class AdminController {

    private final UserRepository userRepository;
    private final JobRepository jobRepository;

    public AdminController(UserRepository userRepository, JobRepository jobRepository) {
        this.userRepository = userRepository;
        this.jobRepository = jobRepository;
    }

    @GetMapping("/admin/manage")
    public String manageView(Model model) {
        model.addAttribute("users", userRepository.findAll());
        model.addAttribute("jobs", jobRepository.findAll());
        return "admin-dashboard";
    }
}
