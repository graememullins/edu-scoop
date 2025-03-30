<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeachingJobResource\Pages;
use App\Filament\Resources\TeachingJobResource\RelationManagers;
use App\Models\TeachingJob;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Components\DateTimePicker;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use App\Models\Profession;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;

class TeachingJobResource extends Resource
{
    protected static ?string $model = TeachingJob::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('keyword_id')
                ->relationship('keyword', 'keyword') // Use the `keyword` relationship for options
                ->label('Keyword Used')
                ->required(),
                Forms\Components\TextInput::make('job_title')
                    ->label('Job Title')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('posted_date'),
                Forms\Components\DatePicker::make('closing_date'),
                Forms\Components\DatePicker::make('created_at')
                    ->label ('Scraped Date'),
                Forms\Components\TextInput::make('posted_by')
                    ->maxLength(255),
                Forms\Components\TextInput::make('reference_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('job_link')
                    ->maxLength(255),
                Forms\Components\TextInput::make('contact_job_title')
                    ->maxLength(255),
                Forms\Components\TextInput::make('contact_name')
                    ->visible(fn () => ! auth()->user()?->hasRole('trial'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('contact_email')
                    ->visible(fn () => ! auth()->user()?->hasRole('trial'))
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('contact_phone')
                    ->visible(fn () => ! auth()->user()?->hasRole('trial'))
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('town')
                    ->maxLength(255),
                Forms\Components\TextInput::make('region')
                    ->maxLength(255),
                Forms\Components\TextInput::make('post_code')
                    ->maxLength(255),
                Forms\Components\TextInput::make('longitude')
                    ->readonly()
                    ->maxLength(255),
                Forms\Components\TextInput::make('latitude')
                    ->readonly()
                    ->maxLength(255),
                Forms\Components\Checkbox::make('post_code_validated')
                    ->label('Postcode Validated'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginationPageOptions(
                auth()->user()?->hasRole('trial')
                    ? [10, 25, 50, 100]
                    : [10, 25, 50, 100, 250, 500]
            )
            ->defaultPaginationPageOption(50)
            ->defaultSort('posted_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('job_title')
                ->sortable()
                ->label('Job Title')
                ->searchable(),
                Tables\Columns\TextColumn::make('profession.name') // Add this column
                    ->label('Profession')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('keyword.keyword') // Add the keyword column
                ->label('Keyword Used')
                ->toggleable(isToggledHiddenByDefault: false)
                ->sortable()
                ->searchable(),
                Tables\Columns\TextColumn::make('posted_date')
                    ->label('Posted Date')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : null)
                    //->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('closing_date')
                    ->label('Closing Date')
                    ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : null)
                    //->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('posted_by')
                    ->label('Posted by') // Keep the label as "Trust"
                    //->formatStateUsing(fn ($record) => $record->trust . ' - <strong>' . e($record->town) . '</strong>')
                    ->html() // Enables HTML rendering for the column
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('town')
                    ->label('Town')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('region')
                    ->label('Region')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('post_code')
                    ->label('Post Code')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference_number')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_name')
                    ->visible(fn () => ! auth()->user()?->hasRole('trial'))
                    ->label('Contact Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_job_title')
                    ->label('Contact Job Title')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_email')
                    ->label('Contact Email')
                    ->visible(fn () => ! auth()->user()?->hasRole('trial'))
                    ->copyable(),
                Tables\Columns\TextColumn::make('contact_phone')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn () => ! auth()->user()?->hasRole('trial'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('profession_id')
                    ->label('Filter by Profession')
                    ->relationship('profession', 'name')
                    ->searchable()
                    ->preload(),
            
                TernaryFilter::make('is_scraped')
                    ->queries(
                        true: fn ($query) => $query->where('is_scraped', 1),
                        false: fn ($query) => $query->where('is_scraped', 0),
                    ),
            
                // Date Filter for `posted_date`
                Filter::make('posted_date')
                ->form([
                    DatePicker::make('posted_date_from')
                        ->label('Date Posted From')
                        ->minDate(Carbon::now()->subYear()) // 1 year ago
                        ->maxDate(Carbon::now()), // Today
        
                    DatePicker::make('posted_date_to')
                        ->label('Date Posted To')
                        ->minDate(Carbon::now()->subYear()) // 1 year ago
                        ->maxDate(Carbon::now()), // Today
                ])
                ->query(function ($query, array $data) {
                    return $query
                        ->when($data['posted_date_from'] ?? null, fn ($query, $date) => $query->whereDate('posted_date', '>=', $date))
                        ->when($data['posted_date_to'] ?? null, fn ($query, $date) => $query->whereDate('posted_date', '<=', $date));
                }),
            ])            
            ->actions([
                Tables\Actions\ViewAction::make()
                ->color('info') // Tailwind color
                ->icon('heroicon-s-eye') // Add an icon
                ->button(),
                Tables\Actions\EditAction::make()
                ->color('success') // Tailwind color
                ->icon('heroicon-s-pencil-square')
                ->button()
                ->visible(fn () => auth()->user()?->hasRole('super_admin')),
            ])
            ->bulkActions([
                //Tables\Actions\DeleteBulkAction::make(),
                ExportBulkAction::make()
                    ->visible(fn () => ! auth()->user()?->hasRole('trial')),
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
            'index' => Pages\ListTeachingJobs::route('/'),
            'create' => Pages\CreateTeachingJob::route('/create'),
            'view' => Pages\ViewTeachingJob::route('/{record}'),
            'edit' => Pages\EditTeachingJob::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPluralLabel(): string
    {
        return 'Teaching Jobs';
    }
}
