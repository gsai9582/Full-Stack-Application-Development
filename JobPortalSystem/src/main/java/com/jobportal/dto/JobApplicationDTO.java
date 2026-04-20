package com.jobportal.dto;

public class JobApplicationDTO {
    private Long id;
    private JobDTO job;
    private UserDTO applicant;
    private String resumeFilename;
    private String status;

    public JobApplicationDTO(Long id, JobDTO job, UserDTO applicant, String resumeFilename, String status) {
        this.id = id;
        this.job = job;
        this.applicant = applicant;
        this.resumeFilename = resumeFilename;
        this.status = status;
    }

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public JobDTO getJob() { return job; }
    public void setJob(JobDTO job) { this.job = job; }

    public UserDTO getApplicant() { return applicant; }
    public void setApplicant(UserDTO applicant) { this.applicant = applicant; }

    public String getResumeFilename() { return resumeFilename; }
    public void setResumeFilename(String resumeFilename) { this.resumeFilename = resumeFilename; }

    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }
}
