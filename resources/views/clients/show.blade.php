<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $client->first_name }} {{ $client->last_name }}
            </h2>
            <a href="{{ route('clients.edit', $client) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Edytuj
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Dane klienta --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Telefon</dt>
                        <dd class="text-lg font-medium">{{ $client->phone }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">E-mail</dt>
                        <dd class="text-lg">{{ $client->email ?? '—' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm text-gray-500">Komentarz</dt>
                        <dd class="mt-1 whitespace-pre-wrap">{{ $client->comment ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Utworzono</dt>
                        <dd class="text-sm">
                            {{ $client->created_at->timezone('Europe/Warsaw')->format('d.m.Y H:i') }}
                            @if($client->creator) przez {{ $client->creator->name }} @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Ostatnia zmiana</dt>
                        <dd class="text-sm">
                            {{ $client->updated_at->timezone('Europe/Warsaw')->format('d.m.Y H:i') }}
                            @if($client->updater) przez {{ $client->updater->name }} @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Historia komentarzy --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Historia komentarza</h3>

                @if($client->commentHistory->isEmpty())
                    <p class="text-gray-500">Brak historii zmian.</p>
                @else
                    <div class="space-y-4">
                        @foreach($client->commentHistory->sortByDesc('created_at') as $history)
                            <div class="border-l-4 border-blue-400 pl-4 py-2">
                                <div class="text-sm text-gray-500 mb-1">
                                    {{ \Carbon\Carbon::parse($history->created_at)->timezone('Europe/Warsaw')->format('d.m.Y H:i') }}
                                    — {{ $history->user->name ?? 'Nieznany' }}
                                </div>
                                @if($history->old_comment)
                                    <div class="text-sm text-red-600 line-through mb-1">{{ $history->old_comment }}</div>
                                @endif
                                <div class="text-sm">{{ $history->new_comment ?? '—' }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <a href="{{ route('clients.index') }}" class="text-gray-600 hover:underline">← Powrót do listy</a>
            </div>

        </div>
    </div>
</x-app-layout>