<?php

declare(strict_types=1);

namespace Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Tables;

use Awcodes\BadgeableColumn\Components\BadgeableColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\Layout\Component as LayoutComponent;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Config;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Actions\AttachRolesAction;
use Misaf\VendraPermission\Filament\Clusters\Resources\Permissions\Actions\DetachRolesAction;
use Misaf\VendraPermission\Models\Permission;

final class PermissionTable
{
    public static function configure(Table $table): Table
    {
        /**
         * @var array<int, Column|ColumnGroup|LayoutComponent> $columns
         */
        $columns = [
            TextColumn::make('row')
                ->label('#')
                ->rowIndex(),

            TextColumn::make('roles.name')
                ->alignStart()
                ->badge()
                ->expandableLimitedList()
                ->icon(Heroicon::UserGroup)
                ->label(__('vendra-permission::navigation.role'))
                ->limitList(2)
                ->listWithLineBreaks(),

            BadgeableColumn::make('name')
                ->alignStart()
                ->description(fn(Permission $record): ?string => $record->description)
                ->label(__('vendra-permission::attributes.name'))
                ->searchable(),

            TextColumn::make('created_at')
                ->alignCenter()
                ->badge()
                ->extraCellAttributes(['dir' => 'ltr'])
                ->label(__('vendra-permission::attributes.created_at'))
                ->sinceTooltip()
                ->toggleable(isToggledHiddenByDefault: true)
                ->unless(
                    app()->isLocale('fa'),
                    fn(TextColumn $column) => $column->jalaliDateTime('Y-m-d H:i', toLatin: true),
                    fn(TextColumn $column) => $column->dateTime('Y-m-d H:i')
                ),

            TextColumn::make('updated_at')
                ->alignCenter()
                ->badge()
                ->extraCellAttributes(['dir' => 'ltr'])
                ->label(__('vendra-permission::attributes.updated_at'))
                ->sinceTooltip()
                ->toggleable(isToggledHiddenByDefault: true)
                ->unless(
                    app()->isLocale('fa'),
                    fn(TextColumn $column) => $column->jalaliDateTime('Y-m-d H:i', toLatin: true),
                    fn(TextColumn $column) => $column->dateTime('Y-m-d H:i')
                ),
        ];

        return $table
            ->columns($columns)
            ->filters(
                [
                    QueryBuilder::make()
                        ->constraints([
                            TextConstraint::make('name')
                                ->label(__('vendra-permission::attributes.name')),

                            SelectConstraint::make('guard_name')
                                ->label(__('vendra-permission::attributes.guard_name'))
                                ->options(
                                    collect(Config::array('auth.guards'))->keys()->mapWithKeys(fn($value): array => [$value => $value])->all()
                                )
                                ->multiple(),
                        ]),
                ],
                layout: FiltersLayout::AboveContentCollapsible,
            )
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),

                    EditAction::make(),

                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    AttachRolesAction::make(),

                    DetachRolesAction::make(),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultGroup(
                Group::make('guard_name')
                    ->label(__('vendra-permission::navigation.role'))
            );
    }
}
