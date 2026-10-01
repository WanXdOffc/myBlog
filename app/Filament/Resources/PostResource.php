<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Post Information')->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(Post::class, 'slug', ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Textarea::make('summary')
                            ->rows(3)
                            ->placeholder('Short summary of the post...')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
                ])->columnSpan(2),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Publishing & Metadata')->schema([
                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => auth()->id() ?? 1),

                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('tags')
                            ->label('Tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft'     => 'Draft',
                                'published' => 'Published',
                            ])
                            ->default('draft')
                            ->required(),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Published At')
                            ->default(now()),

                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Featured Image')
                            ->image()
                            ->directory('posts')
                            ->disk('public'),
                    ]),
                ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Image')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft'     => 'warning',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published At')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'published' => 'Published',
                    ]),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('publish')
                    ->label('Quick Publish')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->action(fn (Post $record) => $record->update([
                        'status'       => 'published',
                        'published_at' => $record->published_at ?? now(),
                    ]))
                    ->visible(fn (Post $record) => $record->status === 'draft')
                    ->requiresConfirmation('Are you sure you want to publish this post now?'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index'  => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit'   => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
