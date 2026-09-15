<?php

namespace App\Livewire\Concerns;

use App\Models\Investor;

/**
 * Lets a Livewire component be registered under both the admin and the
 * investor panel routes, rendering with the right layout and generating
 * links with the right route name prefix depending on who is viewing it.
 */
trait ResolvesPanelContext
{
    protected function panelPrefix(): string
    {
        return auth()->user()->isAdmin() ? 'admin.' : 'investitor.';
    }

    protected function panelLayout(): string
    {
        return auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.investor';
    }

    /**
     * Admins browse investors, so the "back to investor" breadcrumb goes to
     * that investor's detail page. Investors only ever see their own data,
     * so it goes back to their own dashboard instead.
     */
    protected function investorHomeUrl(Investor $investor): string
    {
        return auth()->user()->isAdmin()
            ? route('admin.investors.show', $investor)
            : route('investitor.dashboard');
    }
}
