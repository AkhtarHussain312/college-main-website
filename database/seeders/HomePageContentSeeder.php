<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\SiteContent;
use Illuminate\Database\Seeder;

class HomePageContentSeeder extends Seeder
{
    public function run(): void
    {
        $content = [
            'college_name' => ['College name', 'Dir College Of Nursing & Allied Health Science', 'college details'],
            'college_short_name' => ['Header name', 'DCN', 'college details'],
            'nav_brand_title' => ['Navigation brand title', 'DCN', 'college details'],
            'nav_brand_subtitle' => ['Navigation brand subtitle', 'Dir College Of Nursing & Allied Health Science', 'college details'],
            'admissions_banner' => ['Admissions banner', 'Admissions Open for Fall 2026 - Apply Today', 'college details'],
            'footer_description' => ['Footer description', 'Dir College Of Nursing & Allied Health Science prepares students for professional nursing, allied health sciences, and compassionate healthcare leadership.', 'college details'],
            'hero_kicker' => ['Hero kicker', 'Admissions Open for Fall 2026', 'home page'],
            'hero_title' => ['Hero title', 'Dir College Of Nursing & Allied Health Science', 'home page'],
            'hero_text' => ['Hero description', 'A modern college experience for ambitious students pursuing professional nursing and allied health science pathways.', 'home page'],
            'hero_primary_cta' => ['Primary button', 'Apply Now', 'home page'],
            'hero_secondary_cta' => ['Secondary button', 'Explore Programs', 'home page'],
            'hero_panel_label' => ['Hero panel label', 'Next Intake', 'home page'],
            'hero_panel_title' => ['Hero panel title', 'Fall 2026', 'home page'],
            'hero_panel_text' => ['Hero panel description', 'Applications are open for nursing and allied health science programs.', 'home page'],
            'hero_panel_link' => ['Hero panel link text', 'Talk to Admissions', 'home page'],
            'metric_1_value' => ['Metric 1 value', '3', 'home page'],
            'metric_1_label' => ['Metric 1 label', 'Academic pathways', 'home page'],
            'metric_2_value' => ['Metric 2 value', '2026', 'home page'],
            'metric_2_label' => ['Metric 2 label', 'Admissions open', 'home page'],
            'metric_3_value' => ['Metric 3 value', 'DCN', 'home page'],
            'metric_3_label' => ['Metric 3 label', 'Nursing pathways', 'home page'],
            'about_title' => ['About title', 'Built for Students With Serious Ambition', 'home page'],
            'about_text' => ['About description', 'Pakistan Leadership College combines disciplined academics, supportive guidance, and a future-focused environment where students prepare for competitive fields with confidence.', 'home page'],
            'proof_1_title' => ['Proof card 1 title', 'Guided Learning', 'home page'],
            'proof_1_text' => ['Proof card 1 text', 'Clear support from admission to progression.', 'home page'],
            'proof_2_title' => ['Proof card 2 title', 'Future Focus', 'home page'],
            'proof_2_text' => ['Proof card 2 text', 'Programs aligned with higher study goals.', 'home page'],
            'programs_title' => ['Programs title', 'Choose the Program That Matches Your Future', 'home page'],
            'programs_text' => ['Programs description', 'Purpose-built programs for students preparing for medicine, engineering, and computing careers.', 'home page'],
            'clinical_title' => ['Student experience title', 'Modern Learning With Real Direction', 'home page'],
            'clinical_text' => ['Student experience description', 'Students get a structured environment, focused teaching, academic counselling, and the confidence to move toward competitive higher education goals.', 'home page'],
            'experience_point_1' => ['Experience point 1', 'Academic discipline', 'home page'],
            'experience_point_2' => ['Experience point 2', 'Personal guidance', 'home page'],
            'experience_point_3' => ['Experience point 3', 'Leadership mindset', 'home page'],
            'admissions_title' => ['Admissions title', 'Simple Steps to Join PLC', 'home page'],
            'admissions_text' => ['Admissions description', 'Our admissions team keeps the process clear, practical, and student friendly.', 'home page'],
            'news_title' => ['News title', 'Latest From Campus', 'home page'],
            'news_text' => ['News description', 'Stay connected with announcements, admissions updates, and college highlights.', 'home page'],
            'cta_title' => ['Final CTA title', 'Your Next Academic Step Starts Here', 'home page'],
            'cta_text' => ['Final CTA description', 'Apply for admission or speak with our team to choose the right program for your goals.', 'home page'],
        ];

        $order = 0;

        foreach ($content as $key => $data) {
            SiteContent::updateOrCreate(['key' => $key], [
                'label' => $data[0],
                'value' => $data[1],
                'group' => $data[2],
                'type' => str_contains($key, 'description') || str_contains($key, 'text') ? 'textarea' : 'text',
                'sort_order' => $order++,
            ]);
        }

        $programs = [
            ['Pre-medical', 'pre-medical', '2 Years', 'Focused science preparation for students aiming toward medical and healthcare careers.'],
            ['Pre-engineering', 'pre-engineering', '2 Years', 'Strong mathematics and physics foundations for future engineering pathways.'],
            ['Computer Science', 'computer-science', '2 Years', 'Modern computing fundamentals for students interested in software, data, and technology.'],
        ];

        foreach ($programs as $index => $program) {
            Program::updateOrCreate(['slug' => $program[1]], [
                'name' => $program[0],
                'duration' => $program[2],
                'description' => $program[3],
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }

        Program::whereNotIn('slug', array_column($programs, 1))->update(['is_active' => false]);
    }
}
