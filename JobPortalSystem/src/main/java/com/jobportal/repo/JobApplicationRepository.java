package com.jobportal.repo;

import java.util.List;

import org.springframework.data.jpa.repository.JpaRepository;

import com.jobportal.model.Job;
import com.jobportal.model.JobApplication;
import com.jobportal.model.User;

public interface JobApplicationRepository extends JpaRepository<JobApplication, Long> {
    List<JobApplication> findByJob(Job job);
    List<JobApplication> findByApplicant(User applicant);
}
