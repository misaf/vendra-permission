<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Actions;

use Filament\Actions\BulkAction;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Collection;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Actions\Concerns\ResolvesSelectedRoles;
use Misaf\VendraPermission\Models\Permission;

final class AttachRolesAction extends BulkAction
{
    use CanCustomizeProcess;
    use ResolvesSelectedRoles;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('vendra-permission::actions.attach_roles'));

        $this->successNotificationTitle(__('filament-actions::edit.single.notifications.saved.title'));

        $this->color('primary');

        $this->icon('heroicon-o-link');

        $this->requiresConfirmation();

        $this->modalIcon('heroicon-o-link');

        $this->schema([
            Select::make('roles')
                ->columnSpanFull()
                ->label(__('vendra-permission::navigation.role'))
                ->multiple()
                ->native(false)
                ->options($this->getRoleSelectOptions())
                ->preload()
                ->required()
                ->searchable(),
        ]);

        $this->action(
            /**
             * @param array{roles?: mixed, ...} $data
             */
            function (array $data): void {
                $rolesByGuard = $this->resolveRoleIdsByGuardFromPayload($data);

                $this->process(static function (Collection $records) use ($rolesByGuard): void {
                    foreach ($records as $record) {
                        if ( ! $record instanceof Permission) {
                            continue;
                        }

                        $roleIdsForGuard = $rolesByGuard[$record->guard_name] ?? [];

                        if ([] === $roleIdsForGuard) {
                            continue;
                        }

                        $record->roles()->syncWithoutDetaching($roleIdsForGuard);
                    }
                });

                $this->success();
            }
        );

        $this->deselectRecordsAfterCompletion();
    }

    public static function getDefaultName(): string
    {
        return 'attachRoles';
    }
}
