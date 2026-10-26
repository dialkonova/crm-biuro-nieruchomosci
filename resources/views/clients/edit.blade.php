<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edytuj klienta: {{ $client->first_name }} {{ $client->last_name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('clients.update', $client) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="first_name" value="Imię *" />
                            <x-text-input id="first_name" name="first_name" class="block mt-1 w-full" :value="old('first_name', $client->first_name)" required />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="last_name" value="Nazwisko *" />
                            <x-text-input id="last_name" name="last_name" class="block mt-1 w-full" :value="old('last_name', $client->last_name)" required />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="phone" value="Telefon *" />
                            <x-text-input id="phone" name="phone" class="block mt-1 w-full" :value="old('phone', $client->phone)" required />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="E-mail" />
                            <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email', $client->email)" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-input-label for="comment" value="Komentarz" />
                        <textarea id="comment" name="comment" rows="5" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('comment', $client->comment) }}</textarea>
                        <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('clients.show', $client) }}" class="text-gray-600 hover:underline">Anuluj</a>
                        <x-primary-button>Zapisz zmiany</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>