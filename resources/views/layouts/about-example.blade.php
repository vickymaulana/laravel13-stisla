@extends('layouts.app')

@section('title', 'About Us')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>About Us</h1>
        </div>

        <div class="section-body">
            <h2 class="section-title">Who We Are</h2>
            <p class="section-lead">
                Learn more about our mission, vision, and the team behind our success.
            </p>

            <div class="row">
                <!-- Mission Section -->
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Our Mission</h4>
                        </div>
                        <div class="card-body">
                            <p>
                                Our mission is to empower individuals and organizations with cutting-edge technology and innovative solutions.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Vision Section -->
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Our Vision</h4>
                        </div>
                        <div class="card-body">
                            <p>
                                To become a global leader in providing exceptional services and transformative experiences.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="section-title mt-5">Our Team</h2>
            <p class="section-lead">
                Meet the passionate individuals who make it all possible.
            </p>

            <div class="row">
                <!-- Team Member 1 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Vicky</h4>
                            <span class="badge bg-primary">CEO</span>
                        </div>
                        <div class="card-body">
                            <p>
                                John is the visionary leader who drives the company towards success with his strategic expertise.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Team Member 2 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Jane Smith</h4>
                            <span class="badge bg-success">CTO</span>
                        </div>
                        <div class="card-body">
                            <p>
                                Jane is the technical genius behind our innovative solutions and cutting-edge technology.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Team Member 3 -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Mike Brown</h4>
                            <span class="badge bg-warning">COO</span>
                        </div>
                        <div class="card-body">
                            <p>
                                Mike ensures seamless operations and exceptional service delivery to our customers.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="section-title mt-5">Our History</h2>
            <p class="section-lead">
                A glimpse into our journey and achievements over the years.
            </p>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-point">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <div class="timeline-content">
                        <h4>Founded in 2010</h4>
                        <p>
                            Our company started with a small team of passionate individuals determined to make a difference.
                        </p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-point">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="timeline-content">
                        <h4>Milestone Achievement in 2015</h4>
                        <p>
                            We reached 1 million users and received industry recognition for innovation.
                        </p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-point">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="timeline-content">
                        <h4>Global Expansion in 2020</h4>
                        <p>
                            Expanded our services globally and established offices in 10 countries.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
    <!-- JS Libraries -->

    <!-- Page Specific JS File -->
@endpush
