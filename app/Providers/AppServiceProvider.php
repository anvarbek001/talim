<?php

namespace App\Providers;

use App\Repositories\BookRepository;
use App\Repositories\Contracts\BookRepositoryInterface;
use App\Repositories\Contracts\DtmTestRepositoryInterface;
use App\Repositories\Contracts\LanguageExamTestRepositoryInterface;
use App\Repositories\Contracts\LessonRepositoryInterface;
use App\Repositories\Contracts\SectionRepositoryInterface;
use App\Repositories\Contracts\SertifikatTestRepositoryInterface;
use App\Repositories\Contracts\TopicRepositoryInterface;
use App\Repositories\Contracts\TopicTestRepositoryInterface;
use App\Repositories\DtmTestRepository;
use App\Repositories\LanguageExamTestRepository;
use App\Repositories\LessonRepository;
use App\Repositories\SectionRepository;
use App\Repositories\SertifikatTestRepository;
use App\Repositories\TopicRepository;
use App\Repositories\TopicTestRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SectionRepositoryInterface::class, SectionRepository::class);
        $this->app->bind(TopicRepositoryInterface::class, TopicRepository::class);
        $this->app->bind(LessonRepositoryInterface::class, LessonRepository::class);
        $this->app->bind(TopicTestRepositoryInterface::class, TopicTestRepository::class);
        $this->app->bind(DtmTestRepositoryInterface::class, DtmTestRepository::class);
        $this->app->bind(SertifikatTestRepositoryInterface::class, SertifikatTestRepository::class);
        $this->app->bind(LanguageExamTestRepositoryInterface::class, LanguageExamTestRepository::class);
        $this->app->bind(BookRepositoryInterface::class, BookRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('partials.pagination');
        Paginator::defaultSimpleView('partials.pagination');
    }
}
