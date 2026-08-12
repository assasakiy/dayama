<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hr_employee_positions')) {
            Schema::drop('hr_employee_positions');
        }
    }

    public function down(): void
    {
        // Cannot restore dropped data — migration not reversible
    }
};
