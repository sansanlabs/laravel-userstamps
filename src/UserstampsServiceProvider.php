<?php

namespace SanSanLabs\LaravelUserstamps;

use Illuminate\Support\ServiceProvider;
use SanSanLabs\LaravelUserstamps\Database\Schema\Macros\UserstampsMacro;

class UserstampsServiceProvider extends ServiceProvider {
  public function boot(): void {
    $this->publishes(
      [
        __DIR__ . "/../config/userstamps.php" => config_path("userstamps.php"),
      ],
      "sansanlabs-laravel-userstamps-config",
    );

    $userstampsMacro = new UserstampsMacro();
    $userstampsMacro->register();
  }

  public function register(): void {
    $this->mergeConfigFrom(__DIR__ . "/../config/userstamps.php", "userstamps");
  }
}
