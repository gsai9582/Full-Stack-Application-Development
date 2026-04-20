package com.jobportal.dto;

public class JobDTO {
    private Long id;
    private String title;
    private String location;
    private String skills;
    private String salary;
    private String description;
    private String postedBy;

    public JobDTO(Long id, String title, String location, String skills, String salary, String description, String postedBy) {
        this.id = id;
        this.title = title;
        this.location = location;
        this.skills = skills;
        this.salary = salary;
        this.description = description;
        this.postedBy = postedBy;
    }

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public String getTitle() { return title; }
    public void setTitle(String title) { this.title = title; }

    public String getLocation() { return location; }
    public void setLocation(String location) { this.location = location; }

    public String getSkills() { return skills; }
    public void setSkills(String skills) { this.skills = skills; }

    public String getSalary() { return salary; }
    public void setSalary(String salary) { this.salary = salary; }

    public String getDescription() { return description; }
    public void setDescription(String description) { this.description = description; }

    public String getPostedBy() { return postedBy; }
    public void setPostedBy(String postedBy) { this.postedBy = postedBy; }
}
