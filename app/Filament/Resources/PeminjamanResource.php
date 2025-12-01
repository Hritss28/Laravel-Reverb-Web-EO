<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanResource\Pages;
use App\Models\Peminjaman;
use App\Models\Barang;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Livewire\Attributes\On;

class PeminjamanResource extends Resource
{
    protected static ?string $model = Peminjaman::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationLabel = 'Peminjaman';
    
    protected static ?string $modelLabel = 'Peminjaman';
    
    protected static ?string $pluralModelLabel = 'Peminjaman';
    
    protected static ?string $slug = 'peminjaman';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Peminjaman')
                    ->schema([
                        Forms\Components\TextInput::make('kode_peminjaman')
                            ->label('Kode Peminjaman')
                            ->disabled()
                            ->placeholder('Otomatis dibuat'),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Peminjam')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Components\Select::make('barang_id')
                            ->label('Barang')
                            ->relationship('barang', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn (Barang $record): string => "{$record->kode_barang} - {$record->nama}")
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $barang = Barang::find($state);
                                    $set('stok_tersedia_info', $barang ? $barang->stok_tersedia : 0);
                                }
                            }),
                        
                        Forms\Components\Placeholder::make('stok_tersedia_info')
                            ->label('Stok Tersedia')
                            ->content(function (Forms\Get $get): string {
                                $barangId = $get('barang_id');
                                if (!$barangId) return 'Pilih barang terlebih dahulu';
                                
                                $barang = Barang::find($barangId);
                                return $barang ? $barang->stok_tersedia . ' unit' : '0 unit';
                            }),
                        
                        Forms\Components\TextInput::make('jumlah')
                            ->label('Jumlah Pinjam')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                    ])->columns(2),
                
                Forms\Components\Section::make('Tanggal & Keperluan')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_pinjam')
                            ->label('Tanggal Pinjam')
                            ->required()
                            ->default(now())
                            ->minDate(now())
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $set('tanggal_kembali_rencana', Carbon::parse($state)->addDays(7));
                                }
                            }),
                        
                        Forms\Components\DatePicker::make('tanggal_kembali_rencana')
                            ->label('Tanggal Rencana Kembali')
                            ->required()
                            ->minDate(fn (Get $get) => $get('tanggal_pinjam') ?: now()),
                        
                        Forms\Components\DatePicker::make('tanggal_kembali_aktual')
                            ->label('Tanggal Kembali Aktual')
                            ->hidden(fn (string $context) => $context === 'create'),
                          Forms\Components\Textarea::make('keperluan')
                            ->label('Keperluan/Tujuan Peminjaman')
                            ->required()
                            ->maxLength(500)
                            ->rows(3),
                    ])->columns(2),
                
                Forms\Components\Section::make('Biaya & Pembayaran')
                    ->schema([
                        Forms\Components\TextInput::make('total_biaya_sewa')
                            ->label('Total Biaya Sewa')
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly()
                            ->helperText('Otomatis dihitung berdasarkan durasi dan harga sewa'),
                        
                        Forms\Components\TextInput::make('total_deposit')
                            ->label('Total Deposit')
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly()
                            ->helperText('Otomatis dihitung berdasarkan jumlah barang'),
                        
                        Forms\Components\TextInput::make('denda_keterlambatan')
                            ->label('Denda Keterlambatan')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->helperText('5% per hari dari total biaya sewa'),
                        
                        Forms\Components\TextInput::make('total_pembayaran')
                            ->label('Total Pembayaran')
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly()
                            ->helperText('Total semua biaya'),
                        
                        Forms\Components\Select::make('payment_status')
                            ->label('Status Pembayaran')
                            ->options([
                                'pending' => 'Belum Bayar',
                                'paid' => 'Sudah Bayar',
                                'failed' => 'Gagal Bayar',
                            ])
                            ->default('pending'),
                        
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Tanggal Pembayaran')
                            ->hidden(fn (string $context) => $context === 'create'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Status & Catatan Admin')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                'pending' => 'Menunggu Persetujuan',
                                'disetujui' => 'Disetujui',
                                'ditolak' => 'Ditolak',
                                'dipinjam' => 'Sedang Dipinjam',
                                'dikembalikan' => 'Dikembalikan',
                            ])
                            ->default('pending'),
                        
                        Forms\Components\Textarea::make('catatan_admin')
                            ->label('Catatan Admin')
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('Catatan untuk peminjaman ini...'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_peminjaman')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('barang.nama')
                    ->label('Barang')
                    ->searchable()
                    ->wrap()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->alignCenter()
                    ->suffix(' unit'),
                
                Tables\Columns\TextColumn::make('tanggal_pinjam')
                    ->label('Tgl Pinjam')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('tanggal_kembali_rencana')
                    ->label('Tgl Rencana Kembali')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'disetujui' => 'info',
                        'ditolak' => 'danger',
                        'dipinjam' => 'success',
                        'dikembalikan' => 'gray',
                        default => 'gray',                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                        'dipinjam' => 'Dipinjam',
                        'dikembalikan' => 'Dikembalikan',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('total_pembayaran')
                    ->label('Total Biaya')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Status Bayar')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Belum Bayar',
                        'paid' => 'Sudah Bayar',
                        'failed' => 'Gagal Bayar',
                        default => $state,
                    })
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('terlambat')
                    ->label('Status Keterlambatan')
                    ->badge()
                    ->color(fn ($record): string => $record->terlambat ? 'danger' : 'success')
                    ->formatStateUsing(fn ($record): string => 
                        $record->status === 'dipinjam' 
                            ? ($record->terlambat ? 'Terlambat ' . $record->hari_terlambat . ' hari' : 'Tepat Waktu')
                            : '-'
                    )
                    ->visible(fn () => request()->has('tab') && request('tab') === 'dipinjam'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Menunggu Persetujuan',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                        'dipinjam' => 'Sedang Dipinjam',
                        'dikembalikan' => 'Dikembalikan',
                    ]),
                
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('barang')
                    ->relationship('barang', 'nama')
                    ->searchable()
                    ->preload(),
                
                Filter::make('terlambat')
                    ->label('Terlambat')
                    ->query(fn (Builder $query): Builder => $query->terlambat())
                    ->toggle(),
                
                Filter::make('tanggal_pinjam')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_pinjam', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_pinjam', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Peminjaman $record) {
                        $record->update(['status' => 'disetujui']);
                    })
                    ->visible(fn (Peminjaman $record): bool => $record->status === 'pending'),
                
                Tables\Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('catatan_admin')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (array $data, Peminjaman $record) {
                        $record->update([
                            'status' => 'ditolak',
                            'catatan_admin' => $data['catatan_admin']
                        ]);
                    })
                    ->visible(fn (Peminjaman $record): bool => $record->status === 'pending'),
                
                Tables\Actions\Action::make('pinjamkan')
                    ->label('Pinjamkan')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function (Peminjaman $record) {
                        $record->update(['status' => 'dipinjam']);
                    })
                    ->visible(fn (Peminjaman $record): bool => $record->status === 'disetujui'),
                  Tables\Actions\Action::make('kembalikan')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-left-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Peminjaman $record) {
                        $record->update([
                            'status' => 'dikembalikan',
                            'tanggal_kembali_aktual' => now()
                        ]);
                    })
                    ->visible(fn (Peminjaman $record): bool => $record->status === 'dipinjam'),
                
                Tables\Actions\Action::make('sync_payment')
                    ->label('Sync Payment')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (Peminjaman $record) {
                        if (!$record->midtrans_order_id) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error')
                                ->body('Tidak ada Order ID Midtrans untuk disinkronkan')
                                ->danger()
                                ->send();
                            return;
                        }

                        try {
                            \Midtrans\Config::$serverKey = config('midtrans.server_key');
                            \Midtrans\Config::$isProduction = config('midtrans.is_production');
                            
                            $status = \Midtrans\Transaction::status($record->midtrans_order_id);
                            $transactionStatus = $status->transaction_status ?? 'unknown';
                            
                            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                                $record->update([
                                    'payment_status' => 'paid',
                                    'paid_at' => now(),
                                    'status' => 'disetujui',
                                    'midtrans_response' => json_encode($status)
                                ]);
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Success')
                                    ->body('Status pembayaran berhasil disinkronkan: PAID')
                                    ->success()
                                    ->send();
                                    
                            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                                $record->update([
                                    'payment_status' => 'failed',
                                    'midtrans_response' => json_encode($status)
                                ]);
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Info')
                                    ->body('Status pembayaran berhasil disinkronkan: FAILED')
                                    ->warning()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('Info')
                                    ->body("Status pembayaran masih: {$transactionStatus}")
                                    ->info()
                                    ->send();
                            }
                            
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error')
                                ->body('Gagal sinkronisasi: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (Peminjaman $record): bool => 
                        $record->payment_status === 'pending' && !empty($record->midtrans_order_id)
                    ),
                
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('10s') // Fallback polling every 10 seconds
            ->deferLoading()
            ->striped();
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
            'index' => Pages\ListPeminjaman::route('/'),
            'create' => Pages\CreatePeminjaman::route('/create'),
            // 'view' => Pages\ViewPeminjaman::route('/{record}'),
            'edit' => Pages\EditPeminjaman::route('/{record}/edit'),
        ];
    }
}