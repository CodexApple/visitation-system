<?php

namespace App\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Livewire\Component;

class ChatShortcut extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected $listeners = ['refresh' => '$refresh'];

    public function teamChat(): Action
    {
        return Action::make('team_chat')
            ->color('info')
            ->label('Team Chat')
            ->icon('heroicon-s-chat-bubble-oval-left-ellipsis')
            ->keyBindings(['command+m', 'ctrl+m'])
            ->extraAttributes(['class' => 'w-full'])
            ->badge(auth()->user()->getUnreadCount())
            ->badgeColor(Color::Red)
            ->url(fn() => route('chats'));
    }

    public function userProfile(): ?Action
    {
        if (Filament::getCurrentPanel()?->getId() == 'admin') {
            return Action::make('user_profile')
            ->label('My Profile')
            ->outlined()
            ->color('gray')
            ->icon('heroicon-o-user')
            ->extraAttributes(['class' => 'w-full'])
            ->url(EditProfilePage::getUrl());
        }

        return null;
    }

    public function render(): string
    {
        return <<<'HTML'
            <div class="space-y-2">
                <div wire:poll.2s>
                    {{ $this->teamChat }}
                </div>

                {{ $this->userProfile()?->render() }}
            </div>
        HTML;
    }
}
