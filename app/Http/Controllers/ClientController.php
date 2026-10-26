<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\PhoneNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $phone = $request->input('phone');
        $page = max(1, (int) $request->input('page', 1));
        $perPage = 50;

        $clients = collect();
        $total = 0;
        $error = null;

        if ($phone !== null && $phone !== '') {
            $normalized = PhoneNormalizer::normalize($phone);

            if (strlen($normalized) < 3) {
                $error = 'Wpisz co najmniej 3 cyfry';
            } else {
                $reversed = PhoneNormalizer::reverse($normalized);

                $query = Client::query()
                    ->where(function ($q) use ($normalized, $reversed) {
                        $q->where('phone_normalized', 'like', $normalized . '%')
                          ->orWhere('phone_reversed', 'like', $reversed . '%')
                          ->orWhere('phone_normalized', 'like', '%' . $normalized . '%');
                    })
                    ->orderByRaw("
                        CASE
                            WHEN phone_normalized LIKE ? THEN 1
                            WHEN phone_reversed LIKE ? THEN 2
                            ELSE 3
                        END
                    ", [$normalized . '%', $reversed . '%'])
                    ->orderBy('last_name')
                    ->orderBy('first_name')
                    ->orderBy('id');

                $total = $query->count();
                $clients = $query->skip(($page - 1) * $perPage)->take($perPage)->get();
            }
        }

        return view('clients.index', compact('clients', 'phone', 'page', 'perPage', 'total', 'error'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'phone'      => 'required|string|min:3|max:30',
            'email'      => 'nullable|email|max:255',
            'comment'    => 'nullable|string|max:10000',
        ]);

        $normalized = PhoneNormalizer::normalize($validated['phone']);

        // Ostrzeżenie o duplikacie
        $existing = Client::where('phone_normalized', $normalized)->first();

        $client = Client::create([
            'first_name'        => $validated['first_name'],
            'last_name'         => $validated['last_name'],
            'phone'             => $validated['phone'],
            'phone_normalized'  => $normalized,
            'phone_reversed'    => PhoneNormalizer::reverse($normalized),
            'email'             => $validated['email'] ?? null,
            'comment'           => $validated['comment'] ?? null,
            'owner_user_id'     => Auth::id(),
            'created_by'        => Auth::id(),
            'updated_by'        => Auth::id(),
        ]);

        // Zapis pierwszego wpisu historii komentarza
        if (!empty($validated['comment'])) {
            DB::table('client_comment_history')->insert([
                'client_id'   => $client->id,
                'user_id'     => Auth::id(),
                'old_comment' => null,
                'new_comment' => $validated['comment'],
                'ip_address'  => inet_pton($request->ip()),
                'created_at'  => now(),
            ]);
        }

        $message = 'Klient został dodany.';
        if ($existing) {
            $message .= ' Uwaga: istnieje już klient z tym numerem telefonu (ID: ' . $existing->id . ').';
        }

        return redirect()->route('clients.show', $client)->with('success', $message);
    }

    public function show(Client $client)
{
    \App\Models\ActivityLog::create([
        'user_id'     => auth()->id(),
        'event_type'  => 'client_view',
        'client_id'   => $client->id,
        'url'         => request()->fullUrl(),
        'http_method' => request()->method(),
        'ip_address'  => inet_pton(request()->ip()),
        'user_agent'  => request()->userAgent(),
        'created_at'  => now(),
    ]);

    $client->load(['creator', 'updater', 'commentHistory.user']);
    return view('clients.show', compact('client'));
}

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'phone'      => 'required|string|min:3|max:30',
            'email'      => 'nullable|email|max:255',
            'comment'    => 'nullable|string|max:10000',
        ]);

        $normalized = PhoneNormalizer::normalize($validated['phone']);
        $oldComment = $client->comment;
        $newComment = $validated['comment'] ?? null;

        $client->update([
            'first_name'        => $validated['first_name'],
            'last_name'         => $validated['last_name'],
            'phone'             => $validated['phone'],
            'phone_normalized'  => $normalized,
            'phone_reversed'    => PhoneNormalizer::reverse($normalized),
            'email'             => $validated['email'] ?? null,
            'comment'           => $newComment,
            'updated_by'        => Auth::id(),
        ]);

        // Historia komentarza tylko przy zmianie
        if ($oldComment !== $newComment) {
            DB::table('client_comment_history')->insert([
                'client_id'   => $client->id,
                'user_id'     => Auth::id(),
                'old_comment' => $oldComment,
                'new_comment' => $newComment,
                'ip_address'  => inet_pton($request->ip()),
                'created_at'  => now(),
            ]);
        }

        return redirect()->route('clients.show', $client)->with('success', 'Klient został zaktualizowany.');
    }
}