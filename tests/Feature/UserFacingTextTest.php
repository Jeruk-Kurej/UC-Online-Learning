<?php

use App\Models\Business;
use App\Models\Category;
use App\Models\Collab;
use App\Models\Company;
use App\Models\Message;
use App\Models\User;

// --- What people read: why a page is not public, who someone is, how to reach them ---

function textBusiness(User $owner, string $status = 'approved', array $extra = []): Business
{
    $business = new Business($extra + ['user_id' => $owner->id, 'name' => 'Text Venture', 'description' => 'Sells things.', 'category_id' => Category::firstOrCreate(['slug' => 'jasa'], ['name' => 'Jasa'])->id, 'offering_type' => 'both', 'is_visible' => $status === 'approved', 'type' => 'entrepreneur']);
    $business->approval_status = $status;
    $business->rejection_reason = $status === 'need_revision' ? 'Add photos of your products.' : null;
    $business->save();

    return $business;
}

test('the owner reads why a business or work profile is not public yet; visitors never see the note', function () {
    $owner = User::factory()->create();

    $this->actingAs($owner)->get(route('businesses.show', textBusiness($owner, 'pending')))
        ->assertSee('Waiting for approval')->assertSee('Only you and the admin team can see this business.');
    $this->actingAs($owner)->get(route('businesses.show', textBusiness($owner, 'need_revision')))
        ->assertSee('Changes requested')->assertSee('Message from the admin team:')->assertSee('Add photos of your products.');

    $approved = textBusiness($owner);
    $this->actingAs($owner)->get(route('businesses.show', $approved))->assertDontSee('Waiting for approval')->assertDontSee('Only you and the admin team');
    auth()->logout();
    $this->get(route('businesses.show', $approved))->assertDontSee('Only you and the admin team');

    $company = Company::create(['user_id' => $owner->id, 'name' => 'Waiting Corp', 'position' => 'Lead', 'job_description' => 'Leads the team.', 'approval_status' => 'pending', 'is_visible' => true]);
    $this->actingAs($owner)->get('/business/'.$company->slug)->assertSee('Only you and the admin team can see this work profile.');
});

test('a business page says Business, people read Student or Alumni, company sizes read in English', function () {
    $owner = User::factory()->create(['student_status' => 'student aktif']);
    $legacy = textBusiness($owner, extra: ['type' => 'intrapreneur']);

    $this->get(route('businesses.show', $legacy))->assertSee('>Business</span>', false)->assertDontSee('Intrapreneur</span>', false)->assertSee('Student')->assertDontSee('Student aktif');
    $this->get('/'.$owner->slug)->assertSee('Status')->assertDontSee('Focus');

    $alumni = User::factory()->create(['student_status' => 'alumni']);
    $company = Company::create(['user_id' => $alumni->id, 'name' => 'Size Corp', 'position' => 'Lead', 'job_description' => 'Leads the team.', 'company_scale' => 'Perusahaan swasta nasional', 'approval_status' => 'approved', 'is_visible' => true]);
    $this->get('/business/'.$company->slug)->assertSee('Private company, national')->assertDontSee('Perusahaan swasta nasional')->assertSee('Alumni');
});

test('accepted connections see each other\'s contact details; others are told how to get them', function () {
    $private = User::factory()->create(['whatsapp' => '0812555', 'show_contact_details' => false]);
    $friend = User::factory()->create();
    $stranger = User::factory()->create();
    Collab::create(['sender_id' => $friend->id, 'recipient_id' => $private->id, 'status' => 'accepted']);

    $this->actingAs($friend)->get('/'.$private->slug)->assertSee('0812555');
    $this->actingAs($stranger)->get('/'.$private->slug)->assertDontSee('0812555')->assertSee('Once they accept a collab request from you, you can see them.');
    auth()->logout();
    $this->get('/'.$private->slug)->assertDontSee('0812555')->assertDontSee('Once they accept a collab request');
});

test('a collab request says what accepting shares and no longer shows the sender\'s email', function () {
    $sender = User::factory()->create(['name' => 'Sender Person', 'email' => 'sender@example.test']);
    $recipient = User::factory()->create();

    $this->actingAs($sender)->post(route('collabs.store', $recipient));
    $message = Message::where('recipient_id', $recipient->id)->firstOrFail();

    expect($message->subject)->toBe('Collab request from '.$sender->name);
    $this->actingAs($recipient)->get(route('inbox.show', $message))
        ->assertSeeText('If you accept, you and '.$sender->name.' can see each other\'s WhatsApp, email and LinkedIn.', false)
        ->assertDontSee('sender@example.test');
});

test('an owner whose business has no contact details is asked to add them; visitors are not', function () {
    $owner = User::factory()->create();
    $business = textBusiness($owner);

    $this->actingAs($owner)->get(route('businesses.show', $business))->assertSee('No contact details yet')->assertSee('Add contact details');
    auth()->logout();
    $this->get(route('businesses.show', $business))->assertDontSee('No contact details yet');

    $business->update(['whatsapp' => '0812999']);
    $this->actingAs($owner)->get(route('businesses.show', $business))->assertDontSee('No contact details yet');
});

test('a person with private contacts is told so on their own page instead of seeing them as if public', function () {
    $person = User::factory()->create(['whatsapp' => '0812777', 'show_contact_details' => false]);

    $this->actingAs($person)->get('/'.$person->slug)->assertDontSee('0812777')->assertSee('Your contact details are private')->assertSee('Change this in My profile');
});
