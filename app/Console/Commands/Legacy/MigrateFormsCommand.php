<?php

namespace App\Console\Commands\Legacy;

use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSubmission;

class MigrateFormsCommand extends BaseMigrationCommand
{
    protected $signature = 'migrate:legacy:forms
                            {--fresh : Truncate forms tables before migrating}
                            {--submissions : Also migrate form submission logs}';

    protected $description = 'Migrate form submissions from legacy form_logs table';

    public function handle(): int
    {
        $this->info('📋 Starting Forms Migration (form_logs → forms + form_submissions)...');

        if (!$this->checkLegacyConnection()) {
            return self::FAILURE;
        }

        if ($this->option('fresh') && $this->confirm('This will delete all existing forms and submissions. Continue?')) {
            FormSubmission::truncate();
            FormField::truncate();
            Form::truncate();
            $this->warn('Forms tables truncated.');
        }

        // Step 1: Create form definitions from unique form categories
        $this->info('  📝 Analyzing form categories...');
        $formCategories = $this->legacy('form_logs')
            ->select('mkate')
            ->distinct()
            ->get()
            ->pluck('mkate')
            ->filter();

        foreach ($formCategories as $category) {
            $categoryName = $this->toUtf8($category);
            if (empty($categoryName)) {
                continue;
            }

            $form = Form::firstOrCreate(
                ['slug' => $this->generateSlug($categoryName, 'forms', 'slug')],
                [
                    'name' => $categoryName,
                    'description' => "Migrated from legacy form: {$categoryName}",
                    'recipient_email' => null,
                    'is_active' => true,
                    'success_message' => 'Form başarıyla gönderildi.',
                ]
            );

            // Create basic fields for this form
            $this->createDefaultFormFields($form);

            // Store mapping using a hash of the category name
            $legacyId = crc32($category);
            $this->storeLegacyMapping($legacyId, $form->id, 'form');

            $this->info("    ✅ Form: {$categoryName}");
        }

        // Step 2: Migrate form submissions
        if ($this->option('submissions')) {
            $this->migrateSubmissions();
        }

        $this->printSummary('Forms');

        return self::SUCCESS;
    }

    /**
     * Create default form fields based on common form_logs columns.
     */
    protected function createDefaultFormFields(Form $form): void
    {
        $fields = [
            ['label' => 'Ad Soyad', 'type' => 'text', 'name' => 'name', 'required' => true, 'sort_order' => 1],
            ['label' => 'E-posta', 'type' => 'email', 'name' => 'email', 'required' => true, 'sort_order' => 2],
            ['label' => 'Konu', 'type' => 'text', 'name' => 'subject', 'required' => false, 'sort_order' => 3],
            ['label' => 'Mesaj', 'type' => 'textarea', 'name' => 'message', 'required' => true, 'sort_order' => 4],
        ];

        foreach ($fields as $field) {
            FormField::firstOrCreate(
                ['form_id' => $form->id, 'name' => $field['name']],
                [
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'is_required' => $field['required'],
                    'sort_order' => $field['sort_order'],
                ]
            );
        }
    }

    /**
     * Migrate form submission logs.
     */
    protected function migrateSubmissions(): void
    {
        $this->info('  📨 Migrating form submissions...');

        $legacySubmissions = $this->legacy('form_logs')->orderBy('tarih')->get();

        if ($legacySubmissions->isEmpty()) {
            $this->warn('  No form submissions found.');
            return;
        }

        $bar = $this->output->createProgressBar($legacySubmissions->count());
        $bar->start();

        foreach ($legacySubmissions as $submission) {
            try {
                $category = $submission->mkate ?? '';
                $legacyFormId = crc32($category);
                $formId = $this->mapLegacyId($legacyFormId, 'form');

                if (!$formId) {
                    // Create a default form if not found
                    $form = Form::firstOrCreate(
                        ['slug' => 'legacy-form'],
                        ['name' => 'Legacy Form', 'is_active' => true]
                    );
                    $formId = $form->id;
                }

                $data = [
                    'name' => $this->toUtf8($submission->uyeid ?? ''),
                    'subject' => $this->toUtf8($submission->mesajb ?? ''),
                    'message' => $this->toUtf8($submission->mesajt ?? ''),
                    'reply' => $this->toUtf8($submission->cevap ?? ''),
                    'email' => $submission->alici_email ?? '',
                    'order_id' => $submission->sipid ?? null,
                ];

                FormSubmission::create([
                    'form_id' => $formId,
                    'data' => $data,
                    'ip_address' => $submission->ip ?? null,
                    'is_read' => true, // Legacy submissions marked as read
                    'created_at' => $this->parseDate($submission->tarih ?? null) ?? now(),
                ]);

                $this->migrated++;
            } catch (\Exception $e) {
                $this->failed++;
                $this->newLine();
                $this->error("Failed to migrate submission: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
    }
}
