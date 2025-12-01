<?php

namespace App\Filament\Resources\PeminjamanResource\Pages;

use App\Filament\Resources\PeminjamanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;

class ListPeminjaman extends ListRecords
{
    protected static string $resource = PeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    #[On('echo:peminjaman-updates,peminjaman.updated')]
    #[On('echo:peminjaman-updates,peminjaman.created')]
    #[On('echo:peminjaman-updates,peminjaman.deleted')]
    #[On('echo:peminjaman-updates,peminjaman.status_changed')]
    public function refreshTable(): void
    {
        // Add notification for debugging
        Notification::make()
            ->title('Real-time Update')
            ->body('Data peminjaman diperbarui secara real-time')
            ->info()
            ->duration(3000)
            ->send();
            
        $this->dispatch('$refresh');
    }

    public function getTitle(): string
    {
        return 'Peminjaman (Real-time)';
    }
}
