package com.jobportal.controller;

import java.util.List;
import java.util.stream.Collectors;

import org.springframework.security.core.Authentication;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

import com.jobportal.model.Job;
import com.jobportal.model.JobApplication;
import com.jobportal.model.User;
import com.jobportal.repo.JobApplicationRepository;
import com.jobportal.repo.JobRepository;
import com.jobportal.repo.UserRepository;
import com.jobportal.service.JobService;
import com.jobportal.service.UserService;
import com.jobportal.service.JobApplicationService;

@Controller
public class DashboardController {

    private final UserRepository userRepository;
    private final JobRepository jobRepository;
    private final JobApplicationRepository applicationRepository;
    private final UserService userService;
    private final JobService jobService;
    private final JobApplicationService applicationService;

    public DashboardController(UserRepository userRepository,
                               JobRepository jobRepository,
                               JobApplicationRepository applicationRepository,
                               UserService userService,
                               JobService jobService,
                               JobApplicationService applicationService) {
        this.userRepository = userRepository;
        this.jobRepository = jobRepository;
        this.applicationRepository = applicationRepository;
        this.userService = userService;
        this.jobService = jobService;
        this.applicationService = applicationService;
    }

    @GetMapping("/dashboard")
    public String dashboard(Authentication authentication, Model model) {
        User user = userRepository.findByEmail(authentication.getName()).orElseThrow();
        model.addAttribute("user", user);

        if ("ADMIN".equals(user.getRole())) {
            long totalUsers = userService.getTotalUsersCount();
            long totalJobs = jobService.getTotalJobsCount();
            long totalApplications = applicationService.getTotalApplicationsCount();
            long jobSeekers = userService.getUserCountByRole("JOB_SEEKER");
            long employers = userService.getUserCountByRole("EMPLOYER");
            
            model.addAttribute("totalUsers", totalUsers);
            model.addAttribute("totalJobs", totalJobs);
            model.addAttribute("totalApplications", totalApplications);
            model.addAttribute("jobSeekers", jobSeekers);
            model.addAttribute("employers", employers);
            model.addAttribute("submittedApplications", applicationService.getApplicationsCountByStatus("Submitted"));
            model.addAttribute("approvedApplications", applicationService.getApplicationsCountByStatus("Approved"));
            model.addAttribute("rejectedApplications", applicationService.getApplicationsCountByStatus("Rejected"));
            model.addAttribute("users", userRepository.findAll());
            model.addAttribute("jobs", jobRepository.findAll());
            return "admin-dashboard";
        }

        if ("EMPLOYER".equals(user.getRole())) {
            List<Job> jobs = jobRepository.findByPostedBy(user.getEmail());
            List<JobApplication> applications = jobs.stream()
                    .flatMap(job -> applicationRepository.findByJob(job).stream())
                    .collect(Collectors.toList());
            
            long totalPostedJobs = jobs.size();
            long totalApplicationsReceived = applications.size();
            long approvedApplications = applications.stream().filter(a -> "Approved".equals(a.getStatus())).count();
            long rejectedApplications = applications.stream().filter(a -> "Rejected".equals(a.getStatus())).count();
            
            model.addAttribute("jobs", jobs);
            model.addAttribute("applications", applications);
            model.addAttribute("totalPostedJobs", totalPostedJobs);
            model.addAttribute("totalApplicationsReceived", totalApplicationsReceived);
            model.addAttribute("approvedApplications", approvedApplications);
            model.addAttribute("rejectedApplications", rejectedApplications);
            return "employer-dashboard";
        }

        List<Job> jobs = jobRepository.findAll();
        List<JobApplication> applications = applicationRepository.findByApplicant(user);
        
        long totalJobsApplied = applications.size();
        long approvedApplications = applications.stream().filter(a -> "Approved".equals(a.getStatus())).count();
        long rejectedApplications = applications.stream().filter(a -> "Rejected".equals(a.getStatus())).count();
        long pendingApplications = applications.stream().filter(a -> "Submitted".equals(a.getStatus())).count();
        
        model.addAttribute("jobs", jobs);
        model.addAttribute("applications", applications);
        model.addAttribute("totalJobsApplied", totalJobsApplied);
        model.addAttribute("approvedApplications", approvedApplications);
        model.addAttribute("rejectedApplications", rejectedApplications);
        model.addAttribute("pendingApplications", pendingApplications);
        return "student-dashboard";
    }
}
