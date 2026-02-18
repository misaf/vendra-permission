<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Filament\Clusters\Resources\Roles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rules\Unique;
use Livewire\Component as Livewire;
use Misaf\VendraTenant\Models\Tenant;

final class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->afterStateUpdated(fn(Livewire $livewire) => $livewire->validateOnly("data.name"))
                    ->autofocus()
                    ->columnSpan(['lg' => 1])
                    ->label(__('vendra-permission::attributes.name'))
                    ->live(debounce: 500)
                    ->required()
                    ->string()
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
                    ->columnSpan(['lg' => 1])
                    ->label(__('vendra-permission::attributes.guard_name'))
                    ->live()
                    ->native(false)
                    ->options(Arr::mapWithKeys(
                        array_keys(Config::array('auth.guards')),
                        fn(string $guard): array => [$guard => $guard],
                    ))
                    ->preload()
                    ->required()
                    ->searchable()
                    ->string(),

                Textarea::make('description')
                    ->afterStateUpdated(fn(Livewire $livewire) => $livewire->validateOnly("data.description"))
                    ->columnSpanFull()
                    ->label(__('vendra-permission::attributes.description'))
                    ->live(debounce: 500)
                    ->nullable()
                    ->rows(5)
                    ->string(),
            ]);
    }
}
