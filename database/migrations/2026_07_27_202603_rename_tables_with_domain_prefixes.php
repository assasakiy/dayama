<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $renames = [
        // Core
        'persons'                  => 'core_persons',
        'users'                    => 'core_users',
        'roles'                    => 'core_roles',
        'permissions'              => 'core_permissions',
        'role_user'                => 'core_role_user',
        'model_has_roles'          => 'core_model_has_roles',
        'model_has_permissions'    => 'core_model_has_permissions',
        'role_has_permissions'     => 'core_role_has_permissions',
        'permission_groups'        => 'core_permission_groups',
        'permission_group_permission' => 'core_permission_group_permission',
        'user_profiles'            => 'core_user_profiles',
        'user_emails'              => 'core_user_emails',
        'connected_accounts'       => 'core_connected_accounts',
        'login_histories'          => 'core_login_histories',
        'institutions'             => 'core_institutions',
        'institution_types'        => 'core_institution_types',
        'institution_addresses'    => 'core_institution_addresses',
        'institution_contacts'     => 'core_institution_contacts',
        'institution_legalities'   => 'core_institution_legalities',
        'positions'                => 'core_positions',
        'person_positions'         => 'core_person_positions',
        'contact_types'            => 'core_contact_types',
        'contacts'                 => 'core_contacts',
        'address_types'            => 'core_address_types',
        'addresses'                => 'core_addresses',
        'skills'                   => 'core_skills',
        'person_skills'            => 'core_person_skills',
        'languages'                => 'core_languages',
        'person_languages'         => 'core_person_languages',
        'professions'              => 'core_professions',
        'person_professions'       => 'core_person_professions',
        'certificates'             => 'core_certificates',
        'media'                    => 'core_media',
        'media_folders'            => 'core_media_folders',
        'settings'                 => 'core_settings',
        'setting_groups'           => 'core_setting_groups',
        'email_templates'          => 'core_email_templates',
        'system_assets'            => 'core_system_assets',
        'notifications'            => 'core_notifications',
        'activity_log'             => 'core_activity_log',
        'backups'                  => 'core_backups',
        'personal_access_tokens'   => 'core_personal_access_tokens',

        // Academic
        'students'                 => 'academic_students',
        'alumni'                   => 'academic_alumni',
        'student_transfers'        => 'academic_student_transfers',
        'education_levels'         => 'academic_education_levels',
        'person_educations'        => 'academic_person_educations',
        'academic_years'           => 'academic_academic_years',
        'subjects'                 => 'academic_subjects',
        'classrooms'               => 'academic_classrooms',
        'classroom_student'        => 'academic_classroom_student',
        'teaching_assignments'     => 'academic_teaching_assignments',

        // HR
        'employee_profiles'        => 'hr_employee_profiles',
        'employment_histories'     => 'hr_employment_histories',
        'employment_statuses'      => 'hr_employment_statuses',

        // CRM
        'relationship_types'       => 'crm_relationship_types',
        'family_relations'         => 'crm_family_relations',

        // CMS
        'posts'                    => 'cms_posts',
        'categories'               => 'cms_categories',
        'tags'                     => 'cms_tags',
        'comments'                 => 'cms_comments',
        'post_tag'                 => 'cms_post_tag',
        'category_post'            => 'cms_category_post',
        'post_revisions'           => 'cms_post_revisions',
        'post_views'               => 'cms_post_views',
        'bookmarks'                => 'cms_bookmarks',
        'reading_histories'        => 'cms_reading_histories',
        'comment_reactions'        => 'cms_comment_reactions',
        'reactions'                => 'cms_reactions',

        // Landing
        'pages'                    => 'landing_pages',
        'faqs'                     => 'landing_faqs',
        'ctas'                     => 'landing_ctas',
        'stat_groups'              => 'landing_stat_groups',
        'newsletter_subscribers'   => 'landing_newsletter_subscribers',
    ];

    public function up(): void
    {
        foreach ($this->renames as $from => $to) {
            if (Schema::hasTable($from) && !Schema::hasTable($to)) {
                Schema::rename($from, $to);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->renames as $from => $to) {
            if (Schema::hasTable($to) && !Schema::hasTable($from)) {
                Schema::rename($to, $from);
            }
        }
    }
};
