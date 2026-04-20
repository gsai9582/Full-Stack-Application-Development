package com.jobportal.controller;

import com.jobportal.dto.ApiResponse;
import com.jobportal.service.JobService;
import com.jobportal.service.UserService;
import com.jobportal.service.JobApplicationService;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.HashMap;
import java.util.Map;

@RestController
@RequestMapping("/api/statistics")
@CrossOrigin(origins = "*", maxAge = 3600)
public class StatisticsApiController {

    private final JobService jobService;
    private final UserService userService;
    private final JobApplicationService applicationService;

    public StatisticsApiController(JobService jobService, UserService userService, JobApplicationService applicationService) {
        this.jobService = jobService;
        this.userService = userService;
        this.applicationService = applicationService;
    }

    @GetMapping("/dashboard")
    public ResponseEntity<ApiResponse<Map<String, Object>>> getDashboardStatistics() {
        Map<String, Object> stats = new HashMap<>();
        
        stats.put("totalJobs", jobService.getTotalJobsCount());
        stats.put("totalUsers", userService.getTotalUsersCount());
        stats.put("totalApplications", applicationService.getTotalApplicationsCount());
        stats.put("jobSeekers", userService.getUserCountByRole("JOB_SEEKER"));
        stats.put("employers", userService.getUserCountByRole("EMPLOYER"));
        stats.put("admins", userService.getUserCountByRole("ADMIN"));
        stats.put("submittedApplications", applicationService.getApplicationsCountByStatus("Submitted"));
        stats.put("approvedApplications", applicationService.getApplicationsCountByStatus("Approved"));
        stats.put("rejectedApplications", applicationService.getApplicationsCountByStatus("Rejected"));
        
        return ResponseEntity.ok(new ApiResponse<>(true, "Dashboard statistics", stats));
    }

    @GetMapping("/jobs")
    public ResponseEntity<ApiResponse<Map<String, Object>>> getJobStatistics() {
        Map<String, Object> stats = new HashMap<>();
        stats.put("totalJobs", jobService.getTotalJobsCount());
        stats.put("timestamp", System.currentTimeMillis());
        
        return ResponseEntity.ok(new ApiResponse<>(true, "Job statistics", stats));
    }

    @GetMapping("/users")
    public ResponseEntity<ApiResponse<Map<String, Object>>> getUserStatistics() {
        Map<String, Object> stats = new HashMap<>();
        stats.put("totalUsers", userService.getTotalUsersCount());
        stats.put("jobSeekers", userService.getUserCountByRole("JOB_SEEKER"));
        stats.put("employers", userService.getUserCountByRole("EMPLOYER"));
        stats.put("admins", userService.getUserCountByRole("ADMIN"));
        stats.put("timestamp", System.currentTimeMillis());
        
        return ResponseEntity.ok(new ApiResponse<>(true, "User statistics", stats));
    }

    @GetMapping("/applications")
    public ResponseEntity<ApiResponse<Map<String, Object>>> getApplicationStatistics() {
        Map<String, Object> stats = new HashMap<>();
        stats.put("totalApplications", applicationService.getTotalApplicationsCount());
        stats.put("submitted", applicationService.getApplicationsCountByStatus("Submitted"));
        stats.put("approved", applicationService.getApplicationsCountByStatus("Approved"));
        stats.put("rejected", applicationService.getApplicationsCountByStatus("Rejected"));
        stats.put("timestamp", System.currentTimeMillis());
        
        return ResponseEntity.ok(new ApiResponse<>(true, "Application statistics", stats));
    }
}
