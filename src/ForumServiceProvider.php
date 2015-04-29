<?php namespace BishopB\Forum;

use Illuminate\Support\ServiceProvider;
use \Illuminate\Contracts\Auth\Authenticatable as AppUser;

class ForumServiceProvider extends ServiceProvider
{
    /**
     * Routing is about to happen, define things we'll need for routing.
     *
     * @return void
     */
    public function boot()
    {
        // Removed this line for Lara5
        //$this->package('bishopb/laravel-forums', 'forum', __DIR__);
        $this->publishes([
            realpath(__DIR__.'/migrations') => $this->app->databasePath().'/migrations',
        ]);

        require_once __DIR__ . '/boot/helpers.php';
        require_once __DIR__ . '/boot/routes.php';

        $this->commands('forum::commands.migrate', 'forum::commands.connect');
    }

    /**
     * Register the service provider. Keep it fast.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(
            'Felixkiss\UniqueWithValidator\UniqueWithValidatorServiceProvider'
        );

        // providers
        $this->app->bind(
            'BishopB\Forum\UserMapperInterface',
            'BishopB\Forum\UserMapperAllGuestAccess'
        );

        // commands
        $this->app['forum::commands.migrate'] = $this->app->share(function ($app) {
            return new VanillaMigrate();
        });
        $this->app['forum::commands.connect'] = $this->app->share(function ($app) {
            return new VanillaConnect();
        });
    }

    /**
     * We have views and configuration: can't defer.
     *
     * @var bool
     */
    protected $defer = false;
}
