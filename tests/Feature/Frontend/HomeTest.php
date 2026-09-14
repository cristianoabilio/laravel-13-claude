<?php

use App\Models\Clinic;
use App\Models\DoctorService;
use App\Models\Faq;
use App\Models\HomeBanner;
use App\Models\HomeBookUsFaq;
use App\Models\HomeBookUsSection;
use App\Models\HomeReason;
use App\Models\HomeReasonSection;
use App\Models\HomeService;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\Testimonial;
use App\Models\User;

test('the homepage loads successfully with no doctors', function () {
    $this->get(route('home'))->assertOk();
});

test('the homepage banner shows default heading text when none has been saved', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Discover Health: Find Your Trusted', false);
    $response->assertSee('Doctors', false);
    $response->assertSee('Today', false);
});

test('the homepage banner shows the real saved heading and image', function () {
    HomeBanner::factory()->create([
        'heading_prefix' => 'Your Health, Our Priority: Meet',
        'heading_highlight' => 'Specialists',
        'heading_suffix' => 'Now',
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Your Health, Our Priority: Meet', false);
    $response->assertSee('Specialists', false);
    $response->assertSee('width="464" height="606"', false);
});

test('the homepage shows real doctor data in the featured doctors section', function () {
    $doctor = User::factory()->doctor()->create([
        'display_name' => 'Dr Edalin Hendry',
        'designation' => 'Cardiologist',
        'availability_status' => 'available',
    ]);
    Clinic::factory()->create(['doctor_id' => $doctor->id, 'location' => 'Minneapolis, MN']);
    DoctorService::factory()->create(['doctor_id' => $doctor->id, 'price' => 150]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Dr Edalin Hendry');
    $response->assertSee('Cardiologist');
    $response->assertSee('Minneapolis, MN');
    $response->assertSee('150.00');
    $response->assertSee('Available');
});

test('a doctor without a clinic or priced service still renders gracefully', function () {
    User::factory()->doctor()->create([
        'display_name' => 'Dr No Extras',
        'availability_status' => 'not_available',
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Dr No Extras');
    $response->assertSee('Contact for pricing');
    $response->assertSee('Not Available');
});

test('patients are not shown in the featured doctors section', function () {
    User::factory()->patient()->create(['first_name' => 'Should', 'last_name' => 'NotAppear']);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('ShouldNotAppear');
});

test('the speciality carousel shows real specialities with a real doctor count', function () {
    $speciality = Speciality::factory()->create(['name' => 'Cardiology']);
    $service = Service::factory()->create(['speciality_id' => $speciality->id]);
    $doctor = User::factory()->doctor()->create();
    DoctorService::factory()->create(['doctor_id' => $doctor->id, 'service_id' => $service->id]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Cardiology');
    $response->assertSee('1 Doctor', false);
    $response->assertSee(route('doctor.all.speciality', $speciality), false);
});

test('the homepage shows the seeded default home services', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Multi Speciality Treatments & Doctors');
    $response->assertSee('Home Care Services');
});

test('the homepage shows a real admin managed service with its real link', function () {
    HomeService::query()->delete();
    HomeService::factory()->create(['title' => 'Emergency Care', 'url' => 'https://example.com/emergency', 'sort_order' => 0]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Emergency Care');
    $response->assertSee('https://example.com/emergency', false);
});

test('the services section does not render when there are no services', function () {
    HomeService::query()->delete();

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('services-section', false);
});

test('the homepage shows the seeded default reasons', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Compelling Reasons to Choose');
    $response->assertSee('Follow-Up Care');
    $response->assertSee('Patient-Centered Approach');
    $response->assertSee('Convenient Access');
});

test('the homepage shows a real admin managed reason section and item', function () {
    HomeReasonSection::query()->delete();
    HomeReasonSection::factory()->create([
        'badge_text' => 'Trusted Care',
        'heading' => 'Reasons People Choose Us',
    ]);
    HomeReason::query()->delete();
    HomeReason::factory()->create([
        'icon' => 'isax isax-heart',
        'icon_color' => 'text-danger',
        'title' => 'Emergency Support',
        'description' => 'Round the clock emergency support for every patient.',
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Trusted Care');
    $response->assertSee('Reasons People Choose Us');
    $response->assertSee('Emergency Support');
    $response->assertSee('isax isax-heart text-danger', false);
});

test('the reason items do not render when there are none but the heading still shows', function () {
    HomeReason::query()->delete();

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Compelling Reasons to Choose');
    $response->assertDontSee('reason-item', false);
});

test('the homepage shows the seeded default book us section and faqs', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('We are committed to understanding your');
    $response->assertSee('unique needs and delivering care.');
    $response->assertSee('Our Vision');
    $response->assertSee('Our Mission');
});

test('the homepage shows a real admin managed book us section and faq', function () {
    HomeBookUsSection::query()->delete();
    HomeBookUsSection::factory()->create([
        'badge_text' => 'Book Today',
        'heading_prefix' => 'Real prefix text',
        'heading_highlight' => 'real highlight',
        'description' => 'A real description for the book us section.',
    ]);
    HomeBookUsFaq::query()->delete();
    HomeBookUsFaq::factory()->create([
        'title' => 'Our Values',
        'description' => 'We value transparency in every interaction.',
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Book Today');
    $response->assertSee('Real prefix text');
    $response->assertSee('real highlight');
    $response->assertSee('A real description for the book us section.');
    $response->assertSee('Our Values');
});

test('the faq accordion does not render when there are no faqs but the section still shows', function () {
    HomeBookUsFaq::query()->delete();

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('We are committed to understanding your');
    $response->assertDontSee('Our Vision');
    $response->assertDontSee('Our Mission');
});

test('the homepage shows the seeded default testimonials', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Deny Hendrawan');
    $response->assertSee('Johnson DWayne');
    $response->assertSee('Rayan Smith');
    $response->assertSee('Sofia Doe');
});

test('the homepage shows a real admin managed testimonial', function () {
    Testimonial::query()->delete();
    Testimonial::factory()->create([
        'title' => 'Amazing Care',
        'quote' => 'A truly amazing experience from start to finish.',
        'patient_name' => 'Real Patient',
        'patient_country' => 'Germany',
        'rating' => 3,
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Amazing Care');
    $response->assertSee('A truly amazing experience from start to finish.');
    $response->assertSee('Real Patient');
    $response->assertSee('Germany');
});

test('the testimonials slider does not render when there are no testimonials', function () {
    Testimonial::query()->delete();

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('15k Users Trust Doccure Worldwide');
    $response->assertDontSee('testimonials-slider', false);
});

test('the homepage shows the seeded default faqs with the first one expanded', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('How do I book an appointment with a doctor?');
    $response->assertSee('Can I book appointments for family members or dependents?');

    $firstFaq = Faq::orderBy('sort_order')->orderBy('id')->first();
    $response->assertSee('id="site-faq-collapse-'.$firstFaq->id.'" class="accordion-collapse collapse  show', false);
});

test('the homepage shows a real admin managed faq', function () {
    Faq::query()->delete();
    Faq::factory()->create([
        'question' => 'Do you offer telehealth visits?',
        'answer' => 'Yes, many of our doctors offer telehealth consultations.',
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Do you offer telehealth visits?');
    $response->assertSee('Yes, many of our doctors offer telehealth consultations.');
});

test('the faq accordion on the faq page does not render when there are no faqs', function () {
    Faq::query()->delete();

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Your Questions are Answered');
    $response->assertDontSee('site-faq-heading-', false);
});
