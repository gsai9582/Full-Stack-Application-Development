package com.jobportal.service;

import com.jobportal.dto.JobDTO;
import com.jobportal.model.Job;
import com.jobportal.repo.JobRepository;
import org.springframework.stereotype.Service;
import java.util.List;
import java.util.Optional;
import java.util.stream.Collectors;

@Service
public class JobService {

    private final JobRepository jobRepository;

    public JobService(JobRepository jobRepository) {
        this.jobRepository = jobRepository;
    }

    public List<JobDTO> getAllJobs() {
        return jobRepository.findAll().stream()
                .map(this::convertToDTO)
                .collect(Collectors.toList());
    }

    public JobDTO getJobById(Long id) {
        return jobRepository.findById(id)
                .map(this::convertToDTO)
                .orElse(null);
    }

    public List<JobDTO> searchJobs(String query) {
        if (query == null || query.isBlank()) {
            return getAllJobs();
        }
        return jobRepository.findByTitleContainingIgnoreCaseOrLocationContainingIgnoreCaseOrSkillsContainingIgnoreCase(query, query, query)
                .stream()
                .map(this::convertToDTO)
                .collect(Collectors.toList());
    }

    public List<JobDTO> getJobsByEmployer(String email) {
        return jobRepository.findByPostedBy(email).stream()
                .map(this::convertToDTO)
                .collect(Collectors.toList());
    }

    public Job createJob(Job job) {
        return jobRepository.save(job);
    }

    public Job updateJob(Long id, Job jobDetails) {
        Optional<Job> job = jobRepository.findById(id);
        if (job.isPresent()) {
            Job existingJob = job.get();
            existingJob.setTitle(jobDetails.getTitle());
            existingJob.setLocation(jobDetails.getLocation());
            existingJob.setSkills(jobDetails.getSkills());
            existingJob.setSalary(jobDetails.getSalary());
            existingJob.setDescription(jobDetails.getDescription());
            return jobRepository.save(existingJob);
        }
        return null;
    }

    public void deleteJob(Long id) {
        jobRepository.deleteById(id);
    }

    public long getTotalJobsCount() {
        return jobRepository.count();
    }

    private JobDTO convertToDTO(Job job) {
        return new JobDTO(job.getId(), job.getTitle(), job.getLocation(), 
                job.getSkills(), job.getSalary(), job.getDescription(), job.getPostedBy());
    }
}
