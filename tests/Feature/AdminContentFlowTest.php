<?php

use App\Livewire\Pages\AdminAnnualReportPage;
use App\Livewire\Pages\AdminBerandaContentPage;
use App\Livewire\Pages\AdminDashboardDataPage;
use App\Livewire\Pages\AdminFeedbackPage;
use App\Livewire\Pages\AdminMonthlyStatsPage;
use App\Livewire\Pages\AdminProfilePage;
use App\Livewire\Pages\AdminProgramAgendaPage;
use App\Livewire\Pages\AdminUserPage;
use App\Models\AnnualReportSection;
use App\Models\DashboardProgramItem;
use App\Models\DashboardMonthlyStat;
use App\Models\DashboardYearStat;
use App\Models\HomePageSetting;
use App\Models\ContactFeedback;
use App\Models\Prodi;
use App\Models\ProfileSection;
use App\Models\User;
use Database\Seeders\StatistikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(StatistikSeeder::class);
});

function adminAndSiContext(): array
{
    $admin = User::query()->where('role', 'admin')->firstOrFail();
    $prodi = Prodi::query()->where('code', 'SI')->firstOrFail();
    session()->put(['admin_prodi_id' => $prodi->id, 'public_prodi_id' => $prodi->id]);

    return [$admin, $prodi];
}

test('admin annual report changes persist and appear publicly', function () {
    [$admin, $prodi] = adminAndSiContext();
    $year = DashboardYearStat::withoutGlobalScopes()->where('prodi_id', $prodi->id)->max('year');
    $marker = 'Konten laporan audit end-to-end';

    Livewire::actingAs($admin)
        ->test(AdminAnnualReportPage::class)
        ->set('sections.0.content', $marker)
        ->call('simpan')
        ->assertHasNoErrors();

    expect(AnnualReportSection::withoutGlobalScopes()
        ->where('prodi_id', $prodi->id)
        ->where('year', $year)
        ->where('content', $marker)
        ->exists())->toBeTrue();

    $this->withSession(['public_prodi_id' => $prodi->id])
        ->get(route('laporan', ['tahun' => $year]))
        ->assertOk()
        ->assertSee($marker);
});

test('admin profile and contact changes persist and appear publicly', function () {
    [$admin, $prodi] = adminAndSiContext();
    $section = ProfileSection::withoutGlobalScopes()->where('prodi_id', $prodi->id)->orderBy('sort_order')->firstOrFail();
    $profileMarker = 'Ringkasan profil hasil audit';
    $emailMarker = 'audit-si@example.test';

    Livewire::actingAs($admin)
        ->test(AdminProfilePage::class)
        ->call('pilihProdi', $prodi->id)
        ->call('editSection', $section->slug)
        ->set('editSummary', $profileMarker)
        ->call('simpanSection')
        ->assertHasNoErrors();

    Livewire::actingAs($admin)
        ->test(AdminBerandaContentPage::class)
        ->set('contactEmail', $emailMarker)
        ->call('simpanKontak')
        ->assertHasNoErrors();

    expect(ProfileSection::withoutGlobalScopes()->findOrFail($section->id)->summary)->toBe($profileMarker)
        ->and(HomePageSetting::withoutGlobalScopes()->where('prodi_id', $prodi->id)->firstOrFail()->contact_email)->toBe($emailMarker);

    $this->withSession(['public_prodi_id' => $prodi->id])
        ->get(route('profil.detail', ['slug' => $section->slug]))
        ->assertOk()
        ->assertSee($profileMarker);

    $this->withSession(['public_prodi_id' => $prodi->id])
        ->get(route('kontak'))
        ->assertOk()
        ->assertSee($emailMarker);
});

test('admin agenda changes and deletions persist to public portal', function () {
    [$admin, $prodi] = adminAndSiContext();
    $agenda = DashboardProgramItem::withoutGlobalScopes()->where('prodi_id', $prodi->id)->orderByDesc('year')->orderBy('sort_order')->firstOrFail();
    $marker = 'Agenda publik hasil audit';

    $component = Livewire::actingAs($admin)->test(AdminProgramAgendaPage::class);
    $index = collect($component->get('programItems'))->search(fn (array $item) => (int) $item['id'] === $agenda->id);
    $component
        ->set("programItems.$index.title", $marker)
        ->call('simpanProgram')
        ->assertHasNoErrors();

    expect(DashboardProgramItem::withoutGlobalScopes()->findOrFail($agenda->id)->title)->toBe($marker);

    $this->withSession(['public_prodi_id' => $prodi->id])
        ->get(route('home'))
        ->assertOk()
        ->assertSee($marker);

    Livewire::actingAs($admin)
        ->test(AdminProgramAgendaPage::class)
        ->call('hapusAgenda', 0);

    expect(DashboardProgramItem::withoutGlobalScopes()->whereKey($agenda->id)->exists())->toBeFalse();
});

