
package com.jobportal.controller;

import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

import com.jobportal.repo.JobRepository;

@Controller
public class HomeController {
    private final JobRepository repo;

    public HomeController(JobRepository repo) {
        this.repo = repo;
    }

    @GetMapping("/")
    public String home(Model m) {
        m.addAttribute("jobs", repo.findAll().stream().limit(6).toList());
        return "index";
    }
}

