<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\VendorBankAccount;
use App\Policies\VendorBankAccountPolicy;
use App\Models\VendorCompany;
use App\Policies\VendorCompanyPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        VendorBankAccount::class => VendorBankAccountPolicy::class,
        VendorCompany::class => VendorCompanyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
