<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Move positions from Core → HR ──────────────────────────────────
        if (Schema::hasTable('core_positions') && !Schema::hasTable('hr_positions')) {
            Schema::rename('core_positions', 'hr_positions');
        }

        // ── Move person_positions → hr_employee_positions ───────────────────
        if (Schema::hasTable('core_person_positions') && !Schema::hasTable('hr_employee_positions')) {
            Schema::rename('core_person_positions', 'hr_employee_positions');
        }

        // ── Rename employee_profiles → employees ───────────────────────────
        if (Schema::hasTable('hr_employee_profiles') && !Schema::hasTable('hr_employees')) {
            Schema::rename('hr_employee_profiles', 'hr_employees');
        }

        // ── Move person_educations from Academic → Core ───────────────────
        if (Schema::hasTable('academic_person_educations') && !Schema::hasTable('core_person_educations')) {
            Schema::rename('academic_person_educations', 'core_person_educations');
        }

        // ── Move settings from Core → System ───────────────────────────────
        if (Schema::hasTable('core_settings') && !Schema::hasTable('system_settings')) {
            Schema::rename('core_settings', 'system_settings');
        }
        if (Schema::hasTable('core_setting_groups') && !Schema::hasTable('system_setting_groups')) {
            Schema::rename('core_setting_groups', 'system_setting_groups');
        }

        // ── Move activity_log, backups, notifications → System ────────────
        if (Schema::hasTable('core_activity_log') && !Schema::hasTable('system_activity_logs')) {
            Schema::rename('core_activity_log', 'system_activity_logs');
        }
        if (Schema::hasTable('core_backups') && !Schema::hasTable('system_backups')) {
            Schema::rename('core_backups', 'system_backups');
        }
        if (Schema::hasTable('core_notifications') && !Schema::hasTable('system_notifications')) {
            Schema::rename('core_notifications', 'system_notifications');
        }
        if (Schema::hasTable('core_personal_access_tokens') && !Schema::hasTable('system_personal_access_tokens')) {
            Schema::rename('core_personal_access_tokens', 'system_personal_access_tokens');
        }
        if (Schema::hasTable('core_email_templates') && !Schema::hasTable('system_email_templates')) {
            Schema::rename('core_email_templates', 'system_email_templates');
        }
        if (Schema::hasTable('core_system_assets') && !Schema::hasTable('system_assets')) {
            Schema::rename('core_system_assets', 'system_assets');
        }

        // ── Move relationship_types from CRM → Core ────────────────────────
        if (Schema::hasTable('crm_relationship_types') && !Schema::hasTable('core_relationship_types')) {
            Schema::rename('crm_relationship_types', 'core_relationship_types');
        }

        // ── Drop academic_alumni (replaced by student_statuses + graduations)
        if (Schema::hasTable('academic_alumni')) {
            Schema::drop('academic_alumni');
        }

        // ── Drop academic_student_transfers ────────────────────────────────
        if (Schema::hasTable('academic_student_transfers')) {
            Schema::drop('academic_student_transfers');
        }

        // ── Move newsletter_subscribers from Landing → CRM ────────────────
        if (Schema::hasTable('landing_newsletter_subscribers') && !Schema::hasTable('crm_subscribers')) {
            Schema::rename('landing_newsletter_subscribers', 'crm_subscribers');
        }

        // ── Drop core_permission_groups (simplifying) ──────────────────────
        if (Schema::hasTable('core_permission_group_permission')) {
            Schema::drop('core_permission_group_permission');
        }
        if (Schema::hasTable('core_permission_groups')) {
            Schema::drop('core_permission_groups');
        }
    }

    public function down(): void
    {
        // Reverse all operations (simplified — not all operations are reversible
        // since data may be lost from dropped tables)
        if (Schema::hasTable('hr_positions') && !Schema::hasTable('core_positions')) {
            Schema::rename('hr_positions', 'core_positions');
        }
        if (Schema::hasTable('hr_employee_positions') && !Schema::hasTable('core_person_positions')) {
            Schema::rename('hr_employee_positions', 'core_person_positions');
        }
        if (Schema::hasTable('hr_employees') && !Schema::hasTable('hr_employee_profiles')) {
            Schema::rename('hr_employees', 'hr_employee_profiles');
        }
        if (Schema::hasTable('core_person_educations') && !Schema::hasTable('academic_person_educations')) {
            Schema::rename('core_person_educations', 'academic_person_educations');
        }
        if (Schema::hasTable('system_settings') && !Schema::hasTable('core_settings')) {
            Schema::rename('system_settings', 'core_settings');
        }
        if (Schema::hasTable('system_setting_groups') && !Schema::hasTable('core_setting_groups')) {
            Schema::rename('system_setting_groups', 'core_setting_groups');
        }
        if (Schema::hasTable('system_activity_logs') && !Schema::hasTable('core_activity_log')) {
            Schema::rename('system_activity_logs', 'core_activity_log');
        }
        if (Schema::hasTable('system_backups') && !Schema::hasTable('core_backups')) {
            Schema::rename('system_backups', 'core_backups');
        }
        if (Schema::hasTable('system_notifications') && !Schema::hasTable('core_notifications')) {
            Schema::rename('system_notifications', 'core_notifications');
        }
        if (Schema::hasTable('system_personal_access_tokens') && !Schema::hasTable('core_personal_access_tokens')) {
            Schema::rename('system_personal_access_tokens', 'core_personal_access_tokens');
        }
        if (Schema::hasTable('system_email_templates') && !Schema::hasTable('core_email_templates')) {
            Schema::rename('system_email_templates', 'core_email_templates');
        }
        if (Schema::hasTable('system_assets') && !Schema::hasTable('core_system_assets')) {
            Schema::rename('system_assets', 'core_system_assets');
        }
        if (Schema::hasTable('core_relationship_types') && !Schema::hasTable('crm_relationship_types')) {
            Schema::rename('core_relationship_types', 'crm_relationship_types');
        }
        if (Schema::hasTable('crm_subscribers') && !Schema::hasTable('landing_newsletter_subscribers')) {
            Schema::rename('crm_subscribers', 'landing_newsletter_subscribers');
        }
    }
};
