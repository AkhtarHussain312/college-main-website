<?php

namespace App\Services;

use App\Models\AboutSection;
use App\Models\AdmissionMilestone;
use App\Models\Program;
use App\Models\SiteContent;

class ChatbotKnowledgeService
{
    /**
     * Build a structured, plain-text snapshot of live database records
     * for injection into the Gemini system instruction.
     */
    public function getDatabaseContext(): string
    {
        $sections = [];

        // 1. College Profile & Direct Contact Details
        $content = SiteContent::get()->pluck('value', 'key');
        $collegeName = $content->get('college_name', 'Dir College of Nursing & Allied Health Science');
        $collegeShortName = $content->get('college_short_name', 'DCN');
        $collegeAddress = $content->get('college_address', 'Dir, Khyber Pakhtunkhwa, Pakistan');
        $collegeEmail = $content->get('college_email', 'admissions@dcn.edu.pk');
        $collegePhone = $content->get('college_phone', '+92 300 1234567');
        $whatsapp = $content->get('college_whatsapp', '+92 300 1234567');
        $admissionsPhone = $content->get('admissions_phone', '+92 301 1234567');
        $supportPhone = $content->get('support_phone', '+92 302 1234567');
        $accountsPhone = $content->get('accounts_phone', '+92 303 1234567');
        $officeHours = $content->get('contact_office_hours', 'Monday - Friday, 8:30 AM - 5:00 PM');
        $officeDays = $content->get('contact_office_days', 'Monday - Friday');
        $responseTime = $content->get('contact_response_time', 'Under 2 working days');
        $banner = $content->get('admissions_banner', 'Admissions Open for Fall 2026 - Apply Today');
        $aboutText = $content->get('footer_description', 'Dir College Of Nursing & Allied Health Science prepares students for professional nursing, allied health sciences, and healthcare leadership.');

        $collegeBlock = "=== 1. INSTITUTION & CONTACT DIRECTORY ===\n";
        $collegeBlock .= "Name: {$collegeName} ({$collegeShortName})\n";
        $collegeBlock .= "Current Announcement: {$banner}\n";
        $collegeBlock .= "Campus Address: {$collegeAddress}\n";
        $collegeBlock .= "Official Email: {$collegeEmail}\n";
        $collegeBlock .= "General Phone: {$collegePhone}\n";
        $collegeBlock .= "WhatsApp Line: {$whatsapp}\n";
        $collegeBlock .= "Admissions Helpline: {$admissionsPhone}\n";
        $collegeBlock .= "Student Support Helpline: {$supportPhone}\n";
        $collegeBlock .= "Accounts Office Helpline: {$accountsPhone}\n";
        $collegeBlock .= "Office Working Days: {$officeDays}\n";
        $collegeBlock .= "Office Working Hours: {$officeHours}\n";
        $collegeBlock .= "Inquiry Response Time: {$responseTime}\n";
        $collegeBlock .= "Mission Overview: {$aboutText}\n";
        $sections[] = $collegeBlock;

        // 2. Active Academic Programs & Detailed Fee Structure
        $programs = Program::with(['fees' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $programsBlock = "=== 2. ACADEMIC PROGRAMS & COMPLETE FEE STRUCTURES ===\n";
        if ($programs->isEmpty()) {
            $programsBlock .= "No active programs currently listed in the database. Please direct prospective students to admissions.\n";
        } else {
            foreach ($programs as $program) {
                $programsBlock .= "\n[PROGRAM] {$program->name}\n";
                $programsBlock .= "Duration: {$program->duration}\n";
                $programsBlock .= "Description: {$program->description}\n";
                $programsBlock .= "Application Link: /apply\n";

                $fees = $program->fees;
                if ($fees->isEmpty()) {
                    $programsBlock .= "Fee Breakdown: Specific breakdown pending confirmation. Contact admissions for latest schedule.\n";
                } else {
                    $programsBlock .= "Fee Breakdown:\n";
                    $totalFirstYear = 0;
                    foreach ($fees as $fee) {
                        $formattedAmount = number_format((float) $fee->amount);
                        $programsBlock .= "  - {$fee->fee_type}: {$fee->currency} {$formattedAmount} ({$fee->billing_basis})";
                        if (!empty($fee->notes)) {
                            $programsBlock .= " — Note: {$fee->notes}";
                        }
                        $programsBlock .= "\n";
                        if (str_contains(strtolower($fee->billing_basis), 'one time') || str_contains(strtolower($fee->billing_basis), 'year')) {
                            $totalFirstYear += (float) $fee->amount;
                        }
                    }
                    if ($totalFirstYear > 0) {
                        $programsBlock .= "  - Approximate Total (First Year): PKR " . number_format($totalFirstYear) . "\n";
                    }
                }
            }
        }
        $sections[] = $programsBlock;

        // 3. Admissions Process, Steps & Eligibility
        $milestones = AdmissionMilestone::orderBy('sort_order')->get();
        $admissionsBlock = "=== 3. ADMISSION STEPS & ELIGIBILITY GUIDANCE ===\n";
        $admissionsBlock .= "How to Apply Online:\n";
        if ($milestones->isNotEmpty()) {
            foreach ($milestones as $index => $milestone) {
                $stepNumber = $index + 1;
                $admissionsBlock .= "  Step {$stepNumber}: {$milestone->title}\n";
            }
        } else {
            $admissionsBlock .= "  Step 1: Choose Your Program\n  Step 2: Check Eligibility\n  Step 3: Submit Online Application\n  Step 4: Verification & Enrollment\n";
        }
        $admissionsBlock .= "Eligibility & Requirements:\n";
        $admissionsBlock .= "  - Candidates must meet the academic qualifications (such as Matriculation / FSc Pre-Medical / SSC / HSSC science requirements or equivalent).\n";
        $admissionsBlock .= "  - Required documents for online submission: Scanned Academic Transcripts / Certificates and National ID / B-Form / Passport.\n";
        $admissionsBlock .= "  - Online application form url: /apply\n";
        $sections[] = $admissionsBlock;

        // 4. Institutional Highlights & Campus Overview
        $aboutSections = AboutSection::where('is_active', true)->orderBy('sort_order')->get();
        if ($aboutSections->isNotEmpty()) {
            $aboutBlock = "=== 4. ABOUT THE COLLEGE & CAMPUS FACILITIES ===\n";
            foreach ($aboutSections as $about) {
                $aboutBlock .= "Topic: {$about->title}\n";
                $aboutBlock .= "Details: {$about->body}\n\n";
            }
            $sections[] = trim($aboutBlock);
        }

        // 5. Frequently Asked Questions (from SiteContent)
        $faqPairs = [];
        for ($i = 1; $i <= 6; $i++) {
            $q = $content->get("contact_faq_{$i}_question");
            $a = $content->get("contact_faq_{$i}_answer");
            if (!empty($q) && !empty($a)) {
                $faqPairs[] = "Q: {$q}\nA: {$a}";
            }
        }
        if (!empty($faqPairs)) {
            $faqBlock = "=== 5. FREQUENTLY ASKED QUESTIONS ===\n" . implode("\n\n", $faqPairs) . "\n";
            $sections[] = $faqBlock;
        }

        return implode("\n\n", $sections);
    }
}
