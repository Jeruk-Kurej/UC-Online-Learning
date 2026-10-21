<?php

use App\Models\Business;
use App\Models\Collab;
use App\Models\Company;
use App\Models\Message;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// --- Records as imports leave them (missing category, price, description; odd names, links and lists): every page still renders and escapes text ---

test('every page renders odd records and escapes their text', function () {
    DB::connection()->getPdo()->sqliteCreateFunction('REGEXP', fn ($pattern, $value) => (int) preg_match('/'.$pattern.'/', (string) $value), 2);
    $admin = User::factory()->admin()->create();
    $odd = User::factory()->create(['name' => 'Ödd Ñame <b>bold</b> & co', 'current_status' => null, 'year_of_enrollment' => null, 'major' => null, 'profile_photo_url' => 'not a url', 'activities_doc_url' => 'a.png; ;b.pdf', 'expertise_certification_url' => ' , ;', 'linkedin' => 'https://', 'whatsapp' => 'abc', 'show_contact_details' => true, 'testimony' => '   ', 'is_featured' => true, 'is_featured_testimony' => true]);
    $b = new Business(['user_id' => $odd->id, 'name' => 'Odd Venture', 'description' => null, 'category_id' => null, 'offering_type' => null, 'is_visible' => true, 'type' => 'entrepreneur', 'city' => null, 'province' => null, 'website' => 'not a url', 'instagram' => '@,@@,', 'whatsapp' => '+62 (812) 000', 'established_date' => null]);
    $b->approval_status = 'approved';
    $b->is_featured = true;
    $b->save();
    Product::create(['business_id' => $b->id, 'type' => 'product', 'name' => 'No Price', 'description' => null, 'price_type' => 'fixed', 'price' => null, 'photo_url' => 'https://drive.google.com/file/d/abc/view']);
    Product::create(['business_id' => $b->id, 'type' => 'service', 'name' => 'Weird', 'description' => '', 'price_type' => 'something-else', 'price' => '-5']);
    $c = Company::create(['user_id' => $odd->id, 'name' => 'Odd Corp', 'category_id' => null, 'position' => null, 'job_description' => null, 'achievement' => "-\n- \n-  x", 'company_scale' => 'Unknown size', 'year_started_working' => null, 'is_visible' => true, 'approval_status' => 'approved']);
    $friend = User::factory()->create();
    Collab::create(['sender_id' => $friend->id, 'recipient_id' => $odd->id, 'status' => 'accepted']);
    $m = Message::create(['sender_id' => $friend->id, 'recipient_id' => $odd->id, 'subject' => '', 'body' => '', 'type' => 'collab_invite']);
    Message::create(['sender_id' => $friend->id, 'recipient_id' => $odd->id, 'subject' => 'Plain', 'body' => '<script>x</script>', 'type' => 'other']);

    $pages = ['/', '/business', '/business?view=intrapreneur', '/business/'.$b->slug, '/business/'.$c->slug, '/'.$odd->slug, '/about'];
    $failures = [];
    foreach (['guest' => null, 'odd' => $odd, 'friend' => $friend, 'admin' => $admin] as $role => $user) {
        $list = $pages;
        if ($role === 'odd') {
            $list = array_merge($list, ['/profile', '/my-business', '/my-testimony', '/inbox', '/inbox/'.$m->id, '/business/'.$b->slug.'/edit', '/intrapreneurs/'.$c->slug.'/edit']);
        }
        if ($role === 'admin') {
            $list = array_merge($list, ['/admin/users', '/admin/business', '/admin/business?type=intrapreneur', '/admin/testimonies', '/admin/users/'.$odd->slug.'/edit', '/business/'.$b->slug.'/edit']);
        }
        foreach ($list as $path) {
            auth()->logout();
            $r = ($user ? $this->actingAs($user) : $this)->get($path);
            if ($r->getStatusCode() >= 500) {
                $e = $r->exception;
                $failures[] = "$role $path -> ".$r->getStatusCode().' '.($e ? class_basename($e).': '.mb_substr($e->getMessage(), 0, 170) : '');
            }
            if (str_contains($r->getContent(), '<script>x</script>')) {
                $failures[] = "$role $path -> unescaped script tag";
            }
        }
    }
    expect($failures)->toBe([]);
});
