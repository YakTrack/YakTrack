<?php

namespace App\Http\Controllers\Json;

use App\Http\Controllers\Controller;
use App\Models\Queries\IndexSessionQuery;
use App\Models\Session;
use Illuminate\Http\Request;

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
                        'date'                      => $date,
                        'sessions'                  => $sessionsOnDay,
                        'totalDurationForHumans'    => $sessionsOnDay->totalDurationForHumans(),
                    ];
                })->values(),
            'total'       => $total = Session::count(),
            'perPage'     => request('per-page'),
            'page'        => $page,
            'lastPage'    => request('per-page') ? ceil($total / request('per-page')) : 1,
        ]);
    }
}
