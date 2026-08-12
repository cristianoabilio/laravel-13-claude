MINIMUM VIABLE PRODUCT
Doccure MVP
What we build, what we cut, and why it matters

1. What is an MVP?
   MVP stands for Minimum Viable Product. Three words. Three meanings.
   • Minimum: The smallest version possible — nothing extra.
   • Viable: It actually works for real users — not a prototype, not a demo.
   • Product: Something shippable. Something people can pay for.

The Golden Rule
If a feature isn't required for a patient to book an appointment and a doctor to accept it, it's cut from the MVP. 2. The MVP Decision Test
For every feature you're tempted to add, ask one question:

Ask Yourself
Can a patient successfully book an appointment and pay for it WITHOUT this feature?

If YES — cut the feature. It belongs in Phase 2.
If NO — keep the feature. It's part of MVP. 3. The 15 MVP Features
These 15 features pass the test. Nothing more. Nothing less.

#

Feature
Role
Why It's In
1
3-role authentication
Admin + Doctor + Patient
Foundation
2
Doctor profile + speciality + fees
Doctor
Supply side
3
Patient profile
Patient
Demand side
4
Doctor search + filters
Patient
Discovery
5
Doctor detail page
Patient
Trust + conversion
6
Doctor weekly availability
Doctor
Scheduling foundation
7
Appointment booking + slot lock
Patient
Core transaction
8
Stripe payment integration
Patient
Revenue
9
Appointment status flow
Doctor + Patient
Lifecycle
10
Email notifications
All roles
Communication
11
Patient dashboard
Patient
Retention
12
Doctor dashboard
Doctor
Daily UX
13
Admin dashboard
Admin
Operations
14
Reviews + ratings
Patient → Doctor
Social proof
15
Settings + profile updates
All roles
Table stakes 4. What We Cut (and Why)
These features sound great. They look impressive in a demo. They are NOT in the MVP. Each was cut deliberately. Each one alone takes 1 to 2 weeks to build properly.

Cut Feature
Why We Cut It
Pharmacy module
Patient can book a doctor without it. Phase 2.
Blog module
Adds zero value to the booking flow. Phase 2.
In-app video calls
Use external Zoom link as workaround. Real video infra is weeks of work.
Live chat / messaging
Beautiful feature, but bookings happen via dashboard. Phase 2.
Medical records upload
Heavy compliance burden (HIPAA-style). Out of scope for MVP.
Dependants management
Booking for family members. Adds DB complexity. Phase 2.
Patient wallet system
Stripe direct works fine. Wallets add accounting complexity.
Multi-language support
English only for v1. i18n adds 2-3 weeks across every screen.
Native mobile app
Web app is mobile-responsive. Native = entirely separate project.
Advanced analytics
Basic dashboards in MVP. Advanced = Phase 2.
Insurance integration
Massive third-party integration. Out of scope.
E-prescriptions
Regulatory minefield. Out of scope.

The Math
12 cut features × 1.5 weeks each = 18 weeks saved. That's 4 months of development time. That's the difference between shipping in 2 months versus 6 months. 5. The Real Killer: Scope Creep
Scope creep is when you slowly add features that weren't in your original plan. 'Just one more thing,' you tell yourself. Then another. Then another.
Six months later, your MVP is still not shipped. This is how 90% of side projects die. Not because the code was bad — because the scope kept growing.

The Discipline Rule
If it's not on the 15-feature MVP list, we don't build it. Phase 2 features get written down in a separate notes file — but we never touch them during MVP development. 6. MVP Ships When...
These are the only criteria for declaring the MVP done. Nothing else.
• A new patient can register, find a doctor, book an appointment, and pay in under 3 minutes
• A new doctor can register, set their profile + availability, and receive a booking
• Admin can see all activity on one dashboard
• All three roles have zero cross-permission leaks
• Stripe payments work in test mode end-to-end
• Emails are sent on every key lifecycle event
• Pest test coverage is at least 60% on critical flows
• No CRITICAL or HIGH security findings from the security subagent 7. Phase 2 Roadmap (After MVP Ships)
Once the MVP is in production with real users, prioritise Phase 2 features based on actual user feedback — not assumptions.
Likely Phase 2 Priorities
• In-app messaging: Patients want to ask quick questions before booking.
• Medical records upload: Doctors want patient history before the visit.
• Video calls: Telemedicine demand keeps growing.
• Dependants: Parents want to book for kids and elderly.
• Multi-language: Expand into non-English markets.
How to Decide What to Build Next
• Talk to 10 real users. Ask what stops them from using Doccure more.
• Track support tickets. The most common complaint = the next feature.
• Check abandoned bookings. Where do users drop off?
• Look at revenue per feature. What would users pay extra for? 8. The MVP Mindset
MVP is not about building a worse version of your dream product. MVP is about discovering what your real users actually need — before you build the wrong thing.

Remember
Discipline is what separates shippers from dreamers. The course that teaches 15 features deeply beats the course that teaches 40 features shallowly. The product that ships in 2 months beats the product that's still in development at month 6.

— End of MVP Document —
