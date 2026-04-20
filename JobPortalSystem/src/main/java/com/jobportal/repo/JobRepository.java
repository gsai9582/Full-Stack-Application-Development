package com.jobportal.repo;

import java.util.List;

import org.springframework.data.jpa.repository.JpaRepository;

import com.jobportal.model.Job;

public interface JobRepository extends JpaRepository<Job, Long> {
    List<Job> findByTitleContainingIgnoreCaseOrLocationContainingIgnoreCaseOrSkillsContainingIgnoreCase(String title, String location, String skills);
    List<Job> findByPostedBy(String postedBy);
}

