package com.jobportal.service;

import com.jobportal.dto.JobApplicationDTO;
import com.jobportal.dto.JobDTO;
import com.jobportal.dto.UserDTO;
import com.jobportal.model.Job;
import com.jobportal.model.JobApplication;
import com.jobportal.model.User;
import com.jobportal.repo.JobApplicationRepository;
import com.jobportal.repo.JobRepository;
import com.jobportal.repo.UserRepository;
import org.springframework.stereotype.Service;
import java.util.List;
import java.util.Optional;
import java.util.stream.Collectors;

@Service
public class JobApplicationService {

    private final JobApplicationRepository applicationRepository;
    private final JobRepository jobRepository;
    private final UserRepository userRepository;

    public JobApplicationService(JobApplicationRepository applicationRepository, 
                                JobRepository jobRepository, 
                                UserRepository userRepository) {
        this.applicationRepository = applicationRepository;
        this.jobRepository = jobRepository;
        this.userRepository = userRepository;
    }

    public List<JobApplicationDTO> getApplicationsByApplicant(Long userId) {
        Optional<User> user = userRepository.findById(userId);
        if (user.isPresent()) {
            return applicationRepository.findByApplicant(user.get()).stream()
                    .map(this::convertToDTO)
                    .collect(Collectors.toList());
        }
        return List.of();
    }

    public List<JobApplicationDTO> getApplicationsByJob(Long jobId) {
        Optional<Job> job = jobRepository.findById(jobId);
        if (job.isPresent()) {
            return applicationRepository.findByJob(job.get()).stream()
                    .map(this::convertToDTO)
                    .collect(Collectors.toList());
        }
        return List.of();
    }

    public List<JobApplicationDTO> getApplicationsByEmployer(String employerEmail) {
        List<Job> jobs = jobRepository.findByPostedBy(employerEmail);
        return jobs.stream()
                .flatMap(job -> applicationRepository.findByJob(job).stream())
                .map(this::convertToDTO)
                .collect(Collectors.toList());
    }

    public JobApplication createApplication(JobApplication application) {
        return applicationRepository.save(application);
    }

    public JobApplication updateApplicationStatus(Long applicationId, String status) {
        Optional<JobApplication> application = applicationRepository.findById(applicationId);
        if (application.isPresent()) {
            JobApplication existingApp = application.get();
            existingApp.setStatus(status);
            return applicationRepository.save(existingApp);
        }
        return null;
    }

    public JobApplicationDTO getApplicationById(Long id) {
        return applicationRepository.findById(id)
                .map(this::convertToDTO)
                .orElse(null);
    }

    public void deleteApplication(Long id) {
        applicationRepository.deleteById(id);
    }

    public long getTotalApplicationsCount() {
        return applicationRepository.count();
    }

    public long getApplicationsCountByStatus(String status) {
        return applicationRepository.findAll().stream()
                .filter(app -> app.getStatus().equals(status))
                .count();
    }

    public long getApplicationsCountByUser(Long userId) {
        Optional<User> user = userRepository.findById(userId);
        if (user.isPresent()) {
            return applicationRepository.findByApplicant(user.get()).size();
        }
        return 0;
    }

    private JobApplicationDTO convertToDTO(JobApplication app) {
        Job job = app.getJob();
        User user = app.getApplicant();
        
        JobDTO jobDTO = new JobDTO(job.getId(), job.getTitle(), job.getLocation(), 
                job.getSkills(), job.getSalary(), job.getDescription(), job.getPostedBy());
        
        UserDTO userDTO = new UserDTO(user.getId(), user.getName(), user.getEmail(), 
                user.getRole(), user.getResumePath());
        
        return new JobApplicationDTO(app.getId(), jobDTO, userDTO, app.getResumeFilename(), app.getStatus());
    }
}
