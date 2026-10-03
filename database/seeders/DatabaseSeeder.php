<?php

namespace Database\Seeders;

use App\Models\AdmissionMilestone;
use App\Models\Event;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\ProgramFee;
use App\Models\SiteContent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::whereIn('email', ['admin@northbridge.edu', 'akhtar@website.com'])->first() ?? new User;
        $admin->fill([
            'name' => 'Website Administrator',
            'email' => 'akhtar@website.com',
            'password' => Hash::make('1234qwer.'),
            'is_admin' => true,
        ])->save();

        $programs = [
            ['Pre-medical', 'pre-medical', '2 Years', 'Focused science preparation for students aiming toward medical and healthcare careers.', 25000, 180000, 20000],
            ['Pre-engineering', 'pre-engineering', '2 Years', 'Strong mathematics and physics foundations for future engineering pathways.', 25000, 180000, 20000],
            ['Computer Science', 'computer-science', '2 Years', 'Modern computing fundamentals for students interested in software, data, and technology.', 25000, 180000, 20000],
        ];

        foreach ($programs as $i => $p) {
            $program = Program::updateOrCreate(['slug' => $p[1]], [
                'name' => $p[0],
                'duration' => $p[2],
                'description' => $p[3],
                'sort_order' => $i,
                'is_active' => true,
            ]);

            foreach ([['Admission Fee', $p[4], 'one time'], ['Tuition Fee', $p[5], 'per year'], ['Laboratory Fee', $p[6], 'per year']] as $order => $fee) {
                ProgramFee::updateOrCreate(['program_id' => $program->id, 'fee_type' => $fee[0]], [
                    'amount' => $fee[1],
                    'currency' => 'PKR',
                    'billing_basis' => $fee[2],
                    'notes' => 'Fees are indicative and may be revised for each academic session.',
                    'sort_order' => $order,
                    'is_active' => true,
                ]);
            }
        }

        Program::whereNotIn('slug', array_column($programs, 1))->update(['is_active' => false]);

        $faculty = [
            ['Dr. Sarah Bennett', 'Principal', 'PhD'],
            ['Prof. Michael Chen', 'Science Faculty', 'MSc'],
            ['Dr. Amina Brooks', 'Computer Science Faculty', 'PhD'],
            ['Prof. Elena Rossi', 'Student Counsellor', 'MEd'],
        ];

        foreach ($faculty as $i => $f) {
            Faculty::updateOrCreate(['name' => $f[0]], ['title' => $f[1], 'qualification' => $f[2], 'sort_order' => $i]);
        }

        $events = [
            ['Admissions Open for Fall 2026', 'admissions-open-2026', 'Start your application for Pre-medical, Pre-engineering, or Computer Science.', 'Applications are now open for the upcoming academic intake at Pakistan Leadership College. Students can explore available programs, review eligibility requirements, and submit their application online.'],
            ['Orientation Session Announced', 'orientation-session-announced', 'Meet faculty, explore programs, and understand the admissions process.', 'Pakistan Leadership College will host an orientation session for prospective students and families. The session will cover academic pathways, admissions guidance, campus expectations, and student support.'],
            ['Computer Science Lab Activities', 'computer-science-lab-activities', 'Students build practical confidence through guided computing activities.', 'Computer Science students are strengthening their programming and problem-solving skills through structured lab sessions, collaborative projects, and faculty-led practice.'],
        ];

        foreach ($events as $i => $e) {
            Event::updateOrCreate(['slug' => $e[1]], [
                'title' => $e[0],
                'excerpt' => $e[2],
                'body' => $e[3],
                'event_date' => now()->addDays($i * 14),
                'is_published' => true,
            ]);
        }

        foreach (['Choose Your Program', 'Check Eligibility', 'Submit Application', 'Begin Your Journey'] as $i => $m) {
            AdmissionMilestone::updateOrCreate(['title' => $m], ['sort_order' => $i]);
        }

        $images = [
            'hero_image' => 'Hero background',
            'about_image' => 'About section image',
            'clinical_image' => 'Student experience image',
            'scholarship_image' => 'Scholarship and testimonial image',
            'campus_image' => 'Campus tour image',
        ];

        foreach ($images as $key => $label) {
            SiteContent::firstOrCreate(['key' => $key], ['group' => 'website images', 'label' => $label, 'value' => null, 'type' => 'image', 'sort_order' => 0]);
        }

        $details = [
            'college_name' => ['College name', 'Dir College Of Nursing & Allied Health Science'],
            'college_short_name' => ['Header name', 'DCN'],
            'nav_brand_title' => ['Navigation brand title', 'DCN'],
            'nav_brand_subtitle' => ['Navigation brand subtitle', 'Dir College Of Nursing & Allied Health Science'],
            'college_phone' => ['Phone number', '+92 300 1234567'],
            'college_email' => ['Email address', 'admissions@dcn.edu.pk'],
            'college_address' => ['Street address', 'Dir, Khyber Pakhtunkhwa, Pakistan'],
            'admissions_banner' => ['Admissions banner', 'Admissions Open for Fall 2026 - Apply Today'],
            'footer_description' => ['Footer description', 'Dir College Of Nursing & Allied Health Science prepares students for professional nursing, allied health sciences, and compassionate healthcare leadership.'],
        ];

        $this->syncContent($details, 'college details');

        $contact = [
            'contact_eyebrow' => ['Contact label', 'GET IN TOUCH'],
            'contact_title' => ['Contact page title', 'Let us Start a Conversation'],
            'contact_description' => ['Contact introduction', 'Get clear guidance from our admissions and student support teams whenever you need it.'],
            'college_whatsapp' => ['WhatsApp number', '+92 300 1234567'],
            'admissions_phone' => ['Admissions phone', '+92 301 1234567'],
            'support_phone' => ['Student support phone', '+92 302 1234567'],
            'accounts_phone' => ['Accounts phone', '+92 303 1234567'],
            'contact_office_hours' => ['Office hours', 'Monday-Friday, 8:30 AM-5:00 PM'],
            'contact_office_days' => ['Office days', 'Monday-Friday'],
            'contact_response_time' => ['Average response time', 'Under 2 working days'],
            'contact_form_title' => ['Contact form title', 'Send Us a Message'],
            'contact_form_text' => ['Contact form description', 'Complete the form and a member of our team will respond within two working days.'],
            'contact_faq_1_question' => ['FAQ 1 question', 'How quickly will you respond?'],
            'contact_faq_1_answer' => ['FAQ 1 answer', 'Our team normally responds within two working days.'],
            'contact_faq_2_question' => ['FAQ 2 question', 'Which number should I use for admissions?'],
            'contact_faq_2_answer' => ['FAQ 2 answer', 'Use the admissions number for questions about programs, eligibility, and applications.'],
            'contact_faq_3_question' => ['FAQ 3 question', 'Can I contact you through WhatsApp?'],
            'contact_faq_3_answer' => ['FAQ 3 answer', 'Yes. Use any WhatsApp button on this page to begin a conversation.'],
            'contact_faq_4_question' => ['FAQ 4 question', 'What are your office hours?'],
            'contact_faq_4_answer' => ['FAQ 4 answer', 'Our standard office hours are shown in the direct contact panel.'],
        ];

        $this->syncContent($contact, 'contact page');

        $home = [
            'hero_kicker' => ['Hero kicker', 'Admissions Open for Fall 2026'],
            'hero_title' => ['Hero title', 'Pakistan Leadership College'],
            'hero_text' => ['Hero description', 'A modern college experience for ambitious students pursuing Pre-medical, Pre-engineering, and Computer Science pathways.'],
            'hero_primary_cta' => ['Primary button', 'Apply Now'],
            'hero_secondary_cta' => ['Secondary button', 'Explore Programs'],
            'hero_panel_label' => ['Hero panel label', 'Next Intake'],
            'hero_panel_title' => ['Hero panel title', 'Fall 2026'],
            'hero_panel_text' => ['Hero panel description', 'Applications are open for Pre-medical, Pre-engineering, and Computer Science.'],
            'hero_panel_link' => ['Hero panel link text', 'Talk to Admissions'],
            'metric_1_value' => ['Metric 1 value', '3'],
            'metric_1_label' => ['Metric 1 label', 'Academic pathways'],
            'metric_2_value' => ['Metric 2 value', '2026'],
            'metric_2_label' => ['Metric 2 label', 'Admissions open'],
            'metric_3_value' => ['Metric 3 value', 'PLC'],
            'metric_3_label' => ['Metric 3 label', 'Leadership culture'],
            'about_title' => ['About title', 'Built for Students With Serious Ambition'],
            'about_text' => ['About description', 'Pakistan Leadership College combines disciplined academics, supportive guidance, and a future-focused environment where students prepare for competitive fields with confidence.'],
            'proof_1_title' => ['Proof card 1 title', 'Guided Learning'],
            'proof_1_text' => ['Proof card 1 text', 'Clear support from admission to progression.'],
            'proof_2_title' => ['Proof card 2 title', 'Future Focus'],
            'proof_2_text' => ['Proof card 2 text', 'Programs aligned with higher study goals.'],
            'programs_title' => ['Programs title', 'Choose the Program That Matches Your Future'],
            'programs_text' => ['Programs description', 'Purpose-built programs for students preparing for medicine, engineering, and computing careers.'],
            'clinical_title' => ['Student experience title', 'Modern Learning With Real Direction'],
            'clinical_text' => ['Student experience description', 'Students get a structured environment, focused teaching, academic counselling, and the confidence to move toward competitive higher education goals.'],
            'experience_point_1' => ['Experience point 1', 'Academic discipline'],
            'experience_point_2' => ['Experience point 2', 'Personal guidance'],
            'experience_point_3' => ['Experience point 3', 'Leadership mindset'],
            'admissions_title' => ['Admissions title', 'Simple Steps to Join PLC'],
            'admissions_text' => ['Admissions description', 'Our admissions team keeps the process clear, practical, and student friendly.'],
            'news_title' => ['News title', 'Latest From Campus'],
            'news_text' => ['News description', 'Stay connected with announcements, admissions updates, and college highlights.'],
            'cta_title' => ['Final CTA title', 'Your Next Academic Step Starts Here'],
            'cta_text' => ['Final CTA description', 'Apply for admission or speak with our team to choose the right program for your goals.'],
        ];

        $this->syncContent($home, 'home page');
    }

    private function syncContent(array $items, string $group): void
    {
        $order = 0;

        foreach ($items as $key => $data) {
            SiteContent::updateOrCreate(['key' => $key], [
                'group' => $group,
                'label' => $data[0],
                'value' => $data[1],
                'type' => str_contains($key, 'description') || str_contains($key, 'text') || str_contains($key, 'answer') ? 'textarea' : 'text',
                'sort_order' => $order++,
            ]);
        }
    }
}
