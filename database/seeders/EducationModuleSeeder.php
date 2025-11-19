<?php

namespace Database\Seeders;

use App\Models\EducationModule;
use Illuminate\Database\Seeder;

class EducationModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'title' => 'Budget Reset Sprint',
                'content' => 'Workshop praktis untuk melakukan audit cash flow, memotong kebocoran harian, dan merancang ulang alokasi amplop anggaran agar siap menghadapi musim pengeluaran tinggi.',
                'category' => 'budgeting',
                'difficulty' => 'beginner',
                'estimated_time' => 20,
                'language' => 'id',
                'tags' => ['budget', 'cash flow', 'envelope', 'debt'],
                'learning_objectives' => [
                    'Menentukan prioritas pengeluaran dengan metode 50/30/20.',
                    'Membuat ulang kategori amplop anggaran dalam 15 menit.',
                    'Mengidentifikasi kebocoran pengeluaran yang sering terlewat.',
                ],
                'resource_links' => [
                    ['label' => 'Template Budget Reset', 'url' => 'https://example.com/docs/budget-reset-id'],
                    ['label' => 'Panduan Amplop Digital', 'url' => 'https://example.com/docs/envelope-digital'],
                ],
                'metadata' => [
                    'persona_focus' => ['spender', 'avoider'],
                    'best_for' => ['cash_flow_gap', 'debt'],
                ],
            ],
            [
                'title' => 'Behavioral Spending Detox',
                'content' => 'Sesi refleksi perilaku yang membantu pengguna memetakan pemicu emosional, mencatat eksperimen mindful spending, dan menyiapkan respon otomatis sebelum transaksi impulsif terjadi.',
                'category' => 'behavioral',
                'difficulty' => 'intermediate',
                'estimated_time' => 25,
                'language' => 'id',
                'tags' => ['trigger', 'psychology', 'mindful'],
                'learning_objectives' => [
                    'Mengenali 5 pemicu utama pengeluaran impulsif.',
                    'Membangun ritual mindful spending sebelum transaksi.',
                    'Mencatat eksperimen keuangan mikro selama 7 hari.',
                ],
                'resource_links' => [
                    ['label' => 'Worksheet Trigger Tracker', 'url' => 'https://example.com/docs/trigger-tracker'],
                    ['label' => 'Audio Mindful Spending', 'url' => 'https://example.com/audio/mindful-spending'],
                ],
                'metadata' => [
                    'persona_focus' => ['spender', 'emotional'],
                    'best_for' => ['triggers', 'habits'],
                ],
            ],
            [
                'title' => 'Debt Avalanche Studio',
                'content' => 'Toolkit untuk mengurutkan hutang berdasarkan suku bunga, menjalankan simulasi timeline pembayaran, dan menghubungkan task list dengan automation agar progres terasa konsisten.',
                'category' => 'debt',
                'difficulty' => 'intermediate',
                'estimated_time' => 30,
                'language' => 'en',
                'tags' => ['debt', 'avalanche', 'snowball'],
                'learning_objectives' => [
                    'Mengurutkan hutang berdasarkan suku bunga dan urgensi.',
                    'Menyusun simulasi timeline pelunasan 12 bulan.',
                    'Menyiapkan automation transfer untuk akselerasi pembayaran.',
                ],
                'resource_links' => [
                    ['label' => 'Debt Comparison Sheet', 'url' => 'https://example.com/docs/debt-sheet'],
                    ['label' => 'Automation Checklist', 'url' => 'https://example.com/docs/automation-checklist'],
                ],
                'metadata' => [
                    'persona_focus' => ['planner', 'balanced'],
                    'best_for' => ['debt', 'automation'],
                ],
            ],
            [
                'title' => 'Investing Fundamentals Lab',
                'content' => 'Laboratorium mini yang mengenalkan prinsip alokasi aset, penilaian risiko pribadi, serta cara memilih instrumen dasar untuk tujuan 3-5 tahun dengan bahasa yang mudah.',
                'category' => 'investing',
                'difficulty' => 'beginner',
                'estimated_time' => 35,
                'language' => 'en',
                'tags' => ['investing', 'risk', 'portfolio'],
                'learning_objectives' => [
                    'Memahami kerangka alokasi aset yang relevan untuk risiko moderat.',
                    'Menghitung expected return sederhana dan volatilitas.',
                    'Membuat daftar ETF/reksadana sesuai tujuan 3-5 tahun.',
                ],
                'resource_links' => [
                    ['label' => 'Risk Profiling Card', 'url' => 'https://example.com/docs/risk-card'],
                    ['label' => 'Asset Allocation Planner', 'url' => 'https://example.com/docs/allocation-planner'],
                ],
                'metadata' => [
                    'persona_focus' => ['investor', 'balanced'],
                    'best_for' => ['long_term', 'wealth'],
                ],
            ],
            [
                'title' => 'Savings Automation Blueprint',
                'content' => 'Blueprint singkat untuk merancang alur auto-transfer multi rekening, menautkannya ke habit tracker aplikasi, dan menjaga motivasi lewat notifikasi pengingat streak.',
                'category' => 'saving',
                'difficulty' => 'beginner',
                'estimated_time' => 15,
                'language' => 'id',
                'tags' => ['automation', 'emergency fund', 'habit'],
                'learning_objectives' => [
                    'Mendesain alur auto-transfer multi rekening.',
                    'Menghubungkan savings habit dengan goal tracker aplikasi.',
                    'Membuat sistem notifikasi untuk menjaga streak tabungan.',
                ],
                'resource_links' => [
                    ['label' => 'Automation Flow Whiteboard', 'url' => 'https://example.com/docs/automation-flow'],
                ],
                'metadata' => [
                    'persona_focus' => ['saver', 'planner'],
                    'best_for' => ['emergency_fund', 'habits'],
                ],
            ],
            [
                'title' => 'Family Money Council',
                'content' => 'Program komunikasi keuangan keluarga lengkap dengan agenda meeting, format pengambilan keputusan, serta contoh skrip untuk membahas wishlist dan batas pengeluaran.',
                'category' => 'family_finance',
                'difficulty' => 'intermediate',
                'estimated_time' => 28,
                'language' => 'id',
                'tags' => ['family', 'communication', 'shared goals'],
                'learning_objectives' => [
                    'Menetapkan ritual meeting finansial keluarga bulanan.',
                    'Memetakan shared goals dan owner setiap milestone.',
                    'Mempelajari teknik conflict resolution untuk topik uang.',
                ],
                'resource_links' => [
                    ['label' => 'Family Money Agenda', 'url' => 'https://example.com/docs/family-agenda'],
                ],
                'metadata' => [
                    'persona_focus' => ['family', 'collaborator'],
                    'best_for' => ['family', 'goals'],
                ],
            ],
            [
                'title' => 'Financial Resilience Drills',
                'content' => 'Latihan skenario untuk menguji daya tahan keuangan keluarga, menentukan prioritas penghematan cepat, dan memetakan playbook respon ketika pendapatan berubah drastis.',
                'category' => 'resilience',
                'difficulty' => 'advanced',
                'estimated_time' => 40,
                'language' => 'en',
                'tags' => ['scenario', 'stress-test', 'risk'],
                'learning_objectives' => [
                    'Melakukan stress test arus kas terhadap 3 skenario ekonomi.',
                    'Menentukan prioritas penghematan cepat ketika pendapatan turun.',
                    'Merancang playbook respon darurat finansial keluarga.',
                ],
                'resource_links' => [
                    ['label' => 'Scenario Builder Sheet', 'url' => 'https://example.com/docs/scenario-builder'],
                ],
                'metadata' => [
                    'persona_focus' => ['planner', 'saver'],
                    'best_for' => ['risk', 'emergency_fund'],
                ],
            ],
            [
                'title' => 'Ethical & Sustainable Investing Primer',
                'content' => 'Primer yang mengupas dasar investasi berkelanjutan, cara membaca rating ESG, sekaligus alat evaluasi dampak agar portofolio sejalan dengan nilai pribadi.',
                'category' => 'investing',
                'difficulty' => 'advanced',
                'estimated_time' => 32,
                'language' => 'en',
                'tags' => ['esg', 'sustainability', 'impact'],
                'learning_objectives' => [
                    'Memahami rating ESG dan keterbatasannya.',
                    'Menyusun watchlist instrumen impact investing.',
                    'Menilai trade-off risiko/imbal hasil portofolio hijau.',
                ],
                'resource_links' => [
                    ['label' => 'Impact Investment Checklist', 'url' => 'https://example.com/docs/impact-checklist'],
                ],
                'metadata' => [
                    'persona_focus' => ['investor'],
                    'best_for' => ['values', 'wealth'],
                ],
            ],
        ];

        foreach ($modules as $index => $module) {
            EducationModule::updateOrCreate(
                ['title' => $module['title']],
                array_merge($module, [
                    'order' => $index + 1,
                    'is_active' => true,
                ])
            );
        }
    }
}
