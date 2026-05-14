<?php

use App\Livewire\Pages\BerandaPage;
use App\Livewire\Pages\AdminDashboardDataPage;
use App\Livewire\Pages\AdminBerandaContentPage;
use App\Livewire\Pages\AdminAnnualReportPage;
use App\Livewire\Pages\AdminDocumentPage;
use App\Livewire\Pages\AdminFeedbackPage;
use App\Livewire\Pages\AdminProgramAgendaPage;
use App\Livewire\Pages\AdminProfilePage;
use App\Livewire\Pages\DokumenPage;
use App\Livewire\Pages\GaleriPage;
use App\Livewire\Pages\KontakPage;
use App\Livewire\Pages\LaporanPage;
use App\Livewire\Pages\ProfilPage;
use App\Livewire\Pages\ProgramAgendaDetailPage;
use App\Livewire\Pages\ProfileDetailPage;
use App\Livewire\Pages\StatistikPage;
use App\Livewire\Pages\AdminMonthlyStatsPage;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DocumentCategoryPdfController;
use App\Http\Controllers\LaporanAllYearsPdfController;
use App\Http\Controllers\LaporanPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', BerandaPage::class)->name('home');
Route::get('/profil', ProfilPage::class)->name('profil');
Route::get('/profil/{slug}', ProfileDetailPage::class)->name('profil.detail');
Route::get('/laporan', LaporanPage::class)->name('laporan');
Route::get('/laporan/pdf', LaporanPdfController::class)->name('laporan.pdf');
Route::get('/laporan/pdf-semua', LaporanAllYearsPdfController::class)->name('laporan.pdf.semua');
Route::get('/statistik', StatistikPage::class)->name('statistik');
Route::get('/dokumen', DokumenPage::class)->name('dokumen');
Route::get('/dokumen/pdf/{kategori?}', DocumentCategoryPdfController::class)->name('dokumen.pdf');
Route::get('/dokumen/kategori/{kategori}', DokumenPage::class)->name('dokumen.category');
Route::get('/galeri', GaleriPage::class)->name('galeri');
Route::get('/galeri/kategori/{kategori}', GaleriPage::class)->name('galeri.category');
Route::get('/kontak', KontakPage::class)->name('kontak');
Route::get('/program-agenda/{id}-{slug}', ProgramAgendaDetailPage::class)->name('program-agenda.detail');
Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
Route::redirect('/admin/login', '/login');

Route::middleware('admin.auth')->group(function (): void {
    Route::get('/admin/dashboard-data', AdminDashboardDataPage::class)->name('admin.dashboard-data');
    Route::get('/admin/laporan-tahunan', AdminAnnualReportPage::class)->name('admin.annual-report');
    Route::get('/admin/program-agenda', AdminProgramAgendaPage::class)->name('admin.program-agenda');
    Route::get('/admin/bulanan-statistik', AdminMonthlyStatsPage::class)->name('admin.monthly-stats');
    Route::get('/admin/profil', AdminProfilePage::class)->name('admin.profile');
    Route::get('/admin/konten-beranda', AdminBerandaContentPage::class)->name('admin.beranda-content');
    Route::get('/admin/dokumen', AdminDocumentPage::class)->name('admin.documents');
    Route::get('/admin/umpan-balik', AdminFeedbackPage::class)->name('admin.feedback');
});
