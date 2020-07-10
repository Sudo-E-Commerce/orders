<?php
 
namespace Sudo\Order\Providers;
 
use Illuminate\Support\ServiceProvider;
use File;
class OrderServiceProvider extends ServiceProvider
{
    /**
     * Register config file here
     * alias => path
     */
    private $configFile = [
        //
    ];

    /**
     * Register commands file here
     * alias => path
     */
    protected $commands = [
        //
    ];

    /**
     * Register middleare file here
     * name => middleware
     */
    protected $middleare = [
        //
    ];

	/**
     * Register bindings in the container.
     */
    public function register()
    {
        // Đăng ký config cho từng Module
        $this->mergeConfig();
        // boot commands
        $this->commands($this->commands);
    }

	public function boot()
	{
		$this->registerModule();

        $this->publish();

        $this->registerMiddleware();
	}

	private function registerModule() {
		$modulePath = __DIR__.'/../../';
        $moduleName = 'Order';

        // boot route
        if (File::exists($modulePath."routes/routes.php")) {
            $this->loadRoutesFrom($modulePath."/routes/routes.php");
        }

        // boot migration
        if (File::exists($modulePath . "migrations")) {
            $this->loadMigrationsFrom($modulePath . "migrations");
        }

        // boot languages
        if (File::exists($modulePath . "resources/lang")) {
            $this->loadTranslationsFrom($modulePath . "resources/lang", $moduleName);
            $this->loadJSONTranslationsFrom($modulePath . 'resources/lang');
        }

        // boot views
        if (File::exists($modulePath . "resources/views")) {
            $this->loadViewsFrom($modulePath . "resources/views", $moduleName);
        }

	    // boot all helpers
        if (File::exists($modulePath . "helpers")) {
            // get all file in Helpers Folder 
            $helper_dir = File::allFiles($modulePath . "helpers");
            // foreach to require file
            foreach ($helper_dir as $key => $value) {
                $file = $value->getPathName();
                require $file;
            }
        }
	}

    /*
    * publish dự án ra ngoài
    * publish config File
    * publish assets File
    */
    public function publish()
    {
        if ($this->app->runningInConsole()) {
            $assets = [
                //
            ];
            $config = [
                //
            ];
            $view = [
                //
            ];
            $all = array_merge($assets, $config, $view);
            // Chạy riêng
            $this->publishes($all, 'sudo/order');
            $this->publishes($assets, 'sudo/order/assets');
            $this->publishes($config, 'sudo/order/config');
            $this->publishes($view, 'sudo/order/view');
            // Khởi chạy chung theo core
            $this->publishes($all, 'sudo/core');
            $this->publishes($assets, 'sudo/core/assets');
            $this->publishes($config, 'sudo/core/config');
            $this->publishes($view, 'sudo/core/view');
        }
    }

    /*
    * Đăng ký config cho từng Module
    * $this->configFile
    */
    public function mergeConfig() {
        foreach ($this->configFile as $alias => $path) {
            $this->mergeConfigFrom(__DIR__ . "/../../config/" . $path, $alias);
        }
    }

    /**
     * Đăng ký Middleare
     */
    public function registerMiddleware()
    {
        foreach ($this->middleare as $key => $value) {
            $this->app['router']->pushMiddlewareToGroup($key, $value);
        }
    }
}