package com.jobportal.controller;

import com.jobportal.dto.ApiResponse;
import com.jobportal.dto.JobDTO;
import com.jobportal.service.JobService;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
@RequestMapping("/api/jobs")
@CrossOrigin(origins = "*", maxAge = 3600)
public class JobApiController {

    private final JobService jobService;

    public JobApiController(JobService jobService) {
        this.jobService = jobService;
    }

    @GetMapping
    public ResponseEntity<ApiResponse<List<JobDTO>>> getAllJobs() {
        List<JobDTO> jobs = jobService.getAllJobs();
        return ResponseEntity.ok(new ApiResponse<>(true, "Jobs retrieved successfully", jobs));
    }

    @GetMapping("/{id}")
    public ResponseEntity<ApiResponse<JobDTO>> getJobById(@PathVariable Long id) {
        JobDTO job = jobService.getJobById(id);
        if (job != null) {
            return ResponseEntity.ok(new ApiResponse<>(true, "Job retrieved successfully", job));
        }
        return ResponseEntity.notFound().build();
    }

    @GetMapping("/search")
    public ResponseEntity<ApiResponse<List<JobDTO>>> searchJobs(@RequestParam String query) {
        List<JobDTO> jobs = jobService.searchJobs(query);
        return ResponseEntity.ok(new ApiResponse<>(true, "Search completed", jobs));
    }

    @GetMapping("/employer/{email}")
    public ResponseEntity<ApiResponse<List<JobDTO>>> getJobsByEmployer(@PathVariable String email) {
        List<JobDTO> jobs = jobService.getJobsByEmployer(email);
        return ResponseEntity.ok(new ApiResponse<>(true, "Employer jobs retrieved", jobs));
    }

    @PostMapping
    public ResponseEntity<ApiResponse<String>> createJob(@RequestBody JobDTO jobDTO) {
        try {
            com.jobportal.model.Job job = new com.jobportal.model.Job();
            job.setTitle(jobDTO.getTitle());
            job.setLocation(jobDTO.getLocation());
            job.setSkills(jobDTO.getSkills());
            job.setSalary(jobDTO.getSalary());
            job.setDescription(jobDTO.getDescription());
            job.setPostedBy(jobDTO.getPostedBy());
            
            jobService.createJob(job);
            return ResponseEntity.ok(new ApiResponse<>(true, "Job created successfully", "Job ID: " + job.getId()));
        } catch (Exception e) {
            return ResponseEntity.badRequest().body(new ApiResponse<>(false, "Error creating job"));
        }
    }

    @PutMapping("/{id}")
    public ResponseEntity<ApiResponse<String>> updateJob(@PathVariable Long id, @RequestBody JobDTO jobDTO) {
        try {
            com.jobportal.model.Job job = new com.jobportal.model.Job();
            job.setTitle(jobDTO.getTitle());
            job.setLocation(jobDTO.getLocation());
            job.setSkills(jobDTO.getSkills());
            job.setSalary(jobDTO.getSalary());
            job.setDescription(jobDTO.getDescription());
            
            jobService.updateJob(id, job);
            return ResponseEntity.ok(new ApiResponse<>(true, "Job updated successfully", "Job ID: " + id));
        } catch (Exception e) {
            return ResponseEntity.badRequest().body(new ApiResponse<>(false, "Error updating job"));
        }
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<ApiResponse<String>> deleteJob(@PathVariable Long id) {
        try {
            jobService.deleteJob(id);
            return ResponseEntity.ok(new ApiResponse<>(true, "Job deleted successfully", "Job ID: " + id));
        } catch (Exception e) {
            return ResponseEntity.badRequest().body(new ApiResponse<>(false, "Error deleting job"));
        }
    }

    @GetMapping("/stats/total")
    public ResponseEntity<ApiResponse<Long>> getTotalJobsCount() {
        long count = jobService.getTotalJobsCount();
        return ResponseEntity.ok(new ApiResponse<>(true, "Total jobs count", count));
    }
}
