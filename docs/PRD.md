PRODUCT REQUIREMENTS DOCUMENT
Doccure
Doctor Appointment Booking SaaS — Laravel 13 + Claude

1. Product Summary
   Product Name
   Doccure
   One-Line Description
   A web platform where patients can find doctors, book appointments with time slots, and pay online — while doctors manage their schedule and admins oversee the entire system.
   Core Value Proposition
   Reduces the friction of finding a doctor and booking an appointment from days (phone calls, waiting rooms) down to two minutes (search → pick slot → pay).
   Target Market
   Small-to-medium independent clinics, solo practitioners, and patients in regions where in-person doctor booking is still a slow, manual process.
2. User Roles
   Doccure has three distinct user roles. Each role has a separate dashboard, separate permissions, and a separate user journey.
   2.1 Admin
   • Manages all doctors, patients, appointments, and specialities
   • Views platform revenue, user growth, and analytics dashboards
   • Manages reviews, transactions, and global site settings
   • Has full visibility into the entire platform
   2.2 Doctor
   • Manages own profile (bio, experience, education, fees, availability)
   • Accepts or rejects appointment requests
   • Views patient list and appointment history
   • Manages clinic locations and weekly availability timings
   • Views earnings and payout dashboard
   2.3 Patient
   • Searches doctors by speciality, location, and availability
   • Views doctor profile, ratings, and reviews
   • Books appointments by selecting a date and time slot
   • Pays online via Stripe
   • Views upcoming and past appointments
   • Manages own profile and basic medical information
3. MVP Features (15 Total)
   These are the only features built in version 1. Anything not listed here is explicitly out of scope. See the Phase 2 / MVP document for cut features.

#

Feature
Why It's In MVP
1
3-role authentication
Foundation — Admin, Doctor, Patient
2
Doctor registration + profile
Core supply side — speciality, fees, bio
3
Patient registration + profile
Core demand side
4
Doctor listing + search + filters
Discovery — speciality, price, availability
5
Doctor detail page
Trust + conversion — bio, reviews, timings
6
Availability system
Doctor sets weekly time slots
7
Appointment booking
Patient picks date and slot — core transaction
8
Stripe payment integration
Revenue capture on booking
9
Appointment status management
Pending / Confirmed / Completed / Cancelled
10
Email notifications
Booking confirmed, reminders, cancellations
11
Patient dashboard
Upcoming + past appointments
12
Doctor dashboard
Today's appointments, stats, weekly chart
13
Admin dashboard
Users, doctors, appointments, revenue
14
Review + rating system
Submitted after appointment completion
15
Settings + profile updates
All 3 roles can update their info 4. Tech Stack

