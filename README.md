# Fleet-Payroll-Management-System

A professional Object-Oriented PHP project designed to simulate how a real transport and logistics company manages employees, vehicles, payroll, authentication, payments, and notifications.

This project is being built as a learning-focused enterprise application to master PHP OOP concepts, software architecture, design patterns, SOLID principles, and clean code practices.

---

## Project Overview

The Fleet & Payroll Management System is a centralized platform that helps a transport company manage its workforce and operations.

The system provides:

- User Authentication
- Role-Based Access Control
- Employee Management
- Vehicle Management
- Payroll Processing
- Payment Processing
- Notification Services
- Activity Logging

The project is intentionally designed to grow from a simple OOP application into a professionally structured backend system.

---

## Why This Project Exists

Many PHP OOP tutorials teach concepts in isolation:

- Animal classes
- Vehicle examples
- Employee examples

While useful for understanding syntax, they do not reflect how software is built in real organizations.

This project exists to bridge that gap.

By building one large cohesive application, I can learn:

- Encapsulation
- Inheritance
- Polymorphism
- Abstraction
- Interfaces
- Dependency Injection
- SOLID Principles
- Design Patterns
- Scalable Architecture
- Enterprise Development Practices

The goal is not only to learn PHP OOP but also to learn how software engineers design maintainable systems.

---

## Business Problem

A logistics company needs a system that can:

1. Authenticate users securely.
2. Manage employees and roles.
3. Assign vehicles to drivers.
4. Calculate employee salaries.
5. Process salary payments.
6. Notify employees about important events.
7. Maintain logs of all activities.

This application solves that problem.

---

## Core Features

### Authentication Module

- User registration
- Password hashing
- Password verification
- Login simulation
- Role management

Roles:

- Admin
- Manager
- Employee

---

### Employee Management Module

Manage:

- Full-Time Employees
- Part-Time Employees
- Contract Employees

Features:

- Salary calculation
- Tax calculation
- Employee records

---

### Fleet Management Module

Manage:

- Cars
- Bikes
- Trucks

Features:

- Vehicle registration
- Engine control
- Fuel usage calculation
- Vehicle assignment

---

### Payment Module

Supported providers:

- M-Pesa
- PayPal
- Stripe

Features:

- Salary payments
- Refund simulation
- Provider abstraction

---

### Notification Module

Supported channels:

- Email
- SMS
- Push Notifications

Features:

- Send notifications
- Notification management

---

### Payroll Module

Features:

- Salary generation
- Tax deductions
- Net salary calculation
- Payment processing integration

---

### Logging Module

Track:

- Logins
- Payroll generation
- Payments
- Notifications
- Vehicle assignments

---

## Learning Objectives

This project is designed to teach:

### Beginner Concepts

- Classes
- Objects
- Properties
- Methods
- Constructors
- Encapsulation

### Intermediate Concepts

- Inheritance
- Polymorphism
- Abstraction
- Interfaces
- Static Methods
- Constants

## Advanced Concepts

- SOLID Principles
- Dependency Injection
- Design Patterns
- Layered Architecture
- Service-Oriented Design

---

## Project Structure

src/

├── Authentication
│   ├── User.php
│   ├── Admin.php
│   └── Role.php
│
├── Employees
│   ├── Employee.php
│   ├── FullTimeEmployee.php
│   ├── PartTimeEmployee.php
│   └── ContractEmployee.php
│
├── Vehicles
│   ├── Vehicle.php
│   ├── Truck.php
│   ├── Car.php
│   └── Bike.php
│
├── Payments
│   ├── PaymentInterface.php
│   ├── MpesaPayment.php
│   ├── PaypalPayment.php
│   └── StripePayment.php
│
├── Notifications
│   ├── NotificationInterface.php
│   ├── EmailNotification.php
│   ├── SMSNotification.php
│   └── PushNotification.php
│
├── Payroll
│   └── PayrollService.php
│
├── Logging
│   ├── LoggerInterface.php
│   └── FileLogger.php
│
└── bootstrap.php

---

# Technologies

- PHP 8+
- Object-Oriented Programming
- Composer (later)
- Git
- GitHub
- MySQL

Future additions:

- PHPUnit
- Laravel-inspired architecture

---

## Installation

## Clone Repository

```bash
git clone git@github.com:chege-manasseh/Fleet-Payroll-Management-System.git
```

Move into project folder:

```bash
cd fleet-payroll-system
```

---

## Requirements

- PHP 8.0+
- VS Code
- Git
- Mysql

Check PHP version:

```bash
php -v
```

---

# Running The Project

For early development:

```bash
php index.php
```

Or:

```bash
php src/bootstrap.php
```

Later, when a web interface is introduced:

```bash
php -S localhost:8000
```

Open:

```
http://localhost:8000
```

in your browser.

---

# Development Roadmap

Phase 1:

- Authentication System

Phase 2:

- Employee Management

Phase 3:

- Vehicle Management

Phase 4:

- Payment System

Phase 5:

- Notification System

Phase 6:

- Payroll Integration

Phase 7:

- Logging

Phase 8:

- Database Integration

Phase 9:

- Testing

Phase 10:

- API Development

---

# Design Principles

This project follows:

- Single Responsibility Principle
- Open/Closed Principle
- Liskov Substitution Principle
- Interface Segregation Principle
- Dependency Inversion Principle

Goal:

Build maintainable and scalable software.

---

# Contributing

Contributions are welcome.

To contribute:

1. Fork the repository.
2. Create a feature branch.

```bash
git checkout -b feature/new-feature
```

3. Commit your changes.

```bash
git commit -m "Add new feature"
```

4. Push your branch.

```bash
git push origin feature/new-feature
```

5. Open a Pull Request.

---

# Future Improvements

- Database persistence
- API layer
- Authentication tokens
- Reporting dashboard
- Vehicle tracking
- Payroll exports
- Queue processing
- Unit testing
- Docker support
- CI/CD pipelines

---

# Project Status

🚧 Under Development

This project is being built incrementally as part of a structured journey toward mastering professional PHP OOP and backend architecture.

---

# Author

Built as a professional PHP OOP learning project focused on mastering real-world software engineering concepts and practices.