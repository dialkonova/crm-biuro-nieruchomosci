<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Klienci
            </h2>
            <a href="{{ route('clients.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                + Dodaj klienta
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Wyszukiwarka --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('clients.index') }}" class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Szukaj po numerze telefonu</label>
                        <input type="text" name="phone" value="{{ $phone }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm" 
                               placeholder="np. 600 123 456" autofocus>
                    </div>
                    <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded hover:bg-gray-700">
                        Szukaj
                    </button>
                </form>

                @if($error)
                    <p class="mt-3 text-red-600">{{ $error }}</p>
                @endif
            </div>

            {{-- Wyniki --}}
            @if($phone && !$error)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b text-sm text-gray-600">
                        Znaleziono: <strong>{{ $total }}</strong> wyników
                    </div>

                    @if($clients->isEmpty())
                        <div class="p-6 text-gray-500">Brak wyników.</div>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Imię i nazwisko</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefon</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-mail</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Komentarz</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ostatnia zmiana</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($clients as $client)
                                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('clients.show', $client) }}'">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $client->first_name }} {{ $client->last_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $client->phone }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $client->email ?? '—' }}</td>
                                        <td class="px-6 py-4">{{ Str::limit($client->comment, 100) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $client->updated_at->timezone('Europe/Warsaw')->format('d.m.Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Paginacja --}}
                        @if($total > $perPage)
                            <div class="p-4 flex justify-center gap-2">
                                @php $lastPage = ceil($total / $perPage); @endphp
                                @if($page > 1)
                                    <a href="?phone={{ urlencode($phone) }}&page={{ $page - 1 }}" class="px-3 py-1 border rounded">«</a>
                                @endif
                                <span class="px-3 py-1">Strona {{ $page }} z {{ $lastPage }}</span>
                                @if($page < $lastPage)
                                    <a href="?phone={{ urlencode($phone) }}&page={{ $page + 1 }}" class="px-3 py-1 border rounded">»</a>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>