<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PinoycoopPageController extends Controller
{
    public function home(): View
    {
        return view('pinooycoop.pages.index', [
            'latestNews' => $this->newsQuery()
                ->limit(5)
                ->get(['id', 'title', 'slug', 'subcontext', 'template', 'published_at', 'updated_at', 'content', 'image_blob', 'image_mime']),
            'homeCounters' => Cache::get('cms.home_counters', $this->defaultHomeCounters()),
        ]);
    }

    public function about(): View
    {
        return view('pinooycoop.pages.about');
    }

    public function service(): View
    {
        return view('pinooycoop.pages.service');
    }

    public function contact(): View
    {
        return view('pinooycoop.pages.contact');
    }

    public function storeContactMessage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::create([
            ...$data,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('status', 'Your message has been sent.');
    }

    public function events(): View
    {
        return view('pinooycoop.pages.events', [
            'items' => $this->newsQuery()
                ->limit(30)
                ->get(['id', 'title', 'slug', 'subcontext', 'template', 'published_at', 'updated_at', 'content', 'image_blob', 'image_mime']),
        ]);
    }

    public function eventCategory(Request $request, string $category): View
    {
        $categories = [
            'headlines' => [
                'label' => 'Headlines',
                'templates' => ['headline'],
                'description' => 'Lead stories and top updates from MASS-SPECC.',
            ],
            'featured-news' => [
                'label' => 'Featured News',
                'templates' => ['feature_story'],
                'description' => 'Feature stories and highlighted updates from MASS-SPECC.',
            ],
            'standard-news-short-brief' => [
                'label' => 'Standard News & Short Brief',
                'templates' => ['standard_news', 'news', 'short_brief'],
                'description' => 'Recent standard news items and short briefs from MASS-SPECC.',
            ],
        ];

        abort_unless(isset($categories[$category]), 404);

        $filters = $request->validate([
            'date' => ['nullable', 'date'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'day' => ['nullable', 'integer', 'between:1,31'],
            'year' => ['nullable', 'integer', 'between:1900,2100'],
        ]);

        $selectedDate = $filters['date'] ?? null;
        if (! $selectedDate && ! empty($filters['month']) && ! empty($filters['day']) && ! empty($filters['year'])) {
            $candidate = sprintf('%04d-%02d-%02d', $filters['year'], $filters['month'], $filters['day']);
            $selectedDate = checkdate((int) $filters['month'], (int) $filters['day'], (int) $filters['year']) ? $candidate : null;
        }

        $items = Page::query()
            ->where('is_published', true)
            ->whereIn('template', $categories[$category]['templates'])
            ->when($selectedDate, fn ($query, string $date) => $query->whereDate('published_at', $date))
            ->when(! $selectedDate && ! empty($filters['month']), fn ($query) => $query->whereMonth('published_at', $filters['month']))
            ->when(! $selectedDate && ! empty($filters['day']), fn ($query) => $query->whereDay('published_at', $filters['day']))
            ->when(! $selectedDate && ! empty($filters['year']), fn ($query) => $query->whereYear('published_at', $filters['year']))
            ->orderByDesc('published_at')
            ->orderByDesc('updated_at')
            ->paginate(12, ['id', 'title', 'slug', 'subcontext', 'template', 'published_at', 'updated_at', 'content', 'image_blob', 'image_mime'])
            ->appends($request->query());

        return view('pinooycoop.pages.events-category', [
            'category' => $categories[$category],
            'categorySlug' => $category,
            'items' => $items,
            'selectedDate' => $selectedDate,
            'selectedMonth' => $selectedDate ? (int) Carbon::parse($selectedDate)->format('n') : ($filters['month'] ?? null),
            'selectedDay' => $selectedDate ? (int) Carbon::parse($selectedDate)->format('j') : ($filters['day'] ?? null),
            'selectedYear' => $selectedDate ? (int) Carbon::parse($selectedDate)->format('Y') : ($filters['year'] ?? null),
            'hasDateFilter' => $selectedDate || ! empty($filters['month']) || ! empty($filters['day']) || ! empty($filters['year']),
        ]);
    }

    public function cmsPage(string $slug): View
    {
        $page = Page::query()
            ->with('subImages')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('pinooycoop.pages.cms-page', [
            'page' => $page,
            'recentPosts' => $this->newsQuery()
                ->where('id', '!=', $page->id)
                ->limit(4)
                ->get(['id', 'title', 'slug', 'subcontext', 'content', 'template', 'published_at', 'updated_at', 'image_blob', 'image_mime']),
        ]);
    }

    public function blog(): View
    {
        return view('pinooycoop.pages.blog', [
            'articles' => $this->sampleArticles(),
        ]);
    }

    public function blogSingle(): View
    {
        return view('pinooycoop.pages.blog-single', [
            'article' => [
                'title' => 'Empowering Cooperatives: MASS-SPECC\'s Strategic Initiatives for 2024',
                'image' => 'images/blog/blog-lg.jpg',
                'author' => 'John Mackel',
                'date' => '19 June 2024',
                'comments_count' => 12,
                'read_time' => '8 min read',
                'content' => 'MASS-SPECC continues to lead the way in empowering cooperatives across the Philippines through innovative programs, strategic partnerships, and community-driven initiatives that create lasting positive impact in communities.',
                'tags' => ['Cooperatives', 'Development', 'Strategy', 'Innovation', 'Community'],
            ],
        ]);
    }

    public function homeTwo(): View
    {
        return view('pinooycoop.pages.index-2');
    }

    public function homeThree(): View
    {
        return view('pinooycoop.pages.index-3');
    }

    private function newsQuery()
    {
        return Page::query()
            ->where('is_published', true)
            ->whereIn('template', ['headline', 'feature_story', 'standard_news', 'short_brief', 'event', 'news'])
            ->orderByDesc('published_at')
            ->orderByDesc('updated_at');
    }

    private function defaultHomeCounters(): array
    {
        return [
            ['icon' => 'icofont-heart', 'value' => 460, 'label' => 'Our Happy Clients'],
            ['icon' => 'icofont-rocket', 'value' => 60, 'label' => 'Projects Done'],
            ['icon' => 'icofont-hand-power', 'value' => 30, 'label' => 'Experienced stuff'],
            ['icon' => 'icofont-shield-alt', 'value' => 25, 'label' => 'Ongoning Projects'],
        ];
    }

    private function sampleArticles(): array
    {
        return [
            ['id' => 1, 'title' => 'Empowering Cooperatives: MASS-SPECC\'s Strategic Initiatives for 2024', 'image' => 'images/blog/blog-lg.jpg', 'author' => 'John Mackel', 'date' => '19 Jun 2024', 'comments_count' => 12, 'excerpt' => 'Discover how MASS-SPECC is revolutionizing the cooperative sector through innovative programs, strategic partnerships, and community-driven initiatives.', 'category' => 'Featured', 'slug' => 'empowering-cooperatives-2024'],
            ['id' => 2, 'title' => 'Digital Transformation in Cooperative Marketing', 'image' => 'images/blog/blog-1.jpg', 'author' => 'Jane Doe', 'date' => '15 Jun 2024', 'comments_count' => 8, 'excerpt' => 'Learn how digital tools are transforming the way cooperatives market their products and services.', 'category' => 'Marketing', 'slug' => 'digital-transformation-marketing'],
            ['id' => 3, 'title' => 'Building Sustainable Cooperative Enterprises', 'image' => 'images/blog/blog-2.jpg', 'author' => 'Mark Smith', 'date' => '12 Jun 2024', 'comments_count' => 6, 'excerpt' => 'Strategies for creating long-term sustainability in cooperative business models.', 'category' => 'Development', 'slug' => 'sustainable-cooperative-enterprises'],
            ['id' => 4, 'title' => 'Capacity Building Programs for Cooperative Leaders', 'image' => 'images/blog/blog-3.jpg', 'author' => 'Sarah Johnson', 'date' => '10 Jun 2024', 'comments_count' => 15, 'excerpt' => 'Comprehensive training programs designed to enhance leadership skills in the cooperative sector.', 'category' => 'Training', 'slug' => 'capacity-building-programs'],
            ['id' => 5, 'title' => 'Technology-Driven Solutions for Modern Cooperatives', 'image' => 'images/blog/blog-4.jpg', 'author' => 'Michael Brown', 'date' => '08 Jun 2024', 'comments_count' => 9, 'excerpt' => 'Exploring cutting-edge technologies that are reshaping the cooperative landscape.', 'category' => 'Innovation', 'slug' => 'technology-driven-solutions'],
        ];
    }
}
