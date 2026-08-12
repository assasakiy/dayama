<?php

declare(strict_types=1);

namespace App\Observers;

use App\Services\YayasanPersonIndexService;
use Modules\Core\Models\Person;

class PersonObserver
{
    public function created(Person $person): void
    {
        app(YayasanPersonIndexService::class)->syncPerson($person);
    }

    public function updated(Person $person): void
    {
        if ($person->isDirty('nik') || $person->isDirty('nama_lengkap') || $person->isDirty('tanggal_lahir')) {
            if ($person->getOriginal('nik') && $person->getOriginal('nik') !== $person->nik) {
                $oldPerson = clone $person;
                $oldPerson->nik = $person->getOriginal('nik');
                app(YayasanPersonIndexService::class)->removePerson($oldPerson);
            }
            app(YayasanPersonIndexService::class)->syncPerson($person);
        }
    }

    public function deleted(Person $person): void
    {
        app(YayasanPersonIndexService::class)->removePerson($person);
    }

    public function restored(Person $person): void
    {
        app(YayasanPersonIndexService::class)->syncPerson($person);
    }

    public function forceDeleted(Person $person): void
    {
        app(YayasanPersonIndexService::class)->removePerson($person);
    }
}
