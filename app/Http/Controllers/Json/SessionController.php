<?php

namespace App\Http\Controllers\Json;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Queries\IndexSessionQuery;

class SessionController extends Controller
{
    public function __construct(IndexSessionQuery $indexSessionQuery)
    {
        $this->indexSessionQuery = $indexSessionQuery;
    }

    public function index()
    {
        $page = request('page') ?? 1;

        $sessions = $this->indexSessionQuery
            ->offset(request('per-page') * $page)
            ->paginate(request('per-page'))
            ->execute();

        return response()->json([
            'days' => $sessions
                ->groupBy(function ($session) {
                    return $session->localStartedAt->format('Y-m-d');
                })->map(function ($sessionsOnDay, $date) {
                    return [
                        'date'     => $date,
                        'sessions' => $sessionsOnDay,
                    ];
                })->values(),
            'total'       => $total = Session::count(),
            'perPage'     => request('per-page'),
            'page'        => $page,
            'lastPage'    => request('per-page') ? ceil($total / request('per-page')) : 1,
        ]);
    }
}
