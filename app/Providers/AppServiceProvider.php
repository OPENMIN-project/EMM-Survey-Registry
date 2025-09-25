<?php

namespace App\Providers;

use App\Observers\SurveyObserver;
use App\OptionsRepository;
use App\Survey;
use App\SurveyFieldsRepository;
use App\Vite;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    const NULL_VALUES = [
        null, '',
        -9, -99, -999,
        "-9", "-99", "-999",
        "Not applicable", "Don't know", "Information not available",
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SurveyFieldsRepository::class, function () {
            return SurveyFieldsRepository::boot();
        });
         $this->app->singleton('laravel-vite-manifest', function () {
            return new Vite;
        });
        $this->app->alias(SurveyFieldsRepository::class, 'fields-repository');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws \Exception
     */
    public function boot()
    {
        Collection::macro('whereRegex', function ($field, $expression) {
            /** @var Collection $this */
            return $this->filter(function ($item) use ($expression, $field) {
                return preg_match($expression, $item[$field]);
            });
        });

        // if (app()->runningUnitTests() || !app()->runningInConsole()) {
        try {
            $options = OptionsRepository::options();
            view()->share('options', $options);
        } catch (\Throwable $e) {
            logger()->warning('Failed to load options repository', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        Blade::if('valid', function ($value) {
            if (is_array($value)) {
                return array_reduce($value, function ($carry, $item) {
                    return !in_array($item, static::NULL_VALUES, true) ? $carry + 1 : $carry;
                }, 0);
            }

            return !in_array($value, static::NULL_VALUES, true);
        });
        Blade::directive('spaceless', function () {
            return '<?php ob_start() ?>';
        });
        Blade::directive('endspaceless', function ($expression) {
            $args = explode(', ', $expression);
            $regex = !empty($args[0]) ? $args[0] : "'/\s{2,}|\\r+|\\n+/'";
            $replace = !empty($args[1]) ? $args[1] : "''";
            return "<?php echo str_replace('&nbsp;', ' ', preg_replace({$regex},{$replace}, ob_get_clean())); ?>";
        });
        Blade::directive('noindent', function () {
            return '<?php ob_start() ?>';
        });
        Blade::directive('endnoindent', function ($expression) {
            $args = explode(',', $expression);
            $regex = $args[0] != '' ?: '/^\s+|(?<=\n)\s+/';
            $replace = $args[1] ?? '';
            return "<?php echo preg_replace('{$regex}','{$replace}',ob_get_clean()); ?>";
        });
        Blade::directive('vite', function ($entries) {
            $entries = explode(',', $entries);
            $entries = array_map(fn($e) => trim($e, "'\" "), $entries);
            $vite = new Vite();
            foreach($entries as $entry) {
                return $vite($entry);
            }
        });
        Survey::observe(SurveyObserver::class);
    }
}
