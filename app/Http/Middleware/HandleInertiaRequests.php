<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use App\Models\Manufacturer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $currentUrl = URL::current();
        $previousUrl = URL::previous();

        if ($previousUrl && $previousUrl != $currentUrl) {
            session(['inertia_prev_url' =>  $previousUrl]);
        } else {
            $previousUrl = session('inertia_prev_url');
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
                'permissions' => User::permissions(),
            ],
            'query' => $request->query(),
            'currentUrl' => $currentUrl,
            'previousUrl' => $previousUrl,
            'manufacturerId' => Helper::get_current(),
            'manufacturerList' => Manufacturer::all(),
        ];
    }
}