Layer
Choice
Reason
Backend
Laravel 13
Latest stable, AI SDK built-in
Language
PHP 8.3+
Required for Laravel 13
Database
MySQL 8
Reliable, well-supported
Frontend
Blade + Bootstrap 5 (Doccure HTML template)
Use existing template — don't rebuild wheels
Authentication
Laravel Breeze (extended for 3 roles)
Fast, clean, no bloat
Authorization
Spatie Permission
Industry standard for roles
Payments
Laravel Cashier (Stripe)
Battle-tested, official
Queue
Database (dev), Redis (production)
Start simple, scale later
Email
Mailtrap (dev), Resend / SES (prod)
Reliable, affordable
Storage
Local (dev), S3 (production)
Standard
Testing
Pest
Modern, fast, expressive
Deployment
Laravel Forge + DigitalOcean / Railway
Production-grade hosting 5. Success Criteria
MVP is considered shipped when ALL of the following are true:
• A new patient can register, find a doctor, book an appointment, and pay in under 3 minutes
• A new doctor can register, set their profile and availability, and receive a booking
• Admin can see all activity on a single dashboard
• All three roles have zero cross-permission leaks (no patient can access admin pages, etc.)
• Stripe payments process successfully in test mode without errors
• Emails are sent on every key lifecycle event (booking, reminder, cancellation)
• Pest test coverage is at least 60% on critical user flows
• All security subagent reviews pass with no CRITICAL or HIGH findings 6. User Stories
Format: As a [role], I want to [action], so that [benefit].
6.1 Patient Stories
• As a patient, I want to search doctors by speciality, so that I can find the right specialist quickly.
• As a patient, I want to see doctor reviews and ratings, so that I can trust who I'm booking.
• As a patient, I want to pick a specific time slot, so that I get my preferred appointment time.
• As a patient, I want to pay online when booking, so that I don't waste time at the clinic.
• As a patient, I want to cancel my appointment, so that I'm not stuck with a wrong booking.
• As a patient, I want to leave a review after my appointment, so that I can help other patients.
6.2 Doctor Stories
• As a doctor, I want to set my weekly availability, so that patients only book when I'm actually free.
• As a doctor, I want to accept or reject bookings, so that I have control over my schedule.
• As a doctor, I want to see today's appointments at a glance, so that I'm prepared for my day.
• As a doctor, I want to see my earnings and payouts, so that I can track my business.
• As a doctor, I want to update my profile and clinic info, so that patients see accurate details.
6.3 Admin Stories
• As an admin, I want to see all users on the platform, so that I can manage them effectively.
• As an admin, I want to see total revenue and growth, so that I can track business health.
• As an admin, I want to remove fake or inappropriate reviews, so that the platform stays trustworthy.
• As an admin, I want to manage specialities and clinics, so that the platform stays organised. 7. System Architecture
High-Level Flow
Browser (Admin / Doctor / Patient) → Laravel Backend (Controllers → Services → Models) → MySQL Database
External services: Stripe (payments), Mail Service (notifications), S3 (file storage in production)
Folder Structure
• Controllers: app/Http/Controllers/{Admin, Doctor, Patient, Public}
• Services: app/Services/{Domain} — all business logic lives here
• Form Requests: app/Http/Requests/{Admin, Doctor, Patient}
• Views: resources/views/{admin, doctor, patient, public}
• Tests: tests/Feature and tests/Unit (Pest)
Request Lifecycle Example
A patient clicks 'Book Appointment'. The request flows: Browser → Route → Controller → AppointmentService → Database → Stripe Webhook → Mail Queue → Response back to browser.
Each layer has a single responsibility. Controllers handle HTTP. Services handle logic. Models handle data. 8. Security Requirements (Non-Negotiable)
• Every user input must pass through a Form Request validation class
• Every Eloquent query must use parameter binding — no raw queries
• Every route must be protected by the appropriate role middleware
• Every sensitive action must use a Laravel Policy for authorization
• Passwords, tokens, and PII must NEVER be logged
• .env files and API keys must NEVER be committed to Git
• All payment-related code must be reviewed by the security subagent before merge
• Rate limiting must be applied on login, booking, and webhook endpoints
• HTTPS must be enforced in production (no HTTP fallback) 9. Project Constraints
• Scope: MVP only. No Phase 2 features built during this course.
• Timeline: Designed to be built in approximately 8 weeks of part-time work.
• Budget: Open-source-first. Only paid services are Stripe (transaction fee), email provider, and hosting.
• Team: Solo developer using Claude as the AI pair programmer.
• Quality bar: Production-ready. Security subagent reviews mandatory. 10. Out of Scope (See Phase 2 Document)
These features are explicitly NOT built in MVP. Each is documented separately in the Phase 2 document.
• Pharmacy module
• Blog module
• In-app video calls (use external Zoom link as workaround)
• Live chat / real-time messaging
• Medical records upload (PDFs, scans)
• Dependants management (booking for family members)
• Patient wallet system
• Multi-language support
• Mobile application (native iOS / Android)
• Advanced analytics and reporting
• Insurance integration
• Prescriptions and e-pharmacy 11. Document Control
This PRD is a living document. When changes are made, version it.
• Owner: Project Lead (Ariyan / Kazi Ariyan)
• Version: 1.0
• Status: Approved for development
• Companion documents: MVP_FEATURES.md, CLAUDE.md, PHASE_2_FEATURES.md

— End of PRD —
