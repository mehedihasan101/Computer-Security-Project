# ⚖️ JustiFi  
### Secure Legal & Dispute Resolution Platform

JustiFi is a **secure, role-based web application** built with **PHP and MySQL**, designed to manage **legal consultation, dispute resolution, and arbitration workflows**.  
The system focuses heavily on **computer security best practices**, following **OWASP guidelines** and implementing **real-world defensive mechanisms**.

This project was developed as an **academic Computer Security course project**, emphasizing **secure authentication, access control, attack prevention, and auditing**.

---

## 📌 Project Objectives

- Build a legally-oriented web platform with **strong security foundations**
- Demonstrate **practical implementation of OWASP Top 10 defenses**
- Apply **role-based access control (RBAC)** in a real system
- Protect sensitive legal data through **layered security**
- Provide auditability and monitoring for security events

---

## 👥 User Roles & Capabilities

| Role | Description |
|-----|------------|
| **User** | Register, login, search legal services, book consultations, message lawyers |
| **Admin** | User & role management, system configuration, security monitoring |
| **Lawyer** | Legal oversight, dispute review, document verification |
| **Mediator** | Facilitate negotiation and dispute resolution |
| **Arbitrator** | Make binding decisions for unresolved disputes |

---

## 🛠️ Tech Stack

### Frontend
- HTML5  
- CSS3  
- JavaScript  

### Backend
- PHP (Core PHP)

### Database
- MySQL  
- Prepared Statements (SQL Injection prevention)

### Security & Utilities
- bcrypt (`password_hash()`)  
- PHPMailer (email & password reset)  
- Session-based authentication  
- Custom CAPTCHA system  
- Security logging & audit trails  

---

## 🔐 Security Architecture

JustiFi follows a **Defense-in-Depth** security model.

### Implemented Security Measures

- **Password Hashing**
  - bcrypt with automatic salting
- **SQL Injection Prevention**
  - Prepared statements & parameterized queries
- **Brute-Force Attack Protection**
  - Login attempt tracking & time-based lockout
- **Strong Password Policy**
  - Minimum length and complexity enforcement
- **Secure Password Reset**
  - Token-based, time-limited reset via email
- **Role-Based Access Control (RBAC)**
  - Strict authorization per user role
- **CSRF Protection**
  - Session-based CSRF tokens on all sensitive forms
- **XSS Protection**
  - Input sanitization & output encoding
- **Secure Session Management**
  - HttpOnly, Secure, SameSite cookies
  - Session ID regeneration
- **Security Headers**
  - X-Frame-Options  
  - X-XSS-Protection  
  - X-Content-Type-Options  
- **CAPTCHA**
  - Prevents bot & automated attacks
- **File Upload Security**
  - File size & MIME type validation
- **input validation system.**
  - verify the input
- **Audit & Security Logging**
  - Authentication events & suspicious activity tracking
- **Dangerous PHP Functions Disabled**
  - `exec`, `system`, `shell_exec`, etc.
- **input validation system.**
  - verify the input
- **JWT token-based authentication.**
  - prevent unauthorized  access
- **API protection using middleware.**
  - protect API 
---
##  Clone the Repository
  git clone https://github.com/mehedihasan101/Computer-Security-Project.git


## 🚀 How to Run the Project (Local Setup)
  http://localhost/ComputerSecurityProject/login.php

---



