@extends('frontend.home_master')
@section('home')
    <!-- Home Banner -->
    @include('frontend.layouts.banner')
    <!-- /Home Banner -->

    <!-- List -->
    @include('frontend.layouts.list')
    <!-- /List -->

    <!-- Speciality Section -->
    @include('frontend.layouts.speciality')
    <!-- /Speciality Section -->

    <!-- Doctor Section -->
    @include('frontend.layouts.doctor')
    <!-- /Doctor Section -->

    <!-- Services Section -->
    @include('frontend.layouts.services')
    <!-- /Services Section -->

    <!-- Reasons Section -->
    @include('frontend.layouts.reasons')
    <!-- /Reasons Section -->

    <!-- Bookus Section -->
    @include('frontend.layouts.bookus')
    <!-- /Bookus Section -->

    <!-- Testimonial Section -->
    @include('frontend.layouts.testimonials')
    <!-- /Testimonial Section -->

    @include('frontend.layouts.company')

    @include('frontend.layouts.faq')

    <!-- App Section -->
    @include('frontend.layouts.app')
    <!-- /App Section -->

    <!-- Article Section -->
    @include('frontend.layouts.article')
    <!-- /Article Section -->

    <!-- Info Section -->
    @include('frontend.layouts.info')
    <!-- /Info Section -->
@endsection