<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PageType;
use App\Enums\UserRole;
use App\Models\Faq;
use App\Models\StaticPage;
use App\Models\User;
use App\Settings\SiteSettings;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(
        SiteSettings $siteSettings
    ): void {
        $siteSettings->name = 'TruckIt Connect';
        $siteSettings->description = 'The TruckIt web surface for logistics partners—public pages, admin tooling, and MCP integrations tied to TruckIt freight data.';
        $siteSettings->contact_email = 'hello@truckit.net';
        $siteSettings->save();

        if (app()->isLocal()) {
            User::query()->firstOrCreate([
                'email' => 'developer@test.com',
            ], [
                'name' => 'TruckIt Developer',
                'role' => UserRole::Developer,
                'password' => bcrypt('password'),
            ])->update([
                'job_title' => 'Platform Engineer',
                'bio' => 'Builds TruckIt Connect and MCP tooling on Laravel.',
                'website' => 'https://truckit.net',
            ]);

            User::query()->firstOrCreate([
                'email' => 'admin@test.com',
            ], [
                'name' => 'TruckIt Admin',
                'role' => UserRole::Admin,
                'password' => bcrypt('password'),
            ])->update([
                'job_title' => 'Operations Admin',
                'bio' => 'Manages TruckIt site content and Filament settings.',
            ]);

            User::query()->firstOrCreate([
                'email' => 'user@test.com',
            ], [
                'name' => 'TruckIt User',
                'role' => UserRole::User,
                'password' => bcrypt('password'),
            ]);
        }

        $applySeo = function (
            StaticPage $page,
            string $title,
            string $description,
        ): void {
            $page->seo()->updateOrCreate([
                'model_id' => $page->getKey(),
                'model_type' => $page->getMorphClass(),
            ], [
                'meta_title' => $title,
                'meta_description' => $description,
                'og_title' => $title,
                'og_description' => $description,
                'robots' => ['index', 'follow'],
            ]);
        };

        $landingPage = StaticPage::query()->updateOrCreate([
            'slug' => 'landing-page',
            'type' => PageType::LandingPage,
        ], [
            'title' => $siteSettings->name,
            'description' => $siteSettings->description,
            'tags' => ['home', 'truckit', 'logistics'],
        ]);

        $applySeo(
            $landingPage,
            $siteSettings->name,
            $siteSettings->description,
        );

        $aboutPage = StaticPage::query()->updateOrCreate([
            'slug' => 'about-us',
            'type' => PageType::ContentPage,
        ], [
            'title' => 'About TruckIt',
            'description' => 'How TruckIt connects shippers, carriers, and internal teams.',
            'tags' => ['about', 'truckit', 'freight'],
            'content' => implode('', [
                '<p><strong>TruckIt</strong> is a freight marketplace that matches shippers who need heavy haul capacity with qualified carriers.</p>',
                '<p><strong>TruckIt Connect</strong> is this Laravel application: the public marketing and support site, Filament admin for content, and MCP endpoints that expose TruckIt data to trusted tools.</p>',
                '<p>Local development can read from the business-api and monolith databases while the app database stores sessions, settings, and CMS content. Visit <a href="https://truckit.net">truckit.net</a> for the live marketplace.</p>',
            ]),
        ]);

        $applySeo(
            $aboutPage,
            'About TruckIt',
            'Learn how TruckIt moves freight, what TruckIt Connect does, and how this site fits the TruckIt platform.',
        );

        $faqPage = StaticPage::query()->updateOrCreate([
            'slug' => 'faq',
            'type' => PageType::Faq,
        ], [
            'title' => 'Frequently Asked Questions',
            'name' => 'faq',
            'description' => 'Common questions about TruckIt and this site.',
            'tags' => ['faq', 'help', 'truckit'],
            'content' => '<p>Answers about TruckIt hauling, TruckIt Connect, and getting support.</p>',
        ]);

        $applySeo(
            $faqPage,
            'Frequently Asked Questions',
            'TruckIt and TruckIt Connect FAQs: freight marketplace basics, MCP integrations, and how to reach the team.',
        );

        $contactPage = StaticPage::query()->updateOrCreate([
            'slug' => 'contact',
            'type' => PageType::Contact,
        ], [
            'title' => 'Contact',
            'name' => 'contact',
            'description' => 'Reach TruckIt about hauling, partnerships, or this site.',
            'tags' => ['contact', 'support', 'truckit'],
            'content' => sprintf(
                '<p>Email TruckIt at <a href="mailto:%1$s">%1$s</a> for haul requests, carrier onboarding, or TruckIt Connect questions.</p>',
                $siteSettings->contact_email,
            ),
        ]);

        $applySeo(
            $contactPage,
            'Contact TruckIt',
            'Contact TruckIt for freight quotes, carrier support, partnerships, or help with TruckIt Connect.',
        );

        $faqs = [
            [
                'question' => 'What is TruckIt?',
                'answer' => 'TruckIt is a freight marketplace where shippers post heavy haul and specialty loads and vetted carriers bid or accept work. The platform handles discovery, communication, and operational handoffs so both sides spend less time on phone tag.',
                'sort_order' => 1,
            ],
            [
                'question' => 'What is TruckIt Connect?',
                'answer' => 'TruckIt Connect is this Laravel application. It serves the public TruckIt-facing pages you see in the browser, the Filament admin for FAQs and static content, and MCP tooling that lets approved clients query TruckIt business data safely.',
                'sort_order' => 2,
            ],
            [
                'question' => 'How does this app talk to TruckIt production data?',
                'answer' => 'The app keeps its own MySQL database for sessions, cache, queues, and CMS tables. Read-only connections to the business-api and monolith databases are configured separately so engineers can inspect live TruckIt data without mixing it into migrations.',
                'sort_order' => 3,
            ],
            [
                'question' => 'Who can access the Filament admin?',
                'answer' => 'Only authenticated TruckIt staff accounts with the right roles can open /admin. Local seeds create developer and admin users for testing; production accounts are provisioned through your normal TruckIt identity process.',
                'sort_order' => 4,
            ],
            [
                'question' => 'How do I get help with a haul or carrier issue?',
                'answer' => 'For live freight on truckit.net, use the marketplace support channels or your account rep. For this Connect site or MCP integrations, use the contact page or email hello@truckit.net with load numbers or environment details so the platform team can respond quickly.',
                'sort_order' => 5,
            ],
            [
                'question' => 'What stack runs TruckIt Connect?',
                'answer' => 'PHP 8.3, Laravel 13, Livewire 4, Filament 5, and Tailwind CSS. Optional Open Graph image generation uses Browsershot when enabled. Run composer test before deploying changes that touch routes, settings, or MCP servers.',
                'sort_order' => 6,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::query()->updateOrCreate([
                'question' => $faq['question'],
            ], [
                'answer' => $faq['answer'],
                'sort_order' => $faq['sort_order'],
                'is_active' => true,
            ]);
        }
    }
}
