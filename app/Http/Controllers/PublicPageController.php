<?php

namespace App\Http\Controllers;

use App\Actions\SubmitApplicationAction;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\StoreContactMessageRequest;
use App\Http\Resources\ProgramResource;
use App\Models\AboutSection;
use App\Models\AdmissionMilestone;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\SiteContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    private function site(): array
    {
        try {
            if (! Schema::hasTable('site_contents')) {
                return [];
            }

            return SiteContent::get()
                ->mapWithKeys(fn ($item) => [
                    $item->key => $item->type === 'image' && $item->value ? asset('storage/'.$item->value) : $item->value,
                ])
                ->toArray();
        } catch (\Throwable) {
            return [];
        }
    }

    private function imageUrl(?string $path): ?string
    {
        return $path ? asset('storage/'.$path) : null;
    }

    private function view(string $name, array $data = []): View
    {
        $site = $data['site'] ?? $this->site();
        $college = $site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science';

        return view($name, array_merge([
            'site' => $site,
            'collegeName' => $college,
            'metaTitle' => $data['metaTitle'] ?? $college,
            'metaDescription' => $data['metaDescription'] ?? ($site['footer_description'] ?? 'Professional nursing and allied health sciences education for compassionate healthcare careers.'),
            'metaImage' => $data['metaImage'] ?? ($site['hero_image'] ?? asset('logo.png')),
            'canonical' => url()->current(),
            'errors' => session('errors', new ViewErrorBag),
        ], $data));
    }

    public function home(): View
    {
        $site = $this->site();
        $programs = ProgramResource::collection(
            Program::with(['fees' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
        )->resolve();
        $faculty = Faculty::where('is_active', true)->orderBy('sort_order')->limit(4)->get();
        $events = Event::where('is_published', true)->orderByDesc('event_date')->orderByDesc('created_at')->limit(3)->get();
        $milestones = AdmissionMilestone::orderBy('sort_order')->get();

        return $this->view('public.home', [
            'site' => $site,
            'programs' => $programs,
            'faculty' => $faculty,
            'events' => $events,
            'milestones' => $milestones,
            'metaTitle' => ($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science').' | Admissions Open',
        ]);
    }

    public function about(): View
    {
        $site = $this->site();
        $sections = AboutSection::where('is_active', true)->orderBy('sort_order')->get();

        return $this->view('public.about', [
            'site' => $site,
            'sections' => $sections,
            'metaTitle' => 'About | '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science'),
            'metaDescription' => $site['about_text'] ?? 'Learn about '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science').', our values, clinical facilities, and academic leadership.',
        ]);
    }

    public function programs(): View
    {
        $site = $this->site();
        $programs = ProgramResource::collection(
            Program::with(['fees' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
        )->resolve();

        return $this->view('public.programs', [
            'site' => $site,
            'programs' => $programs,
            'metaTitle' => 'Programs | '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science'),
            'metaDescription' => 'Explore professional nursing and allied health science academic programs at '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science').'.',
        ]);
    }

    public function faculty(): View
    {
        $site = $this->site();
        $faculty = Faculty::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return $this->view('public.faculty', [
            'site' => $site,
            'faculty' => $faculty,
            'metaTitle' => 'Faculty | '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science'),
            'metaDescription' => 'Meet the dedicated faculty members and clinical instructors at '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science').'.',
        ]);
    }

    public function news(Request $request): View
    {
        $site = $this->site();
        $search = trim((string) $request->query('search'));
        $events = Event::where('is_published', true)
            ->when($search, fn ($query) => $query->where(fn ($inner) => $inner
                ->where('title', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%")
                ->orWhere('body', 'like', "%{$search}%")))
            ->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return $this->view('public.news-index', [
            'site' => $site,
            'events' => $events,
            'search' => $search,
            'metaTitle' => 'News & Events | '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science'),
            'metaDescription' => 'Read the latest and previous news, announcements, and events from '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science').'.',
        ]);
    }

    public function newsShow(string $slug): View
    {
        $site = $this->site();
        $event = Event::where('slug', $slug)->where('is_published', true)->firstOrFail();
        $events = Event::where('is_published', true)
            ->where('id', '!=', $event->id)
            ->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return $this->view('public.news-show', [
            'site' => $site,
            'event' => $event,
            'events' => $events,
            'metaTitle' => $event->title.' | '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science'),
            'metaDescription' => $event->excerpt ?: str($event->body)->limit(155)->toString(),
            'metaImage' => $this->imageUrl($event->image_path) ?? ($site['hero_image'] ?? asset('logo.png')),
        ]);
    }

    public function apply(): View
    {
        $site = $this->site();
        $programs = Program::with(['fees' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return $this->view('public.apply', [
            'site' => $site,
            'programs' => $programs,
            'metaTitle' => 'Online Application | '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science'),
            'metaDescription' => 'Submit your online admission application to '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science').'.',
        ]);
    }

    public function applyStore(StoreApplicationRequest $request, SubmitApplicationAction $action)
    {
        $application = $action->execute($request);

        return redirect()->to(url('/apply'))->with('success_reference', $application->reference);
    }

    public function contact(): View
    {
        $site = $this->site();

        return $this->view('public.contact', [
            'site' => $site,
            'metaTitle' => 'Contact | '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science'),
            'metaDescription' => 'Contact '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science').' admissions, student support, and main office.',
        ]);
    }

    public function contactStore(StoreContactMessageRequest $request)
    {
        ContactMessage::create($request->validated());

        return back()->with('success', 'Your message was sent successfully. We will be in touch soon.');
    }

    public function admissions(): View
    {
        $site = $this->site();
        $defaultSteps = [
            ['title' => 'Choose Your Program', 'desc' => 'Explore our recognized nursing and allied health science programs to find your ideal pathway.', 'icon' => 'bi-search-heart'],
            ['title' => 'Review Eligibility', 'desc' => 'Check your academic credentials and documentation to ensure you meet all qualification criteria.', 'icon' => 'bi-clipboard-check'],
            ['title' => 'Submit Online Application', 'desc' => 'Complete the online application form and upload scanned copies of your transcripts and identity card.', 'icon' => 'bi-cloud-arrow-up'],
            ['title' => 'Interview & Enrollment', 'desc' => 'Attend the academic interview, verify your original documents, and secure your seat upon acceptance.', 'icon' => 'bi-mortarboard'],
        ];
        $milestones = AdmissionMilestone::orderBy('sort_order')->get();
        $steps = $milestones->isNotEmpty() ? $milestones : collect($defaultSteps);

        $programs = Program::with(['fees' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return $this->view('public.admissions', [
            'site' => $site,
            'steps' => $steps,
            'programs' => $programs,
            'metaTitle' => 'Admissions & Enrollment | '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science'),
            'metaDescription' => 'Explore admission criteria, eligibility requirements, step-by-step application process, and scholarships at '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science').'.',
        ]);
    }

    public function campusLife(): View
    {
        $site = $this->site();
        $faculty = Faculty::where('is_active', true)->orderBy('sort_order')->limit(4)->get();
        $events = Event::where('is_published', true)->orderByDesc('event_date')->limit(3)->get();

        return $this->view('public.campus-life', [
            'site' => $site,
            'faculty' => $faculty,
            'events' => $events,
            'metaTitle' => 'Campus Life & Clinical Facilities | '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science'),
            'metaDescription' => 'Discover student life, modern nursing simulation laboratories, hospital clinical training, and campus facilities at '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science').'.',
        ]);
    }

    public function trackApplication(Request $request): View
    {
        $site = $this->site();
        $query = trim((string) $request->query('query', $request->query('reference', '')));
        $application = null;
        $error = null;

        if ($query !== '') {
            $application = \App\Models\Application::with('program')
                ->where(function ($q) use ($query) {
                    $q->where('reference', $query)
                      ->orWhere('national_id', $query)
                      ->orWhere('email', $query);
                })
                ->latest()
                ->first();

            if (! $application) {
                $error = 'No application record found for "'.$query.'". Please verify your reference number or CNIC.';
            }
        }

        return $this->view('public.track', [
            'site' => $site,
            'query' => $query,
            'application' => $application,
            'searchError' => $error,
            'metaTitle' => 'Track Application Status | '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science'),
            'metaDescription' => 'Track your online admission application status, document verification progress, and interview notifications.',
        ]);
    }

    public function meritLists(Request $request): View
    {
        $site = $this->site();
        $category = trim((string) $request->query('category'));
        $search = trim((string) $request->query('search'));

        $query = \App\Models\MeritList::with('program:id,name')
            ->where('is_published', true)
            ->when($category, fn ($q) => $q->where('category', $category))
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->orderByDesc('published_at');

        $documents = $query->paginate(12)->withQueryString();

        return $this->view('public.merit-lists', [
            'site' => $site,
            'documents' => $documents,
            'activeCategory' => $category,
            'search' => $search,
            'metaTitle' => 'Merit Lists & Downloads | '.($site['college_name'] ?? 'Dir College Of Nursing & Allied Health Science'),
            'metaDescription' => 'Download official merit lists, admission fee challan, college prospectus, and academic guidelines.',
        ]);
    }

    public function sitemap()
    {
        $urls = collect([
            url('/'),
            url('/about'),
            url('/programs'),
            url('/faculty'),
            url('/admissions'),
            url('/campus-life'),
            url('/merit-lists'),
            url('/track'),
            url('/news-events'),
            url('/apply'),
            url('/contact'),
        ])->merge(
            Event::where('is_published', true)->pluck('slug')->map(fn ($slug) => url('/news-events/'.$slug))
        );

        return response()
            ->view('public.sitemap', ['urls' => $urls], 200)
            ->header('Content-Type', 'application/xml');
    }
}
