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
    public const MCP_CONNECTOR_URL = 'https://mcp.truckit.net/mcp';

    /**
     * Seed the application's database.
     */
    public function run(
        SiteSettings $siteSettings
    ): void {
        $siteSettings->name = 'TruckIt Connect';
        $siteSettings->description = 'Get an instant Truckit price for your move, right in the chat. Then book it on Truckit in a few taps.';
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
                'bio' => 'Builds the Truckit MCP server and TruckIt Connect on Laravel.',
                'website' => 'https://truckit.net',
            ]);

            User::query()->firstOrCreate([
                'email' => 'admin@test.com',
            ], [
                'name' => 'TruckIt Admin',
                'role' => UserRole::Admin,
                'password' => bcrypt('password'),
            ])->update([
                'job_title' => 'Connector Program Admin',
                'bio' => 'Manages connector help content, FAQs, and Filament site settings.',
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
            'title' => 'Moving something big? Just ask Claude.',
            'description' => $siteSettings->description,
            'tags' => ['home', 'truckit', 'claude', 'connector'],
        ]);

        $applySeo(
            $landingPage,
            'Moving something big? Just ask Claude. | Truckit',
            'Get an instant Truckit price in Claude for Australian freight and removals. Free to get a price—book and pay safely on truckit.net.',
        );

        $aboutPage = StaticPage::query()->updateOrCreate([
            'slug' => 'about-us',
            'type' => PageType::ContentPage,
        ], [
            'title' => 'How it works',
            'description' => 'Three steps, one question—add Truckit to Claude, ask for a price, book on Truckit.',
            'tags' => ['about', 'truckit', 'claude', 'how-it-works'],
            'content' => implode('', [
                '<p><strong>Truckit</strong> is Australia’s freight marketplace. <strong>Truckit in Claude</strong> gives you a real Book Now price in chat—not a budget guess—then hands you off to book on truckit.net.</p>',
                '<h2>Three steps, one question</h2>',
                '<ol>',
                '<li><strong>Add Truckit to Claude</strong> — it takes under a minute. In Claude, open Settings → Connectors → Add custom connector, name it Truckit, and paste <code>'.self::MCP_CONNECTOR_URL.'</code>. Once Truckit is listed in Claude’s connector directory, you’ll find it there too.</li>',
                '<li><strong>Ask for a price</strong> — for example: “How much to move a 3 seater couch from New Farm to Byron Bay?”</li>',
                '<li><strong>Book on Truckit</strong> — tap the link in chat; your job is ready to book. Payment happens on Truckit, never inside the chat.</li>',
                '</ol>',
                '<h2>A real price, not a guess</h2>',
                '<ul>',
                '<li><strong>Priced on the spot</strong> — Truckit prices your job straight away. No forms, no waiting for quotes.</li>',
                '<li><strong>Rated providers</strong> — every job goes to a reviewed transport provider.</li>',
                '<li><strong>Pay safely on Truckit</strong> — you book and pay on Truckit, not inside the chat.</li>',
                '<li><strong>Moves of every size</strong> — furniture, vehicles, pallets and more.</li>',
                '</ul>',
            ]),
        ]);

        $applySeo(
            $aboutPage,
            'How Truckit works in Claude',
            'Add Truckit to Claude in under a minute, ask for an instant price, and book on truckit.net—three steps for Australian freight and removals.',
        );

        $faqPage = StaticPage::query()->updateOrCreate([
            'slug' => 'faq',
            'type' => PageType::Faq,
        ], [
            'title' => 'Good to know',
            'name' => 'faq',
            'description' => 'Questions about Truckit in Claude.',
            'tags' => ['faq', 'claude', 'connector'],
            'content' => '<p>Answers about pricing, accounts, privacy, and using Truckit in Claude and ChatGPT.</p>',
        ]);

        $applySeo(
            $faqPage,
            'Good to know — Truckit in Claude',
            'Is Truckit in Claude free? Do you need an account? Is the price final? What data does Truckit see? Does it work in ChatGPT?',
        );

        $contactPage = StaticPage::query()->updateOrCreate([
            'slug' => 'contact',
            'type' => PageType::Contact,
        ], [
            'title' => 'Contact',
            'name' => 'contact',
            'description' => 'Help with Truckit in Claude or marketplace hauls.',
            'tags' => ['contact', 'support', 'connector'],
            'content' => sprintf(
                '<p>Need help adding Truckit to Claude or using the connector? Email <a href="mailto:%1$s">%1$s</a>.</p>'
                .'<p>For hauls already on truckit.net, use your usual Truckit support channels.</p>'
                .'<p class="text-sm">Claude is a product of Anthropic. Truckit is not affiliated with Anthropic.</p>',
                $siteSettings->contact_email,
            ),
        ]);

        $applySeo(
            $contactPage,
            'Contact TruckIt Connect',
            'Contact Truckit about the Claude connector or freight on truckit.net.',
        );

        $faqs = [
            [
                'question' => 'Is it free?',
                'answer' => 'Yes—getting an instant Truckit price in Claude is free. There is no charge to connect Truckit or to ask for a quote. You only pay Truckit when you book a job on truckit.net, at the price shown in chat (including GST where applicable).',
                'sort_order' => 1,
            ],
            [
                'question' => 'Do I need a Truckit account?',
                'answer' => 'Not to get a price. You can ask Claude for an instant quote without signing in. You will need a Truckit account—and to sign in through OAuth in Claude—before posting a job, booking, or managing your listings, so Truckit only acts on your behalf when you approve it.',
                'sort_order' => 2,
            ],
            [
                'question' => 'Is the price final?',
                'answer' => 'When Truckit returns a Book Now price in Claude, it is the total in Australian dollars including GST for that quoted job, subject to the items and suburbs you gave. The same amount should appear at truckit.net checkout. If your job cannot be priced instantly, Claude can post it to the marketplace so providers can quote instead.',
                'sort_order' => 3,
            ],
            [
                'question' => 'What does Truckit see?',
                'answer' => 'For a quote, Truckit uses what you describe in chat—typically pickup and drop-off suburbs, items, dates, and access notes—not your full Claude conversation. After you connect your account, Truckit sees only the data needed for the tools you use (your jobs, quotes, and bookings). Suburbs are used before booking; street addresses are collected on truckit.net when you pay.',
                'sort_order' => 4,
            ],
            [
                'question' => 'Does it work in ChatGPT?',
                'answer' => 'Yes. The same public Truckit MCP server is built for Claude’s Connectors Directory and for ChatGPT’s plugin directory in the same release, so you can get Truckit prices where you already chat. Setup steps differ slightly by app; the connector URL is '.self::MCP_CONNECTOR_URL.'.',
                'sort_order' => 5,
            ],
            [
                'question' => 'How do I add Truckit to Claude?',
                'answer' => 'In Claude, open Settings, then Connectors. Choose Add custom connector, name it Truckit, and paste '.self::MCP_CONNECTOR_URL.'. Start a chat and ask for a price—for example, how much to move a 2 bedroom apartment from Newtown to Wollongong. Free to get a price; no sign in needed for many instant quotes.',
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
