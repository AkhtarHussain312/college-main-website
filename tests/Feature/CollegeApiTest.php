<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Program;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CollegeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_endpoint_returns_active_programs(): void
    {
        Program::create(['name' => 'BS Nursing', 'slug' => 'bs-nursing', 'duration' => '4 Years', 'description' => 'Clinical nursing education', 'is_active' => true]);
        $this->getJson('/api/college-content')->assertOk()->assertJsonPath('data.programs.0.slug', 'bs-nursing');
    }

    public function test_inquiry_is_validated_and_persisted(): void
    {
        $payload = ['name' => 'Ayesha Khan', 'email' => 'ayesha@example.com', 'program' => 'BS Nursing'];
        $this->postJson('/api/inquiries', $payload)->assertCreated();
        $this->assertDatabaseHas('inquiries', ['email' => 'ayesha@example.com']);
    }

    public function test_invalid_inquiry_returns_validation_errors(): void
    {
        $this->postJson('/api/inquiries', [])->assertUnprocessable()->assertJsonValidationErrors(['name', 'email', 'program']);
    }

    public function test_contact_message_is_validated_and_persisted(): void
    {
        $payload = ['name' => 'Ayesha Khan', 'email' => 'ayesha@example.com', 'phone' => '+92 300 1234567', 'subject' => 'Admissions guidance', 'message' => 'Please share the admission requirements.'];
        $this->postJson('/api/contact-messages', $payload)->assertCreated()->assertJsonPath('message', 'Your message has been sent successfully.');
        $this->assertDatabaseHas('contact_messages', ['email' => 'ayesha@example.com', 'subject' => 'Admissions guidance', 'status' => 'new']);
    }

    public function test_published_event_has_a_dedicated_api_page(): void
    {
        $event = Event::create(['title' => 'Clinical Awards', 'slug' => 'clinical-awards', 'excerpt' => 'Award details', 'body' => 'The complete award article.', 'is_published' => true]);
        $this->getJson('/api/events/'.$event->slug)->assertOk()->assertJsonPath('data.body', 'The complete award article.');
    }

    public function test_unpublished_event_is_not_publicly_accessible(): void
    {
        $event = Event::create(['title' => 'Draft Event', 'slug' => 'draft-event', 'excerpt' => 'Draft details', 'is_published' => false]);
        $this->getJson('/api/events/'.$event->slug)->assertNotFound();
    }

    public function test_online_application_is_stored_with_private_documents(): void
    {
        Storage::fake('local');
        $program = Program::create(['name' => 'BS Nursing', 'slug' => 'bs-nursing', 'duration' => '4 Years', 'description' => 'Clinical education', 'is_active' => true]);
        $payload = ['program_id' => $program->id, 'first_name' => 'Ayesha', 'last_name' => 'Khan', 'date_of_birth' => '2000-05-10', 'gender' => 'female', 'email' => 'ayesha@example.com', 'phone' => '+92 300 1234567', 'address' => '10 College Road', 'city' => 'Lahore', 'country' => 'Pakistan', 'highest_qualification' => 'FSc Pre-Medical', 'institution' => 'City College', 'graduation_year' => 2022, 'confirmation' => true, 'transcript' => UploadedFile::fake()->create('transcript.pdf', 100, 'application/pdf'), 'identity_document' => UploadedFile::fake()->create('id.pdf', 100, 'application/pdf')];
        $this->post('/api/applications', $payload, ['Accept' => 'application/json'])->assertCreated()->assertJsonStructure(['data' => ['reference', 'status']]);
        $this->assertDatabaseHas('applications', ['email' => 'ayesha@example.com', 'status' => 'submitted']);
    }

    public function test_web_application_submission_redirects_with_reference(): void
    {
        Storage::fake('local');
        $program = Program::create(['name' => 'Post RN BSN', 'slug' => 'post-rn-bsn', 'duration' => '2 Years', 'description' => 'Advance nursing practice', 'is_active' => true]);
        $payload = [
            'program_id' => $program->id,
            'first_name' => 'Fatima',
            'last_name' => 'Ali',
            'date_of_birth' => '1998-03-15',
            'gender' => 'female',
            'email' => 'fatima@example.com',
            'phone' => '+92 300 7654321',
            'address' => '25 University Town',
            'city' => 'Peshawar',
            'country' => 'Pakistan',
            'highest_qualification' => 'General Nursing Diploma',
            'institution' => 'Nursing Institute',
            'graduation_year' => 2021,
            'confirmation' => true,
            'transcript' => UploadedFile::fake()->create('transcript.pdf', 100, 'application/pdf'),
            'identity_document' => UploadedFile::fake()->create('id.pdf', 100, 'application/pdf'),
        ];

        $response = $this->post('/apply', $payload);
        $response->assertRedirect('/apply');
        $response->assertSessionHas('success_reference');
        $this->assertDatabaseHas('applications', ['email' => 'fatima@example.com', 'status' => 'submitted']);
    }

    public function test_admissions_page_loads_successfully(): void
    {
        $response = $this->get('/admissions');
        $response->assertOk()
            ->assertSee('How to Apply for Admission')
            ->assertSee('Eligibility Requirements');
    }

    public function test_campus_life_page_loads_successfully(): void
    {
        $response = $this->get('/campus-life');
        $response->assertOk()
            ->assertSee('Life Beyond the Classroom')
            ->assertSee('Hospital Clinical Rotations');
    }

    public function test_application_page_loads_wizard_successfully(): void
    {
        $response = $this->get('/apply');
        $response->assertOk()
            ->assertSee('Admission Application Portal')
            ->assertSee('Program Selection')
            ->assertSee('Personal Details')
            ->assertSee('Documents & Review', false);
    }

    public function test_about_page_loads_successfully(): void
    {
        $response = $this->get('/about');
        $response->assertOk()
            ->assertSee('ABOUT OUR COLLEGE')
            ->assertSee('Our History & Mission')
            ->assertSee('What Makes Us Different');
    }

    public function test_contact_page_loads_successfully(): void
    {
        $response = $this->get('/contact');
        $response->assertOk()
            ->assertSee('GET IN TOUCH')
            ->assertSee('DIRECT CONTACT')
            ->assertSee('Choose the Right Number')
            ->assertSee('Send Us a Message')
            ->assertSee('Before You Contact Us');
    }

    public function test_programs_page_loads_with_lhv_and_structured_fees(): void
    {
        $this->seed();
        $response = $this->get('/programs');
        $response->assertOk()
            ->assertSee('ACADEMIC PROGRAMS')
            ->assertSee('All Programs')
            ->assertSee('LHV')
            ->assertSee('Essential training for maternal and child healthcare, community midwifery, and primary healthcare services.')
            ->assertSee('Fee Structure')
            ->assertSee('Admission Fee')
            ->assertSee('Tuition Fee')
            ->assertSee('Apply Now')
            ->assertSee('Request details')
            ->assertDontSee('nothing special');
    }

    public function test_track_page_loads_successfully(): void
    {
        $response = $this->get('/track');
        $response->assertOk()
            ->assertSee('Track Application Status')
            ->assertSee('Find Your Admission File');
    }

    public function test_track_api_returns_application_status(): void
    {
        $program = Program::first() ?? Program::create(['name' => 'BS Nursing', 'slug' => 'bs-nursing-test', 'duration' => '4 Years', 'description' => 'Test', 'is_active' => true]);
        $app = \App\Models\Application::create([
            'reference' => 'DCN-2026-99999',
            'program_id' => $program->id,
            'first_name' => 'Sara',
            'last_name' => 'Khan',
            'date_of_birth' => '2002-05-10',
            'gender' => 'female',
            'email' => 'sara.test@example.com',
            'phone' => '+92 311 1234567',
            'national_id' => '15302-1234567-8',
            'address' => 'Hospital Road, Dir',
            'city' => 'Dir',
            'country' => 'Pakistan',
            'highest_qualification' => 'F.Sc Pre-Medical',
            'institution' => 'Govt Degree College',
            'graduation_year' => 2024,
            'transcript_path' => 'applications/transcripts/test.pdf',
            'identity_document_path' => 'applications/identity/test.pdf',
            'status' => 'shortlisted',
            'submitted_at' => now(),
        ]);

        $response = $this->getJson('/api/applications/track?query=DCN-2026-99999');
        $response->assertOk()
            ->assertJsonPath('data.reference', 'DCN-2026-99999')
            ->assertJsonPath('data.status', 'shortlisted')
            ->assertJsonPath('data.current_step', 3);
    }

    public function test_merit_lists_page_loads_successfully(): void
    {
        $response = $this->get('/merit-lists');
        $response->assertOk()
            ->assertSee('Official Merit Lists & Downloads', false)
            ->assertSee('All Documents');
    }

    public function test_merit_lists_api_returns_published_documents(): void
    {
        \App\Models\MeritList::create([
            'title' => 'Test Merit List 2026',
            'category' => 'merit_list',
            'file_path' => 'documents/test.pdf',
            'file_size' => '1.2 MB',
            'is_published' => true,
        ]);

        $response = $this->getJson('/api/merit-lists');
        $response->assertOk()
            ->assertJsonStructure(['data' => [['id', 'title', 'category', 'file_url']]]);
    }

    public function test_admin_can_export_applications_to_csv(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin_export_'.uniqid().'@test.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get('/api/admin/applications/export');
        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
    }

    public function test_admin_merit_lists_endpoint_returns_program_options(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin_ml_'.uniqid().'@test.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->getJson('/api/admin/merit-lists');
        $response->assertOk()
            ->assertJsonStructure(['data', 'options' => ['programs']]);
    }

    public function test_admin_can_create_merit_list_with_or_without_program(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin_create_ml_'.uniqid().'@test.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $program = Program::first();

        // 1. With program
        $res1 = $this->actingAs($admin)->postJson('/api/admin/merit-lists', [
            'title' => 'BS Nursing Merit List 2026',
            'category' => 'merit_list',
            'program_id' => $program?->id,
            'session_year' => '2026-2027',
            'is_published' => true,
        ]);
        $res1->assertCreated();

        // 2. Without program (General download document)
        $res2 = $this->actingAs($admin)->postJson('/api/admin/merit-lists', [
            'title' => 'Official College Prospectus 2026',
            'category' => 'prospectus',
            'program_id' => '',
            'session_year' => '2026-2027',
            'is_published' => true,
        ]);
        $res2->assertCreated();
        $this->assertNull($res2->json('data.program_id'));
    }

    public function test_admin_can_delete_program_and_cascade_cleanup(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin_del_prog_'.uniqid().'@test.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $program = Program::create([
            'name' => 'Biomedical Engineering',
            'slug' => 'biomedical-engineering-'.uniqid(),
            'duration' => '4 Years',
            'description' => 'Medical engineering and technology',
            'is_active' => true,
        ]);

        $fee = \App\Models\ProgramFee::create([
            'program_id' => $program->id,
            'fee_type' => 'Tuition Fee',
            'amount' => 50000,
            'currency' => 'PKR',
            'billing_basis' => 'per semester',
            'is_active' => true,
        ]);

        $app = \App\Models\Application::create([
            'reference' => (string) \Illuminate\Support\Str::uuid(),
            'program_id' => $program->id,
            'first_name' => 'Ali',
            'last_name' => 'Raza',
            'date_of_birth' => '2004-05-10',
            'gender' => 'male',
            'email' => 'ali.raza@test.com',
            'phone' => '+92 300 9876543',
            'address' => 'Test Address',
            'city' => 'Dir',
            'country' => 'Pakistan',
            'highest_qualification' => 'FSc Pre-Medical',
            'institution' => 'Govt Degree College',
            'graduation_year' => 2023,
            'transcript_path' => 'transcripts/test.pdf',
            'identity_document_path' => 'identity/test.pdf',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $meritList = \App\Models\MeritList::create([
            'title' => 'Biomedical Merit List',
            'category' => 'merit_list',
            'program_id' => $program->id,
            'file_path' => 'documents/test.pdf',
            'is_published' => true,
        ]);

        // Delete the program via admin API
        $response = $this->actingAs($admin)->deleteJson('/api/admin/programs/'.$program->id);
        $response->assertNoContent();

        // Verify program is deleted
        $this->assertDatabaseMissing('programs', ['id' => $program->id]);

        // Verify fees were deleted
        $this->assertDatabaseMissing('program_fees', ['id' => $fee->id]);

        // Verify application preserved with nullified program_id
        $this->assertDatabaseHas('applications', [
            'id' => $app->id,
            'program_id' => null,
        ]);

        // Verify merit list preserved with nullified program_id
        $this->assertDatabaseHas('merit_lists', [
            'id' => $meritList->id,
            'program_id' => null,
        ]);
    }

    public function test_admin_can_delete_application(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin_del_app_'.uniqid().'@test.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $app = \App\Models\Application::create([
            'reference' => (string) \Illuminate\Support\Str::uuid(),
            'program_id' => null,
            'first_name' => 'Usman',
            'last_name' => 'Khan',
            'date_of_birth' => '2003-01-15',
            'gender' => 'male',
            'email' => 'usman.khan@test.com',
            'phone' => '+92 300 1122334',
            'address' => 'Test Address',
            'city' => 'Dir',
            'country' => 'Pakistan',
            'highest_qualification' => 'FSc Pre-Medical',
            'institution' => 'Govt Degree College',
            'graduation_year' => 2022,
            'transcript_path' => 'transcripts/test_del.pdf',
            'identity_document_path' => 'identity/test_del.pdf',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($admin)->deleteJson('/api/admin/applications/'.$app->id);
        $response->assertNoContent();

        $this->assertDatabaseMissing('applications', ['id' => $app->id]);
    }

    public function test_admin_can_update_login_credentials(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Original Admin',
            'email' => 'admin_orig_'.uniqid().'@test.com',
            'password' => bcrypt('oldPassword123'),
            'is_admin' => true,
        ]);

        // 1. Update name and email
        $newEmail = 'admin_updated_'.uniqid().'@test.com';
        $res1 = $this->actingAs($admin)->putJson('/api/admin/profile', [
            'name' => 'Updated Admin Name',
            'email' => $newEmail,
        ]);
        $res1->assertOk()
            ->assertJsonPath('data.name', 'Updated Admin Name')
            ->assertJsonPath('data.email', $newEmail);

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Updated Admin Name',
            'email' => $newEmail,
        ]);

        // 2. Update password with correct current password
        $res2 = $this->actingAs($admin)->putJson('/api/admin/profile', [
            'name' => 'Updated Admin Name',
            'email' => $newEmail,
            'current_password' => 'oldPassword123',
            'password' => 'newSecretPassword456',
            'password_confirmation' => 'newSecretPassword456',
        ]);
        $res2->assertOk();

        // Verify new password works
        $admin->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newSecretPassword456', $admin->password));

        // 3. Fail when current password is wrong
        $res3 = $this->actingAs($admin)->putJson('/api/admin/profile', [
            'name' => 'Updated Admin Name',
            'email' => $newEmail,
            'current_password' => 'wrongPassword',
            'password' => 'anotherPassword789',
            'password_confirmation' => 'anotherPassword789',
        ]);
        $res3->assertUnprocessable()
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_admin_can_list_and_add_other_users(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'super_admin_'.uniqid().'@test.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        // 1. List users
        $resList = $this->actingAs($admin)->getJson('/api/admin/users');
        $resList->assertOk()
            ->assertJsonStructure(['data' => ['data']]);

        // 2. Add new user
        $newUserEmail = 'new_staff_'.uniqid().'@test.com';
        $resCreate = $this->actingAs($admin)->postJson('/api/admin/users', [
            'name' => 'New Staff Member',
            'email' => $newUserEmail,
            'password' => 'staffSecure123',
            'is_admin' => true,
        ]);
        $resCreate->assertCreated()
            ->assertJsonPath('data.name', 'New Staff Member')
            ->assertJsonPath('data.email', $newUserEmail);

        $this->assertDatabaseHas('users', [
            'email' => $newUserEmail,
            'is_admin' => true,
        ]);
    }

    public function test_admin_can_update_and_delete_other_users(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Admin Controller',
            'email' => 'admin_ctrl_'.uniqid().'@test.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $otherUser = \App\Models\User::create([
            'name' => 'Other Admin',
            'email' => 'other_admin_'.uniqid().'@test.com',
            'password' => bcrypt('password123'),
            'is_admin' => true,
        ]);

        // 1. Update other user
        $resUpdate = $this->actingAs($admin)->putJson('/api/admin/users/'.$otherUser->id, [
            'name' => 'Updated Other Name',
            'email' => $otherUser->email,
            'is_admin' => false,
        ]);
        $resUpdate->assertOk()
            ->assertJsonPath('data.name', 'Updated Other Name')
            ->assertJsonPath('data.is_admin', false);

        // 2. Delete other user
        $resDelete = $this->actingAs($admin)->deleteJson('/api/admin/users/'.$otherUser->id);
        $resDelete->assertNoContent();
        $this->assertDatabaseMissing('users', ['id' => $otherUser->id]);

        // 3. Attempt to delete self (should fail with 422)
        $resSelfDelete = $this->actingAs($admin)->deleteJson('/api/admin/users/'.$admin->id);
        $resSelfDelete->assertStatus(422)
            ->assertJsonPath('message', 'You cannot delete your own logged-in account.');
    }
}
