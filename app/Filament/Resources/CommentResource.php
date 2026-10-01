<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Community Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Comment Information')->schema([
                    Forms\Components\Select::make('post_id')
                        ->relationship('post', 'title')
                        ->required()
                        ->searchable(),
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->required()
                        ->searchable(),
                    Forms\Components\Textarea::make('body')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('post.title')
                    ->label('Post')
                    ->searchable()
                    ->sortable()
                    ->limit(35),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Commenter')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('body')
                    ->label('Comment')
                    ->searchable()
                    ->limit(60),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted At')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Delete Spam')
                    ->modalHeading('Delete Spam Comment')
                    ->modalDescription('Are you sure you want to delete this comment? This action cannot be undone.')
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected (Spam)'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit'   => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
