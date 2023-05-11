<?php

namespace Filament\Tables\Actions;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ForceDeleteBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'forceDelete';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::force-delete.multiple.label'));

        $this->modalHeading(fn (): string => __('filament-actions::force-delete.multiple.modal.heading', ['label' => $this->getPluralRecordLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::force-delete.multiple.modal.actions.delete.label'));

        $this->successNotificationTitle(__('filament-actions::force-delete.multiple.messages.deleted'));

        $this->color('danger');

        $this->icon('heroicon-m-trash');

        $this->requiresConfirmation();

        $this->action(function (): void {
            $this->process(static function (Collection $records): void {
                $records->each(fn (Model $record) => $record->forceDelete());
            });

            $this->success();
        });

        $this->deselectRecordsAfterCompletion();

        $this->hidden(function (HasTable $livewire): bool {
            $trashedFilterState = $livewire->getTableFilterState(TrashedFilter::class) ?? [];

            if (! array_key_exists('value', $trashedFilterState)) {
                return false;
            }

            return blank($trashedFilterState['value']);
        });
    }
}
