package com.jobportal.controller;

import com.jobportal.dto.ApiResponse;
import com.jobportal.dto.JobApplicationDTO;
import com.jobportal.service.JobApplicationService;
import com.jobportal.model.JobApplication;
import com.jobportal.repo.JobApplicationRepository;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
@RequestMapping("/api/applications")
@CrossOrigin(origins = "*", maxAge = 3600)
public class JobApplicationApiController {

    private final JobApplicationService applicationService;
    private final JobApplicationRepository applicationRepository;

    public JobApplicationApiController(JobApplicationService applicationService, JobApplicationRepository applicationRepository) {
        this.applicationService = applicationService;
        this.applicationRepository = applicationRepository;
    }

    @GetMapping("/applicant/{userId}")
    public ResponseEntity<ApiResponse<List<JobApplicationDTO>>> getApplicationsByApplicant(@PathVariable Long userId) {
        List<JobApplicationDTO> applications = applicationService.getApplicationsByApplicant(userId);
        return ResponseEntity.ok(new ApiResponse<>(true, "Applications retrieved", applications));
    }

    @GetMapping("/job/{jobId}")
    public ResponseEntity<ApiResponse<List<JobApplicationDTO>>> getApplicationsByJob(@PathVariable Long jobId) {
        List<JobApplicationDTO> applications = applicationService.getApplicationsByJob(jobId);
        return ResponseEntity.ok(new ApiResponse<>(true, "Job applications retrieved", applications));
    }

    @GetMapping("/employer/{email}")
    public ResponseEntity<ApiResponse<List<JobApplicationDTO>>> getApplicationsByEmployer(@PathVariable String email) {
        List<JobApplicationDTO> applications = applicationService.getApplicationsByEmployer(email);
        return ResponseEntity.ok(new ApiResponse<>(true, "Employer applications retrieved", applications));
    }

    @GetMapping("/{id}")
    public ResponseEntity<ApiResponse<JobApplicationDTO>> getApplicationById(@PathVariable Long id) {
        JobApplicationDTO application = applicationService.getApplicationById(id);
        if (application != null) {
            return ResponseEntity.ok(new ApiResponse<>(true, "Application retrieved", application));
        }
        return ResponseEntity.notFound().build();
    }

    @PutMapping("/{id}/status")
    public ResponseEntity<ApiResponse<String>> updateApplicationStatus(@PathVariable Long id, @RequestParam String status) {
        try {
            applicationService.updateApplicationStatus(id, status);
            return ResponseEntity.ok(new ApiResponse<>(true, "Application status updated", "Application ID: " + id));
        } catch (Exception e) {
            return ResponseEntity.badRequest().body(new ApiResponse<>(false, "Error updating status"));
        }
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<ApiResponse<String>> deleteApplication(@PathVariable Long id) {
        try {
            applicationService.deleteApplication(id);
            return ResponseEntity.ok(new ApiResponse<>(true, "Application deleted", "Application ID: " + id));
        } catch (Exception e) {
            return ResponseEntity.badRequest().body(new ApiResponse<>(false, "Error deleting application"));
        }
    }

    @GetMapping("/stats/total")
    public ResponseEntity<ApiResponse<Long>> getTotalApplicationsCount() {
        long count = applicationService.getTotalApplicationsCount();
        return ResponseEntity.ok(new ApiResponse<>(true, "Total applications count", count));
    }

    @GetMapping("/stats/by-status/{status}")
    public ResponseEntity<ApiResponse<Long>> getApplicationsCountByStatus(@PathVariable String status) {
        long count = applicationService.getApplicationsCountByStatus(status);
        return ResponseEntity.ok(new ApiResponse<>(true, "Applications by status: " + status, count));
    }

    @GetMapping("/stats/by-user/{userId}")
    public ResponseEntity<ApiResponse<Long>> getApplicationsCountByUser(@PathVariable Long userId) {
        long count = applicationService.getApplicationsCountByUser(userId);
        return ResponseEntity.ok(new ApiResponse<>(true, "User applications count", count));
    }
}
