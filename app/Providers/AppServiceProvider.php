<?php

namespace App\Providers;

use App\Contracts\ISezrisService;
use App\Services\ApplicationService;
use App\Services\UploadService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void {}

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    //access cco only
    Gate::define('access-cco', function ($user) {
      return optional($user->details)->position_id === 37 &&
        optional($user->details)->department_id === 12 &&
        optional($user->details)->role_id === 2 &&
        optional($user->details)->permission_id === 2;
    });
    //osac access only
    Gate::define('access-osac', function ($user) {
      return optional($user->details)->position_id === 36 &&
        optional($user->details)->department_id === 12 &&
        optional($user->details)->role_id === 2 &&
        optional($user->details)->permission_id === 2;
    });
    //locator access
    Gate::define('access-locator', function ($user) {
      return  optional($user->details)->role_id == 3 &&
        optional($user->details)->permission_id == 2;
    });
    Gate::define('access-sezadManager', function ($user) {
      return optional($user->details)->position_id == 50  &&
        optional($user->details)->department_id === 12 &&
        optional($user->details)->user_function_id === 5 &&
        optional($user->details)->role_id === 2 &&
        optional($user->details)->permission_id === 1;
    });
    Gate::define('access-finance', function ($user) {
      return optional($user->details)->position_id == null  &&
        optional($user->details)->department_id === 10 &&
        optional($user->details)->user_function_id === null &&
        optional($user->details)->role_id === 2 &&
        optional($user->details)->permission_id === 2;
    });

    // Superadmin / Admin
    Gate::define(
      'is-admin',
      fn($user) =>
      optional($user->details)->role_id === 1 &&
        optional($user->details)->permission_id === 1 &&
        optional($user->details)->department_id === 9 &&
        optional($user->details)->division_id === 3 &&
        optional($user->details)->user_function_id === null
    );

    // SEZAD Accreditation
    // Gate::define(
    //   'sezad-accreditation',
    //   fn($user) =>
    //   optional($user->details)->role_id === 2 &&
    //     optional($user->details)->permission_id === 2 &&
    //     optional($user->details)->department_id === 12 &&
    //     optional($user->details)->division_id === null &&
    //     optional($user->details)->user_function_id === 1
    // );

    // SEZAD Customs
    Gate::define(
      'sezad-customs',
      fn($user) =>
      optional($user->details)->role_id === 2 &&
        optional($user->details)->permission_id === 2 &&
        optional($user->details)->department_id === 12 &&
        optional($user->details)->division_id === null &&
        optional($user->details)->user_function_id === 2
    );

    // SEZAD Labor
    Gate::define(
      'sezad-labor',
      fn($user) =>
      optional($user->details)->role_id === 2 &&
        optional($user->details)->permission_id === 2 &&
        optional($user->details)->department_id === 12 &&
        optional($user->details)->division_id === null &&
        optional($user->details)->user_function_id === 3
    );

    // SEZAD OSAC
   
    Gate::define(
      'sezad-osac',
      fn($user) =>
      optional($user->details)->position_id === 36 &&
      optional($user->details)->role_id === 2 &&
        optional($user->details)->permission_id === 2 &&
        optional($user->details)->department_id === 12 &&
        optional($user->details)->division_id === null &&
        optional($user->details)->user_function_id === 4
    );
    
    // SEZAD Manager
    Gate::define(
      'sezad-manager',
      fn($user) =>
      optional($user->details)->role_id === 2 &&
        optional($user->details)->permission_id === 2 &&
        optional($user->details)->department_id === 12 &&
        optional($user->details)->division_id === null &&
        optional($user->details)->user_function_id === 5
    );

    Gate::define(
      'accreditation-spsnbe',
      fn($user) =>
      optional($user->details)->role_id === 4 &&
        optional($user->details)->department_id === 12

    );

    Gate::define(
      'accreditation-ceoc',
      fn($user) =>
      optional($user->details)->role_id === 5 &&
        optional($user->details)->department_id === 12

    );

    Gate::define(
      'accreditation-tfbosta',
      fn($user) =>
      optional($user->details)->role_id === 6 &&
        optional($user->details)->department_id === 12

    );
    Gate::define(
      'accreditation-vme',
      fn($user) =>
      optional($user->details)->role_id === 7 &&
        optional($user->details)->department_id === 12

    );

    Gate::define(
      'accreditation-provitional',
      fn($user) =>
      optional($user->details)->role_id === 8 &&
        optional($user->details)->department_id === 12

    );




    $roles = [
      'isAdmin' => 'is-admin',
      'sezadManager' => 'sezad-manager',
      'sezadOSAC' => 'sezad-osac',
      'sezadLabor' => 'sezad-labor',
      'sezadCustoms' => 'sezad-customs',
      'accreditationSpsnbe' => 'accreditation-spsnbe',
      'accreditationCeoc' => 'accreditation-ceoc',
      'accreditationTfbosta' => 'accreditation-tfbosta',
      'accreditationVme' => 'accreditation-vme',
      'accreditationProvitional' => 'accreditation-provitional',
    ];

    Inertia::share([
      'permissions' => function () use ($roles) {
        return collect($roles)->mapWithKeys(fn($gate, $key) => [$key => Gate::allows($gate)])->toArray();

      }
      
    ]);
  }
}
