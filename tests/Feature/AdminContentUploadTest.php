<?php

use App\Livewire\Pages\AdminBerandaContentPage;
use App\Livewire\Pages\AdminDashboardDataPage;
use App\Livewire\Pages\AdminDashboardPage;
use App\Livewire\Pages\AdminDocumentPage;
use App\Livewire\Pages\AdminProgramAgendaPage;
use App\Models\DashboardProgramItem;
use App\Models\DashboardYearStat;
use App\Models\DocumentItem;
use App\Models\HomePageSetting;
use App\Models\Prodi;
use App\Models\User;
use Database\Seeders\StatistikSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(StatistikSeeder::class);
    Storage::fake('public');
});

test('admin can add a gallery photo with an uploaded image', function () {
    $admin = User::query()->where('role', 'admin')->firstOrFail();
    $prodi = Prodi::query()->where('code', 'SI')->firstOrFail();
    $settings = HomePageSetting::withoutGlobalScopes()->where('prodi_id', $prodi->id)->firstOrFail();
    $before = count($settings->gallery_items ?? []);

    session()->put(['admin_prodi_id' => $prodi->id, 'public_prodi_id' => $prodi->id]);
    $component = Livewire::actingAs($admin)
        ->test(AdminBerandaContentPage::class)
        ->call('tambahGalleryItem');

    $index = count($component->get('galleryItems')) - 1;
    $component
        ->set("galleryImageFiles.$index", UploadedFile::fake()->image('kegiatan.jpg'))
        ->set("galleryItems.$index.title", 'Foto Baru Admin')
        ->call('simpanGaleri')
        ->assertHasNoErrors();

    $settings->refresh();
    expect($settings->gallery_items)->toHaveCount($before + 1)
        ->and($settings->gallery_items[$before]['title'])->toBe('Foto Baru Admin');
});

test('admin can add a document with an uploaded file', function () {
    $admin = User::query()->where('role', 'admin')->firstOrFail();
    $prodi = Prodi::query()->where('code', 'SI')->firstOrFail();
    $before = DocumentItem::withoutGlobalScopes()->where('prodi_id', $prodi->id)->count();

    session()->put(['admin_prodi_id' => $prodi->id, 'public_prodi_id' => $prodi->id]);
    $component = Livewire::actingAs($admin)
        ->test(AdminDocumentPage::class)
        ->call('tambahDokumen');

    $index = count($component->get('documents')) - 1;
    $component
        ->set("documentFiles.$index", UploadedFile::fake()->create('panduan.pdf', 20, 'application/pdf'))
        ->set("documents.$index.title", 'Dokumen Baru Admin')
        ->call('simpanDokumen')
        ->assertHasNoErrors();

    expect(DocumentItem::withoutGlobalScopes()->where('prodi_id', $prodi->id)->count())->toBe($before + 1)
        ->and(DocumentItem::withoutGlobalScopes()->where('prodi_id', $prodi->id)->where('title', 'Dokumen Baru Admin')->exists())->toBeTrue();
});

test('kaprodi can append gallery and document but cannot change existing records', function () {
    $kaprodi = User::query()->where('role', 'kaprodi')->whereHas('prodi', fn ($q) => $q->where('code', 'SI'))->firstOrFail();
    $prodi = $kaprodi->prodi;
    $settings = HomePageSetting::withoutGlobalScopes()->where('prodi_id', $prodi->id)->firstOrFail();
    $originalGallery = $settings->gallery_items;
    $existingDocument = DocumentItem::withoutGlobalScopes()->where('prodi_id', $prodi->id)->firstOrFail();
    $originalTitle = $existingDocument->title;

    session()->put(['admin_prodi_id' => $prodi->id, 'public_prodi_id' => $prodi->id]);
    $gallery = Livewire::actingAs($kaprodi)
        ->test(AdminBerandaContentPage::class)
        ->call('tambahGalleryItem');
    $galleryIndex = count($gallery->get('galleryItems')) - 1;
    $gallery
        ->set("galleryItems.0.title", 'Tidak Boleh Diubah')
        ->set("galleryImageFiles.$galleryIndex", UploadedFile::fake()->image('kaprodi.jpg'))
        ->call('simpanGaleri')
        ->assertHasNoErrors();

    $document = Livewire::actingAs($kaprodi)
        ->test(AdminDocumentPage::class)
        ->set('documents.0.title', 'Tidak Boleh Diubah')
        ->call('tambahDokumen');
    $documentIndex = count($document->get('documents')) - 1;
    $document
        ->set("documentFiles.$documentIndex", UploadedFile::fake()->create('tambahan.pdf', 20, 'application/pdf'))
        ->call('simpanDokumen')
        ->assertHasNoErrors();

    expect(HomePageSetting::withoutGlobalScopes()->where('prodi_id', $prodi->id)->first()->gallery_items[0]['title'])->toBe($originalGallery[0]['title'])
        ->and(DocumentItem::withoutGlobalScopes()->findOrFail($existingDocument->id)->title)->toBe($originalTitle)
        ->and(DocumentItem::withoutGlobalScopes()->where('prodi_id', $prodi->id)->count())->toBeGreaterThan(1);
});

