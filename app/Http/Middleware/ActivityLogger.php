<?php

namespace App\Http\Middleware;

use App\Models\Activity;
use Closure;
use Illuminate\Http\Request;

class ActivityLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !session()->has('login_activity')) {
            $activity = new Activity;
            $activity->user = auth()->user()->name;
            $activity->description = 'Login';
            $activity->status = 'text-primary';
            $activity->save();

            session(['login_activity' => true]);
        }

        if ($request->isMethod('post')) {
            $activity = new Activity;
            $activity->user = auth()->user()->name;

            $pathWithoutNumbers = preg_replace('/\d+/', '', $request->path());
            $path1 = str_replace(['/', '-', '_', '%'], ' ', $pathWithoutNumbers);
            $path = str_replace('20', '', $path1);
            $activity->description = $path;

            $activity->status = 'text-warning';

            $pathSegments = explode('/', $request->path());
            $secondSegment = isset($pathSegments[1]) ? $pathSegments[1] : null;
            $activity->tabel = $secondSegment;
            $activity->save();
        }

        if ($request->isMethod('delete')) {
            $activity = new Activity;
            $activity->user = auth()->user()->name;
            $path1 = str_replace(['/', '-', '_', '%'], ' ', $request->path());
            $path = str_replace('20', '', $path1);
            $activity->description = $path;
            $activity->status = 'text-danger';
            $activity->save();
        }

        return $next($request);
    }
}
