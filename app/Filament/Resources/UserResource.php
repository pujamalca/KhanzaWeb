<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Pegawai;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function getNavigationBadge(): ?string
        {
            return static::getModel()::count();
        }

    protected static ?string $navigationGroup = 'Admin';


    // Label jamak, ganti dengan singular jika perlu
    protected static ?string $pluralLabel = 'Pengguna'; // Setel ke bentuk singular

    // Label seperti button new akan berubah
    protected static ?string $label = 'Pengguna';

    // title menu akan berubah
    protected static ?string $navigationLabel = 'Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                Select::make('pegawai_id')
                        ->label('Pilih Pegawai')
                        ->placeholder('Ambil dari data pegawai')
                        ->options(
                            Pegawai::all()->mapWithKeys(function ($pegawai) {
                                return [$pegawai->id => "{$pegawai->nik} - {$pegawai->nama}"];
                            })->toArray()
                        )
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn ($state, callable $set) => self::updateUserData($state, $set)), // Update fields otomatis


                Forms\Components\TextInput::make('name')
                        ->label('Nama')
                        ->required()
                        ->maxLength(255)
                        ->disabled()
                        ->dehydrated(),

                Forms\Components\TextInput::make('username')
                        ->label('NIK')
                        ->required()
                        ->maxLength(255)
                        ->disabled()
                        ->dehydrated(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                // Using Select Component
                Forms\Components\Select::make('roles')
                ->relationship('roles', 'name')
                ->multiple()
                ->preload()
                ->searchable(),

                // Using CheckboxList Component
                Forms\Components\CheckboxList::make('roles')
                ->relationship('roles', 'name')
                ->searchable(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Diaktifkan untuk pengguna')
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger')
                    ->inline(),
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->maxLength(255)
                    ->visibleOn('create')
                    ->revealable(),
                    ])
                    ->columns(3),
            ]);
    }

    private static function updateUserData($pegawaiId, callable $set)
    {
        if (!$pegawaiId) {
            return;
        }

        $pegawai = Pegawai::find($pegawaiId);
        if ($pegawai) {
            $set('name', $pegawai->nama);
            $set('username', $pegawai->nik);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->label('NIK')
                    ->copyable()
                    ->copyMessage('Username copied')
                    ->copyMessageDuration(1500)
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->separator(',')
                    ->label('Role')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_session_id')
                    ->label('Sesi')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->sortable()
                    ->label('Aktif'),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                //
                Tables\Filters\Filter::make('verified')
                ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([

                    Action::make('clearSession_user_user')
                    ->label('Clear Session')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->action(function (User $record) {
                        // Jika user memiliki session aktif, hapus session tersebut dari tabel sessions
                        if ($record->last_session_id) {
                            DB::table('sessions')
                                ->where('id', $record->last_session_id)
                                ->delete();
                        }

                        // Kosongkan last_session_id pada tabel users
                        $record->update(['last_session_id' => null]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn ($record) => auth()->user()->can('clearSession_user_user', $record)),

                    Action::make('reset_password')
                            ->label('Reset Password')
                            ->icon('heroicon-o-key')
                            ->color('warning')
                            ->action(fn (User $record) => $record->update(['password' => bcrypt($record->username)]))
                            ->requiresConfirmation(),

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),


                ])
                ->button()
                ->label('Menu'),
            ],position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Action::make('clear_session')
                            ->label('Clear Session')
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->action(fn (User $record) => $record->update(['last_session_id' => null]))
                            ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'clearSession_user'
        ];
    }
}