test('kaprodi can replace only the header logo', function () {
    $kaprodi = User::query()->where('role', 'kaprodi')->whereHas('prodi', fn ($q) => $q->where('code', 'SI'))->firstOrFail();
    $prodi = $kaprodi->prodi;
    $settings = HomePageSetting::withoutGlobalScopes()->where('prodi_id', $prodi->id)->firstOrFail();
    $oldLabel = $settings->header_logo_label;
    $oldTitle = $settings->header_title_text;
    session()->put(['admin_prodi_id' => $prodi->id, 'public_prodi_id' => $prodi->id]);

    $dataUrl = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';
    Livewire::actingAs($kaprodi)
        ->test(AdminBerandaContentPage::class)
        ->set('croppedHeaderLogoDataUrl', $dataUrl)
        ->set('headerLogoLabel', 'Tidak Boleh Diubah')
        ->call('simpanHeaderPortal')
        ->assertHasNoErrors();

    $saved = HomePageSetting::withoutGlobalScopes()->where('prodi_id', $prodi->id)->firstOrFail();
    expect($saved->header_logo_url)->toContain('/storage/home-content/header/logo-')
        ->and($saved->header_logo_label)->toBe($oldLabel)
        ->and($saved->header_title_text)->toBe($oldTitle);
});

test('kaprodi cannot update existing statistics or agenda through livewire actions', function () {
    $kaprodi = User::query()->where('role', 'kaprodi')->whereHas('prodi', fn ($q) => $q->where('code', 'SI'))->firstOrFail();
    $prodi = $kaprodi->prodi;
    session()->put(['admin_prodi_id' => $prodi->id, 'public_prodi_id' => $prodi->id]);

    $stat = DashboardYearStat::withoutGlobalScopes()->where('prodi_id', $prodi->id)->orderByDesc('year')->firstOrFail();
    $originalKpi = $stat->kpi;
    Livewire::actingAs($kaprodi)
        ->test(AdminDashboardDataPage::class)
        ->set('statistik.mahasiswa_aktif', 999999)
        ->call('simpanStatistik');

    $agenda = DashboardProgramItem::withoutGlobalScopes()->where('prodi_id', $prodi->id)->orderBy('sort_order')->firstOrFail();
    $originalTitle = $agenda->title;
    Livewire::actingAs($kaprodi)
        ->test(AdminProgramAgendaPage::class)
        ->set('programItems.0.title', 'Tidak Boleh Diubah')
        ->call('simpanProgram');

    expect($stat->fresh()->kpi)->toBe($originalKpi)
        ->and($agenda->fresh()->title)->toBe($originalTitle);
});

test('publication KPI reaches database, admin dashboard, and public home card', function () {
    $admin = User::query()->where('role', 'admin')->firstOrFail();
    $prodi = Prodi::query()->where('code', 'SI')->firstOrFail();
    session()->put(['admin_prodi_id' => $prodi->id, 'public_prodi_id' => $prodi->id]);

    Livewire::actingAs($admin)
        ->test(AdminDashboardDataPage::class)
        ->set('statistik.publikasi', 777)
        ->call('simpanStatistik')
        ->assertHasNoErrors();

    $stat = DashboardYearStat::withoutGlobalScopes()
        ->where('prodi_id', $prodi->id)
        ->orderByDesc('year')
        ->firstOrFail();

    expect((float) data_get($stat->kpi, '3.value'))->toBe(777.0);

    Livewire::actingAs($admin)
        ->test(AdminDashboardPage::class)
        ->assertSee('Publikasi (Realisasi)')
        ->assertSee('Target tahunan');

    $this->withSession(['public_prodi_id' => $prodi->id])
        ->get(route('home'))
        ->assertOk()
        ->assertSee('Publikasi (Realisasi YTD)');

    $this->withSession(['public_prodi_id' => $prodi->id])
        ->get(route('statistik'))
        ->assertOk()
        ->assertSee('Target Publikasi');
});

test('admin can delete gallery and document items from database and public pages', function () {
    $admin = User::query()->where('role', 'admin')->firstOrFail();
    $prodi = Prodi::query()->where('code', 'SI')->firstOrFail();
    session()->put(['admin_prodi_id' => $prodi->id, 'public_prodi_id' => $prodi->id]);

    $settings = HomePageSetting::withoutGlobalScopes()->where('prodi_id', $prodi->id)->firstOrFail();
    $galleryTitle = data_get($settings->gallery_items, '0.title');
    $galleryCount = count($settings->gallery_items ?? []);
    Livewire::actingAs($admin)
        ->test(AdminBerandaContentPage::class)
        ->call('hapusGalleryItem', 0)
        ->call('simpanGaleri')
        ->assertHasNoErrors();

    expect(HomePageSetting::withoutGlobalScopes()->where('prodi_id', $prodi->id)->firstOrFail()->gallery_items)->toHaveCount($galleryCount - 1);
    $this->withSession(['public_prodi_id' => $prodi->id])
        ->get(route('galeri'))
        ->assertOk()
        ->assertDontSee($galleryTitle);

    $document = DocumentItem::withoutGlobalScopes()->where('prodi_id', $prodi->id)->orderBy('sort_order')->firstOrFail();
    Livewire::actingAs($admin)
        ->test(AdminDocumentPage::class)
        ->call('hapusDokumen', 0);

    expect(DocumentItem::withoutGlobalScopes()->whereKey($document->id)->exists())->toBeFalse();
    $this->withSession(['public_prodi_id' => $prodi->id])
        ->get(route('dokumen'))
        ->assertOk()
        ->assertDontSee($document->title);
});