test('admin can create edit and delete a user', function () {
    [$admin, $prodi] = adminAndSiContext();

    $component = Livewire::actingAs($admin)
        ->test(AdminUserPage::class)
        ->set('name', 'User Audit')
        ->set('username', 'user.audit')
        ->set('email', 'user.audit@example.test')
        ->set('role', 'sekprodi')
        ->set('prodi_id', $prodi->id)
        ->set('password', 'password123')
        ->call('save')
        ->assertHasNoErrors();

    $user = User::query()->where('username', 'user.audit')->firstOrFail();
    $component
        ->call('edit', $user->id)
        ->set('name', 'User Audit Diedit')
        ->set('password', '')
        ->call('save')
        ->assertHasNoErrors();

    expect($user->fresh()->name)->toBe('User Audit Diedit');

    $component->call('delete', $user->id);
    expect(User::query()->whereKey($user->id)->exists())->toBeFalse();
});

test('admin monthly publication changes persist and feed the public chart', function () {
    [$admin, $prodi] = adminAndSiContext();
    $component = Livewire::actingAs($admin)->test(AdminMonthlyStatsPage::class);
    $year = $component->get('tahunDipilih');

    $component
        ->set('bulanan.0.publikasi', 123)
        ->call('simpanBulanan')
        ->assertHasNoErrors();

    $january = DashboardMonthlyStat::withoutGlobalScopes()
        ->where('prodi_id', $prodi->id)
        ->where('year', $year)
        ->where('month', 1)
        ->firstOrFail();
    expect((float) data_get($january->kpi, 'publikasi'))->toBe(123.0);

    $this->withSession(['public_prodi_id' => $prodi->id])
        ->get(route('statistik'))
        ->assertOk()
        ->assertSee('123');
});

test('admin home highlights and feedback changes persist', function () {
    [$admin, $prodi] = adminAndSiContext();
    $marker = 'Highlight hasil audit';

    Livewire::actingAs($admin)
        ->test(AdminBerandaContentPage::class)
        ->set('quickHighlights.0.title', $marker)
        ->call('simpanQuickHighlights')
        ->assertHasNoErrors();

    expect(data_get(HomePageSetting::withoutGlobalScopes()->where('prodi_id', $prodi->id)->firstOrFail()->quick_highlights, '0.title'))->toBe($marker);

    $this->withSession(['public_prodi_id' => $prodi->id])
        ->get(route('home'))
        ->assertOk()
        ->assertSee($marker);

    $feedback = ContactFeedback::withoutGlobalScopes()->create([
        'prodi_id' => $prodi->id,
        'name' => 'Pengirim Audit',
        'email' => 'feedback@example.test',
        'subject' => 'Audit',
        'message' => 'Pesan audit',
    ]);

    Livewire::actingAs($admin)
        ->test(AdminFeedbackPage::class)
        ->call('tandaiDibaca', $feedback->id);
    expect($feedback->fresh()->read_at)->not->toBeNull();

    Livewire::actingAs($admin)
        ->test(AdminFeedbackPage::class)
        ->call('hapusFeedback', $feedback->id);
    expect(ContactFeedback::withoutGlobalScopes()->whereKey($feedback->id)->exists())->toBeFalse();
});

test('admin year deletion removes related data only from selected prodi', function () {
    [$admin, $prodi] = adminAndSiContext();
    $otherProdi = Prodi::query()->where('code', 'IF')->firstOrFail();
    $year = DashboardYearStat::withoutGlobalScopes()->where('prodi_id', $prodi->id)->min('year');

    Livewire::actingAs($admin)
        ->test(AdminDashboardDataPage::class)
        ->call('hapusTahun', $year);

    expect(DashboardYearStat::withoutGlobalScopes()->where('prodi_id', $prodi->id)->where('year', $year)->exists())->toBeFalse()
        ->and(DashboardMonthlyStat::withoutGlobalScopes()->where('prodi_id', $prodi->id)->where('year', $year)->exists())->toBeFalse()
        ->and(DashboardProgramItem::withoutGlobalScopes()->where('prodi_id', $prodi->id)->where('year', $year)->exists())->toBeFalse()
        ->and(AnnualReportSection::withoutGlobalScopes()->where('prodi_id', $prodi->id)->where('year', $year)->exists())->toBeFalse()
        ->and(DashboardYearStat::withoutGlobalScopes()->where('prodi_id', $otherProdi->id)->where('year', $year)->exists())->toBeTrue();
});
