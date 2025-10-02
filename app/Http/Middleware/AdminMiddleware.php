<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan pengguna terautentikasi DAN memiliki role = 1 (Admin)
        if (auth()->check() && auth()->user()->role === 1) {
            return $next($request);
        }

        // Jika tidak, arahkan kembali ke dashboard dengan pesan error
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki hak akses untuk halaman ini.');
    }
}