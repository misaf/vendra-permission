<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rules\Unique;
use Livewire\Component as Livewire;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\RelationManagers\PermissionRelationManager;
use Misaf\VendraTenant\Models\Tenant;

final class PermisssionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->afterStateUpdated(fn(Livewire $livewire) => $livewire->validateOnly("data.name"))
                    ->autofocus()
                    ->columnSpanFull()
                    ->label(__('vendra-permission::attributes.name'))
                    ->required()
                    ->unique(
                        modifyRuleUsing: function (Unique $rule, Get $get): void {
                            $rule->where('tenant_id', Tenant::current()?->id);

                            $guardName = $get->string('guard_name', isNullable: true);

                            if (null !== $guardName) {
                                $rule->where('guard_name', $guardName);
                            }
                        },
                    ),

                Select::make('guard_name')
                    ->afterStateUpdated(fn(Livewire $livewire) => $livewire->validateOnly("data.guard_name"))
                    ->columnSpanFull()
                    ->hiddenOn(PermissionRelationManager::class)
                    ->label(__('vendra-permission::attributes.guard_name'))
                    ->native(false)
                    ->options(
                        collect(Config::array('auth.guards'))
                            ->keys()
                            ->mapWithKeys(fn($value): array => [$value => $value])
                            ->all()
                    )
                    ->preload()
                    ->required()
                    ->searchable(),

                Textarea::make('description')
                    ->columnSpanFull()
                    ->label(__('vendra-permission::attributes.description'))
                    ->rows(5),
            ]);
    }
}
