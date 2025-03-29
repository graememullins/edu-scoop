<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfessionResource\Pages;
use App\Filament\Resources\ProfessionResource\RelationManagers;
use App\Models\Profession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProfessionResource extends Resource
{
    protected static ?string $model = Profession::class;

    protected static ?string $navigationIcon = 'heroicon-m-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
    
                // Global toggle for all keywords
                Forms\Components\Toggle::make('toggle_all_keywords')
                    ->label('Toggle All Keywords')
                    ->helperText('Enable or disable all keywords at once.')
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('keywords.*.status', $state)),
    
                // Repeater for keywords
                Forms\Components\Repeater::make('keywords')
                    ->relationship('keywords') // Define the relationship with the `Keyword` model
                    ->schema([
                        Forms\Components\TextInput::make('keyword')
                            ->required()
                            ->label('Keyword'),
                        Forms\Components\Toggle::make('status')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->createItemButtonLabel('Add Keyword') // Label for the add button
                    ->columns(1) // Display one field per row
                    ->collapsed(false), // Keep fields expanded by default
            ]);
    } 

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('keywords')
                ->label('Associated Keywords')
                ->formatStateUsing(function ($record) {
                    return $record->keywords->pluck('keyword')->join(', ');
                }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasRole('super_admin')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
                    //Tables\Actions\ForceDeleteBulkAction::make(),
                    //Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfessions::route('/'),
            //'create' => Pages\CreateProfession::route('/create'),
            //'edit' => Pages\EditProfession::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
