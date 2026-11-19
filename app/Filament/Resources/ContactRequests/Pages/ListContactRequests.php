<?php

namespace App\Filament\Resources\ContactRequests\Pages;

use App\Filament\Resources\ContactRequests\ContactRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListContactRequests extends ListRecords
{
    protected static string $resource = ContactRequestResource::class;

    public function mount(): void
    {
        parent::mount();

        // Mở danh sách là coi như đã xem các bản ghi mới, huy hiệu trên menu mất ngay.
        static::getResource()::markRecordsAsSeen();
    }
}
